<script>
// Js to listusers view

var confirm_master_dialog;

var change_status_dialog;

var history_dialog;

var alert_dialog;

var files_upload_dialog;

/*$(".status-button").mouseover(function(){
	$(this).find('.change-message').show();
});

$(".status-button").mouseout(function(){
	$(this).find('.change-message').hide();
});*/

/*$(document).delegate('input[name="file-upload-form-submit"]','click',function(){
	//alert($('input[name="fileToUpload"]').val());
	
	var file_name = $('input[name="fileToUpload"]').val();
	
	if(file_name == "") {
		return false;
	}
	
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'mandate_file_upload', file : file_name, s_id : session_id },
		success:function(data){
			console.log(data);
			$('#confirm-calendar-container').html('');
			$('#confirm-calendar-container').html(data['content']);
			//alert(data['content']);
			$('#calendar-ajax-loader').hide();
			$('#calendar-ajax-loader-bg').hide();
		}
	});
	
	return false;
});*/

$(document).delegate('.compact-mandate-view-link','click',function(){
	//alert($(this).data('id'));
	
	var id = $(this).data('id');
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'compact_mandate_view', id : id, s_id : session_id },
		success:function(data){
			console.log(data);
			$('#compact-mandate-view').html('');
			$('#compact-mandate-view').html(data['content']);
		}
	});
	
	compact_mandate_view_dialog = $("#compact-mandate-view").dialog({
		autoOpen: false,
		modal:true,
		title: 'A megbízás további adatai',
    	width: 800,
    	buttons: {
    		"Mégsem": function () {
        		$(this).dialog("destroy")
    		}
		}
	});
	compact_mandate_view_dialog.dialog('open');
	return false;
});

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

$(document).delegate('.file-upload-button','click',function(){
	
	load_filelist($(this).data('id'));
	
	$('input[name="uploadform-mandate-id"]').val($(this).data('id'));
	
	
	//files_upload_dialog = $('#file-upload-dialog').dialog();
	files_upload_dialog = $('#file-upload-dialog').dialog({
		autoOpen: false,
		modal:true,
		title: 'Fájl feltöltés',
    	width: 500,
    	buttons: {
    		"Bezárás": function () {
        		$(this).dialog("destroy");
    		}
		}
	});
	
	/*$('.image-popup-fit-width').magnificPopup({
          type: 'image',
          closeOnContentClick: true,
          image: {
            verticalFit: false
          }
    });*/
	
	files_upload_dialog.dialog('open');

	return false;
	
});

$(document).delegate('.calendar-step','click',function(){
	var month = $(this).data('month');
	var year = $(this).data('year');
	var master_id = $(this).data('master-id');
	var mandate_id = $(this).data('mandate-id');
	//alert(year+" "+month);
	show_calendar_loader();
	$('#calendar-ajax-loader-bg').show();
	$('#calendar-ajax-loader').show();
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'refresh_confirm_calendar', month : month, year : year, master_id : master_id, mandate_id : mandate_id, s_id : session_id },
		success:function(data){
			console.log(data);
			$('#confirm-calendar-container').html('');
			$('#confirm-calendar-container').html(data['content']);
			//alert(data['content']);
			$('#calendar-ajax-loader').hide();
			$('#calendar-ajax-loader-bg').hide();
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

$(document).delegate('.worksheet-link','click',function(){
	var mandate_id = $(this).data('mandate-id');
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'generate_worksheet', id : mandate_id, s_id : session_id },
		success:function(data){
			//console.log(data);
			//$('#confirm-master-dialog').html(data['content']);
			//if(data['status'] == 'ok') {
			//	window.open('http://'+data['content']);
			//}
		}
	}).done(function(data){
		if(data['status'] == 'ok') {
			window.open('http://'+data['content']);
		}
	});
})

$(document).delegate(".confirmed-span","click",function(){
	//alert($(this).data('master-id')+" "+$(this).data('mandate-id'));
	
	var master_id = $(this).data('master-id');
	
	var mandate_id = $(this).data('mandate-id');
	
	var datum = $(this).data('datum');
	
	//alert(datum);
	
	confirm_mandate($(this).data('mandate-id'),$(this).data('master-id'),datum);
	
	confirm_master_dialog.dialog('destroy');
	
	location.reload();
	
	return false;
});

$(document).delegate(".master-confirm-link","click",function(){
	//alert($(this).data('master-id')+" "+$(this).data('mandate-id'));
	
	var master_id = $(this).data('master-id');
	
	var mandate_id = $(this).data('mandate-id');
	
	$(".master-confirm-link").each(function(){
		$(this).removeClass('btn-success');
	});
	
	$(this).addClass('btn-success');
	
	//confirm_mandate($(this).data('mandate-id'),$(this).data('master-id'));
	
	//confirm_master_dialog.dialog('destroy');
	
	//location.reload();
	
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'refresh_confirm_calendar', master_id : master_id, mandate_id : mandate_id, s_id : session_id },
		success:function(data){
			console.log(data);
			$('#confirm-calendar-container').html('');
			$('#confirm-calendar-container').html(data['content']);
			//alert(data['content']);	
		}
	});
	
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

function confirm_mandate(mandate_id, master_id, datum) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'confirm_mandate', master_id : master_id, id : mandate_id, datum : datum, s_id : session_id },
		success:function(data){
			console.log(data);
			$('#confirm-master-dialog').html(data['content']);
		}
	});
}

$(document).delegate(".mandate-unconfirm-link","click",function(){
	//alert($(this).data('id'));
	unconfirm_mandate($(this).data('id'));
	
	//confirm_master_dialog.dialog('destroy');
	//alert("Itt");
	//window.location.reload();
	
	return false;
});

$(document).delegate(".cell-info-link","click",function(e){
	//alert($(this).data('id'));

	var id = $(this).data('id');
	
	$('.mobile-table-info-cell').hide();
	//alert($('#info-cell-'+id).is(':visible'));
	if($('#info-cell-'+id).is(':visible')){
		$('#info-cell-'+id).hide();
	} else {
		$('#info-cell-'+id).slideDown();
	}
	
	return false;
});

$(document).delegate(".info-cell-close","click",function(){
	var id = $(this).data('id');
	$('#info-cell-'+id).hide();
});

$(document).delegate(".mandate-confirm-link","click",function(){
	load_master_to_confirm($(this).data('id'),$(this).data('subcontactor'));
	
	confirm_master_dialog = $('#confirm-master-dialog').dialog();
	
	confirm_master_dialog.dialog({
		autoOpen: false,
		modal:true,
		title: 'Kiosztás mesterre',
    	width: 320,
    	buttons: {
    		"Bezárás": function () {
        		$(this).dialog("close")
    		}
		}
	});
	confirm_master_dialog.dialog('open');
	return false;
});

function unconfirm_mandate(mandate_id) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'unconfirm_mandate', id : mandate_id, s_id : session_id },
		success:function(data){
			console.log(data);
			//$('#confirm-master-dialog').html(data['content']);
			window.location.reload();
		}
	});
}

$(document).delegate(".mandate-history-link","click",function(){

		//$('#confirm-master-dialog').html($(this).data('id')+" "+$(this).data('subcontactor'));
		
		var id = $(this).data('id');
		
		var serial = $(this).data('serial');
		
		load_history($(this).data('id'));
		
		//var w = $(window).width();
		
		//load_master_to_confirm($(this).data('id'),$(this).data('subcontactor'));
		
		$('.history-table').DataTable({
			dom: 'Bfrtip',
			"language": {
				"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
			},
			responsive: true,
			"pageLength": 3,
			buttons: [
				/*'copy', 'csv', 'excel', 'pdf', 'print'*/
			]
		});
		
		history_dialog = $('#history-dialog').dialog();
		
		history_dialog.dialog({
			autoOpen: false,
			modal:true,
			title: 'Stásutsz history: '+serial,
        	width: $(window).width() > 600 ? 600 : 'auto', //sets the initial size of the dialog box 
			fluid: true,
			height:'auto',
			/*create: function( event, ui ) {
				// Set maxWidth
				$(this).css("maxWidth", "660px");
			},*/
        	buttons: {
        		"Bezárás": function () {
            		$(this).dialog("close")
        		}
    		}
		});
		history_dialog.dialog('open');
		return false;
});

$(document).ready(function($){

	
	$("#file-upload-form").on('submit',function(e) {
		
		if($('input[name="fileToUpload"]').val() == "") {
			$('#file-upload-form').find('.error-msg').html('Nem választott ki fájlt!');
			$('#file-upload-form').find('.error-msg').show('slow').delay(2000).hide('slow');
			
			return false;
			
		}
	
		var file_name = $('input[name="fileToUpload"]').val();
		
		var fileuploads_url = 'http://'+$('input[name="upload-file-url"]').val();
		
		var mandate_id = $('input[name="uploadform-mandate-id"]').val();
		
		var attachment_type = $('select[name="attachment-type"]').val();
		
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
				console.log(data);
				if(data['status'] !== 'ok') {
					$('#file-upload-form').find('.error-msg').html(data['content']);
					$('#file-upload-form').find('.error-msg').show('slow').delay(2000).hide('slow');
				}
				load_filelist(mandate_id);
				$('input[name="fileToUpload"]').val('');
				return false;
			}
		});
		
		return false;
	});
	

	$('.js-basic-example').DataTable({
        responsive: true
    });
	
	//Exportable table
    /*$('.mandate-table').DataTable({
        dom: 'Bfrtip',
		"language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
        },
        responsive: true,
        "order": [[ 1, "desc" ]],
		//"bSort" : false,
		//"ordering": false,
		//"aaSorting" : [[]],
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });*/
	//console.log(ajax_url);
	var screen_width = $(window).width();
	
	//console.log(screen_width);
	if(screen_width > 768){
		$('.mandate-table').DataTable({
			"processing": true,
	        "serverSide": true,
	        "ajax": {
	            "url": ajax_url,
	            "type": "POST",
	            "data": {
	            	"ajax":"1",
	            	"m":"masterservice",
	            	"act":"mandates_table_source",
	            	"screen_width":screen_width,
	            	"s_id":session_id,
	            },
	        },
	        dom: 'Bfrtip',
			"language": {
	            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
	        },
	        responsive: true,
	        "order": [[ 0, "desc" ]],
			//"bSort" : false,
			//"ordering": false,
			//"aaSorting" : [[]],
	        buttons: [
	            'copy', 'csv', 'excel', 'pdf', 'print'
	        ]
	    });
	} else {
		$('.mandate-table-mobile').DataTable({
			"processing": true,
	        "serverSide": true,
	        "ajax": {
	            "url": ajax_url,
	            "type": "POST",
	            "data": {
	            	"ajax":"1",
	            	"m":"masterservice",
	            	"act":"mandates_table_source",
	            	"screen_width":screen_width,
	            	"s_id":session_id,
	            },
	        },
	        "autoWidth": false,
	        "columnDefs": [
	                       { "width": "80%", "targets": 0 },
	                       { "width": "20%", "targets": 0 }
	                     ],
	        dom: 'Bfrtip',
			"language": {
	            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
	        },
	        responsive: false,
	        "order": [[ 0, "desc" ]],
			//"bSort" : false,
			//"ordering": false,
			//"aaSorting" : [[]],
	        buttons: [
	            'copy', 'csv', 'excel', 'pdf', 'print'
	        ]
	    });
	}
	
	// User active checked
	
	$('.icheckbox_flat-green').click(function(){
		
		//var user_id = $(this).attr('id');
		
		var check = $(this).find('input[type="checkbox"]').is(":checked");
		
		var user_id = $(this).find('input[type="checkbox"]').attr('id');
		
		user_id = user_id.replace('master-active_','');
		
		//var checked = $(this).is(':checked') ? 1 : 0;
		
		//console.log(checked);
		
		$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'master_set_active', id : user_id, value : check ? 0 : 1, s_id : session_id },
				success:function(data){
					console.log(data);
				}
			});
	});
	
	$(".user-edit-link").click(function(){
		//alert($(this).data('id'));
		//return false;
	});
	
	/*$(".master-confirm-link").click(function(){
		alert($(this).data('master-id'));
		return false;
	});*/
	
	/*$(".mandate-confirm-link").click(function(){

		//$('#confirm-master-dialog').html($(this).data('id')+" "+$(this).data('subcontactor'));
		
		load_master_to_confirm($(this).data('id'),$(this).data('subcontactor'));
		
		confirm_master_dialog = $('#confirm-master-dialog').dialog();
		
		confirm_master_dialog.dialog({
			autoOpen: false,
			modal:true,
			title: 'Kiosztás mesterre',
        	//width: 300,
        	buttons: {
        		"Bezárás": function () {
            		$(this).dialog("close")
        		}
    		}
		});
		confirm_master_dialog.dialog('open');
		return false;
	});*/
	
	/*$(".status-button").click(function(){
		
		load_statuses($(this).data('id'),$(this).data('status'));
		
		change_status_dialog = $("#change-status-dialog").dialog();
		
		change_status_dialog.dialog({
			autoOpen: false,
			modal:true,
			title: 'Státusz módosítás',
        	//width: 300,
        	buttons: {
        		"Mégsem": function () {
            		$(this).dialog("close")
        		}
    		}
		});
		change_status_dialog.dialog('open');
		return false;
	});*/
	
	/*$('.worksheet-link').click(function(){
		alert('Itt');
	});*/
	
    $( "#routing-sortable-2" ).sortable();
    $( "#routing-sortable-2" ).disableSelection();
	
	//var orig_onclick = $('.collapse-link').prop('onclick');
	//$('.collapse-link').removeProp('onclick');
	
	$('.collapse-link').click(function(e){

		var id = $(this).data('id');
		var str = $(this).find('i').attr('class');
		
		
		
		
		if(str.indexOf('up') > 0) {
			load_geocords(id);
			//load_routing_content(id);
			$( "#routing-sortable-"+id ).sortable();
			$( "#routing-sortable-"+id ).disableSelection();
		} else {
			$('#routing-content-'+id).html('');
		}
		
		//alert($('#routing-content-'+id).is(':visible'));
		
		//if($('#routing-content-'+id).is(':visible')) {
		//	$('#routing-content-'+id).html('');
		//} else {
			//load_routing_content(id);
			//$( "#routing-sortable-"+id ).sortable();
			//$( "#routing-sortable-"+id ).disableSelection();
		//}
		
		//return orig_onclick.call(this, e.originalEvent);
	});
});

function load_geocords(id) {
	var addresses = "";
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'load_geocords', id : id, s_id : session_id },
		success:function(data){
			//console.log(data);
			//$('#routing-content-'+id).html(data['content']);
			addresses = data['content'];
			load_routing_content(id,addresses);
		}
	});
}

function load_routing_content(id,addresses) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'load_routing', id : id, s_id : session_id },
		success:function(data){
			//console.log(data);
			$('#routing-content-'+id).html(data['content']);
			//initMap(id,addresses);
		}
	});
}

function load_history(mandate_id) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'load_history', id : mandate_id, s_id : session_id },
		success:function(data){
			//console.log(data);
			$('#history-dialog').html(data['content']);
		}
	});
}

function load_statuses(mandate_id,act_status) {
	
	if(act_status > 4) {
		$('#change-status-dialog').html('A megbízás státusza nem módosítható!');
		
		return;
	}
	
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'load_statuses', id : mandate_id, act_status : act_status, s_id : session_id },
		success:function(data){
			//console.log(data);
			$('#change-status-dialog').html(data['content']);
		}
	});
}

function load_master_to_confirm(mandate_id, mandate_subcontactor) {
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : false,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'master_to_mandate', subcontactor_id : mandate_subcontactor, id : mandate_id, s_id : session_id },
		success:function(data){
			//console.log(data);
			$('#confirm-master-dialog').html(data['content']);
		}
	});
}

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

function initMap(container_id, addresses) {
	var points = JSON.parse(addresses);
	console.log(points);
	// Create a map object and specify the DOM element for display.
	var map = new google.maps.Map(document.getElementById('routing-map-container-'+container_id), {
		center: {lat: points[0]['lat'], lng: points[0]['lng']},
		scrollwheel: false,
		zoom: 9
	});
	marker = new google.maps.Marker({
       map: map,
       position: new google.maps.LatLng(points[0]['lat'], points[0]['lng'])
    });
	for (i = 1; i <= points.length; i++) { 
		//text += cars[i] + "<br>";
		marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(points[i]['lat'], points[i]['lng'])
		});
	}
}

function show_calendar_loader() {
	var position = $('#confirm-calendar-container').offset();
	var width = $('#confirm-calendar-container').width();
	var height = $('#confirm-calendar-container').height();
	//console.log(width);
	
	var loader_left = (position.left-$(window).scrollLeft())+((width/2)-27);
	var loader_top = (position.top-$(window).scrollTop())+((height/2)-27);
	//console.log(loader_left);
	$('#calendar-ajax-loader-bg').css({'width':width});
	$('#calendar-ajax-loader-bg').css({'height':height});
	$('#calendar-ajax-loader-bg').css({'left':position.left-$(window).scrollLeft()});
	$('#calendar-ajax-loader-bg').css({'top':position.top-$(window).scrollTop()});
	
	$('#calendar-ajax-loader').css({'left':loader_left});
	$('#calendar-ajax-loader').css({'top':loader_top});
	
}

</script>
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjM_Ntoikutbnt9AdDrkyaHtpjUzmC7iw" defer></script>-->
<script src="https://maps.googleapis.com/maps/api/js" defer></script>
