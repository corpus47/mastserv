<?php

?>
<div class="right_col" role="main">
	<div class="">
		<div class="page-title">
		</div>
		<div class="clearfix"></div>
		<?php if($this->subclients->check_rule()):?>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Almegbízók</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<!--<div id="clients-table-container">-->
						<?php echo $this->subclients->subclients_table();?>
						<!--</div>-->
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
		<?php endif;?>
	</div>
</div><!-- #right_col -->
