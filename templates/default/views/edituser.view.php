<?php
$user = $this->user->load_user($_GET['id']);

// Lock vizsgálata
$lock = $this->user->locked_user($_GET['id']);

if(!$lock && $user['Locked'] != $_SESSION["HDT_uid"]) {
	$this->user->user_lock($_GET['id']);
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
						<?php elseif(!$this->user->user_access($user)):?>
							<p>Nincs joga szerkeszteni ezt a felhasználót</p>
							<?php $this->user->unlock($user['ID']);?>
						<?php else:?>
							<?php if($user['ID'] == $_SESSION['HDT_uid']):?>
								<h2>Saját adatok szerkesztése</h2>
							<?php else:?>
								<h2>Felhasználó szerkesztése : <?php echo $user["FullName"] . ' ( ' . $user['Login'] . ' )';?></h2>
							<?php endif;?>
						<?php endif;?>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<?php if($lock !== true && $this->user->user_access($user)):?>
					<div class="x_content">
						<form id="user-edit-form" method="POST" class="edit-form form-horizontal form-label-left" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="user_edit" />
							<input type="hidden" name="id" value="<?php echo $user['ID'];?>" />
							<?php if($lock !== true):?>
							<input type="hidden" name="lock" value="<?php echo $_SESSION["HDT_uid"]?>" />
							<?php endif;?>
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
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-fullname">Teljes név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="user-fullname" name="user-fullname" value="<?php echo $user['FullName'];?>" required>  
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-phonenum">Telefonszám</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									 <input type="text" class="form-control" id="user-phonenum" name="user-phonenum" value="<?php echo $user['Phone'];?>" required>  
								 </div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12">E-mail</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="user-email" name="user-email" value="<?php echo $user['Email'];?>" required>   
								</div>
							</div>
							
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-level">Felhasználó típusa</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<?php echo $this->user->user_level_select($user['UserType']);?>
								</div>
							</div>
							<?php
							$logged_user = $this->user->load_user($_SESSION['HDT_uid']);
							?>
							<?php if($logged_user['UserType'] <= ADMIN_USER):?>
							<?php
							// Ha Megbízó admin, kijelölni mely klient az övé
							$owner = $this->clients->check_owner($user['ID']);
							if($owner['Owner'] == $user['ID']) {
								$own = $owner['ID']; 
							} else {
								$own = null;
							}
							if($user['UserType'] != 6):
								?><div id="client-select-container" class="form-group form-float" style="display:none;"><?php
							else:
								?><div id="client-select-container" class="form-group form-float"><?php
							endif;
							?>
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-level">Megbízó kiválasztása</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select name="user-own" class="form-control ch_mark">
										<?php if($own == null):?>
										<option value="">Válasszon</option>
										<?php else:?>
										<option value="-1">Adminisztrátori jogkör megszüntetése</option>
										<?php endif;?>
										<?php echo $this->clients->clients_select($own);?>
									</select>
								</div>
							</div>
							<div id="partner-id-select" class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-partner-id">Tulajdonos felhasználó</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select name="user-partner-id" class="form-control ch_mark">
										<?php echo $this->user->users_select($user['Partner_ID']);?>
									</select>
								</div>
							</div>
							<?php else:?>
							<input type="hidden" name="user-partner-id" value="<?php echo $user['Partner_ID']?>" />
							<?php endif;?>
							
							<?php 
								if($user['List_style'] == 1) {
									$checked = 'checked = "checked" ';
								} else {
									$checked = '';
								}
							?>
							
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-list_style">Kompakt megbízás lista</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="user-list_style" name="user-list_style" class="ui-checkbox"  <?php echo $checked;?> type="checkbox">
											<label class="user-list_style" for="user-list_style"></label>
										</label>
									</div>
								</div>
							</div>
							
							<?php
							
							unset($checked);
							
							if($user['Active'] == 1) {
								$checked = 'checked = "" ';
							} else {
								$checked = '';
							}
							?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="user-active" name="user-active" class="ui-checkbox" <?php echo $checked;?> type="checkbox">
											<label class="user-active" for="user-active"></label>
										</label>
									</div>
								</div>
							</div>
							<!--<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
									<?php if($user['Active'] == 1):?>
									<label class="">
									  <div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat" name="user-active" checked="checked" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
									</label>
									<?php else: ?>
									<label class="">
									  <div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat" name="user-active" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
									</label>
									<?php endif; ?>
								  </div>
								</div>		
							</div>-->
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="user-edit-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-id="<?php echo $user['ID'];?>" data-cancel-href="<?php echo $this->create_url('listusers');?>" id="user-form-cancel" class="btn btn-default" type="button">Mégsem</button>
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
