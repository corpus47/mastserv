<?php
//$partner_group = $this->parcel->load_client($_GET['id']);
$subclient = $this->subclients->load_subclient($_GET['id']);

$lock = $this->subclients->locked_subclient($_GET['id']);

if(!$lock && $subclient['Locked'] != $_SESSION["HDT_uid"]) {
	$this->subclients->subclient_lock($_GET['id']);
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
						<h2>Almegbízó szerkesztése: <?php echo $subclient['Name'];?> ( <?php echo $subclient['Prefix'];?> )</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="subclient-edit-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="subclient_edit" />
							<input name="id" type="hidden" value="<?php echo $subclient['ID'];?>" />
							<?php if($lock !== true):?>
							<input type="hidden" name="lock" value="<?php echo $_SESSION["HDT_uid"]?>" />
							<?php endif;?>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-clientid">Ügyfél</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
								<h4><?php echo $this->clients->load_client($subclient['ClientID'])['Name'];?></h4>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-name">Név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="subclient-name" name="subclient-name" required value="<?php echo $subclient['Name']?>" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-prefix">Előtag</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="subclient-prefix" name="subclient-prefix" required value="<?php echo $subclient['Prefix']?>" />
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-zipcode">Irányítószám</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select id="subclient-zipcode-select" name="subclient-zipcode-select" class="form-control select2_single short-field" >
										<option value="">Válasszon!</option>
										<?php echo $this->cities->zipcodes_select($subclient['Zipcode']);?>
									</select>
								</div>
								<input type="hidden" name="subclient-zipcode" value="" />
							</div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-city">Helység</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subclient-city" name="subclient-city" required value="<?php echo $subclient['City']?>" />
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-address">Cím</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subclient-address" name="subclient-address" required value="<?php echo $subclient['Address']?>" />
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-phonenum">Telefonszám</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subclient-phonenum" name="subclient-phonenum" required value="<?php echo $subclient['Telephone']?>" />
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-email">E-mail</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subclient-email" name="subclient-email" required value="<?php echo $subclient['Email']?>" />
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment">Megjegyzés</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="6" class="form-control no-resize" id="subclient-comment" name="subclient-comment" required placeholder=""><?php echo $subclient['Name']?></textarea> 
                                </div>
                            </div>
                            
                            <?php
								//Enable cash on master
								if($subclient['Master_cash'] == 1) {
									$cash_checked = 'checked = "" ';
								} else {
									$cash_checked = '';
								}
                            ?>
                            
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-master_cash">Készpénzfizetés a mesternél</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="subclient-master_cash" name="subclient-master_cash" class="ui-checkbox" <?php echo $cash_checked;?> type="checkbox">
											<label class="subclient-master_cash" for="subclient-master_cash"></label>
										</label>
									</div>
								</div>
							</div>
							
							<?php // Felhasználók kijelölése ?>
							<?php
							$users = array(
											'admin' => array(),
											'user' => array(),
											'import' => array(),
											'export' => array(),
											);
							?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment">Admin felhasználók</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                     <?php echo $this->subclients->subclient_users($subclient['ID'],'admin');?>
                                </div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment">Normál felhasználók</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                     <?php echo $this->subclients->subclient_users($subclient['ID'],'user');?>
                                </div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment">Import felhasználók</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                     <?php echo $this->subclients->subclient_users($subclient['ID'],'import');?>
                                </div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment">Export felhasználók</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                     <?php echo $this->subclients->subclient_users($subclient['ID'],'export');?>
                                </div>
								
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment"></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
									<div class="ln_solid"></div>
									<h4>Rendelhető installációk</h4>
									<div class="ln_solid"></div>
								</div>
							</div>
							<?php $this->installations->load_installations_checkboxes($_GET['id'],true);?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment"></label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="ln_solid"></div>
								</div>
							</div>
							<?php
							if($subclient['Active'] == 1) {
								$checked = 'checked = "" ';
							} else {
								$checked = '';
							}
							?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="subclient-active" name="subclient-active" class="ui-checkbox" <?php echo $checked;?> type="checkbox">
											<label class="subclient-active" for="subclient-active"></label>
										</label>
									</div>
								</div>
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="subclient-edit-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-id="<?php echo $subclient['ID'];?>" data-cancel-href="<?php echo $this->create_url('listsubclients');?>" id="subclient-form-cancel" class="btn btn-default" type="button">Mégsem</button>
								</div>
							</div>
						</form>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
