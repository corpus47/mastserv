<script>
//js for addSubcontactor.view.php
$(document).ready(function(){
	//$('#subcontactor-form').submit(function(){
	$('#subcontactor-form-submit').click(function(){
		
		$('.error').remove();
		
		var ret = true;
		
		if($('input[name="subcontactor-name"]').val() == "") {
			$('input[name="subcontactor-name"]').after('<div class="error">A név nem lehet üres!</div>');
			ret = false
		} else if($('input[name="subcontactor-name"]').val() !== "") {
			//$('.page-loader-wrapper').show();
			// Egyediség vizsgálata ajax-szal
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'checkunique_subcontactor', fieldname : 'Name', value : $('input[name="subcontactor-name"]').val(), s_id : session_id },
				success:function(data){
					if(data['status'] == 'true') {
						$('input[name="subcontactor-name"]').after('<div class="error">Ezen a néven vállalkozó már létezik a rendszerben!</div>');
						ret = false;
					} else {
						ret = true;
					}
					//$('.page-loader-wrapper').hide();
				}
			/*}).done(function(data){
				if(data['status'] === 'true') {
					alert('Status '+data['status']);
					$('input[name="user-name"]').after('<div class="error">A felhasználónév már létezik a rendszerben!</div>');
					ret = false;
				}*/
			});
			
		}
		
		if($('input[name="subcontactor-contactperson"]').val() == "") {
			$('input[name="subcontactor-contactperson"]').after('<div class="error">A név nem lehet üres!</div>');
			ret = false
		}
		
		if($('input[name="subcontactor-phonenum"]').val() == "") {
			//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
			$('input[name="subcontactor-phonenum"]').after('<div class="error">Adjon meg telefonszámot!</div>');
			ret = false;
		} else {
			var phonenum_match = /^\d{11}$/;
			var phonenum = $('input[name="subcontactor-phonenum"]').val();
			if(!phonenum.match(phonenum_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="subcontactor-phonenum"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		if($('input[name="subcontactor-email"]').val() == "") {
			$('input[name="subcontactor-email"]').after('<div class="error">Az e-mail nem lehet üres!</div>');
			ret = false
		} else {
			var email_match = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			var email = $('input[name="subcontactor-email"]').val();
			if(!email.match(email_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="subcontactor-email"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		if(ret == true) {
			$('#subcontactor-form').submit();
		}
		
	});
	
	$('.edit-form').find('input').focus(function(){
		$(this).next('.error').remove();
	});
});
</script>