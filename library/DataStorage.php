<?php

class SandboxDataStorage extends OAuthDataStore {

	// Return OAuthConsumer if found, return something that evaluates to false if not
	function lookup_consumer($consumer_key) {
		$table = new ApiConsumer_Table();
		return $table->find('key', $consumer_key, AF::DELAY_SAFE);
	}

	// Return a OAuthToken if found, return something that evaluates to false if not
	function lookup_token($consumer, $token_type, $token_string) {
		if( $token_type == 'request' ) {
			$table = new ApiRequestToken_Table();
		} else if( $token_type == 'access' ) {
			$table = new ApiAccessToken_Table();
		}

		if( $table ) {
			$token = $table->find('key', $token_string, AF::DELAY_SAFE);
		} else {
			$token = NULL;
		}
		
		if( $token && $consumer->id == $token->consumerid ) {
			// The token belongs to the consumer that presented the token-string
			return $token;
		}
	}

	// Return something that evaluates to true if the token is known
	function lookup_nonce($consumer, $token, $nonce, $timestamp) {
		if( ! $consumer->check_nonce ) return false;
		
		$token = ($token) ? $token->key : 'two-legged';
		
		$table = new ApiNonce_Table();
		$nonce_obj = reset($table->fetch('consumerid = ? AND token = ? AND nonce = ? AND timestamp = ?', array($consumer->id, $token, $nonce, $timestamp), AF::DELAY_SAFE));
		
		if( $nonce_obj ) return true;
		
		$nonce_obj = $table->create();
		$nonce_obj->consumerid = $consumer->id;
		$nonce_obj->token = $token;
		$nonce_obj->nonce = $nonce;
		$nonce_obj->timestamp = $timestamp;
		$nonce_obj->save();
		
	    return false;
	}

	// Return a fresh OAuthToken
	function new_request_token($consumer, $callback) {
	    // return a new token attached to this consumer
		$table = new ApiRequestToken_Table();
		$token = $table->create();
		$token->consumerid = $consumer->id;
		$token->authorized = 0;
		if( $callback ) {
			$token->callback = $callback;
		}
		$token->save();
		return $token;
	}

	function new_access_token($request_token, $consumer, $verifier) {
		$at_table = new ApiAccessToken_Table();
		$rt_table = new ApiRequestToken_Table();

		// Delete the Request Token
		$rt_table->query("DELETE FROM :table WHERE :primary = ?", array($request_token->id), AF::NO_DELAY);
	
		if( $consumer->id === $request_token->consumerid ) {
			if( $request_token->authorized && $request_token->userid ) {
				if( $request_token->verifier !== NULL && $request_token->verifier !== $verifier ) {
					// We are running Revision A but the recieved verifier doesn't match the verifier issued
					throw new OAuthException('Invalid verifier. Your Access Token was still deleted though. Nice Try.');
				}
				
				$access_token = $at_table->create();
				$access_token->consumerid = $consumer->id;
				$access_token->userid = $request_token->userid;
				$access_token->save(AF::NO_DELAY);			
				
				return $access_token;
			} else {
				// Token wasn't authorized
				throw new OAuthException('You can\'t swap a unauthorized request token for a access token. Your Access Token was still deleted though. Nice try..');
			}
		} else {
			// Token was fubar
			throw new OAuthException('This Request Token doesn\'t belong to your Consumer Key. Your Access Token was still deleted though. Nice Try.');
		}
	}
}
