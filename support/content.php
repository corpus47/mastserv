<?php
if($user != NULL):
?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
	<div class="row">
<?php //var_dump($_SESSION);?>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Hiba jelentése</div>
				<?php
					//var_dump($_POST);
					//var_dump($_FILES);
					//var_dump(dirname(__FILE__));
				?>
				<div class="panel-body">
					<form id="new-bug-form" role="form" enctype="multipart/form-data" method="post" action="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>">
						<div class="form-group">
							<label>Url *</label>
							<input type="text" name="bug-url" class="form-control" placeholder="Másolja ide a hiba url-jét!"/>
						</div>
						<div class="form-group">
							<label>Hiba leírása *</label>
							<textarea name="bug-description" class="form-control" rows="10"></textarea>
						</div>
						<div class="form-group">
							<label class="custom-file-upload">
								<input type="file" name="attachment-file"/>
								Képernyőkép feltöltése
							</label>
							<span id="filename-placeholder">Nincs file kiválasztva</span>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary" name="buginfo-send">Beküldöm</button>
						</form>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif;?>
