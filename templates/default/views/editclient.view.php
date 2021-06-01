<?php
//$partner_group = $this->parcel->load_client($_GET['id']);
$client = $this->clients->load_client($_GET['id']);

$lock = $this->clients->locked_client($_GET['id']);

if(!$lock && $client['Locked'] != $_SESSION["HDT_uid"]) {
	$this->clients->client_lock($_GET['id']);
}
?>
<div class="right_col" role="main">
	<div class="">
		<div class="page-title">
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<!-- alert -->
					<div class="alert alert-danger alert-dismissible fade in" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Bezárás"><span aria-hidden="true">×</span>
						</button>
						<strong>Szerkesztésre nyitott tétel.</strong> Mentse a változásokat, mielőtt tovább lépne!
					</div>
					<div class="x_title">
						<h2>Megbízó szerkesztése: <?php echo $client['Name'];?> ( <?php echo $client['Prefix'];?> )</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="client-edit-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="client_edit" />
							<input name="id" type="hidden" value="<?php echo $client['ID'];?>" />
							<?php if($lock !== true):?>
							<input type="hidden" name="lock" value="<?php echo $_SESSION["HDT_uid"]?>" />
							<?php endif;?>
							
							<?php if($client['Parcel_user'] != 0):?>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-parcel-user">Parcel ügyfél</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h4><?php echo $this->parcel->load_client($client['Parcel_user'])['partner_group_name'];?></h4>
								</div>
							</div>
							<?php endif;?>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-name">Név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="client-name" name="client-name" value="<?php echo $client['Name'];?>" required>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-prefix">Előtag</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="client-prefix" name="client-prefix" value="<?php echo $client['Prefix'];?>" required>
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-comment">Megjegyzés</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="6" class="form-control no-resize" id="client-comment" name="client-comment" required placeholder=""><?php echo $client['Comment'];?></textarea> 
                                </div>
                            </div>
							<?php if($this->user->is_super($_SESSION['HDT_uid']) || $this->user->is_admin($_SESSION['HDT_uid'])):?>
							<?php
							$default = $this->clients->load_default_client();

							if(count($default) == 0) {
								$default_client = 'Nincs még beállítva alap megbízó!';
							} else {
								$default_client = 'Jelenleg beállítva: ' . $default[0]['Name'];
							}
							
							if($client['Default_Client'] == 1) {
								$checked = 'checked = "" ';
							} else {
								$checked = '';
							}

							?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment"></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
									<div class="ln_solid"></div>
									<h4>Rendelhető installációk</h4>
									<div class="ln_solid"></div>
								</div>
							</div>
							<?php $this->installations->load_installations_checkboxes($_GET['id']);?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment"></label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-default">Alap megbízó</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="client-default" name="client-default" class="ui-checkbox" <?php echo $checked;?> type="checkbox">
											<label class="client-default" for="client-default"><?php echo $default_client;?></label>
										</label>
									</div>
								</div>
							</div>
							<?php endif;?>
							<?php
							if($client['Active'] == 1) {
								$checked = 'checked = "" ';
							} else {
								$checked = '';
							}
							?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="client-active" name="client-active" class="ui-checkbox" <?php echo $checked;?> type="checkbox">
											<label class="client-active" for="client-active"></label>
										</label>
									</div>
								</div>
							</div>
							
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="client-edit-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-id="<?php echo $client['ID'];?>" data-cancel-href="<?php echo $this->create_url('listclients');?>" id="client-form-cancel" class="btn btn-default" type="button">Mégsem</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
