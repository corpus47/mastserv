$(document).ready(function(){
	
	$('.theme-select').change(function(){
		$.ajax({
			url: ajax_url,
			type : "POST",
			dataType:"json",
			data : { ajax : 1, m : 'masterservice', act : 'changetheme', theme : $(this).val(), s_id : session_id },
		}).done(function(ret){
			if(ret['status'] == 'ok') {
				window.location.href = ret['http_referer'];
			}
		});
	});
	if(document.getElementById('ok_message_modal') !== null){
		$('#ok_message_modal').modal('show');
		setTimeout(function(){
			$('#ok_message_modal').modal('hide')
		}, 3000);
	}
	
	var $loading = $('.page-loader-wrapper');
	
	//var $anicircle_loader = $("#anicircle-loader");
	
	$(document)
		.ajaxStart(function () {
		$loading.show();
		//$anicircle_loader.gSpinner();
	})
		.ajaxStop(function () {
		$loading.hide();
		//$anicircle_loader.gSpinner("hide");
	});
	
	/*$('input.flat').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
    });*/
});
$(window).unload(function(){
		//alert('Itt');
});