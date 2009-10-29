$(function() {
	$("#step_1 button").click(function() {
		$("#step_2").show();
		$("#step_1").hide();
		$("#step_2 input")[0].focus();
	});
	
	$("#step_2 input").bind('blur keypress', function(e) { 
		if ( (e.type == 'keypress' && e.which == 13)
		  || e.type == 'blur' ) {
			var name = $(this).val();
			$("#teh_form form input[name=name]").val(name);
			$("#teh_form form").submit();
		}
	});
});
