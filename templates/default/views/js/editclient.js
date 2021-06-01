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
	
	$('#client-form-cancel').click(function(){
		var cancel_href = $(this).data('cancel-href');
		
		var id = $(this).data('id');
		
		unlock(id);
		
		//console.log(cancel_href);
		window.location.href = cancel_href;
		return false;
	});
	
	$('#client-edit-form-submit').click(function(){
		
		$('.error').remove();
		
		var ret = true;
		
		if($('input[name="client-name"]').val() == "") {
			$('input[name="client-name"]').after('<div class="error">A név nem lehet üres!</div>');
			ret = false
		}
		
		console.log(ret);
		
		if(ret === true){
			$('#client-edit-form').submit();
		}
		
	});
	
	/*$('#client-form-cancel').click(function(){
		$('#client-edit-form').cancel();
	});*/
	
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
	
	/*$('#client-options-form-submit').click(function() {
		$('#client-options-form').submit();
	});*/
	
	
});
function unlock(id) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'client_unlock', id : id, s_id : session_id },
		success:function(data){
			console.log(data);			
		}
	});
}
</script>


