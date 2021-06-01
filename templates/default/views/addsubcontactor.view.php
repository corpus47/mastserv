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
						<h2>Új alvállalkozó</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="subcontactor-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="subcontactor" />
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-name">Megnevezés</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-name" name="subcontactor-name" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-address">Telephely</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-address" name="subcontactor-address" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-contactname">Kapcsolattartó</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-contactname" name="subcontactor-contactperson" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-phonenum">Telefonszám (Pl.: 06101234567)</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-phonenum" name="subcontactor-phonenum" required>  
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-email">E-mail</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subcontactor-email" name="subcontactor-email" required>
                                    
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-zips">Irányítószámok</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="6" class="form-control no-resize" id="subcontactor-zips" name="subcontactor-zips" required placeholder=""></textarea>
                                    
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-active">Aktív</label>
								<!--<input type="checkbox" id="subcontactor-active" name="subcontactor-active" class="filled-in chk-col-light-green" >-->
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label class="">
										  <div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat" name="subcontactor-active" checked="checked" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
										</label>
									</div>
								</div>
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="subcontactor-form-submit" class="btn btn-success" type="button">Felvisz</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->