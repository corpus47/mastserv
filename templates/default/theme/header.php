<?php
/*
*
* @ theme_uri
*
*/
?>
<!DOCTYPE html>
<html lang="en">
    <head>        
        <title>Home Delivery Team</title>      
        <meta charset="UTF-8" />   
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		
		<!--  favicon -->
		
		<link rel="apple-touch-icon" sizes="57x57" href="http://<?php echo $this->params['theme_uri'];?>img/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="http://<?php echo $this->params['theme_uri'];?>img/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="http://<?php echo $this->params['theme_uri'];?>img/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="http://<?php echo $this->params['theme_uri'];?>img/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="http://<?php echo $this->params['theme_uri'];?>img/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="http://<?php echo $this->params['theme_uri'];?>img/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="http://<?php echo $this->params['theme_uri'];?>img/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="http://<?php echo $this->params['theme_uri'];?>img/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="http://<?php echo $this->params['theme_uri'];?>img/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="http://<?php echo $this->params['theme_uri'];?>img/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="http://<?php echo $this->params['theme_uri'];?>img/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="http://<?php echo $this->params['theme_uri'];?>img/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="http://<?php echo $this->params['theme_uri'];?>img/favicon-16x16.png">
		<link rel="manifest" href="http://<?php echo $this->params['theme_uri'];?>img/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="http://<?php echo $this->params['theme_uri'];?>img/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
		
		<!-- Bootstrap -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<!-- NProgress -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/nprogress/nprogress.css" rel="stylesheet">
		<!-- iCheck -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/iCheck/skins/flat/green.css" rel="stylesheet">
		<!-- bootstrap-wysiwyg -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
		<!-- Select2 -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/select2/dist/css/select2.min.css" rel="stylesheet">
		<!-- Switchery -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/switchery/dist/switchery.min.css" rel="stylesheet">
		<!-- starrr -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/starrr/dist/starrr.css" rel="stylesheet">
		<!-- bootstrap-daterangepicker -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
		<!-- jQuery-UI -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/jquery-ui/jquery-ui.css" rel="stylesheet">
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/jquery-ui/jquery-ui.structure.css" rel="stylesheet">
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/jquery-ui/jquery-ui.theme.css" rel="stylesheet">
		<!-- Datatables -->
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
		
		<!-- DropZone -->
		
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/dropzone/dist/min/dropzone.min.css" rel="stylesheet">
		
		<!-- Magnific popup -->
		
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/magnific-popup/dist/magnific-popup.css" rel="stylesheet">
		
		<!-- FancyBox -->
		
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/fancybox/dist/jquery.fancybox.min.css" rel="stylesheet">
		
		<!-- drawingboard -->
		
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/drawingboard/dist/drawingboard.min.css" rel="stylesh<eet">
		
		<!-- fs.stepper for number input -->
		
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/fs_stepper/src/jquery.fs.stepper.css" rel="stylesh<eet">
		
		<!-- Custom Theme Style -->
		
		<link href="http://<?php echo $this->params['theme_uri'];?>css/custom.min.css" rel="stylesheet">
		
		<!-- Animated Circle Loader -->
		
		<link href="http://<?php echo $this->params['theme_uri'];?>vendors/anicircles/dist/css/gspinner.css">
		
		<link rel="stylesheet" type="text/css" href="http://<?php echo $this->params['theme_uri'];?>css/masterservice.css" />
		
	</head>
	<?php
	if($this->action == 'login') {
		$body_class = 'login';
	} else {
		$body_class = 'nav-md';
	}
	?>
	<body class="<?php echo $body_class?>">
		<!-- fileupload loader -->
		<div id="fileupload-loader"></div>
		<div id="anicircle-loader"></div>
		<!-- Calendar ajax loader -->
		<div id="calendar-ajax-loader"></div>
		<div id="calendar-ajax-loader-bg"></div>
		<!-- message modal -->
		<?php if(isset($_SESSION["HDT_ok_message"])):?>
		<div class="modal fade" id="ok_message_modal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content modal-col-green">
					<!--<div class="modal-header">
						<h4 class="modal-title" id="defaultModalLabel">Rendszerüzenet</h4>
					</div> modal-header -->
					<div class="modal-body">
						<?php echo $_SESSION["HDT_ok_message"];?>
					</div><!-- modal body -->
					<!--<div class="modal-footer">
						<button type="button" class="btn btn-link waves-effect idle-button" data-dismiss="modal">RENDBEN</button>
					</div> modal footer -->
				</div><!-- modal content -->
			</div><!-- modal-dialog -->
		</div><!-- modal -->
		<?php unset($_SESSION["HDT_ok_message"]); endif;?>
		
		<!-- idle modal dialog -->
		<div class="modal fade" id="idleModal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content modal-col-red">
					<div class="modal-header">
						<h4 class="modal-title" id="defaultModalLabel">Időtúllépés</h4>
					</div><!-- modal-header -->
					<div class="modal-body">
						Az inaktivitás meghaladta a beállított értéket. A rendszer kiléptette. A további munkához újra be kell jelentkeznie.
					</div><!-- modal body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-link waves-effect idle-button" data-dismiss="modal">RENDBEN</button>
					</div><!-- modal footer -->
				</div><!-- modal content -->
			</div><!-- modal-dialog -->
		</div><!-- modal -->
		<!-- #idle modal dialog -->
		
        <div class="container body">
			<div class="main_container">
