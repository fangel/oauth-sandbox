var username = '';
var kitten = '';
var signup = false;

$(function() {
	$("#need_login").click(function() {
		$("#step_2").show();
		$("#step_1").hide();
		$("#username input")[0].focus();
	});
	
	$("#need_signup").click(function() {
		signup = true;
		$("#step_2 p").text('Choose the username you want');
		$("#step_2").show();
		$("#step_1").hide();
		$("#username input")[0].focus();
	});
	
	$("#username input").bind('blur keypress', function(e) { 
		if ( (e.type == 'keypress' && e.which == 13)
		  || e.type == 'blur' ) {
			if( signup ) {
				$("#step_3 p").text('Choose the kitten you want to guard your access');
			}
			username = $(this).val();
			$("#step_3").show();
			$("#step_2").hide();
		}
	});
	
	$("#kittens .kitten").click(function() {
		kitten = $(this).attr('src').match(/kitten_([a-z]*)\.jpg/)[1];
		
		$("#teh_form form input[name=username]").val(username);
		$("#teh_form form input[name=kitten]").val(kitten);
		if( signup ) {
			$("#teh_form form input[name=signup]").attr('checked','checked');
		}
		$("#teh_form form").submit();
	});
});
