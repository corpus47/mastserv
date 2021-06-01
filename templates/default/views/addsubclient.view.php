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
						<h2>Új almegbízó hozzáadása</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="subclient-form" method="POST" class="edit-form form-horizontal form-label-left" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="subclient" />
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-clientid">Ügyfél</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select id="subclient-clientid" name="subclient-clientid" class="form-control select2_single short-field" >
										<option value="">Válasszon!</option>
										<?php echo $this->clients->clients_select();?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-name">Név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="subclient-name" name="subclient-name" required>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-prefix">Előtag</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="subclient-prefix" name="subclient-prefix" required>
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-zipcode">Irányítószám</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select id="subclient-zipcode-select" name="subclient-zipcode-select" class="form-control select2_single short-field" >
										<option value="">Válasszon!</option>
										<?php echo $this->cities->zipcodes_select();?>
									</select>
								</div>
								<input type="hidden" name="subclient-zipcode" value="" />
							</div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-city">Helység</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subclient-city" name="subclient-city" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-address">Cím</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subclient-address" name="subclient-address" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-phonenum">Telefonszám</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subclient-phonenum" name="subclient-phonenum" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-email">E-mail</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="subclient-email" name="subclient-email" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-comment">Megjegyzés</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="6" class="form-control no-resize" id="subclient-comment" name="subclient-comment" required placeholder=""></textarea> 
                                </div>
                            </div>
                            
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-master_cash">Fizetés a mesternél</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="subclient-master_cash" name="subclient-master_cash" class="ui-checkbox" type="checkbox">
											<label class="subclient-master_cash" for="subclient-master_cash"></label>
										</label>
									</div>
								</div>
							</div>
							
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subclient-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="subclient-active" name="subclient-active" class="ui-checkbox" type="checkbox">
											<label class="subclient-active" for="subclient-active"></label>
										</label>
									</div>
								</div>
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="subclient-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-cancel-href="<?php echo $this->create_url('listsubclients');?>" id="subclient-form-cancel" class="btn btn-default" type="button">Mégsem</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
