<?php
$id = false;
if(isset($_POST['tracking_uname']) && isset($_POST['tracking_passw'])) {
	
	//$uname = mysql_real_escape_string($_POST['tracking_uname']);
	//$passw = mysql_real_escape_string($_POST['tracking_passw']);
	
	$uname = $_POST['tracking_uname'];
	$passw = $_POST['tracking_passw'];
	
	unset($_POST['tracking_uname']);
	unset($_POST['tracking_passw']);
	
	$id = $this->mandate_tracking->login_track($uname,$passw);

} elseif(isset($_GET['uname']) && isset($_GET['passw'])) {
	
	$uname = $_GET['uname'];
	$passw = $_GET['passw'];
	
	$id = $this->mandate_tracking->login_track($uname,$passw);
	
}

?>
<div class="block-mandate-track">	
		<div class="block-mandate-track-content">
			<div class="center_col" role="main">
			<div class="">
				<div class="page-title">
					<div class="logo-container"></div>
				</div>
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="x_panel">
							<div class="x_title">
								<h2>Megbízás követés</h2>
								<div class="clearfix"></div>
							</div><!-- #x_title -->
							<div class="x_content">
							<?php if($id == false):?>
							<form id="tracking-login" class="tracking-login-form" method="post" action="http://<?php echo ROOT_URL?>/nyomkovetes">
								<div class="form-group">
									<input type="text" class="form-control" name="tracking_uname" placeholder="Felhasználónév" />
								</div>
								<div class="form-group">
									<input type="text" class="form-control" name="tracking_passw" placeholder="Jelszó" />
								</div>
								<div class="form-group">
									<button class="btn btn-primary btn-block" name="signin" type="submit">Belépés</button>
								</div>
							</form>
							<?php else:?>
							<?php 
								$mandate = $this->mandates->load_mandate($id);
								//var_dump($mandate);
								?><p><h4>A szolgáltatás státusza</h4></p><?php
								$status = $this->statuses->get_status($mandate['Master_status']);
								?><p><span class="tracking-status-block" style="background-color:<?php echo $status['color']?>;"><?php echo $status['label'];?></span></p><?php
								?><div class="ln_solid"></div><?php
								?><p><strong>A megbízás száma: </strong><?php echo $mandate['Mandate_serial'];?></p><?php
								?><p style="font-style:italic;">(Kérjük, minden esetben erre hivatkozzon, amennyiben ügyfélszolgálatunkat felkeresi!)</p><?php
								$subclient = $this->subclients->load_subclient($mandate['PartnerID']);
								?><p><strong>A vásárlás helye:</strong> <?php echo $subclient['Name'];?></p><?php
								?><div class="ln_solid"></div><?php
								//var_dump($subclient);
								?><p><h4>A vásárló adatai</h4></p><?php
								?><p><strong>A vásárló neve:</strong><?php echo $mandate['CustomerName'];?></p><?php
								$address = $mandate['CustomerZipcode'] . " " . $mandate["CustomerCity"] . " " . $mandate["CustomerAddress"];
								?><p><strong>Az installáció helyének címe:</strong><?php echo $address;?></p><?php
								?><p><strong>A vásárló telefonszáma:</strong><?php echo $mandate['CustomerPhone'];?></p><?php
								?><p><strong>Az vásárló e-mail címe:</strong><?php echo $mandate['CustomerEmail'];?></p><?php
								?><div class="ln_solid"></div><?php
								?><!-- <p><h4>Megrendelt szolgáltatások</h4></p><?php
								?><div class="ln_solid"></div>--><?php
								
							?>	
							<?php endif;?>
							</div><!-- #x_content -->
						</div><!-- #x_panel -->
					</div><!-- #col -->
				</div><!-- #row -->
			</div>
		</div><!-- #right_col -->
		</div><!-- block-mandate-track-content -->
</div><!-- block-mandate-track -->
<?php unset($_SESSION['HDT_mandate_tracking']); ?>