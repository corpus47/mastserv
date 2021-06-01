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
						<h2>Új fenntartott dátum felvitel</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
					<form id="day-form" method="POST" class="edit-form form-horizontal form-label-left" novalidate="novalidate">
						<input type="hidden" name="save_db" value="" />
						<input type="hidden" name="act" value="day" />
						<div class="form-group form-float">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-kiszallitas">Fenntartott dátum</label>
                            <div class="col-md-6 xdisplay_inputx col-sm-6 col-xs-12">
                                <input type="text" class="form-control has-feedback-left short-field" id="day-datum" aria-describedby="inputSuccess2Status" name="day-datum">
								<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                            </div>
                        </div>
						<div class="form-group form-float">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="day-allyear">A nap minden évben ismétlődik</label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="checkbox">
									<label>
										<input id="day-allyear" name="day-allyear" class="ui-checkbox" type="checkbox">
										<label class="user-active" for="day-allyear"></label>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group form-float">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="day-workingday">Hétvégi munkanap</label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="checkbox">
									<label>
										<input id="day-workingday" name="day-workingday" class="ui-checkbox" type="checkbox">
										<label class="user-active" for="day-workingday"></label>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group form-float">
							<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
								<button id="day-form-submit" class="btn btn-primary" type="button">Felvisz</button>
								<button data-cancel-href="<?php echo $this->create_url('listdays');?>" id="day-form-cancel" class="btn btn-default" type="button">Mégsem</button>
							</div>
						</div>
					</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->