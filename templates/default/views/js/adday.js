<script>
$(document).ready(function(){
	
	$('#day-datum').datepicker({ dateFormat: 'yy.mm.dd' });
	
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
	
	$('#day-form-cancel').click(function(){
		var cancel_href = $(this).data('cancel-href');
		//console.log(cancel_href);
		window.location.href = cancel_href;
		return false;
	});
	
	$('#day-form-submit').click(function(){
		
		$('#day-form').submit();
		
	});
	
});
/* Hungarian initialisation for the jQuery UI date picker plugin. */
/* Written by Istvan Karaszi (jquery@spam.raszi.hu). */
jQuery(function($){
	        $.datepicker.regional['hu'] = {
	                closeText: 'bezárás',
	                prevText: '&laquo;&nbsp;vissza',
	                nextText: 'előre&nbsp;&raquo;',
	                currentText: 'ma',
	                monthNames: ['Január', 'Február', 'Március', 'Április', 'Május', 'Június',
	                'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'],
	                monthNamesShort: ['Jan', 'Feb', 'Már', 'Ápr', 'Máj', 'Jún',
	                'Júl', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
	                dayNames: ['Vasárnap', 'Hétfö', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'],
	                dayNamesShort: ['Vas', 'Hét', 'Ked', 'Sze', 'Csü', 'Pén', 'Szo'],
	                dayNamesMin: ['V', 'H', 'K', 'Sze', 'Cs', 'P', 'Szo'],
	                weekHeader: 'Hé',
	                dateFormat: 'yy-mm-dd',
	                firstDay: 1,
	                isRTL: false,
	                showMonthAfterYear: false,
	                yearSuffix: ''};
	        $.datepicker.setDefaults($.datepicker.regional['hu']);
});
</script>