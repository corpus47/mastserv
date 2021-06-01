<?php

$user = $this->user->load_user($_SESSION['HDT_uid']);

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
						<h2>Új üzenet hozzáadása</h2>
						<div class="clearfix"></div>
					</div><!-- #x_title -->
					<div class="x_content">
						<form id="email_contents-form" method="POST" class="edit-form form-horizontal form-label-left" novalidate="novalidate">
							<input type="hidden" name="save_db" value="" />
							<input type="hidden" name="act" value="email_contents" />
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_contents-label">Címke</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="email_contents-label" name="email_contents-label" required>
								</div>
							</div>
							<?php if($user['UserType'] == SUPER_USER):?>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_contents-hook">Hook</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<input type="text" class="form-control" id="email_contents-hook" name="email_contents-hook" required>
								</div>
							</div>
							<?php endif;?>
							<div class="form-group">
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="email_contents-content">Szöveg</label>
								<div class="col-md-6 col-sm-6 col-xs-12">
									
									<div class="btn-toolbar editor" data-role="editor-toolbar" data-target="#editor-one">
										<div class="btn-group">
										  <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font"><i class="fa fa-font"></i><b class="caret"></b></a>
										  <ul class="dropdown-menu">
										  </ul>
										</div>

										<div class="btn-group">
										  <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="fa fa-text-height"></i>&nbsp;<b class="caret"></b></a>
										  <ul class="dropdown-menu">
											<li>
											  <a data-edit="fontSize 5">
												<p style="font-size:17px">Huge</p>
											  </a>
											</li>
											<li>
											  <a data-edit="fontSize 3">
												<p style="font-size:14px">Normal</p>
											  </a>
											</li>
											<li>
											  <a data-edit="fontSize 1">
												<p style="font-size:11px">Small</p>
											  </a>
											</li>
										  </ul>
										</div>

										<div class="btn-group">
										  <a class="btn" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
										  <a class="btn" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>
										  <a class="btn" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
										  <a class="btn" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
										</div>

										<div class="btn-group">
										  <a class="btn" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>
										  <a class="btn" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>
										  <a class="btn" data-edit="outdent" title="Reduce indent (Shift+Tab)"><i class="fa fa-dedent"></i></a>
										  <a class="btn" data-edit="indent" title="Indent (Tab)"><i class="fa fa-indent"></i></a>
										</div>

										<div class="btn-group">
										  <a class="btn" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>
										  <a class="btn" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>
										  <a class="btn" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>
										  <a class="btn" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>
										</div>

										<div class="btn-group">
										  <a class="btn dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="fa fa-link"></i></a>
										  <div class="dropdown-menu input-append">
											<input class="span2" placeholder="URL" type="text" data-edit="createLink" />
											<button class="btn" type="button">Add</button>
										  </div>
										  <a class="btn" data-edit="unlink" title="Remove Hyperlink"><i class="fa fa-cut"></i></a>
										</div>

										<!--<div class="btn-group">
										  <a class="btn" title="Insert picture (or just drag & drop)" id="pictureBtn"><i class="fa fa-picture-o"></i></a>
										  <input type="file" data-role="magic-overlay" data-target="#pictureBtn" data-edit="insertImage" />
										</div> -->

										<div class="btn-group">
										  <a class="btn" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>
										  <a class="btn" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>
										</div>
										
										
									</div>
									<p>&nbsp;</p>
									<h4>Helyőrző - installáció helyének adatai</h4>
									
									<div class="btn-toolbar editor" data-role="editor-toolbar" data-target="#editor-one">
										<div class="btn-group" >
											<a data-tag="CUSTOMER-NAME" class="btn tag-insert" type="button">Név</a>
											<a data-tag="CUSTOMER-ADDRESS" class="btn tag-insert" type="button">Cím</a>
											<a data-tag="CUSTOMER-PHONENUM" class="btn tag-insert" type="button">Telefonszám</a>
											<a data-tag="CUSTOMER-TRACKURL" class="btn tag-insert" type="button">Nyomkövetés link</a>
											<a data-tag="MANDATE-SERIAL" class="btn tag-insert" type="button">A megbízás száma</a>
											<a data-tag="STATUS-LABEL" class="btn tag-insert" type="button">Státusz</a>
											<a data-tag="INVOICE-CONTENT" class="btn tag-insert" type="button">Számla</a>
										</div>
									</div>
									
									<div id="editor-one" class="editor-wrapper email_contents-content"></div>
									<textarea style="display:none;" id="email_contents-content" name="email_contents-content"></textarea>
								</div>
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									
								</div>
							</div>
							<div class="form-group form-float">
								<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
									<button id="email_contents-form-submit" class="btn btn-primary" type="button">Felvisz</button>
									<button data-cancel-href="<?php echo $this->create_url('listemail_contents');?>" id="email_contents-form-cancel" class="btn btn-default" type="button">Mégsem</button>
								</div>
							</div>
						</form>
					</div><!-- #x_content -->
				</div><!-- #x_panel -->
			</div><!-- #col -->
		</div><!-- #row -->
	</div>
</div><!-- right_col -->
