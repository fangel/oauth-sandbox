<?php

require 'library/Sandbox.php';

$action = (isset($_GET['action'])) ? preg_replace('/\p{C}/u','', $_GET['action']) : NULL;

$oauth_server = new OAuthServer(new SandboxDataStorage());
$oauth_server->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
$oauth_server->add_signature_method(new OAuthSignatureMethod_PLAINTEXT());

$oauth_request = OAuthRequest::from_request();
$oauth_request->unset_parameter('action');

try {
	if( $action == 'request_token' ) {
		$token = $oauth_server->fetch_request_token( $oauth_request );
		echo "oauth_token=" . OAuthUtil::urlencode_rfc3986($token->key) . 
	         "&oauth_token_secret=" . OAuthUtil::urlencode_rfc3986($token->secret);
		if( isset($token->callback) && $token->callback )
			echo "&oauth_callback_confirmed=true";
	
	} else if( $action == 'access_token' ) {
		$token = $oauth_server->fetch_access_token( $oauth_request );
		echo "oauth_token=" . OAuthUtil::urlencode_rfc3986($token->key) . 
         	"&oauth_token_secret=" . OAuthUtil::urlencode_rfc3986($token->secret);

	} else if( $action == 'authorize' ) {
		if( !$oauth_request->get_parameter('oauth_token') ) throw new Exception('Missing Token');
		
		$rt_table = new ApiRequestToken_Table();
		$c_table = new ApiConsumer_Table();
		$u_table = new User_Table();
		
		$token = $rt_table->find('key', $oauth_request->get_parameter('oauth_token'), AF::DELAY_SAFE);
		if( !$token ) throw new Exception('Unknown Token');
		
		$cons  = $c_table->get($token->consumerid, AF::DELAY_SAFE);
		if( !$cons )  throw new Exception('Failed to find Consumer');
		$cons_author = $u_table->get( $cons->userid, AF::DELAY_SAFE );
		
		if( $token->callback ) {
			$callback = $token->callback;
			$rev_a = true;
		} else {
			$callback = $oauth_request->get_parameter('oauth_callback');
			$rev_a = false;
		}
		
		$return_url = WEB_URL .'/authorize?oauth_token=' . $token->key;
		if( ! $rev_a ) $return_url .= '&oauth_callback=' . urlencode($callback);
		
		if( ! AF::registry()->user ) {
			AF::registry()->messages->login_reason = 'You need to log in to authorize application access';
			AF::registry()->messages->login_return_url = $return_url;
			header("Location: http://" . $_SERVER['HTTP_HOST'] . WEB_URL . '/#please_login');
			die;
		}
			
		if( isset($_POST['token'], $_POST['allow']) && AF::registry()->messages->csrf_token == $_POST['token']) {
			$token->userid = AF::registry()->user->id;
			$token->authorized = 1;
			
			if( $rev_a ) {
				$verifier = ($callback == 'oob') ? sprintf('%04d', rand(0,9999)) : substr(md5(uniqid(rand(), true)),0,16);
				$token->verifier = $verifier;
			}
			
			$token->save();
			
			if( $callback == NULL || $callback == 'oob' ) {
				$tmpl = AF::template();
				$tmpl->authorized = true;
				$tmpl->user = AF::registry()->user;
				$tmpl->rev_a = $rev_a;
				if( $rev_a )
					$tmpl->verifier = $token->verifier;
				$tmpl->display( 'templates/authorize.phtml' );
			} else {
				$callback .= (strpos($callback, '?')) ? '&' : '?';
				$callback .= 'oauth_token=' . $token->key;
				if( $rev_a ) {
					$callback .= '&oauth_verifier=' . $token->verifier;
				}
				header("Location: " . $callback);
				die;
			}
		} else {
			$csrf_token = substr(md5(uniqid(rand(), true)),0,16);;
			AF::registry()->messages->csrf_token = $csrf_token;
						
			$tmpl = AF::template();
			$tmpl->form_action = $return_url;
			$tmpl->consumer = $cons;
			$tmpl->consumer_author = $cons_author;
			$tmpl->csrf_token = $csrf_token;
			$tmpl->rev_a = $rev_a;
			$tmpl->authorized = false;
			$tmpl->user = AF::registry()->user;
			$tmpl->display( 'templates/authorize.phtml' );
		}
	} else if( $action == 'two_legged' ) {
		$user_table = new User_Table();
		list($consumer, $token) = $oauth_server->verify_two_legged_request($oauth_request);
		$consumer_author = $user_table->get( $consumer->userid, AF::DELAY_SAFE );
		
		if( $token )
			$user = $user_table->get( $token->userid, AF::DELAY_SAFE );
		
		echo 'SUCCESS! This is a 2-legged call from the `' . $consumer->name . '` consumer which was made by `' . $consumer_author->username . '`.';
		if( $token && $user ) {
			echo 'The call was, even though it wasn\'t needed, authorized by the user `' . $user->username . '`.';
		}
	} else if( $action == 'three_legged' ) {
		$user_table = new User_Table();
		
		list($consumer, $token) = $oauth_server->verify_request($oauth_request);
		$consumer_author = $user_table->get( $consumer->userid, AF::DELAY_SAFE );
		$user = $user_table->get( $token->userid, AF::DELAY_SAFE );
		
		echo 'SUCCESS! This 3-legged call which was authorized by `' . $user->username . '` to the consumer `' . $consumer->name . '` made by the user `' . $consumer_author->username . '`.';
	} else {
		throw new Exception('Unknown API Action');
	}
} catch( Exception $e ) {
	header("HTTP/500 Internal Server Error"); // Check if this is how you do it!
	echo $e->getMessage();
}
