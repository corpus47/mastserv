<?php
$master = $this->master->load_master($_GET['id']);
if(trim($master['installations']) != "") {
	$master['installations'] = unserialize($master['installations']);
}
//var_dump($master);
// Lock vizsgálata
$lock = $this->master->locked_master($_GET['id']);

if(!$lock && $master['Locked'] != $_SESSION["HDT_uid"]) {
	$this->master->master_lock($_GET['id']);
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
					<!-- #alert -->
					<div class="x_title">
						<?php if($lock == true):?>
							<p>Zárolt! Jelenleg másik felhasználó szerkeszti</p>
						<?php else:?>
							<?php if($master['ID'] == $_SESSION['HDT_uid']):?>
								<h2>Saját adatok szerkesztése</h2>
							<?php else:?>
								<h2>Mester szerkesztése : <?php echo $master["Name"] . ' ( ' . $master['LPNumber'] . ' )';?></h2>
							<?php endif;?>
						<?php endif;?>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<?php if($lock !== true):?>
					<div class="x_content">
						<form id="master-edit-form" method="POST" class="edit-form form-horizontal form-label-left" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="master_edit" />
							<input type="hidden" name="id" value="<?php echo $master['ID'];?>" />
							<?php if($lock !== true):?>
							<input type="hidden" name="lock" value="<?php echo $_SESSION["HDT_uid"]?>" />
							<?php endif;?>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-name">Név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="master-name" name="master-name" value="<?php echo $master['Name'];?>" required>
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="pwd-one">Jelszó</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="password" class="form-control" id="pwd-one" name="pwd-one" required>
								</div>           
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="pwd-true">Jelszó újra</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="password" class="form-control" id="pwd-true" name="pwd-true" required>  
								</div>
							</div>
							<!-- A tulajdonos kijelölése -->
							<?php if($this->user->is_super($_SESSION["HDT_uid"]) || $this->user->is_admin($_SESSION["HDT_uid"])):?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-phonenum">Alvállalkozó</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
								<?php $this->subcontactor->subconid_select($master["Subconid"]);?>
								</div>
							</div>
							<?php elseif($this->user->is_subcon_admin($_SESSION["HDT_uid"])):?>
								<input type="hidden" name="master-subconid" value="<?php echo $_SESSION["HDT_uid"];?>" />
							<?php endif;?>
							<!--<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-fullname">Teljes név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="user-fullname" name="user-fullname" required>  
								</div>
							</div>-->
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-phonenum">Telefonszám</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									 <input type="text" class="form-control" id="master-phonenum" name="master-phonenum" value="<?php echo $master['Phone'];?>" required>  
								 </div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-email">E-mail</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="user-email" name="master-email" value="<?php echo $master['Email'];?>" required>   
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-cartype">Autó típusa</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="master-cartype" name="master-cartype" value="<?php echo $master['Cartype'];?>" required>  
								</div>
							</div>
							<!--<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-lpnumber">Autó rendszáma</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="master-lpnumber" name="master-lpnumber" value="<?php echo $master['LPNumber'];?>" required>  
								</div>
							</div>-->
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-comment">Megjegyzés</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="6" class="form-control no-resize" id="master-comment" name="master-comment" required placeholder=""><?php echo $master['Comment'];?></textarea>
                                    
                                </div>
                            </div>
                            <div class="form-group form-float">
                            	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-comment">Installációk</label>
                            	<div class="col-md-6 col-sm-6 col-xs-12">
                                <?php $this->installations->installations_to_master($master['installations']);?>
                                </div>
                            </div>
							<?php
							if($master['Active'] == 1) {
								$checked = 'checked = "" ';
							} else {
								$checked = '';
							}
							?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
									<label class="">
									  <div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat" name="master-active" <?php echo $checked;?> style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
									</label>
								  </div>
								</div>		
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="master-edit-form-submit" class="btn btn-success" type="button">Felvisz</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
					<?php endif;?>
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
