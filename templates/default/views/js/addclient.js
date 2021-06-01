<script>
//js for addclient.view.php
$(document).ready(function($){
	
	$('#client-form-cancel').click(function(){
		var cancel_href = $(this).data('cancel-href');
		//console.log(cancel_href);
		window.location.href = cancel_href;
		return false;
	});
	
	$('#client-form-submit').click(function(){
		
		$('.error').remove();
		
		var ret = true;
		
		// Check Name field
		
		if($('input[name="client-name"]').val() == "") {
			$('input[name="client-name"]').after('<div class="error">A név nem lehet üres!</div>');
			ret = false
		} else if($('input[name="client-name"]').val() !== "") {
			// Egyediség vizsgálata ajax-szal
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'checkunique_client', fieldname : 'Name', value : $('input[name="client-name"]').val(), s_id : session_id },
				success:function(data){
					if(data['status'] == 'true') {
						$('input[name="client-name"]').after('<div class="error">Ezen a néven megbízó már létezik a rendszerben!</div>');
						ret = false;
					} else {
						ret = true;
					}
				}
			});
			
		}
		
		// Check Prefix field
		
		if($('input[name="client-prefix"]').val() == "") {
			$('input[name="client-prefix"]').after('<div class="error">Az előtag nem lehet üres!</div>');
			ret = false
		} else if($('input[name="client-prefix"]').val() !== "") {
			
			// Egyediség vizsgálata ajax-szal
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'checkunique_client', fieldname : 'Prefix', value : $('input[name="client-prefix"]').val(), s_id : session_id },
				success:function(data){
					if(data['status'] == 'true') {
						$('input[name="client-prefix"]').after('<div class="error">Ez a prefix már létezik a rendszerben!</div>');
						ret = false;
					} else {
						ret = true;
					}
				}
			});
			
		}
		
		if(ret == true) {
			$('#client-form').submit();
		}
		
		
		
	});
	
	$('#import-clientid').change(function(){
		var id = $(this).val();
		//alert(id);
		/*$.ajax({
			url: ajax_url,
			type : "POST",
			async : false,
			dataType:"json",
			data : { ajax : 1, m : 'masterservice', act : 'load_parcel_client', id : id, s_id : session_id },
			success:function(data){
				console.log(data);
				$('input[name="client-name"]').val(data['content']['partner_group_name']);
				$('input[name="client-prefix"]').val(data['content']['partner_group_prefix']);
				$('input[name="partner_group_id"]').val(data['content']['partner_group_id']);
			}
		});*/
		if($(this).val() != "") {
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'load_parcel_client', id : id, subclients : true, s_id : session_id },
				success:function(data){
					console.log(data);
					$('input[name="client-name"]').val(data['client_content']['partner_group_name']);
					$('input[name="client-prefix"]').val(data['client_content']['partner_group_prefix']);
					$('input[name="partner_group_id"]').val(data['client_content']['partner_group_id']);
					$("#import-subclients-container").find('.clientslist-container').html(data['subclients_content']);
				}
			});
			$("#import-subclients-container").show();
			//reloadStylesheets();
		} else {
			$("#import-subclients-container").hide();
			$('input[name="client-name"]').val("");
			$('input[name="client-prefix"]').val("");
			$('input[name="partner_group_id"]').val("");
		}
	});
	
	$('input[name="client-prefix"]').focusin(function(){
		
		var client_name = $('input[name="client-name"]').val();
		
		client_name = client_name.noaccent();
		
		console.log(client_name);
		
		$(this).val(client_name.substring(0,3).toUpperCase());
		
	});
});

function reloadStylesheets() {
    var queryString = '?reload=' + new Date().getTime();
    $('link[rel="stylesheet"]').each(function () {
        this.href = this.href.replace(/\?.*|$/, queryString);
    });
}

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
};

String.prototype.noaccent = function() {
    return this.replace(/[^A-Za-z0-9]/g, function(x) { return hun_map[x] || x; })
};

</script>