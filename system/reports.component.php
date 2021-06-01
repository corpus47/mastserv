<?php

class reports {
	
	private $parent;
	
	private $rules = array(
				SUPER_USER,
				ADMIN_USER,
				SUBCON_ADMIN,
				CLIENT_ADMIN,
				SUBCLIENT_ADMIN
			);
	
	public function __construct($parent = null) {
		
		$this->parent = $parent;
		
	}
	
	public function check_rule() {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);

		return in_array($user['UserType'],$this->rules);
	}
	
	public function DrawMenu() {
		ob_start();
		if($this->check_rule()):
		?>
			<li class="<?php echo $this->parent->action == 'reports' ? 'active' : '';?><?php echo $this->parent->action == 'reports' ? 'active' : '';?><?php echo $this->parent->action == 'reports' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('reports');?>"><i class="fa fa-bar-chart"></i> Jelentések</a>
			</li>
			
		<?php
		endif;
			
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function report($post = null) {
		
		if(!isset($post['reports-filter-form-submit'])){
			return "Üres lista";
		}
		$filter = array();
		
		if(isset($post['reports-filter-client']) && $post['reports-filter-client'] != "" && $post['reports-filter-subclient'] == ""){
			// Check all subclient
			
			$subclients = $this->parent->get_component('subclients')->subclients_list($post['reports-filter-client']);
			
			foreach($subclients as $row){
				$filter[] = "PartnerID = " . $row['ID'];
			}
		}
		
		if(isset($post['reports-filter-subclient']) && $post['reports-filter-subclient'] != ""){
			$filter[] = "PartnerID = " . $post['reports-filter-subclient'];
		}
		
		var_dump($filter);
		
	}
	
	public function reports_filter() {
		
		ob_start();
		?>
		<form id="reports-filter-form" class="edit-form form-horizontal form-label-left" method="post"action="<?php echo $this->parent->create_url('reports');?>" >
		<?php
		
		// Megbízó lista
		
		if($this->parent->get_component('user')->is_super($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_admin($_SESSION['HDT_uid'])):
		?>
			<div class="form-group form-float">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="reports-filter-client">Megbízó:</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
					<select id="reports-filter-client" name="reports-filter-client" class="form-control">
						<option value="">Válasszon</option>
						<?php
						$res = $this->parent->get_component('clients')->client_list();

						while($row = mysql_fetch_assoc($res)){
							//$clients[] = $row;
							?><option value="<?php echo $row['ID'];?>"><?php echo $row['Name'];?></option><?php
						}
						?>
					</select>
				</div>
				<?php //var_dump($clients);?>
			</div>
		<?php
		endif;
		?>
		<?php
		
		// Almegbízó
		
		if( $this->parent->get_component('user')->is_super($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_admin($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_client_admin($_SESSION['HDT_uid'])):
		?>
			<div class="form-group form-float">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="reports-filter-subclient">Almegbízó:</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
					<select id="reports-filter-subclient" name="reports-filter-subclient" class="form-control">
						<option value="">Válasszon</option>
						<?php
						if($this->parent->get_component('user')->is_client_admin($_SESSION['HDT_uid'])) {
							$subclients = $this->parent->get_component('subclients')->subclients_list($_SESSION['HDT_uid']);
						} else {
							$subclients = $this->parent->get_component('subclients')->subclients_list(null,true);
						}
						foreach($subclients as $row) {
							?><option value="<?php echo $row['ID'];?>"><?php echo $row['Name'];?></option><?php
						}
						?>
					</select>
				</div>
				<?php //var_dump($subclients);?>
			</div>
		<?php
		endif;
		?>
		<?php
		
		// Alvállalkozó
		
		if($this->parent->get_component('user')->is_super($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_admin($_SESSION['HDT_uid'])):
		
		?>
			<div class="form-group form-float">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="reports-filter-subcon">Alvállalkozó:</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
					<select id="reports-filter-subcon" name="reports-filter-subcon" class="form-control">
						<option value="">Válasszon</option>
						<?php
						$subcontactors = $this->parent->get_component('subcontactor')->list_subcontactors();
						foreach($subcontactors as $row){
							?><option value="<?php echo $row['ID'];?>"><?php echo $row['Name'];?></option><?php
						}
						?>
					</select>
				</div>
			</div>
			<?php //var_dump($subcontactors);?>			
		<?php
		endif;
		?>
		<?php
		
		// Mester
		
		if($this->parent->get_component('user')->is_super($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_admin($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_subcon_admin($_SESSION['HDT_uid'])):
			
			if($this->parent->get_component('user')->is_subcon_admin($_SESSION['HDT_uid'])){
				$filter = array("AdminID = " . $_SESSION['HDT_uid']);
				$subcontactors = $this->parent->get_component('subcontactor')->list_subcontactors($filter);
				if(isset($subcontactors[0])){
					$filter = array("Subconid = " . $subcontactors[0]['ID']);
				}
			} else {
				$filter = null;
			}
			
		
		?>
			<div class="form-group form-float">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="reports-filter-master">Mester:</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
					<select id="reports-filter-master" name="reports-filter-master" class="form-control">
						<option value="">Válasszon</option>
						<?php
						$masters = $this->parent->get_component('master')->list_masters($filter);
						foreach($masters as $row){
							?><option value="<?php echo $row['ID'];?>"><?php echo $row['Name'];?></option><?php
						}
						?>
					</select>
				</div>
			</div>
		<?php
		endif;
		?>
		<?php
		
		// Teljesítés dátumai
		
		?>
			<div class="form-group form-float">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="reports-filter-date-begin">Teljesítés dátuma - kezdő</label>
                <div class="col-md-6 xdisplay_inputx col-sm-6 col-xs-12">
                    <input type="text" class="form-control has-feedback-left short-field" id="reports-filter-date-begin" aria-describedby="inputSuccess2Status" name="reports-filter-date-begin">
					<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                 </div>
            </div>
			<p>&nbsp;</p>
			<div class="form-group form-float">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="reports-filter-date-end">Teljesítés dátuma - vége</label>
                <div class="col-md-6 xdisplay_inputx col-sm-6 col-xs-12">
                    <input type="text" class="form-control has-feedback-left short-field" id="reports-filter-date-end" aria-describedby="inputSuccess2Status" name="reports-filter-date-end">
					<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                 </div>
            </div>
			
			<div class="form-group form-float">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					<button id="reports-filter-form-submit" name="reports-filter-form-submit" class="btn btn-success" type="submit">Generálás</button>
				</div>
			</div>
		</form>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		echo $content;
		
	}
	
}

?>