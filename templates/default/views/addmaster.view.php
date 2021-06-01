<?php
?>
<?php
if(isset($_SESSION['HDT_parcel_user'])):?>
<div class="right_col" role="main">
	<div class="">
		<div class="page-title">
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Új mester</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
					Önnek nincs jogosultsága ehhez a területhez! Ha MasterService felhasználó, jelentkezzen be újra!
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
<?php return; ?>
<?php endif;?>
<div class="right_col" role="main">
	<div class="">
		<div class="page-title">
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Új mester</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="master-form" method="POST" class="edit-form form-horizontal form-label-left" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="master" />
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-name">Név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="master-name" name="master-name" required>
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
							<!--<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user-fullname">Teljes név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="user-fullname" name="user-fullname" required>  
								</div>
							</div>-->
							<!-- A tulajdonos kijelölése -->
							<?php if($this->user->is_super($_SESSION["HDT_uid"]) || $this->user->is_admin($_SESSION["HDT_uid"])):?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-phonenum">Alvállalkozó</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
								<?php $this->subcontactor->subconid_select();?>
								</div>
							</div>
							<?php elseif($this->user->is_subcon_admin($_SESSION["HDT_uid"])):?>
								<input type="hidden" name="master-subconid" value="<?php echo $_SESSION["HDT_uid"];?>" />
							<?php endif;?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-phonenum">Telefonszám</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									 <input type="text" class="form-control" id="master-phonenum" name="master-phonenum" required>  
								 </div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-email">E-mail</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="user-email" name="master-email" required>   
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-cartype">Autó típusa</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="master-cartype" name="master-cartype" required>  
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-lpnumber">Autó rendszáma</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="master-lpnumber" name="master-lpnumber" required>  
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-comment">Megjegyzés</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="6" class="form-control no-resize" id="master-comment" name="master-comment" required placeholder=""></textarea>
                                    
                                </div>
                            </div>
                            <div class="form-group form-float">
                            	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-comment">Installációk</label>
                            	<div class="col-md-6 col-sm-6 col-xs-12">
                                <?php $this->installations->installations_to_master();?>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
									<label class="">
									  <div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat" name="master-active" checked="checked" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
									</label>
								  </div>
								</div>		
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="master-form-submit" class="btn btn-success" type="button">Felvisz</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
