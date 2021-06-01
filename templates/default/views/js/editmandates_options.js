<script>
//js for addSubcontactor.view.php
$(document).ready(function(){
	//$('#user-form').submit(function(){
	$('#edit-mandates-option-form-submit').click(function(){

		$('.error').remove();
		
		var ret = true;
		
		if($('input[name="mandates-option-name"]').val() == "") {
			$('input[name="mandates-option-name"]').after('<div class="error">A név nem lehet üres!</div>');
			ret = false;
		} /*else if($('input[name="mandates-option-name"]').val() !== "") {
			//$('.page-loader-wrapper').show();
			// Egyediség vizsgálata ajax-szal
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'checkunique_mandates_option', fieldname : 'OptionName', value : $('input[name="mandates-option-name"]').val(), s_id : session_id },
				success:function(data){
					if(data['status'] == 'true') {
						$('input[name="mandates-option-name"]').after('<div class="error">A név már létezik a rendszerben!</div>');
						ret = false;
					} else {
						ret = true;
					}
					//$('.page-loader-wrapper').hide();
				}
			}).done(function(data){
				if(data['status'] === 'true') {
					alert('Status '+data['status']);
					$('input[name="master-name"]').after('<div class="error">A felhasználónév már létezik a rendszerben!</div>');
					ret = false;
				}
			});
			
		}*/
		
		if($('input[name="mandates-option-distance"]').val() == "") {
			$('input[name="mandates-option-distance"]').after('<div class="error">A távolság nem lehet üres!</div>');
			ret = false;
		}
		
		console.log(ret);

		if(ret === true){
			$('#edit-mandates-option-form').submit();
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
});
</script>
