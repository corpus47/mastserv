<?php
?>
<div class="right_col" role="main">
	<div class="">
		<div class="page-title">
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Új felhasználó</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="user-form" method="POST" class="edit-form form-horizontal form-label-left" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="user" />
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-name">Login név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="user-name" name="user-name" required>
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
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-fullname">Teljes név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="user-fullname" name="user-fullname" required>  
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-phonenum">Telefonszám</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									 <input type="text" class="form-control" id="user-phonenum" name="user-phonenum" required>  
								 </div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-email">E-mail</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="user-email" name="user-email" required>   
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-level">Felhasználó típusa</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<?php echo $this->user->user_level_select();?>
								</div>
							</div>
							<?php
							$logged_user = $this->user->load_user($_SESSION['HDT_uid']);
							?>
							<?php if($logged_user['UserType'] <= ADMIN_USER):?>
							<div id="client-select-container" class="form-group form-float" style="display:none;">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-own">Megbízó kiválasztása</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select name="user-own" class="form-control ch_mark">
										<option value="">Válasszon</option>
										<?php echo $this->clients->clients_select($own);?>
									</select>
								</div>
							</div>
							<div id="partner-id-select" class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-partner-id">Tulajdonos felhasználó</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select name="user-partner-id" class="form-control ch_mark">
										<?php echo $this->user->users_select($_SESSION['HDT_uid']);?>
									</select>
								</div>
							</div>
							<?php else:?>
							<?php //var_dump($_SESSION);?>
							<input type="hidden" name="user-partner-id" value="<?php echo $_SESSION['HDT_uid']?>" />
							<?php endif;?>
							
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-list_style">Kompakt megbízás lista</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="user-list_style" name="user-list_style" class="ui-checkbox" type="checkbox">
											<label class="user-list_style" for="user-list_style"></label>
										</label>
									</div>
								</div>
							</div>
							
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="user-active" name="user-active" class="ui-checkbox" checked="checked" type="checkbox">
											<label class="user-active" for="user-active"></label>
										</label>
									</div>
								</div>
							</div>
							
							<!--<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
									<label class="">
									  <div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat" name="user-active" checked="checked" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
									</label>
								  </div>
								</div>		
							</div>-->
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="user-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-id="<?php echo $user['ID'];?>" data-cancel-href="<?php echo $this->create_url('listusers');?>" id="user-form-cancel" class="btn btn-default" type="button">Mégsem</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
