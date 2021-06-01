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
						<h2>Mesterek</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
							<?php
							
							if($this->user->is_super($_SESSION["HDT_uid"]) || $this->user->is_admin($_SESSION["HDT_uid"])) {
								
								$filter = null;
							} elseif($this->user->is_subcon_admin($_SESSION["HDT_uid"])) {
																
								$filt = array("AdminID = " . $_SESSION["HDT_uid"]);
								
								$subcontactors = $this->get_component('subcontactor')->list_subcontactors($filt);
								
								if(isset($subcontactors[0])){
									$filter = array("Subconid = " . $subcontactors[0]['ID']);
								}
							}
							//var_dump($filter);
							//if(isset($filter)){
							echo $this->master->masters_table($filter);
							//} else {
							//	echo 'Üres lista. Nincsenek mesterei!';
							//}
							
							?>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- #right_col -->
