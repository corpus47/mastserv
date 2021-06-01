<?php
$master = $this->master->load_master($_SESSION['HDT_master_user']);
//$filter = null;
$filter = array(" MasterID = " . $_SESSION['HDT_master_user']);

if(isset($_GET['id'])) {
	
	//$filter[] = " ID = " . $_GET['id'];
	$mandate = $this->mandates->load_mandate($_GET['id']);
	//var_dump($mandate);
}
//var_dump($_SESSION);
?>
	<div class="block-master">
		<div class="block-master-content">
			<div class="header-master-content">
				<div class="master-info-container">
					<h5>Belépve: <?php echo $master['Name'];?></h5>
				</div>
				<div class="master-menu-container">
					<a href="http://<?php echo ROOT_URL .'?logout';?>" class="btn btn-default">Kilépés</a>
				</div>
				<div class="navbar nav_title" style="border: 0;">
					<a href="http://<?php echo ROOT_URL?>" class="site_title master_site_title"><span></span></a>
				</div>
				<div class="nav toggle right">
                	<a id="menu_toggle_master"><i class="fa fa-bars"></i></a>
              	</div>
			</div>
			<!--<pre>
			<?php //var_dump($master);?>
			</pre>-->
			<div class="center_col" role="main">
				<div class="">
					<!--<div class="page-title">
					</div>-->
					<div class="clearfix"></div>
					<div id="demo"></div>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="x_panel">
								<!--Position-->
								<!--<div id="demo"></div>-->
								<div class="x_title">
									<?php if(!isset($_GET['id'])):?>
									<h2>Megbízások</h2>
									<?php else:?>
									<h2>Megbízás: <?php echo $mandate['Mandate_serial'];?></h2>
									<?php endif;?>
									<div class="clearfix"></div>
								</div><!-- #x_title -->
								<div class="x_content">
									<?php if(!isset($_GET['id'])):?>
									<?php echo $this->mandates->mandate_table_to_master($filter);?>
									<?php else:?>
									<?php echo $this->mandates->single_mandate_to_master($_GET['id']);?>
									<?php endif;?>
								</div><!-- #x_content -->
							</div><!-- #x_panel -->
						</div><!-- #col -->
					</div><!-- #row -->
				</div><!-- empty -->
			</div><!-- center_col -->
		</div><!-- block-master-content -->
	</div><!-- block-master -->
<div id="handwrite-upload-dialog" class="modal tables-dialog">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_content">
					<div class="board" id="simple-board"></div>
					<form id="handwrite-canvas-form" action="" class="edit-form form-horizontal form-label-left" method="post" >
						<div class="form-group form-float">
							<p style="width:100%;max-width:50%;display:inline-block;text-align:left;float:left;"><a class="btn btn-success drawing-board-reset" href="javascript:void(0);">Törlés</a></p>
							<p style="width:100%;max-width:50%;display:inline-block;text-align:right;float:left;"><a class="btn btn-primary drawing-board-send" href="javascript:void(0);" data-id="<?php echo $mandate['ID'];?>">Feltöltés</a></p>
						</div><!-- form-group -->
					</form>
				</div><!-- x_content -->
			</div><!-- x_panel -->
		</div><!-- col -->
	</div><!-- row -->
</div><!-- dialog -->
<div id="change-status-dialog" class="modal tables-dialog"></div>
