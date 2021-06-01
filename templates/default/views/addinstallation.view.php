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
						<h2>Új rendelhető installáció</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="installation-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="installation" />
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="installation-cat-name">Megnevezés</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="installation-cat-name" name="installation-cat-name" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            		<div class="ln_solid"></div>
                            	</div>
							</div>
                            <div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            		<button id="installation-add-item" class="btn btn-primary" type="button">Új installáció felvitele</button>
                            		<div class="ln_solid"></div>
                            	</div>
							</div>
							<input type="hidden" name="installation-items-count" value="0" />
							<div id="installations-item-list">
							</div>
							<div class="form-group form-float installation-remove-item-container">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            		<button id="installation-remove-item" class="btn btn-danger" type="button">Sor törlése</button>
                            		<div class="ln_solid"></div>
                            	</div>
							</div>
							<div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            		<div class="ln_solid"></div>
                            	</div>
							</div>
							<!--<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="installation-cost">Díjszabás</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="installation-cost" name="installation-cost" required>
                                </div>
                            </div>-->
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="installation-form-submit" class="btn btn-success" type="button">Felvisz</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
