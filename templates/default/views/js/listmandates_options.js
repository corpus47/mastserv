<script>
// Js to listusers view
$(document).ready(function($){	
	$('.js-basic-example').DataTable({
        responsive: true
    });
	
	//Exportable table
    $('.mandates-options-table').DataTable({
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
	
});
</script>
