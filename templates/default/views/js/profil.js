<script>
//javascript for profil.view.php
$(document).ready(function(){
	
	$('a').bind('click',function(){
		if($('input[name="lock"]').val() == uid){
			//alert('Zárolva');
			$('.alert').show("slow");
			return false;
		}
		return true;
	});
	
	$('#profil-form-cancel').click(function(){
		
		var cancel_href = $(this).data('cancel-href');
		
		var id = $(this).data('id');

		console.log(id);
		
		unlock(id,cancel_href);
		
		return false;
	});
	
	$('#profil-edit-form-submit').click(function(){

		$('.error').remove();
		
		var ret = true;
		
		if($('input[name="pwd-one"]').val() !== "") {
			var md5_passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
			var passw = $('input[name="pwd-one"]').val();
			if(!passw.match(md5_passw)) {

				$('input[name="pwd-one"]').after('<div class="error">Hibás jelszó formátum (Legyen benne szám és legalább egy nagybetű!)</div>');
				ret = false;
			} else {
				first_pwd = true;
			}	
		}
		
		if($('input[name="pwd-true"]').val() !== "") {
			var md5_passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
			var passw_two = $('input[name="pwd-true"]').val();
			if(!passw_two.match(md5_passw)) {

				$('input[name="pwd-true"]').after('<div class="error">Hibás jelszó formátum (Legyen benne szám és legalább egy nagybetű!)</div>');
				ret = false;
			} 
			// Compare passwords
			if(passw != passw_two) {
				//$('input[name="master_password_secound"]').css('border-color','#da4453');
				$('input[name="pwd-true"]').after('<div class="error">A két jelszó nem egyezik</div>');
				ret = false;
			}
		}
		
		if($('input[name="user-phonenum"]').val() == "") {
			//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
			$('input[name="user-phonenum"]').after('<div class="error">Adjon meg telefonszámot!</div>');
			ret = false;
		} else {
			//var phonenum_match = /^\d{11}$/;
			var phonenum_match = /^[\s()+-]*([0-9][\s()+-]*){11}$/;
			var phonenum = $('input[name="user-phonenum"]').val();
			if(!phonenum.match(phonenum_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="user-phonenum"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		if($('input[name="user-email"]').val() == "") {
			$('input[name="user-email"]').after('<div class="error">Az e-mail nem lehet üres!</div>');
			ret = false;
		} else {
			var email_match = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			var email = $('input[name="user-email"]').val();
			if(!email.match(email_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="user-email"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		console.log(ret);

		if(ret === true){
			$('#profil-edit-form').submit();
		}
		
	});
	
});

function unlock(id,cancel_href) {
	
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'user_unlock', id : id, s_id : session_id },
		success:function(data){
			console.log(data);
			location.href = cancel_href;			
		}
	});
}

</script>