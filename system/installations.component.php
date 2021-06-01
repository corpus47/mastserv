<?php

require_once('DbEngines/installations.db.php');
require_once('DbEngines/mandates_options.db.php');
require_once('DbEngines/clientoptions.db.php');
require_once('DbEngines/parcel.db.php');

class installations {
	
	private $installations_db;
	
	private $mandates_options_db;
	
	private $clientoptions;
	
	private $parcel_db;
	
	private $parent;
	
	private $rules = array(
							SUPER_USER,
							ADMIN_USER,
							);
	
	public function __construct($parent = null){
		
		$this->parent = $parent;
		
		$this->installations_db = new InstallationsDB();
		$this->mandates_options_db = new Mandates_optionsDB();
		$this->clientoptions = new ClientoptionsDB();
		$this->parcel_db = new ParcelDB();
		
	}
	
	public function check_rule() {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);

		return in_array($user['UserType'],$this->rules);
	}
	
	public function DrawMenu() {
		ob_start();
		if($this->check_rule()):
		?>
			<li class="<?php echo $this->parent->action == 'installations' ? 'active' : '';?><?php echo $this->parent->action == 'addinstallation' ? 'active' : '';?><?php echo $this->parent->action == 'editinstallation' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('installations');?>"><i class="fa fa-cogs"></i> Rendelhető installációk <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li class="<?php echo $this->parent->action == 'addinstallation' ? 'current-page' : '';?>"><a href="<?php echo $this->parent->create_url('addinstallation');?>">Új felvitel</a></li>
					<li class="<?php echo $this->parent->action == 'installations' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('installations');?>">Lista</a></li>
				</ul>
			</li>
		<?php
		endif;
			
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function load_installation($id = NULL) {
		return $this->installations_db->get_installation($id);
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {
	
		return $this->installations_db->check_unique($fieldname,$value);
		
	}
	
	public function add_new_item($cat_id = NULL, $item_name = NULL,$item_cost = NULL) {
		
		$ret = $this->installations_db->add_item($cat_id, $item_name,$item_cost);
		
		
		
	}
	
	public function installation_add($inserted) {
		$ret = $this->installations_db->add_installation($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen installáció felvitel!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres installáció felvitel!";
			return true;
		}
	}
	
	public function installation_update($inserted) {
		$ret = $this->installations_db->update_installation($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen installáció módosítás!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres installáció módosítás!";
			return true;
		}
		
	}
	
	private function cell_edit($row) {
	
		ob_start();
		
		?>
			<a id="installation_edit_<?php echo $row['ID'];?>" class="installation-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editinstallation&id=<?php echo $row['ID'];?>">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
			</a>
		<?php
		
		$content = ob_get_contents();
		ob_end_clean();
				
		return $content;
	
	}
	
	public function installations_table($filter = NULL) {
	
		//$filter = array("UserType >= ".$this->user_level($_SESSION["HDT_uid"]));
		
		$result = $this->installations_db->list_installations($filter);
		
		if($result == NULL) {
			echo "List user error";
			return;
		}
		
		?>
		<div class="body">
			<table class="table table-bordered table-striped table-hover dataTable installations-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<td>Megnevezés</td>
						<td>Tételek</td>
						<!--<td>Díjszabás</td>-->
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>Megnevezés</td>
						<td>Tételek</td>
						<!--<td>Díjszabás</td>-->
					</tr>
				</tfoot>
				<tbody>
				<?php
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $this->cell_edit($row);?></td>
						<td><?php echo $row['CategoryName'];?></td>
						<td><?php echo $this->installation_items($row);?></td>
						<!--<td><?php echo $row['Cost'];?> Ft </td>-->
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div><!-- #body -->
		<?php
		
		return;
		
	}
	
	public function get_installation_item($id = null) {
		return $this->installations_db->get_item($id);
	}
	
	public function installation_items($row = NULL) {
		
		ob_start();
		
		$items = $this->installations_db->get_items($row['ID']);
		
		?><ul><?php
		foreach($items as $item) {
			?><!--<li><strong><?php echo $item['InstallationName'];?></strong> - <?php echo $item['Cost'];?> Ft</li>--><?php
			?><li><strong><?php echo $item['InstallationName'];?></strong> ( <?php echo $item['Req_Time'];?> óra)</li><?php
		}
		?></ul><?php
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	/*public function installations_select($select_id = NULL,$id = NULL) {
	
		ob_start();
		
		$installation_cats = $this->installations_db->list_installations();
		?><select id="installations-select-<?php echo $select_id;?>" name="mandate-installations[<?php echo $select_id;?>]" class="form-control select2_single"><?php
		?><option value="">Válasszon!</option><?php
		while($row = mysql_fetch_assoc($installation_cats)) {
			?><optgroup label="<?php echo $row['CategoryName']?>"><?php
			$items = $this->installations_db->get_items($row['ID']);
			foreach($items as $item) {
				if($item['ID'] == $id) {
					$selected = "selected";
				} else {
					$selected = '';
				}
				?><option <?php echo $selected;?> value="<?php echo $item['ID'];?>"><?php echo $item['InstallationName'];?></option><?php
			}
			?></optgroup><?php
		}
		?></select><?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
		
	}*/
	
	public function client_installations_cats_select($client_id = NULL) {
		
		ob_start();
		
		$clientoptions = $this->clientoptions->list_subclientoptions($client_id);
		
		?>
		<select id="client-installations-cats-select" name="client-installations-cats-select" class="form-control ch_mark">
			<option value="">Válasszon terméket!</option>
		<?php
		foreach($clientoptions as $clientoption) {

			$item = $this->installations_db->get_item($clientoption['installation_id']);

			$item_cat = $this->load_installation($item['CatID']);
			
			if(!isset($p_id) || $p_id != $item_cat['ID']) {
				?>
				<option value="<?php echo $item_cat['ID'];?>"><?php echo $item_cat['CategoryName'];?></option>
				<?php
			}
			$p_id = $item_cat['ID'];
		}
		?>
		</select>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function clients_installations_select($client_id = NULL,$cat_id = NULL, $costs = false) {
		//var_export($costs);
		//if($costs !== false){
			if(is_array($costs)){
			foreach($costs as $key=>$row){
				if($row == "") {
					unset($costs[$key]);
				}
			}
		}
		
		
		
		ob_start();
		//var_dump($costs);
		$clientoptions = $this->clientoptions->list_subclientoptions($client_id);
		//var_dump($clientoptions);
		
		?>
		<p>&nbsp;</p>
		<!-- <select id="client-installations-select" name="client-installations-select" class="form-control ch_mark">
					<option value="">Válasszon installációt!</option> -->
		<input placeholder="A termék értéke" type="text" name="installation-product-value" value="" class="form-control"/>
		<!--<input placeholder="A termék értéke" type="text" name="installation-product-value[<?php //echo $cat_id; ?>]" value="" class="form-control"/>-->
		<p>&nbsp;</p>
		<p>Darabszám:</p>
		<input type="number" name="installation-product-piece" value="1" min="1" max="5" step="1" class="form-control"/>
		<!--<input type="number" name="installation-product-piece[<?php //echo $cat_id; ?>]" value="1" min="1" max="5" step="1" class="form-control"/>-->
		<p>&nbsp;</p>
		<p>Választható installációk</p>
		<?php
		foreach($clientoptions as $clientoption) {
			if(!isset($p_id) || $p_id != $clientoption['installation_id']) {
				$item = $this->installations_db->get_item($clientoption['installation_id']);
		
				//$item_cat = $this->load_installation($item['CatID']);
				$checked = "";
				if($cat_id == $item['CatID']){
					?>
					<!-- <option value="<?php echo $item['ID'];?>"><?php echo $item['InstallationName'];?></option> -->
					
					<div class="checkbox">
					<label>
						<input id="mandate-installation-<?php echo $item['ID']?>" name="mandate-installations[<?php echo $item['ID']?>]" class="client-installation-checkbox ui-checkbox" type="checkbox" data-id="<?php echo $item['ID']?>" <?php echo $checked;?> />
						<label class="mandate-installation" for="mandate-installation-<?php echo $item['ID']?>"><?php echo $item['InstallationName']?></label>
					</label>
					</div>
					<?php
					if(isset($costs[$item['ID']])){
						$cost = $costs[$item['ID']];
						$cost_display = 'inline-block';
					} else {
						$cost = 0;
						$cost_display = 'none';
					}
					if($this->parent->get_component('user')->is_super($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_admin($_SESSION['HDT_uid'])):
					?>
					<div id="installation-cost-input-container_<?php echo $item['ID']?>" style="display:<?php echo $cost_display;?>;vertical-align:middle;width:100%;"><input data-id="<?php echo $item['ID']?>" placeholder="Ár" type="number" name="mandate-installations-cost_<?php echo $item['ID']?>" class="form-control installation-cost-input" style="max-width:240px;display:inline-block;float:left;" value="<?php echo $cost;?>" /><label style="margin-top:8px;margin-left:10px;vertical-align:middle;">Ft</label></div>
					<?php
					else:
					?>
					<div id="installation-cost-input-container_<?php echo $item['ID']?>" style="display:<?php echo $cost_display;?>;vertical-align:middle;width:100%;">
					<input data-id="<?php echo $item['ID']?>" placeholder="Ár" type="hidden" name="mandate-installations-cost_<?php echo $item['ID']?>" class="form-control installation-cost-input" style="max-width:240px;display:inline-block;float:left;" value="<?php echo $cost;?>" />
					<p>Ár: <span id="cost_label_<?php echo $item['ID']?>"><?php echo $cost;?></span> Ft</p>
					</div>
					<?php
					endif;
				}
			}
			$p_id = $clientoption['installation_id'];
		}
		?>
		<!-- </select> -->
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function installations_select($partner_id = NULL, $mandate_installations = NULL) {

		ob_start();

		/*$partner = $this->parcel_db->get_partner($partner_id);

		$client = $this->parcel_db->get_client($partner['partner_group_id']);
		$clientoptions = $this->clientoptions->list_clientoptions($client['partner_group_id']);*/
		
		$clientoptions = $this->clientoptions->list_subclientoptions($partner_id);
		
		//var_dump($partner_id);
		
		if($mandate_installations != NULL ) {
			$mandate_installations_array = unserialize($mandate_installations);
		} else {
			$mandate_installations_array = array();
		}
		
		foreach($clientoptions as $clientoption) {
			if(!isset($p_id) || $p_id != $clientoption['installation_id']) {
				$item = $this->installations_db->get_item($clientoption['installation_id']);
				?>
				<?php
				if(in_array((int)$item['ID'],$mandate_installations_array)){
					$checked = " checked";
				} else {
					$checked = "";
				}
				?>
				<div class="checkbox">
					<label>
						<input id="mandate-installation-<?php echo $item['ID']?>" name="mandate-installations[<?php echo $item['ID']?>]" class="client-installation-checkbox ui-checkbox" type="checkbox" data-id="<?php echo $item['ID']?>" <?php echo $checked;?> />
						<label class="mandate-installation" for="mandate-installation-<?php echo $item['ID']?>"><?php echo $item['InstallationName']?></label>
					</label>
				</div>
				<?php
			}
			$p_id = $clientoption['installation_id'];
		}
		
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
		
	}
	
	public function installations_items_fields($post = NULL) {

		if($post == NULL || !isset($post['catid'])) {
			$ret['status'] = 'error';
			return $ret;
		}
		
		$items = $this->installations_db->get_items($post['catid']);
		
		$content = '<div class="form-group form-float">
						<div class="col-md-3 col-sm-3 col-xs-12"></div>
						<div class="col-md-3 col-sm-3 col-xs-12">Megnevezés</div>
						'+
						//'<div class="col-md-3 col-sm-3 col-xs-12">Díjazás</div>'
						+'
				   </div>';
		
		$i = 1;
		foreach($items as $item) {

			$content .= '<div id="item-row-'.$i.'" class="form-group form-float">
				<div class="col-md-3 col-sm-3 col-xs-12"></div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<input placeholder="Megnevezés" type="text" class="form-control item-name" id="installation-cat-name-'.$i.'" name="installation-cat-item-name['.$i.']" value="'.$item['InstallationName'].'" required />
					<input type="hidden" name="installation-cat-item-id['.$i.']" value="'.$item['ID'].'" />
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">';
			$content .= '<select name="installation-cat-item-req_time['.$i.']" class="form-control">';
			if($item['Req_Time'] == NULL || $item['Req_Time'] == 0) {
				$content .= '<option selected value="0">Válasszon</option>';
			} else {
				$content .= '<option value="0">Válasszon</option>';
			}
			if($item['Req_Time'] == 1) {
				$content .= '<option selected value="1">1 óra</option>';
			} else {
				$content .= '<option value="1">1 óra</option>';
			}			
			if($item['Req_Time'] == 2) {
				$content .= '<option selected value="2">2 óra</option>';
			} else {
				$content .= '<option value="2">2 óra</option>';
			}		
			if($item['Req_Time'] == 4) {
				$content .= '<option selected value="4">4 óra</option>';
			} else {
				$content .= '<option value="4">4 óra</option>';
			}		
			if($item['Req_Time'] == 8) {
				$content .= '<option selected value="8">Teljes munkanap (8 óra)</option>';
			} else {
				$content .= '<option value="8">Teljes munkanap (8 óra)</option>';
			}	
						
			$content .=	'</select>
			 </div>
				<!--<div class="col-md-3 col-sm-3 col-xs-12">
					<input placeholder="Díjazás" type="text" class="form-control item-cost" id="installation-cat-cost-'.$i.'" name="installation-cat-item-cost['.$i++.']" value="'.$item['Cost'].'" required />
				</div>-->
			</div>';

		}

		$ret['status'] = 'true';
		$ret['content'] = $content;
		$ret['count'] = $i;
		
		return $ret;
		
	}
	
	public function installations_list() {
	
		$ret = array();
		
		
		
		return $ret;
		
	}
	
	public function installations_cat_select($subclient_id = null) {
		
		$subclient = $this->parent->get_component('subclients')->load_subclient($subclient_id);
		
		return;
		
	}
	
	public function installations_to_master($master_installations = NULL) {
		
		$installations_cats_array = array();
		
		$installation_cats = $this->installations_db->list_installations();
		
		while($row = mysql_fetch_assoc($installation_cats)){
			$installations_cats_array[] = $row;
		}
		
		//var_dump($installations_cats_array);
		
		foreach($installations_cats_array as $cat) {
			?>
			<div class="form-group form-float">
			<?php
			?>
			<label class="control-label col-md-3 col-sm-3 col-xs-12" for="installation-cat-name"><?php echo $cat['CategoryName'];?></label>
			<div class="master-options-container" style="display:inline-block;">
			<?php
			$items = $this->installations_db->get_items($cat['ID']);
			$checked = "";
			foreach($items as $item) {
				if(is_array($master_installations) && in_array($item['ID'],$master_installations)) {
					$checked = " checked";
				} else {
					$checked = "";
				}
			?>
			<div class="checkbox-row">
				<div class="checkbox">
					<label class="">
						<div class="icheckbox_flat-green client-installation-icheckbox checked" style="position: relative;">
							<input class="flat client-installation-item-checkbox" data-id="<?php echo $item['ID']?>" name="master-installation[<?php echo $item['ID']?>]" <?php echo $checked;?> style="position: absolute; opacity: 0;" type="checkbox">
							<ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins>
						</div>
					</label>
				</div>
				<span class="name-label"><?php echo $item['InstallationName']?></span>
			</div>
			<?php
			}
			?>
			</div>
			</div>
			<?php
		}
		
		return;
	}
	
	public function load_installations_checkboxes($client_id = NULL,$subclient = false) {
	
		if($client_id == NULL) {
			return NULL;
		}
		
		$installations_cats_array = array();
		
		$installation_cats = $this->installations_db->list_installations();
		
		while($row = mysql_fetch_assoc($installation_cats)){
			$installations_cats_array[] = $row;
		}
		
		// Check empty installations
		
		if($subclient == true){
		
			$empty = true;
			
			foreach($installations_cats_array as $cat) {
				$items = $this->installations_db->get_items($cat['ID']);
				
				$p_client_options = $this->clientoptions->list_subclientoptions($client_id);
				
				foreach($items as $item) {
					$client_options  = array();
						
					foreach($p_client_options as $row) {
						if($row['installation_id'] == $item['ID'] ) {
							$client_options[] = $row;
						}
					}
					if(count($client_options) > 0) {
						$empty = false;
					}
					
				}
				
			}
		
		}
		
		foreach($installations_cats_array as $cat) {
			?>
			<div class="form-group form-float">
			<?php
			//var_dump($client_id);
			
			$items = $this->installations_db->get_items($cat['ID']);
						
			if($subclient == true) {
				$p_client_options = $this->clientoptions->list_subclientoptions($client_id);
				
				/*$empty = true;
				
				foreach($items as $item) {
					$client_options  = array();
						
					foreach($p_client_options as $row) {
						if($row['installation_id'] == $item['ID'] ) {
							$client_options[] = $row;
						}
					}
					if(count($client_options) > 0) {
						$empty = false;
					}
					
				}*/
				var_dump($empty);
				if(isset($empty) && $empty === true) {
					$subclient = $this->parent->get_component('subclients')->load_subclient($client_id);
								
					$p_client_options = $this->clientoptions->list_clientoptions($subclient['ClientID']);
				}
				
				//var_export($p_client_options);
				
			} else {
				$p_client_options = $this->clientoptions->list_clientoptions($client_id);
			}
			?>
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="installation-cat-name"><?php echo $cat['CategoryName'];?></label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<?php
					
					//$items = $this->installations_db->get_items($cat['ID']);
					
					foreach($items as $item) {
					
						$client_options  = array();
						
						//$empty = false;
						
						foreach($p_client_options as $row) {
							if($row['installation_id'] == $item['ID'] ) {
								$client_options[] = $row;
							}
						}
						//var_export(count($client_options));
						/*if(count($client_options) == 0) {
							if($subclient == true) {
								
								$subclient = $this->parent->get_component('subclients')->load_subclient($client_id);
								
								$p_client_options = $this->clientoptions->list_clientoptions($subclient['ClientID']);
								//var_export($p_client_options);
								foreach($p_client_options as $row) {
									if($row['installation_id'] == $item['ID'] ) {
										$client_options[] = $row;
									}
								}
								
							}
						}*/
						
						//if(isset($client_options[0]) && $client_options[0]['installation_id'] == $item['ID']) {
						if(count($client_options)){
							$checked = " checked";
							$display = "display:inline-block;"; 
						} else {
							$checked = "";
							$display = "";
						}
						
						if(isset($client_options[0]) && $client_options[0]['percent'] == 1) {
							$percent_checked = " checked";
						} else {
							$percent_checked = "";
						}
						//var_dump($item);
						?>
						<!--<div class="checkbox-row">
							<div class="checkbox">
								<label class="">
									<div class="icheckbox_flat-green client-installation-icheckbox checked" style="position: relative;">
										<input class="flat client-installation-item-checkbox" data-id="<?php echo $item['ID']?>" name="client-installation[<?php echo $item['ID']?>]" <?php echo $checked;?> style="position: absolute; opacity: 0;" type="checkbox">
											<ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins>
									
								</label>
							</div>
							<span class="name-label"><?php echo $item['InstallationName']?></span>
						</div></div>-->
						<div class="checkbox">
							<label>
								<input id="client-installation-<?php echo $item['ID']?>" name="client-installation[<?php echo $item['ID']?>]" class="client-installation-checkbox ui-checkbox" type="checkbox" data-id="<?php echo $item['ID']?>" <?php echo $checked;?> />
								<label class="client-installation" for="client-installation-<?php echo $item['ID']?>"><?php echo $item['InstallationName']?></label>
							</label>
						</div>
						
						<div class="options-container" id="options-container-<?php echo $item['ID']?>" style="<?php echo $display;?>">
						<span>
							<!--<label class="">
								<div class="icheckbox_flat-green checked" style="position: relative;">
									<input class="flat client-percent-checkbox" data-id="<?php echo $item['ID']?>" name="client-percent[<?php echo $item['ID']?>]" <?php echo $percent_checked;?> style="position: absolute; opacity: 0;" type="checkbox">
									<ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins>
								</div>
							</label>
							<span class="percent-label">Százalék</span>-->
							<div class="checkbox">
								<label>
									<input id="client-percent-<?php echo $item['ID']?>" name="client-percent[<?php echo $item['ID']?>]" class=" client-percent-checkbox ui-checkbox" type="checkbox" data-id="<?php echo $item['ID']?>" <?php echo $percent_checked;?> />
									<label class="client-installation" for="client-percent-<?php echo $item['ID']?>">Százalék</label>
								</label>
							</div>
						</span>
						<?php
						$result = $this->mandates_options_db->list_mandates_options();
						//$options = array();
						//var_export(count($client_options));
						$i = 0;
						while($row = mysql_fetch_assoc($result)) {
							$value = "";
							if(isset($client_options[$i]) && $client_options[$i]['installation_id'] == $item['ID'] && $client_options[$i]['mandates_option_id'] == $row['ID']) {
								$value = $client_options[$i++]['value'];
							} else {
								$value = "";
							}
							?><span><?php echo $row['OptionName'];?>&nbsp;<input type="text" name="option[<?php echo $item['ID'];?>][<?php echo $row['ID'];?>]" value="<?php echo $value;?>" /></span><?php
						}
						?>
						</div>
						<?php
					}
					
					?>
				</div>
			</div>
			<?php
		}
		
	}
	
	public function ws_installations($post = NULL) {
		
		$clientoptions = $this->clientoptions->list_clientoptions($post['partner_group_id']);
		
		$result_array = array();
		
		foreach($clientoptions as $option) {
			$row = $this->installations_db->get_item($option['installation_id']);
			//var_dump($result_array);echo '<br /><br />';
			if(!isset($p_id) || $row['ID'] != $p_id) {
				$result_array[] = array('installation_id'=>$row['ID'],'installation_name'=>$row['InstallationName']);
				$p_id = $row['ID'];
			}
		}
		
		if($post['type'] == 'json') {
		
			return json_encode($result_array,JSON_UNESCAPED_UNICODE);
			
		} elseif($post['type'] == 'html') {
			ob_start();
			?>
			<div class="mandate-installations-container">
			<?php
			foreach($result_array as $row) {
			?>
			<div class="mandate-installation-row">
			<input type="checkbox" name="mandate-installation[<?php echo $row['installation_id']?>]" />&nbsp<?php echo $row['installation_name']?>
			</div>
			<?php
			}
			?>
			</div>
			<?php
			$content = ob_get_contents();
			ob_end_clean();
			
			return $content;
		
		} elseif($post['type'] == 'xml') {
			ob_start();
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			?>
			<installations>
			<?php
			foreach($result_array as $row) {
			?>
			<installation>
				<installation_id><?php echo $row['installation_id']?></installation_id>
				<installation_name><?php echo $row['installation_name']?></installation_name>
			</installation>
			<?php
			}
			?>
			</installations>
			<?php
			$content = ob_get_contents();
			ob_end_clean();
			
			return $content;
		
		}
		
				
		ob_start();
		?>
		<pre>
		<?php
		var_dump($result_array);
		?>
		</pre>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	
	}
	
}
?>
