<script>

var change_status_dialog;

var x = document.getElementById("demo");

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
    x.innerHTML = "Latitude: " + position.coords.latitude + 
    "<br>Longitude: " + position.coords.longitude; 
}

$(document).delegate('.attachment-delete','click',function(){
	//alert($(this).data('id'));
	if(!confirm('Valóban törli a csatolmányt?')) {
		return false;
	}
	var id = $(this).data('id');
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'delete_attachment_file', id : id, s_id : session_id },
		success:function(data){
			console.log(data);
			load_filelist(data['mandate']);
		}
	});
});

var simpleBoard; // Canvas

var handwrite_dialog;

$( window ).on( "orientationchange", function( event ) {
	//if($('.mandate-table-to-master').length != 0) {
	//	window.location.reload();
	//	return;
	//}
	if($('.drawing-board-canvas').length != 0) {
		var context = simpleBoard.canvas.getContext('2d');
		//console.log(context);
		context.clearRect(0,0,simpleBoard.canvas.width,simpleBoard.canvas.height);
		handwrite_dialog.dialog('destroy');
		//window.location.reload();
		/*handwrite_dialog = $('#handwrite-upload-dialog').dialog({
			autoOpen: false,
			modal:true,
			title: 'Aláírás',
			width: dialog_width,
			buttons: {
				"Bezárás": function () {
					$(this).dialog("destroy");
					
				}
			}
		});
		handwrite_dialog.dialog('open');*/
	}
	window.location.reload();
	//$('#DataTables_Table_0').css('width','100% !important');
});

$(document).delegate('.handwrite-dialog-open','click',function(){
	//alert($(this).data('id'));
	
	if($('.status-button').data('status') !== 5) {
		
		//alert($('.status-button').data('status'));
		alert('A művelethez Munka lezárva státuszba kell helyeznie a fuvart!');
		
		return false;
	}
	
	var dialog_width = $(window).width();
	
	//alert(500*0.75);
	
	if(dialog_width > 500)  {
		dialog_width = 500;
	} else {
		$('#simple-board').css('height',"150px");
	}
	
	handwrite_dialog = $('#handwrite-upload-dialog').dialog({
		autoOpen: false,
		modal:true,
		title: 'Aláírás',
    	width: dialog_width,
    	buttons: {
    		"Bezárás": function () {
        		$(this).dialog("destroy");
        		
    		}
		}
	});
	
	
	
	handwrite_dialog.dialog('open');
	
	/* Drawing canvas */
	
	if($('.drawing-board-canvas').length == 0) {
		simpleBoard = new DrawingBoard.Board('simple-board', {
			controls: true,
			webStorage: false
		});
	} else {
		var context = simpleBoard.canvas.getContext('2d');
		//console.log(context);
		context.clearRect(0,0,simpleBoard.canvas.width,simpleBoard.canvas.height);
	}
	return false;
});

$(document).delegate('.drawing-board-reset','click',function(){
	//console.log(simpleBoard.canvas);
	var context = simpleBoard.canvas.getContext('2d');
	console.log(context);
	context.clearRect(0,0,simpleBoard.canvas.width,simpleBoard.canvas.height);
});

$(document).delegate('.drawing-board-send','click',function(){
	//alert($(this).data('id'));
		
	var mandate_id = $(this).data('id');
		
	var context = simpleBoard.canvas.getContext('2d');
		
	//console.log(context.canvas.toDataURL());
		
	var dataURL = context.canvas.toDataURL();
	
	$('#fileupload-loader').show();
	
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'save_handwrite_image', id : mandate_id, DataUrl : dataURL, s_id : session_id },
		success:function(data){
			console.log(data);
			//$('#filelist_container').html('...Betöltés...');
			//$('#filelist_container').html(data['content']);
			handwrite_dialog.dialog('destroy');
			$('#fileupload-loader').hide();
			if(data['status'] !== 'ok') {
				alert('Hiba! '+data['content']);
			} else {
				alert(data['content']);
			}
			
		}
	});
		
	return false;
});

$(document).delegate('.change-status-button','click',function(){
	//alert($(this).data('status'));
	change_status($(this).data('id'),$(this).data('status'));
	
	location.reload();
	
	return false;
});

$(document).delegate(".status-button","click",function(){
		
		load_statuses($(this).data('id'),$(this).data('status'));
		
		//change_status_dialog = $("#change-status-dialog").dialog();
		
		change_status_dialog = $("#change-status-dialog").dialog({
			autoOpen: false,
			modal:true,
			title: 'Státusz módosítás',
        	//width: 300,
        	buttons: {
        		"Mégsem": function () {
            		$(this).dialog("destroy")
        		}
    		}
		});
		change_status_dialog.dialog('open');
		return false;
});

$(document).ready(function(){
	
	$('input[name="mandate-comments-form-submit"]').click(function(){
		//alert($(this).data('id'));
		
		var mandate_id = $(this).data('id');
		
		var master_comment = $('textarea[name="master-comment"]').val();
		
		var customer_comment = $('textarea[name="customer-comment"]').val();
		
		$.ajax({
			url: ajax_url,
			type : "POST",
			async : false,
			dataType:"json",
			data : { ajax : 1, m : 'masterservice', act : 'save_mandate_comments', id : mandate_id, mast_comment: master_comment, cust_comment: customer_comment, s_id : session_id },
			success:function(data){
				console.log(data);
				if(data['status'] == 'ok') {
					$('textarea[name="customer-comment"]').after('<div id="comment-success" style="margin-top:10px;" class="alert alert-success">A megjegyzések mentve</div>');
					$('#comment-success').show().delay(2000).fadeOut('slow');
				} else {
					$('textarea[name="customer-comment"]').after('<div id="comment-danger" style="margin-top:10px;" class="alert alert-danger">Hiba a mentés során!</div>');
					$('#comment-danger').show().delay(2000).fadeOut();
				}
			}
		});
		
	});
	
	getLocation();
	
	$('.nav-md').css('background-color','#999999 !important');
	
	//initMap();
	
	check_browser();
	
	
	$('#menu_toggle_master').click(function(){
		//alert($('.master-menu-container').is(':visible'));
		if($('.master-menu-container').is(':visible') == false) {
			$('.master-menu-container').slideDown();
		} else {
			$('.master-menu-container').slideUp();
		}
	});
	
	//Exportable table
    $('.mandate-table-to-master').DataTable({
        dom: 'Bfrtip',
		"language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
        },
        responsive: true,
        "autoWidth": true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
	
	$('.address-map-link').click(function(){
		//alert($(this).data('lat'));
		if($('#google-maps-container-'+$(this).data('container-id')).is(':visible')) {
			$('#google-maps-container-'+$(this).data('container-id')).slideUp();
			$('#google-maps-container-'+$(this).data('container-id')).html('');
		} else {
			$('#google-maps-container-'+$(this).data('container-id')).show();
			initMap($(this).data('container-id'),$(this).data('lat'),$(this).data('lng'));
			
			
		}
	});
	
	//$('.proba-gomb').click(function(){
	
	var mandate_id = $('input[name="uploadform-mandate-id"]').val();
		
	//alert(mandate_id);
		
	if (typeof mandate_id !== "undefined") {
					
		load_filelist(mandate_id);
		
	}
		
	//});
	
	$("#file-upload-form").on('submit',function(e) {
	
		var file_name = $('input[name="fileToUpload"]').val();
		
		//var fileuploads_url = 'http://'+$('input[name="upload-file-url"]').val();
		
		var mandate_id = $('input[name="uploadform-mandate-id"]').val();
		
		var attachment_type = $('input[name="attachment-type"]').val();
		
		if(file_name == "") {
			return false;
		}
		
		var file_data = $('#fileToUpload').prop('files')[0];   
		var form_data = new FormData();
		//var form_data = $(this).serialize();                 
		form_data.append('file', file_data);
		form_data.append('ajax',1);
		form_data.append('m','masterservice');
		form_data.append('s_id',session_id);
		form_data.append('act','mandate_file_upload');
		form_data.append('mandate_id',mandate_id);
		form_data.append('attachment_type',attachment_type);
		
		//alert(form_data);
		//alert(fileuploads_url);
		
		$('#fileupload-loader').show();
		
		$.ajax({
			//url: fileuploads_url,
			url: ajax_url,
			type : "POST",
			async : false,
			dataType:"json",
			cache: false,
            contentType: false,
            processData: false,
			//data : { ajax : 1, m : 'masterservice', act : 'mandate_file_upload', f_data : form_data, file : file_name, s_id : session_id },
			data : form_data,
			success:function(data){
				$('#fileupload-loader').hide();
				console.log(data['status']);
				if(data['status'] !== 'ok') {
					$('#file-upload-form').find('.error-msg').html(data['content']);
					$('#file-upload-form').find('.error-msg').show('slow').delay(2000).hide('slow');
					//$('#file-upload-form').find('.error-msg').show('slow');
				}
				load_filelist(mandate_id);
				$('input[name="fileToUpload"]').val('');
				return false;
			}
		});
		
		return false;
	});
	
});

function load_filelist(mandate_id) {

	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'load_mandate_filelist', id : mandate_id, s_id : session_id },
		success:function(data){
			console.log(data);
			$('#filelist_container').html('...Betöltés...');
			$('#filelist_container').html(data['content']);
		}
	});
}

function change_status(mandate_id, new_status) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'change_status', new_status : new_status, id : mandate_id, s_id : session_id },
		success:function(data){
			console.log(data);
			//$('#confirm-master-dialog').html(data['content']);
			//alert(data['content']);
			
		}
	});
}

function load_statuses(mandate_id,act_status) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'load_statuses', id : mandate_id, act_status : act_status, master : 1, s_id : session_id },
		success:function(data){
			//console.log(data);
			$('#change-status-dialog').html(data['content']);
		}
	});
}

function initMap(container_id, address_lat, address_lng) {
	// Create a map object and specify the DOM element for display.
	var map = new google.maps.Map(document.getElementById('google-maps-container-'+container_id), {
		center: {lat: address_lat, lng: address_lng},
		scrollwheel: false,
		zoom: 15
	});
	marker = new google.maps.Marker({
       map: map,
       position: new google.maps.LatLng(address_lat, address_lng)
    });
}

function check_browser() {

	var ua = navigator.userAgent.toLowerCase(),
    plat = navigator.platform,
    protocol = '',
    a,
    href;

	var browser_device = ua.match(/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera/i) ? ua.match(/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera/i)[0] : false;

	if (browser_device) {
    switch(browser_device) {
        case 'iphone':
        case 'ipad':
        case 'ipod':
            function iOSversion() {
              if (/iP(hone|od|ad)/.test(navigator.platform)) {
                // supports iOS 2.0 and later: <http://bit. ly/TJjs1V>
                var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
                return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
              }
            }

            var ver = iOSversion() || [0];

            if (ver[0] >= 6) {
              //protocol = 'maps://';
              $('.web-link').hide();
              $('.ios-mobile-link').show();
            } else {
              //protocol = 'http://maps.google.com/maps';
            }
			break;

        case 'android':
			$('.web-link').hide();
            $('.android-mobile-link').show();
        default:
            //protocol = 'http://maps.google.com/maps';
			break;
		}
	}

}


</script>
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjM_Ntoikutbnt9AdDrkyaHtpjUzmC7iw" defer></script>-->
<script src="https://maps.googleapis.com/maps/api/js" defer></script>
