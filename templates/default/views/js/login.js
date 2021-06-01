<script>
//javascript for login.view.php
$(document).ready(function(){
	/*$('.theme-select').change(function(){
		$.ajax({
			url: '[{%AJAX_URL%}]',
			type : "POST",
			dataType:"json",
			data : { ajax : 1, m : 'masterservice', act : 'changetheme', theme : $(this).val(), s_id : '[{%SESSION_ID%}]' },
		}).done(function(ret){
			console.log(ret);
			if(ret['status'] == 'ok') {
				window.location.href = ret['http_referer'];
			}
		});
	});
	$('select[name="login-type"]').change(function(){
		if($(this).val() == 2) {
			//alert("Mester belépés");
			$('input[name="username"]').attr('placeholder','Azonositó');
			$('.master-login-instruction').show();
		} else {
			$('input[name="username"]').attr('placeholder','Név');
			$('.master-login-instruction').hide();
		}
	});
	$("#signinForm").validate({
		rules: {
			login: "required",
				password: "required"
			},
			messages: {
				firstname: "Please enter your login",
				lastname: "Please enter your password"			
		}
	});*/
	$('#signinForm').submit(function(){
		var is_digit = /^\d+$/;
		var login_name = $('input[name="username"]').val();
		if(login_name.match(is_digit)){
			$('input[name="login-type"]').val(2);
		}
		
		/*var passw = $('input[name="password"]').val();
		
		var type = $('input[name="login-type"]').val();
		
		$.ajax({
			url : ajax_url,
			type : "POST",
			async : false,
			dataType:"json",
			data : { ajax : 1, m : 'masterservice', act : 'checklogged', username : login_name, password : passw, logintype : type, s_id : session_id },
		}).done(function(data){
			console.log(data);
		});*/

		return true;
		
		//return false;
	});
});
</script>
