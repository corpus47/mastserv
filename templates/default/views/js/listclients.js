<script>
// Js to listusers view
var clients_table;

$(document).delegate('label.client-active','click',function(){

	var check = $(this).prev('input[type="checkbox"]').is(":checked");
	console.log(check);
	var user_id = $(this).prev('input[type="checkbox"]').data('id');
	console.log(user_id);

	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'client_set_active', id : user_id, value : check ? 0 : 1, s_id : session_id },
		success:function(data){
			console.log(data);
			if(data['status'] == 'ok'){
				if($('#client_delete_'+user_id).is(':visible')){
					$('#client_delete_'+user_id).hide();
				} else {
					$('#client_delete_'+user_id).show();
				}
			}
		}
	});
});

/*$(document).delegate('click','.client-delete-link',function(){
	var id = $(this).data('id');
		
	if(confirm('Valóban törli?')){
		
		$.ajax({
			url: ajax_url,
			type : "POST",
			async : false,
			dataType:"json",
			data : { ajax : 1, m : 'masterservice', act : 'client_delete', id : id, s_id : session_id },
			success:function(data){
				console.log(data);
			}
		});
		
		//load_datatable_content(clients_table);
		
		return false;
		
	}
});*/

$(document).ready(function($){
//$(function () {	
	$('.js-basic-example').DataTable({
        responsive: true
    });
	
	//load_datatable_content(false);
	
	//Clients table
	 
	
    clients_table = $('.clients-table').DataTable({
        dom: 'Bfrtip',
		"language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
        },
        responsive: true,
        buttons: [
            /*'copy', 'csv', 'excel', 'pdf', 'print'*/
        ],
        "columnDefs": [ {
        	"targets": 0,
        	"orderable": false
        }],
        "order": [[ 1, "desc" ]]
    });
	
	/*$('.client-active').click(function(){
		
		var check = $(this).is(":checked");
		console.log(check);
		var user_id = $(this)..data('id');
		console.log(user_id);
		$.ajax({
			url: ajax_url,
			type : "POST",
			async : true,
			dataType:"json",
			data : { ajax : 1, m : 'masterservice', act : 'client_set_active', id : user_id, value : check ? 0 : 1, s_id : session_id },
			success:function(data){
				console.log(data);
				load_datatable_content(clients_table);
				
			}
		});

	});*/
	
	/*$('.icheckbox_flat-green').click(function(){
		
		var check = $(this).find('input[type="checkbox"]').is(":checked");
		
		var user_id = $(this).find('input[type="checkbox"]').data('id');

		$.ajax({
			url: ajax_url,
			type : "POST",
			async : true,
			dataType:"json",
			data : { ajax : 1, m : 'masterservice', act : 'client_set_active', id : user_id, value : check ? 0 : 1, s_id : session_id },
			success:function(data){
				console.log(data);
				load_datatable_content(clients_table);
			}
		});
		
		return false;
		
	});*/
	
	//$('.client-delete-link').click(function(){
	$('.client-delete-link').click(function(){
		//alert($(this).data('id'));
		
		var id = $(this).data('id');
		
		if(confirm('Valóban törli?')){
		
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'client_delete', id : id, s_id : session_id },
				success:function(data){
					console.log(data);
				}
			});
		
		location.reload();
		
		} else {
			return false;
		}
		
	});
	
});

function load_datatable_content(table) {
	
	var filter = $('.clients-table').data('filter');
	
	console.log(filter);
	$('.clients-table').find('tbody').html('');
	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'load_client_table_rows', filter : filter, s_id : session_id },
			success:function(data){
			console.log(data);
			$('.clients-table').find('tbody').html('');
			$('.clients-table').find('tbody').html(data['content']);
		}
	});
	
}

</script>
