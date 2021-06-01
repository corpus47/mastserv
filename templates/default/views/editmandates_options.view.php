<?php
$mandates_option = $this->mandates_options->load_mandates_option($_GET['id']);
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
						<h2>Új megbízás opció</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="edit-mandates-option-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="edit_mandates_option" />
							<input type="hidden" name="id" value="<?php echo $mandates_option['ID'];?>" />
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandates-option-name">Opció megnevezése</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandates-option-name" name="mandates-option-name" value="<?php echo $mandates_option['OptionName'];?>" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandates-option-distance">Opcióhoz rendelt távolság (km)</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandates-option-distance" name="mandates-option-distance" value="<?php echo $mandates_option['Distance'];?>" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="edit-mandates-option-form-submit" class="btn btn-success" type="button">Felvisz</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
