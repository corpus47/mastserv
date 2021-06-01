<script>
// Js to listusers view

$(document).delegate('label.user-active','click',function(){

	var check = $(this).prev('input[type="checkbox"]').is(":checked");
	console.log(check);
	var user_id = $(this).prev('input[type="checkbox"]').data('id');
	console.log(user_id);

	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'user_set_active', id : user_id, value : check ? 0 : 1, s_id : session_id },
		success:function(data){
			console.log(data);
			if(data['status'] == 'ok'){
				if($('#user_delete_'+user_id).is(':visible')){
					$('#user_delete_'+user_id).hide();
				} else {
					$('#user_delete_'+user_id).show();
				}
			}
		}
	});
});

$(document).ready(function($){	
	$('.js-basic-example').DataTable({
        responsive: true
    });
	
	//Exportable table
    $('.user-table').DataTable({
        dom: 'Bfrtip',
		"language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
        },
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
	
	// User active checked
	
	$('.icheckbox_flat-green').click(function(){
		var check = $(this).find('input[type="checkbox"]').is(":checked");
		
		//alert(check);
		
		//var user_id = $(this).attr('id');
		
		var user_id = $(this).find('input[type="checkbox"]').attr('id');
		
		user_id = user_id.replace('user-active_','');
		
		//var checked = $(this).is(':checked') ? 1 : 0;
		
		//console.log(checked);
		
		$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'user_set_active', id : user_id, value : check ? 0 : 1, s_id : session_id },
				success:function(data){
					console.log(data);
				}
			});
	});
	
	$('.user-delete-link').click(function(){
		//alert($(this).data('id'));
		
		var id = $(this).data('id');
		
		if(confirm('Valóban törli?')){
		
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'user_delete', id : id, s_id : session_id },
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
</script>