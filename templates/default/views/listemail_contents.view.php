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
						<h2>E-mail szövegek</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
							<?php
							
							echo $this->email_contents->email_contents_table();
							
							?>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- #right_col -->
