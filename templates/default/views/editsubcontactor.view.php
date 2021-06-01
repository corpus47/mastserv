<?php
$subcontactor = $this->subcontactor->load_subcontactor($_GET['id']);
$lock = $this->subcontactor->locked_subcontactor($_GET['id']);

if(!$lock && $subcontactor['Locked'] != $_SESSION["HDT_uid"]) {
	$this->subcontactor->subcontactor_lock($_GET['id']);
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
						<?php if($lock == true):?>
							<p>Zárolt! Jelenleg másik felhasználó szerkeszti</p>
						<?php else:?>
							<h2>Alvállalkozó szerkesztése : <?php echo $subcontactor["Name"];?></h2>
						<?php endif;?>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<?php if($lock !== true):?>
					<div class="x_content">
						<form id="subcontactor-edit-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="subcontactor_edit" />
							<input type="hidden" name="id" value="<?php echo $subcontactor['ID'];?>" />
							<?php if($lock !== true):?>
							<input type="hidden" name="lock" value="<?php echo $_SESSION["HDT_uid"]?>" />
							<?php endif;?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-name">Megnevezés</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-name" name="subcontactor-name" value="<?php echo $subcontactor["Name"];?>" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-address">Telephely</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-address" name="subcontactor-address" value="<?php echo $subcontactor["Address"];?>" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-contactname">Kapcsolattartó</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-contactname" name="subcontactor-contactperson" value="<?php echo $subcontactor["ContactPerson"];?>" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-phonenum">Telefonszám (Pl.: 06101234567)</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-phonenum" name="subcontactor-phonenum" value="<?php echo $subcontactor["Phone"];?>" required>  
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-email">E-mail</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-email" name="subcontactor-email" value="<?php echo $subcontactor["Email"];?>" required>
                                    
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-zips">Irányítószámok</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="6" class="form-control no-resize" id="subcontactor-zips" name="subcontactor-zips" required placeholder=""><?php echo $subcontactor["Zips"];?></textarea>
                                    
                                </div>
                            </div>
                            <?php //var_dump($subcontactor);?>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-admin_id">Admin felhasználó</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select id="subcontactor-admin_id" name="subcontactor-admin_id" class="form-control select2_single short-field">
										<option value="null">Nincs adminisztrátora</option>
										<?php
										$filter = array("Active = 1", "UserType = " . SUBCON_ADMIN);
										
										$users = $this->get_component('user')->list_users($filter);
										
										$subcontactors = $this->get_component('subcontactor')->list_subcontactors();
										//var_dump($subcontactors);
										//var_dump($users);
										$admins = array();
										foreach($users as $row) {
											
											foreach($subcontactors as $key=>$tag){
												if($tag['AdminID'] == $row['ID'] && $tag['ID'] != $subcontactor['ID']){
													//var_export($tag);
													//unset($users[$key]);
													$admins[] = $row['ID'];
												}
											}
										}
										
										$selected = "";
										
										foreach($users as $row){
											if($subcontactor['AdminID'] == $row['ID']) {
												$selected = "SELECTED";
											}
											if(!in_array($row['ID'],$admins)):
											?><option value="<?php echo $row['ID']?>" <?php echo $selected;?>><?php echo $row['FullName']?></option><?php
											endif;
										}
										
										?>
										
									</select>
									<?php //var_dump($users);?>
								</div>
                            </div>
							<?php
							if($subcontactor['Active'] == 1) {
								$checked = 'checked = "" ';
							} else {
								$checked = '';
							}
							?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-active">Aktív</label>
								<!--<input type="checkbox" id="subcontactor-active" name="subcontactor-active" class="filled-in chk-col-light-green" >-->
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label class="">
										  <div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat" name="subcontactor-active" <?php echo $checked;?> style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
										</label>
									</div>
								</div>
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="subcontactor-edit-form-submit" class="btn btn-success" type="button">Felvisz</button>
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
