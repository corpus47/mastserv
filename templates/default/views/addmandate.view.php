<?php
//$client = $this->clients->check_owner($_SESSION['HDT_uid']);
//var_dump($client);
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
						<form id="mandate-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate" enctype="multipart/form-data">
						<!--<form id="mandate-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate" >-->
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="mandate" />
							<?php
							
							/*if(isset($_SESSION['HDT_parcel_user'])){
								$addmandate_user = $_SESSION['HDT_parcel_user'];
							} else {
								$addmandate_user = "428";
							}*/
							?>
							<?php

							$logged_user = $this->user->load_user($_SESSION['HDT_uid']);
							if($logged_user['UserType'] == SUPER_USER || $logged_user['UserType'] == ADMIN_USER){
								$client = $this->clients->load_default_client();
								//var_dump($client);
								?>
								<div id="partner-id-select" class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-partner-id">A szolgáltatás megrendelője (almegbízó)</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<select id="mandate-partner-id" name="mandate-partner-id" class="form-control ch_mark">
										<?php echo $this->subclients->subclients_select($client[0]['ID']);?>
									</select>
									<input id="mandate-hdt-partner-id" type="hidden" name="mandate-hdt-partner-id" value="" />
								</div>
								</div>
								<?php
							} elseif($logged_user['UserType'] == CLIENT_ADMIN) {
								$client = $this->clients->check_owner($_SESSION['HDT_uid']);
								//var_dump($client);
								?>
								<div id="partner-id-select" class="form-group form-float">
									<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-partner-id">A szolgáltatás megrendelője (almegbízó)</label>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<select id="mandate-partner-id" name="mandate-partner-id" class="form-control ch_mark">
										<?php echo $this->subclients->subclients_select($client['ID']);?>
										</select>
										<input id="mandate-hdt-partner-id" type="hidden" name="mandate-hdt-partner-id" value="" />
									</div>
								</div>
								<?php
							} else {
								
								$parent = $this->user->load_user($logged_user['Partner_ID']);
								$client = $this->clients->check_owner($parent['ID']);
								
								$filter = array(
										"ClientID = " . $client['ID'],
										);
								
								$subclients = $this->subclients->check_subclient_user($logged_user,$filter);
								
								echo  $this->mandates->generate_subclient_check($subclients);
								
							}
							?>
							
							<!--<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-partner-id">Partner</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                	<label class="control-label col-md-3 col-sm-3 col-xs-12 align-left" for="mandate-partner-name"></label>
                                    <input type="hidden" class="form-control" id="mandate-hdt-partner-id" name="mandate-hdt-partner-id" value="" required>
                                    
                                </div>
                            </div>-->
                            <?php //else:?>
                            <!--<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-partner-id">Partnerek</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
								<?php //echo $this->parcel->partner_select();?>
								</div>
							</div>-->
							<?php //endif;?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-name"></label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h4>Installálási hely cím adatok</h4>
									<div class="ln_solid"></div>
								</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-name">Név</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-name" name="mandate-customer-name" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-zipcode">Irányítószám</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
								<select id="cities-list" name="cities-list" class="form-control select2_single short-field" >
								<?php //$this->cities->cities_select();?>
									<option value="">Válasszon!</option>
									<?php echo $this->cities->zipcodes_select();?>
								</select>
								</div>
								<input type="hidden" name="mandate-customer-zipcode" value="" />
							</div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-city">Helység</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-city" name="mandate-customer-city" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-address">Cím</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-address" name="mandate-customer-address" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-phonenum">Telefonszám</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-phonenum" name="mandate-customer-phonenum" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-email">E-mail</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-email" name="mandate-customer-email" required>
                                    <div class="ln_solid"></div>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-kiszallitas">Kiszállítás dátuma</label>
                                <div class="col-md-6 xdisplay_inputx col-sm-6 col-xs-12">
                                    <input type="text" class="form-control has-feedback-left short-field" id="mandate-kiszallitas" aria-describedby="inputSuccess2Status" name="mandate-kiszallitas">
									<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                             		<h4>Installációk rendelése</h4>
                             		<div class="ln_solid"></div>
                             	</div>
                            </div>
                            <div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                             	 <input type="hidden" name="row-index" value="0" />
                             	 <table id="products-list" class="installations-table">
                             		<thead>
                             			<tr>
	                             			<td>Termék</td>
	                             			<td>Installáció</td>
	                             			<td>&nbsp;</td>
	                             		</tr>
                             		</thead>
                             		<tbody>
                             		</tbody>
                             	 </table>
                             	 <div class="ln_solid"></div>
                             	 <table style="border:none;width:100%;">
                             	 	<tr>
                             	 		<td style="width:50%;text-align:right;">
                             	 		<h5>Szolgáltatások díja (netto): <span id="summa-cost-netto">0</span>&nbsp;Ft</h5>
                             	 		<!-- <h5>ÁFA (27%): <span id="summa-cost-afa">0</span>&nbsp;Ft</h5>
                             	 		<h5><strong>Fizetendő: <span id="summa-cost">0</span>&nbsp;Ft</strong></h5>-->
                             	 		</td>
                             	 	</tr>
                             	 </table>
                             	 <div class="ln_solid"></div>
                             	 <button id="add-installation" type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-sm">Installáció hozzáadása</button>
                             	 <div class="ln_solid"></div> 
                             	</div>
                            </div>
							<!-- <div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                             		<div class="ln_solid"></div>
                            		<button id="mandate-add-product" class="btn btn-primary" type="button">Termék hozzáadása</button>
                            		<div class="ln_solid"></div>
                            	</div>
							</div>
                            <input type="hidden" name="mandate-product-count" value="0" />
                            <div id="mandate-product-list">
							</div>
                            <div class="form-group form-float mandate-remove-item-container">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            		<button id="mandate-remove-item" class="btn btn-danger" type="button">Sor törlése</button>
                            		<div class="ln_solid"></div>
                            	</div>
							</div> -->
							<!-- <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="">Installációk</label>
                                <div id="installations-select-container" class="col-md-6 col-sm-6 col-xs-12">
								<?php
								//echo $this->installations->installations_select();
								?>
								<div class="ln_solid"></div>
                                </div>
                            </div>-->
                            <div class="form-group form-float upload-input">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="hdt-order-id">Fájl feltöltése:</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
									<!--<input type="hidden" name="upload_file" value="" />-->
								</div>
							</div>
							<p>&nbsp;</p>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="mandate-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-cancel-href="<?php echo $this->create_url('listmandates');?>&mode=unconfirmed" id="mandate-form-cancel" class="btn btn-default" type="button">Mégsem</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
<!-- Small modal -->
<div id="addInstallation" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
	<div id="add-installation-dialog" class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="add-installation-modal-title">Installáció hozzáadása</h4>
			</div>
			<div class="modal-body">
				<div class="installations-cats-select-container"></div>
				<div class="installations-select-container"></div>
			</div>
			<input name="to-address" type="hidden" value="" />
			<div class="modal-footer">
				<div class="error-msg" style="display:none;"></div>
				<button type="button" class="btn btn-default" data-dismiss="modal">Mégsem</button>
				<button id="add-installation-row" type="button" class="btn btn-primary" data-dismiss="modal">Hozzáadás</button>
			</div>
		</div>
	</div>
</div>
<!-- file upload dialog -->
<div id="file-upload-dialog" class="modal tables-dialog">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_content">
					<div id="filelist_container"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_content">
					<form id="file-upload-form" action="" class="edit-form form-horizontal form-label-left" method="post" enctype="multipart/form-data">
						<div class="form-group form-float">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="hdt-order-id">Csatolás típusa: </label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
								<select name="attachment-type" class="form-control">
									<option value="0">Vásárlási számla</option>
									<option value="1">Lezáró számla</option>
									<option value="2">Helyszíni fotó</option>
								</select>
							</div>
						</div>
						<div class="form-group form-float">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="hdt-order-id">Fájl feltöltése:</label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="file" class="btn btn-default" name="fileToUpload" id="fileToUpload">
								<input type="hidden" name="uploadform-mandate-id" value="" />
							</div>
						</div>
						<div class="form-group form-float">
							<div class="error-msg"></div>
						</div>
						<div class="form-group form-float">
							<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
								<input type="submit" class="btn btn-primary" value="Filefeltöltés" name="file-upload-form-submit">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- /modals -->
