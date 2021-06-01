$(document).ready(function(){
	
	$("input:file").change(function (){
       var fileName = $(this).val();
       $("#filename-placeholder").html(fileName);
    });

    $('input[name="bug-url"]').click(function(){
    	$(this).css('border','1px solid #ddd');
    });

    $('textarea[name="bug-description"]').click(function(){
    	$(this).css('border','1px solid #ddd');
    });

    $("#new-bug-form").submit(function(e){

    	if($('input[name="bug-url"]').val() == "") {

    		$('input[name="bug-url"]').css('border','1px solid red');

			$("#err-message").html('Ki kell töltenie ezt a mezőt:&nbsp;<strong>Url</strong>&nbsp;!');

    		$("#addBugError").modal({
				backdrop: 'static',   // This disable for click outside event
    			keyboard: true        // This for keyboard event
			});
			
			return false;

    	} else {
    		if(/^(http|https|ftp):\/\//i.test($('input[name="bug-url').val()) == false) {

    			$('input[name="bug-url"]').css('border','1px solid red');

				$("#err-message").html('Nem url-t adott meg:&nbsp;<strong>Url</strong>&nbsp;!');

	    		$("#addBugError").modal({
					backdrop: 'static',   // This disable for click outside event
	    			keyboard: true        // This for keyboard event
				});
				
				return false;

    		}
    	}

    	if($('textarea[name="bug-description"]').val() == "") {

    		$('textarea[name="bug-description"]').css('border','1px solid red');
			
			$("#err-message").html('Ki kell töltenie ezt a mezőt:&nbsp;<strong>Hiba leírása</strong>&nbsp;!');

    		$("#addBugError").modal({
				backdrop: 'static',   // This disable for click outside event
    			keyboard: true        // This for keyboard event
			});
			
			return false;

    	}

    	e.preventDefault();

    	var formData = new FormData($(this)[0]);

    	console.log(formData);

    	$.ajax({
	       url: ajaxurl,
	       type: 'POST',
	       data: formData,
	       async: false,
	       cache: false,
	       contentType: false,
	       enctype: 'multipart/form-data',
	       processData: false,
		   dataType:"json",
	       success: function (response) {
	         //alert(response['message']);
			 if(response['status'] == 'ok') {
				$("#successOk").modal({
					backdrop: 'static',   // This disable for click outside event
					keyboard: true        // This for keyboard event
				});
			 }
	       }
	   });

    	return false;
    });

});
