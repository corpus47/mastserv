<script>
// Js to listusers view
var subclients_table;

$(document).delegate('label.subclient-active','click',function(){

	var check = $(this).prev('input[type="checkbox"]').is(":checked");
	console.log(check);
	var user_id = $(this).prev('input[type="checkbox"]').data('id');
	console.log(user_id);

	$.ajax({
		url: ajax_url,
		type : "POST",
		async : true,
		dataType:"json",
		data : { ajax : 1, m : 'masterservice', act : 'subclient_set_active', id : user_id, value : check ? 0 : 1, s_id : session_id },
		success:function(data){
			console.log(data);
			if(data['status'] == 'ok'){
				if($('#subclient_delete_'+user_id).is(':visible')){
					$('#subclient_delete_'+user_id).hide();
				} else {
					$('#subclient_delete_'+user_id).show();
				}
			}
		}
	});
});

$(document).ready(function($){
	
	subclients_table = $('.subclients-table').DataTable({
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
        "order": [[ 0, "desc" ]]
    });
	
	$('.subclient-delete-link').click(function(){
		
		var id = $(this).data('id');
		
		if(confirm('Valóban törli?')){
		
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'subclient_delete', id : id, s_id : session_id },
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
