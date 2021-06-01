<script>
$(document).ready(function(){

	$('a').bind('click',function(){

		if($('input[name="lock"]').val() == uid){
			//alert('Zárolva');
			$('.alert').show("slow");
			return false;
		}
		return true;
	});
	
	$('#subclient-form-cancel').click(function(){
		
		var cancel_href = $(this).data('cancel-href');
		
		var id = $(this).data('id');
		
		unlock(id);

		window.location.href = cancel_href;
		return false;
	});
	$('.client-installation-checkbox').click(function(){
		//var installation_id = $(this).find('input[type="checkbox"]').attr('id');
		var installation_id = $(this).data('id');
		//alert(installation_id);
		//var check = $(this).find('input[type="checkbox"]').is(":checked");
		var check = $(this).is(":checked");
		console.log(check);
		if(check !== false) {
			$('#options-container-'+installation_id).show();
		} else {
			$('#options-container-'+installation_id).hide();
		}
	});
	
	
	$('#subclient-edit-form-submit').click(function(){
		
		$('.error').remove();
		
		var ret = true;
		
		if($('#subclient-clientid').val() == "") {
			$('#subclient-clientid').after('<div class="error">Választania kell megbízót!</div>');
			ret = false
		}
		
		if($('input[name="subclient-name"]').val() == "") {
			$('input[name="subclient-name"]').after('<div class="error">A név nem lehet üres!</div>');
			ret = false
		}
		
		if($('input[name="subclient-prefix"]').val() == "") {
			$('input[name="subclient-prefix"]').after('<div class="error">Az előtag nem lehet üres!</div>');
			ret = false
		}
		
		if($('input[name="subclient-city"]').val() == "") {
			$('input[name="subclient-city"]').after('<div class="error">A helység nem lehet üres!</div>');
			ret = false
		}
		
		if($('input[name="subclient-address"]').val() == "") {
			$('input[name="subclient-address"]').after('<div class="error">A cím nem lehet üres!</div>');
			ret = false
		}
		
		if($('input[name="subclient-phonenum"]').val() == "") {
			//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
			$('input[name="subclient-phonenum"]').after('<div class="error">Adjon meg telefonszámot!</div>');
			ret = false;
		} else {
			/*var phonenum_match = /^\d{11}$/;*/
			//var phonenum_match = /^[\s()+-]*([0-9][\s()+-]*){6,20}$/;
			var phonenum_match = /^[\s()+-]*([0-9][\s()+-]*){11}$/;
			var phonenum = $('input[name="subclient-phonenum"]').val();
			if(!phonenum.match(phonenum_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="subclient-phonenum"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}
		
		if($('input[name="subclient-email"]').val() == "") {
			$('input[name="subclient-email"]').after('<div class="error">Az e-mail nem lehet üres!</div>');
			ret = false;
		} else {
			var email_match = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			var email = $('input[name="subclient-email"]').val();
			if(!email.match(email_match)) {
				//$('input[name="subcontactor-phonenum"]').css('border-color','#da4453');
				$('input[name="subclient-email"]').after('<div class="error">Nem megfelelő formátum!</div>');
				ret = false;
			}
		}

		if(ret == true) {
			$('#subclient-edit-form').submit();
		}
		return false;
	});
	
	$('.edit-form').find('input').focus(function(){
		$(this).next('.error').remove();
	});
	
	$('.edit-form').find('select').focus(function(){
		$(this).next('.error').remove();
	});
	
	$("#subclient-zipcode-select").change(function(){
		
		$('input[name="subclient-zipcode"]').val($("#subclient-zipcode-select option:selected").text());

		$("#subclient-city").val($(this).val());
	});
	
	$('input[name="subclient-prefix"]').focusin(function(){
		
		var client_name = $('input[name="subclient-name"]').val();
		
		client_name = client_name.noaccent();
		
		console.log(client_name);
		
		$(this).val(client_name.substring(0,3).toUpperCase());
		
	});
	
});

var hun_map = {
	    'Á': 'A',
	    'á': 'a',
	    'É': 'E',
	    'é': 'e',
	    'Í': 'I',
		'í': 'i',
		'Ó': 'O',
		'ó': 'o',
		'Ö': 'O',
		'ö': 'o',
		'Ő': 'O',
		'ő': 'o',
		'Ü': 'U',
		'ü': 'u',
		'Ű': 'U',
		'ű': 'u',
		'ú': 'u',
		'Ú': 'U',
	};

String.prototype.noaccent = function() {
	return this.replace(/[^A-Za-z0-9]/g, function(x) { return hun_map[x] || x; })
};

function unlock(id) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'subclient_unlock', id : id, s_id : session_id },
		success:function(data){
			console.log(data);			
		}
	});
}

</script>