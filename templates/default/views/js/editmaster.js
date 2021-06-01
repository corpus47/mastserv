<script>
/* edituser */
$(document).ready(function(){
	
	$('a').bind('click',function(){

		if($('input[name="lock"]').val() == uid){
			//alert('Zárolva');
			$('.alert').show("slow");
			return false;
		}
		return true;
	});
	
	//$('#user-form').submit(function(){
	$('#master-edit-form-submit').click(function(){
		
		
		
		$('.error').remove();
		
		var ret = true;
		
		/*if($('input[name="master-name"]').val() == "") {
			$('input[name="master-name"]').after('<div class="error">A név nem lehet üres!</div>');
			ret = false;
		} else if($('input[name="master-name"]').val() !== "") {
			//$('.page-loader-wrapper').show();
			// Egyediség vizsgálata ajax-szal
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'checkunique_user', fieldname : 'Login', value : $('input[name="master-name"]').val(), s_id : session_id },
				success:function(data){
					if(data['status'] == 'true') {
						$('input[name="master-name"]').after('<div class="error">A felhasználónév már létezik a rendszerben!</div>');
						ret = false;
					} else {
						ret = true;
					}
				}
			});
			
		}*/
		
		// Check first password
		/*if($('input[name="pwd-one"]').val() == "") {

			$('input[name="pwd-one"]').after('<div class="error">A jelszó nem lehet üres</div>');
			ret = false;
		} else {*/
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
		// Check secound password
		/*if($('input[name="pwd-true"]').val() == "") {

			$('input[name="pwd-true"]').after('<div class="error">A jelszó nem lehet üres</div>');
			ret = false;
		} else {*/
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
		
		
		/*if($('input[name="master-fullname"]').val() == "") {
			$('input[name="master-fullname"]').after('<div class="error">A teljes név nem lehet üres!</div>');
			ret = false
		}*/
		
		if($('input[name="master-phonenum"]').val() == "") {
			//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
			$('input[name="master-phonenum"]').after('<div class="error">Adjon meg telefonszámot!</div>');
			ret = false;
		} else {
			var phonenum_match = /^\d{11}$/;
			var phonenum = $('input[name="master-phonenum"]').val();
			if(!phonenum.match(phonenum_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="master-phonenum"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		if($('input[name="master-email"]').val() == "") {
			$('input[name="master-email"]').after('<div class="error">Az e-mail nem lehet üres!</div>');
			ret = false;
		} else {
			var email_match = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			var email = $('input[name="master-email"]').val();
			if(!email.match(email_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="master-email"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		console.log(ret);

		if(ret === true){
			$('#master-edit-form').submit();
		}
		
		
		
	});
});
</script>