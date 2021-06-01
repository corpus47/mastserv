<script>
//js for addorder.view.php
$(document).ready(function(){
	//$('#user-form').submit(function(){
	$('#order-form-submit').click(function(){

		$('.error').remove();
		
		var ret = true;
		
		if($('input[name="order-name"]').val() == "") {
			$('input[name="order-name"]').after('<div class="error">A név nem lehet üres!</div>');
			ret = false;
		} else if($('input[name="order-name"]').val() !== "") {
			//$('.page-loader-wrapper').show();
			// Egyediség vizsgálata ajax-szal
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'checkunique_user', fieldname : 'Login', value : $('input[name="order-name"]').val(), s_id : session_id },
				success:function(data){
					if(data['status'] == 'true') {
						$('input[name="order-name"]').after('<div class="error">A felhasználónév már létezik a rendszerben!</div>');
						ret = false;
					} else {
						ret = true;
					}
					//$('.page-loader-wrapper').hide();
				}
			/*}).done(function(data){
				if(data['status'] === 'true') {
					alert('Status '+data['status']);
					$('input[name="order-name"]').after('<div class="error">A felhasználónév már létezik a rendszerben!</div>');
					ret = false;
				}*/
			});
			
		}
		
		// Check first password
		/*if($('input[name="pwd-one"]').val() == "") {

			$('input[name="pwd-one"]').after('<div class="error">A jelszó nem lehet üres</div>');
			ret = false;
		} else {
			var md5_passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
			var passw = $('input[name="pwd-one"]').val();
			if(!passw.match(md5_passw)) {

				$('input[name="pwd-one"]').after('<div class="error">Hibás jelszó formátum (Legyen benne szám és legalább egy nagybetű!)</div>');
				ret = false;
			} else {
				first_pwd = true;
			}	
		}*/
		// Check secound password
		/*if($('input[name="pwd-true"]').val() == "") {

			$('input[name="pwd-true"]').after('<div class="error">A jelszó nem lehet üres</div>');
			ret = false;
		} else {
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
		}*/
		
		
		/*if($('input[name="order-cartype"]').val() == "") {
			$('input[name="order-cartype"]').after('<div class="error">A gépjármű típusa nem lehet üres!</div>');
			ret = false
		}*/
		
		/*if($('input[name="order-lpnumber"]').val() == "") {
			$('input[name="order-lpnumber"]').after('<div class="error">A gépjármű rendszáma nem lehet üres!</div>');
			ret = false
		}*/
		
		if($('input[name="order-city"]').val() == "") {
			$('input[name="order-city"]').after('<div class="error">A helység nem lehet üres!</div>');
			ret = false
		}
		
		if($('input[name="order-address"]').val() == "") {
			$('input[name="order-address"]').after('<div class="error">A cím nem lehet üres!</div>');
			ret = false
		}
		
		if($('input[name="order-phonenum"]').val() == "") {
			//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
			$('input[name="order-phonenum"]').after('<div class="error">Adjon meg telefonszámot!</div>');
			ret = false;
		} else {
			var phonenum_match = /^\d{11}$/;
			var phonenum = $('input[name="order-phonenum"]').val();
			if(!phonenum.match(phonenum_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="order-phonenum"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		if($('input[name="order-email"]').val() == "") {
			$('input[name="order-email"]').after('<div class="error">Az e-mail nem lehet üres!</div>');
			ret = false;
		} else {
			var email_match = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			var email = $('input[name="order-email"]').val();
			if(!email.match(email_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="order-email"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		console.log(ret);

		if(ret === true){
			$('#order-form').submit();
		}	
	});
	
	$('.edit-form').find('input').focus(function(){
		$(this).next('.error').remove();
	});
	
	var $loading = $('.page-loader-wrapper');
	$(document)
		.ajaxStart(function () {
		$loading.show();
	})
		.ajaxStop(function () {
		$loading.hide();
	});
	
	$("#cities-list").change(function(){
		$("#order-city").val($(this).val());
	});
	
	$("#order-add-item").click(function(){
		
	});
	
});
</script>
