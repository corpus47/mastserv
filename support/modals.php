<div class="modal fade" id="SupportInitError" role="dialog">
	<div class="modal-dialog">      <!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Bejelentkezés</h4>
		  </div>
		  <div class="modal-body">
		  	<p>
		  	A mesterszervizben nem észlelhető bejelentkezett felhasználó.<br/>Jelentkezzen be itt!
		  	</p>
		  	<form id="support-login-form" role="form" method="post">
		  		<div class="form-group">
		  			<label>Név</label>
		  			<input type="text" name="loginname" placeholder="Azonosító" class="form-control" />
		  		</div>
		  		<div class="form-group">
		  			<label>Jelszó</label>
		  			<input type="password" name="loginpwd" placeholder="Jelszó" class="form-control" />
		  		</div>
		  		<div class="form-group">
		  			<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-lock"></span> Belépés</button>
		  		</div>
		  	</form>
		  </div>
		  <div class="modal-footer">
		    
		  </div>
		</div>
	</div>
</div>

<div class="modal fade" id="addBugError" role="dialog">
	<div class="modal-dialog">      <!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Hiba</h4>
		  </div>
		  <div class="modal-body">
		  	<p>
		  	<span id="err-message" style="font-weight:bold;color:red;"></span>!
		  	</p>
		  </div>
		  <div class="modal-footer">
		  	<button type="button" class="btn btn-default" data-dismiss="modal">Rendben</button>
		  </div>
		</div>
	</div>
</div>

<div class="modal fade" id="successOk" role="dialog">
	<div class="modal-dialog">      <!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> hdteam.me - Support</h4>
		  </div>
		  <div class="modal-body">
		  	<p>
		  	<span id="err-message" style="font-weight:bold;color:green;">Köszönjük hibajelzését! Igyekszünk minél előbb kijavítani!</span>!
		  	</p>
		  </div>
		  <div class="modal-footer">
		  	<button type="button" class="btn btn-default" data-dismiss="modal">Rendben</button>
		  </div>
		</div>
	</div>
</div>