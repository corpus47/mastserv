<script>

$(document).ready(function($){

	 $('.email_contents-table').DataTable({
        dom: 'Bfrtip',
		"language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
        },
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
    
    $('.email_contents-delete-link').click(function(){
		
		//alert($(this).data('id'));
		
		var id = $(this).data('id');
		
		if(confirm('Valóban törli?')){
		
			$.ajax({
				url: ajax_url,
				type : "POST",
				async : false,
				dataType:"json",
				data : { ajax : 1, m : 'masterservice', act : 'email_contents_delete', id : id, s_id : session_id },
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
