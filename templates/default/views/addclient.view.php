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
						<h2>Új megbízó hozzáadása</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="client-form" method="POST" class="edit-form form-horizontal form-label-left" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="client" />
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-name">Ügyfél import</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select id="import-clientid" name="import-clientid" class="form-control select2_single short-field" >
										<option value="">Válasszon!</option>
										<?php echo $this->parcel->parcelclients_select_for_import(); ?>
									</select>
									<input type="hidden" name="partner_group_id" value="" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-name">Név</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="client-name" name="client-name" required>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-prefix">Előtag</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="client-prefix" name="client-prefix" required>
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-comment">Megjegyzés</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="6" class="form-control no-resize" id="client-comment" name="client-comment" required placeholder=""></textarea> 
                                </div>
                            </div>
							<div id="import-subclients-container" class="form-group form-float" style="display:none;">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-comment">Almegbízók importja
								</label>
								<div data-role="controlgroup" class="col-md-6 col-sm-6 col-xs-12 clientslist-container"></div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="client-active">Aktív</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<div class="checkbox">
										<label>
											<input id="client-active" name="client-active" class="ui-checkbox" checked="checked" type="checkbox">
											<label class="client-active" for="client-active"></label>
										</label>
									</div>
								</div>
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="client-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-cancel-href="<?php echo $this->create_url('listclients');?>" id="client-form-cancel" class="btn btn-default" type="button">Mégsem</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->