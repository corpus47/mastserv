<script>
$(document).ready(function(){
	$('.day-table').DataTable({
        dom: 'Bfrtip',
		"language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Hungarian.json"
        },
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>