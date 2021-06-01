<script>
// Js to listusers view
$(document).ready(function($){
//$(function () {	
	$('.js-basic-example').DataTable({
        responsive: true
    });
	
	//Exportable table
    $('.subcontactor-table').DataTable({
        dom: 'Bfrtip',
		"language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
        },
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
	
	// Subcontactor active checked
	
	$('.icheckbox_flat-green').click(function(){
		
		var check = $(this).find('input[type="checkbox"]').is(":checked");
		
		//var user_id = $(this).attr('id');
		
		var user_id = $(this).find('input[type="checkbox"]').attr('id');
		
		user_id = user_id.replace('subcontactor-active_','');
		
		//var checked = $(this).is(':checked') ? 1 : 0;
		
		//console.log(checked);
		
		$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'subcontactor_set_active', id : user_id, value : check ? 0 : 1, s_id : session_id },
				success:function(data){
					console.log(data);
				}
			});
	});
	
});
</script>