<?php

?>
	<div class="block-login">
		
		<div class="block-login-content">
			<div class="block-login-logo"></div>
			<h2>Belépés</h2>					
			<form id="signinForm" method="post" action="http://<?php echo $_SERVER['HTTP_HOST'],$_SERVER['REQUEST_URI'];?>">
				<!--<div class="master-login-instruction" style="display:none;">
				Mester belépéshez azonosítót kell megadnia. Pl.: 00001
				</div>-->
				<div class="form-group">                        
					<input type="text" name="username" class="form-control" placeholder="Név" value=""/>
				</div>
				<div class="form-group">                        
					<input type="password" name="password" class="form-control" placeholder="Jelszó" value=""/>
				</div>
				<?php
					if(isset($_SESSION["HDT_login_error"])) {
						?><div class="error" style="margin-bottom:20px;"><center><?php echo $_SESSION["HDT_login_error"];?></center></div><?php
						unset($_SESSION["HDT_login_error"]);
					}
				?>
				<!--<div class="form-group">
					<select name="login-type" class="form-control ch_mark theme-select">
                        <option value="">Váltson témát - jelenleg ( <?php echo $_SESSION['HDT_theme']?> )</option>
                        <option value="default">Régi (MISI)</option>
						<option value="new">Új téma</option>
					</select>
				</div>-->
				<!--<div class="form-group">
					<select name="login-type" class="form-control ch_mark login-type">
                        <option value="0">Mesterszolgáltatás felhasználó</option>
                        <option value="1">HDT felhasználó, partner</option>
						<option value="2">Mester belépés</option>
					</select>
				</div>-->
				<input type="hidden" name="login-type" value="0" />
				<button class="btn btn-primary btn-block" name="signin" type="submit">Belépés</button>                                        	
			</form>
			<div class="sp"></div>                    
			<div class="sp"></div>
			<div class="pull-left" style="text-align:center">
				© All Rights Reserved <b>Home Delivery Team</b> 2015
			</div>
		</div>
	</div>
	<script type="text/javascript">
		     
	</script>
