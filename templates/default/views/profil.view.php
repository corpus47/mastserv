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
						<?php else:?>
							<h2>Saját adatok szerkesztése</h2>
						<?php endif;?>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<?php if($lock !== true):?>
					<div class="x_content">
					<!--<pre><?php //var_dump($user);var_dump($lock);?></pre>-->
					<form id="profil-edit-form" method="POST" class="edit-form form-horizontal form-label-left" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="profile_edit" />
							<input type="hidden" name="id" value="<?php echo $user['ID'];?>" />
							<?php if($lock !== true):?>
							<input type="hidden" name="lock" value="<?php echo $_SESSION["HDT_uid"]?>" />
							<?php endif;?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="pwd-one">Login név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h4><?php echo $user['Login'];?></h4>
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
							
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<?php
									if($user['Active'] == 1) {
										?><input type="hidden" name="user-active" value="on" /><?php
									}
									?>
									<input type="hidden" name="user-level" value="<?php echo $user['UserType'];?>" />
									<input type="hidden" name="user-partner-id" value="<?php echo $user['Partner_ID']?>" />
									<button id="profil-edit-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-id="<?php echo $user['ID'];?>" data-cancel-href="http://<?php echo ROOT_URL;?>" id="profil-form-cancel" class="btn btn-default" type="button">Mégsem</button>
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