<?php

$user = $this->get_component('user')->load_user($_SESSION['HDT_uid']);

//var_dump($user);

$owner = $this->get_component('user')->load_user($user['Partner_ID']);

//var_dump($subclient);

if($this->get_component('user')->is_super($_SESSION['HDT_uid']) || $this->get_component('user')->is_admin($_SESSION['HDT_uid'])) {

	if(isset($_GET['mode']) && $_GET['mode'] == 'unconfirmed'){
		$filter = array(" MasterID IS NULL OR MasterID = 0 ");
	} elseif(isset($_GET['mode']) && $_GET['mode'] == 'confirmed') {
		$filter = array(" MasterID IS NOT NULL AND MasterID != 0 AND Master_status < " . INSTALLATION_SUCCESS);
	} elseif(isset($_GET['mode']) && $_GET['mode'] == 'billable') {
		$filter = array(" MasterID IS NOT NULL AND MasterID != 0 AND Master_status >= " . INSTALLATION_SUCCESS);
	} else {
		$filter = null;
	}
} elseif($this->get_component('user')->is_subcon_admin($_SESSION['HDT_uid'])) {
	
	if(isset($_GET['mode']) && $_GET['mode'] == 'unconfirmed'){
		$filter = array(" MasterID IS NULL OR MasterID = 0 ");
	} elseif(isset($_GET['mode']) && $_GET['mode'] == 'confirmed') {
		$filter = array(" MasterID IS NOT NULL AND MasterID != 0 AND Master_status < " . INSTALLATION_SUCCESS);
	} elseif(isset($_GET['mode']) && $_GET['mode'] == 'billable') {
		$filter = array(" MasterID IS NOT NULL AND MasterID != 0 AND Master_status >= " . INSTALLATION_SUCCESS);
	} else {
		$filter = null;
	}
	
	$filt = array("AdminID = " . $_SESSION['HDT_uid']);
	
	$subcontactors = $this->get_component('subcontactor')->list_subcontactors($filt);
	
	if(isset($subcontactors[0])){
	
		/*$filt = array("Subconid = " .$subcontactors[0]['ID']);
		
		$masters = $this->get_component('master')->list_masters($filt);
		
		$filter_str = "(";
		
		foreach($masters as $row) {
			$filter_str .= " MasterID = " . $row['ID'] . " OR ";
		}
		
		$filter_str = preg_replace('/( OR) $/','',$filter_str);
	
		$filter_str .= ")";*/
		
		$filter[] = "SubcontactorID = " . $subcontactors[0]['ID'];
		
	}
	
	//$filter[] = $filter_str;
	
} else {
	
	$subclients_for_user = $this->get_component('subclients')->get_subclients_for_user($_SESSION['HDT_uid']);
	
	if(isset($_GET['mode']) && $_GET['mode'] == 'unconfirmed'){
		$filter = array(" MasterID IS NULL OR MasterID = 0 ");
	} elseif(isset($_GET['mode']) && $_GET['mode'] == 'confirmed') {
		$filter = array(" MasterID IS NOT NULL AND MasterID != 0 AND Master_status < " . INSTALLATION_SUCCESS);
	} elseif(isset($_GET['mode']) && $_GET['mode'] == 'billable') {
		$filter = array(" MasterID IS NOT NULL AND MasterID != 0 AND Master_status >= " . INSTALLATION_SUCCESS);
	} else {
		$filter = null;
	}
	
	$filter_str = "(";
	
	foreach($subclients_for_user as $row) {
		
		$filter_str .= " PartnerID = " . $row . " OR ";
		
	}
	
	$filter_str = preg_replace('/( OR) $/','',$filter_str);
	
	$filter_str .= ")";
	
	$filter[] = $filter_str;
	
}
$_SESSION['mode'] = $_GET['mode'];
// Rendezés

if(!isset($_GET['order'])) {	
	$_GET['order'] = 'ID';
}
if(!isset($_GET['asc'])) {
	$_GET['direction'] = 'DESC'; 
} else {
	$_GET['direction'] = 'ASC';
}

$filter[] = "ORDER BY `".$_GET['order']."` ".$_GET['direction'];

// Szűrés


if(isset($_POST['mandate-filter'])) {
	foreach($_POST['mandate-filter'] as $key=>$row) {
		if($row != '') {
			$filter[] = $key . " = " . $row;
		}
	}
}
$_SESSION['HDT_form_filter'] = $filter;
?>
<div class="right_col" role="main">
	<div class="">
		<div class="page-title">
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
			<?php //echo $this->mandates->ordering_buttons();?>
			<?php //var_export($filter);?>
			<?php ?>
			<?php echo $this->mandates->filter_form();?>
			</div>
		</div>
		<?php if(isset($_POST['mandate-filter'])):?>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_content">
					<form id="mandate-erase-filter-form" class="edit-form form-horizontal form-label-left" method="post" novalidate="novalidate" action="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>">
						<button id="mandate-erase-filter-form-submit" class="btn btn-success" name="erase-filter" type="submit">Szűrés törlése</button>
					</form>
				</div>
			</div>
			</div>
		</div>
		<?php endif;?>
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Megbízások</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<?php //echo $this->mandates->mandate_table($filter);?>
						<?php //var_export($user);?>
						<table class="table table-bordered table-striped table-hover dataTable mandate-table">
							<thead>
								<tr>
									<?php if($user['List_style'] == 1):?>
										<td style="max-width:50px;">ID</td>
										<td style="max-width:1250px !important;">Adatok</td>
										<td style="width:auto;min-width:200px;text-align:right;">Műveletek</td>
									<?php else: ?>
										<td>Id</td>
										<td>Státusz</td>
										<td>Azonosító</td>
										<td>HDT adatok</td>
										<td>Installáció helyének adatai</td>
										<td>Termékek/Installációk</td>
										<td>Megbízás adatai</td>
										<td>&nbsp;</td>
									<?php endif;?>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<?php if($user['List_style'] == 1):?>
										<td style="max-width:50px;">ID</td>
										<td style="max-width:1250px !important;">Adatok</td>
										<td style="width:auto;min-width:200px;text-align:right;">Műveletek</td>
									<?php else: ?>
										<td>Id</td>
										<td>Státusz</td>
										<td>Azonosító</td>
										<td>HDT adatok</td>
										<td>Installáció helyének adatai</td>
										<td>Termékek/Installációk</td>
										<td>Megbízás adatai</td>
										<td>&nbsp;</td>
									<?php endif;?>
								</tr>
							</tfoot>
							<tbody>
							</tbody>
						</table>
						<table class="table table-bordered table-striped table-hover dataTable mandate-table-mobile">
							<thead>
								<tr>
									<td>Megbízások</td>
									<td></td>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td>Megbízások</td>
									<td></td>
								</tr>
							</tfoot>
							<tbody>
							</tbody>
						</table>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- #right_col -->
<div id="compact-mandate-view" class="modal tables-dialog"></div>
<div id="confirm-master-dialog" class="modal tables-dialog"></div>
<div id="change-status-dialog" class="modal tables-dialog"></div>
<div id="history-dialog" class="modal tables-dialog"></div>
<div id="file-upload-dialog" class="modal tables-dialog">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_content">
					<div id="filelist_container"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_content">
					<form id="file-upload-form" action="" class="edit-form form-horizontal form-label-left" method="post" enctype="multipart/form-data">
						<div class="form-group form-float">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="hdt-order-id">Csatolás típusa: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
								<select name="attachment-type" class="form-control">
									<option value="0">Vásárlási számla</option>
									<option value="1">Lezáró számla</option>
									<option value="2">Helyszíni fotó</option>
								</select>
							</div>
						</div>
						<div class="form-group form-float">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="hdt-order-id">Fájl feltöltése:</label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="file" class="btn btn-default" name="fileToUpload" id="fileToUpload">
								<input type="hidden" name="uploadform-mandate-id" value="" />
							</div>
						</div>
						<div class="form-group form-float">
							<div class="error-msg"></div>
						</div>
						<div class="form-group form-float">
							<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
								<input type="submit" class="btn btn-primary" value="Filefeltöltés" name="file-upload-form-submit">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
