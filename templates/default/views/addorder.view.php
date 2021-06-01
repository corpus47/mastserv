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
						<h2>Új megbízás</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="order-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="order" />
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="order-name">Név</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="order-name" name="order-name" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="order-zipcode">Irányítószám</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
								<?php $this->cities->cities_select();?>
								</div>
							</div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="order-city">Helység</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="order-city" name="order-city" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="order-address">Cím</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="order-address" name="order-address" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="order-phonenum">Telefonszám</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="order-phonenum" name="order-phonenum" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="order-email">E-mail</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="order-email" name="order-email" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            		<div class="ln_solid"></div>
                            	</div>
                            </div>
                            <div class="form-group form-float">
                            	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="order-email">Tétel neve</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="order-item-name" name="order-item-name" required>
                               	</div>
                            </div>
                            <div class="form-group form-float">
                               	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="order-email">Mennyiség</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="order-item-count" name="order-item-count" required>
                               	</div>
                            </div>
                            <div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            		<button id="order-add-item" class="btn btn-primary" type="button">Új tétel</button>
                            		<div class="ln_solid"></div>
                            	</div>
							</div>
							<input type="hidden" name="item-count" value="0" />
                           	<div id="items-container" class="form-group form-float">
                           		<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                           		</div>
                           	</div>
                           	<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            		<div class="ln_solid"></div>
                            	</div>
                            </div>
                            <div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="order-form-submit" class="btn btn-success" type="button">Felvisz</button>
								</div>
							</div>
						</form><!-- #order-form -->
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->