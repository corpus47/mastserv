<?php
$mandate = $this->mandates->load_mandate($_GET['id']);

$lock = $this->mandates->locked_mandate($_GET['id']);

if(!$lock && $mandate['Locked'] != $_SESSION["HDT_uid"]) {
	$this->mandates->mandate_lock($_GET['id']);
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
					<div class="x_title">
						<?php if($lock == true):?>
							<p>Zárolt! Jelenleg másik felhasználó szerkeszti</p>
						<?php else:?>
							<h2>Megbízás szerkesztése : <?php echo $mandate["Mandate_serial"];?></h2>
						<?php endif;?>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<?php if($lock !== true):?>
					<div class="x_content">
						<form id="mandate-form" class="edit-form form-horizontal form-label-left" method="POST" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="mandate_edit" />
							<input type="hidden" name="id" value="<?php echo $mandate['ID'];?>" />
							<!--<input type="hidden" name="subcontactor-id" value="<?php echo $mandate['SubcontactorID'];?>" />-->
							<?php
								if($mandate['MasterID'] == "") {
									$mandate['MasterID'] = "NULL";
								}
							?>
							<input type="hidden" name="master-id" value="<?php echo $mandate['MasterID'];?>" />
							<input type="hidden" name="master-status" value="<?php echo $mandate['Master_status'];?>" />
							<?php if(isset($_GET['mode'])):?>
							<input type="hidden" name="get-mode" value="<?php echo $_GET['mode']?>" />
							<?php endif;?>
							<?php if($lock !== true):?>
							<input type="hidden" name="lock" value="<?php echo $_SESSION["HDT_uid"]?>" />
							<?php endif;?>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="hdt-order-id">Megbízás száma: </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                	<b><h4><?php echo $mandate['Mandate_serial'];?></h4></b>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="hdt-order-id">HDT fuvarszám</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                <?php if($mandate['HDT_order_id'] == NULL):?>
                                	<h5>Nincs fuvarhoz rendelve.</h5>
                                <?php else:?>
                                   <?php echo $mandate['HDT_order_id'];?>
                                <?php endif;?> 
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="hdt-order-id">A szolgáltatás megrendelője (almegbízó)</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                <?php if($mandate['PartnerID'] == NULL):?>
                                	<h5>Nincs partnerhez rendelve.</h5>
                                <?php else:?>
                                	<h5>
                                   <?php
                                   	/*$partner = $this->parcel->load_partner($mandate['PartnerID']);
                                    echo $partner['partner_name'];*/
                                   $subclient = $this->subclients->load_subclient($mandate['PartnerID']);
                                   echo $subclient['Name'];
                                   ?>
                                   </h5>
                                   <input type="hidden" id="mandate-hdt-partner-id" name="mandate-hdt-partner-id" value="<?php echo $mandate['PartnerID'];?>" />
                                <?php endif;?> 
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-id">Alvállalkozó</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
								<?php $this->subcontactor->subconid_select($mandate['SubcontactorID'],"subcontactor-id");?>
								</div>
								<!--<input type="hidden" name="mandate-customer-zipcode" value="<?php echo $mandate['CustomerZipcode'];?>" />-->
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-name">Mester kiválasztása</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="ln_solid"></div>
		                            <div id="confirm-master-dialog"></div>
		                            <input type="hidden" name="kiszallas-date" value="<?php echo $mandate['Kiszallas_Date']?>" />
 								<div class="ln_solid"></div>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h4>Installációs hely cím adatok</h4>
									<div class="ln_solid"></div>
								</div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-name">Név</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-name" name="mandate-customer-name" value="<?php echo $mandate['CustomerName'];?>" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-zipcode">Irányítószám</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
								<?php $this->cities->cities_select($mandate['CustomerZipcode']);?>
								</div>
								<input type="hidden" name="mandate-customer-zipcode" value="<?php echo $mandate['CustomerZipcode'];?>" />
							</div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-city">Helység</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-city" name="mandate-customer-city" value="<?php echo $mandate['CustomerCity'];?>" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-address">Cím</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-address" name="mandate-customer-address" value="<?php echo $mandate['CustomerAddress'];?>" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-phonenum">Telefonszám</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-phonenum" name="mandate-customer-phonenum" value="<?php echo $mandate['CustomerPhone'];?>" required>
                                </div>
                            </div>
                            <div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-customer-email">E-mail</label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" class="form-control" id="mandate-customer-email" name="mandate-customer-email" value="<?php echo $mandate['CustomerEmail'];?>" required>
                                </div>
                            </div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-kiszallitas">Kiszállítás dátuma</label>
                                <div class="col-md-6 xdisplay_inputx col-sm-6 col-xs-12">
                                    <input type="text" class="form-control has-feedback-left short-field" id="mandate-kiszallitas" aria-describedby="inputSuccess2Status" name="mandate-kiszallitas" value="<?php echo date('Y.m.d',strtotime($mandate['Kiszallitas']));?>" />
									<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<div class="ln_solid"></div>
                             		<h4>Rendelt installációk</h4>
                             		<div class="ln_solid"></div>
                             	</div>
                            </div>
                            <div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                             	 <input type="hidden" name="row-index" value="0" />
                             	 <!-- <table id="products-list" class="installations-table">
                             		<thead>
                             			<tr>
	                             			<td>Termék</td>
	                             			<td>Installáció</td>
	                             			<td>&nbsp;</td>
	                             		</tr>
                             		</thead>
                             		<tbody>
                             		</tbody>
                             	 </table>-->
                             	 <?php echo $this->mandates->installations_for_update($mandate);?>
                             	 <div class="ln_solid"></div>
                             	 <button id="add-installation" type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-sm">Installáció hozzáadása</button>
                             	 <div class="ln_solid"></div> 
                             	</div>
                            </div>
                            <!--<div class="form-group form-float">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                             		<div class="ln_solid"></div>
                            		<button id="mandate-add-product" class="btn btn-primary" type="button">Termék hozzáadása</button>
                            		<div class="ln_solid"></div>
                            	</div>
							</div>
							<?php
                            //$products = unserialize($mandate['Mandate_products']);
                            ?>
                             <input type="hidden" name="mandate-product-count" value="<?php echo count($products);?>" />
                            <div id="mandate-product-list">
                            	<div class="form-group form-float">
									<div class="col-md-3 col-sm-3 col-xs-12"></div>
									<div class="col-md-3 col-sm-3 col-xs-12">Termék</div>
				   				</div>
				   				<?php
				   					foreach($products as $key=>$product) {
				   						?>
				   						<div id="item-row-'.$i.'" class="form-group form-float">
											<div class="col-md-3 col-sm-3 col-xs-12"></div>
											<div class="col-md-3 col-sm-3 col-xs-12">
												<input placeholder="Megnevezés" type="text" class="form-control item-name" id="mandate-product-name-'<?php echo $key;?>'" name="mandate-product-name[<?php echo $key;?>]" value="<?php echo $product?>" required />
											</div>
										</div>
				   						<?php
				   					}
				   				?>
							</div>
                            <div class="form-group form-float mandate-remove-item-container">
                             	<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            		<button id="mandate-remove-item" class="btn btn-danger" type="button">Sor törlése</button>
                            		<div class="ln_solid"></div>
                            	</div>
							</div>
							<div class="form-group form-float">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="">Installációk</label>
                                <div id="installations-select-container" class="col-md-6 col-sm-6 col-xs-12">
								<?php
								//echo $this->installations->installations_select($partner['partner_id'],$mandate['Mandate_installations']);
								//echo $this->installations->installations_select($subclient['ID'],$mandate['Mandate_installations']);
								?>
								<div class="ln_solid"></div>
                                </div>
                            </div>-->
                            <div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button data-id="<?php echo $mandate['ID'];?>" class="file-upload-button btn btn-primary" type="button">Csatolt fájlok</button>
								</div>
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="mandate-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-id="<?php echo $mandate['ID'];?>" data-cancel-href="<?php echo $this->create_url('listmandates');?>&mode=unconfirmed" id="mandate-form-cancel" class="btn btn-default" type="button">Mégsem</button>
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
