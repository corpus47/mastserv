<?php
require_once('modals.php');
?>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/chart.min.js"></script>
	<script src="js/chart-data.js"></script>
	<script src="js/easypiechart.js"></script>
	<script src="js/easypiechart-data.js"></script>
	<script src="js/bootstrap-datepicker.js"></script>
	<script src="js/custom.js"></script>
<?php
if($support->check_logged() == false){
	?>
	<script>
		$(document).ready(function(){
			$("#SupportInitError").modal({
				backdrop: 'static',   // This disable for click outside event
    			keyboard: true        // This for keyboard event
			});
		});
	</script>
	<?php
}
?>
	<script src="js/msv-support.js"></script>
</body>
</html>
