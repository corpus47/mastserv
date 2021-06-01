<?php

require_once('DbEngines/clients.db.php');

class clients {
	
	private $clients_db;
	
	private $parent;
	
	private $rules = array(
							SUPER_USER,
							ADMIN_USER,
							);
	
	public function __construct($parent = NULL) {
		
		$this->parent = $parent;
		
		$this->clients_db = new ClientsDB($this->parent);
		
	}
	
	public function check_rule() {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);

		return in_array($user['UserType'],$this->rules);
	}
	
	public function DrawMenu() {
		ob_start();
		if($this->check_rule()):
		
		?>
		<li class="<?php echo $this->parent->action == 'addclient' ? 'active' : '';?><?php echo $this->parent->action == 'editclient' ? 'active' : '';?><?php echo $this->parent->action == 'listclients' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('listclients');?>"><i class="fa fa-users"></i> Megbízók <span class="fa fa-chevron-down"></a>
			<ul class="nav child_menu">
				<li class="<?php echo $this->parent->action == 'addclient' ? 'current-page' : '';?>"><a href="<?php echo $this->parent->create_url('addclient');?>">Megbízó hozzáadása</a>
				</li>
				<li class="<?php echo $this->parent->action == 'listclients' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('listclients');?>">Megbízók</a>
				</li>
			</ul>
		</li>
		<?php
		endif;
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function check_owner($user_id = NULL) {
		
		$owner = $this->clients_db->get_owner($user_id);
		return $owner;
	}
	
	public function load_default_client() {
		$filter = array("Default_Client = 1");
		
		$return = array();
		$result = $this->client_list($filter);

		while($row = mysql_fetch_assoc($result)){
			$return[] = $row;
		}
		
		return $return;
	}
	
	public function client_add($inserted = NULL) {

		$ret = $this->clients_db->add_client($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen megbízó felvitel!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres megbízó felvitel!";
			return true;
		}

	}
	
	public function client_delete($inserted = NULL) {

		$ret = $this->clients_db->delete_client($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen megbízó törlés!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "A megbízó törölve!";
			return true;
		}

	}
	
	private function kill_default() {
		$client = $this->load_default_client();

		if(count($client) > 0) {
			$this->clients_db->kill_default($client[0]['ID']);
		}
		return;
	}
	
	public function unset_owner($inserted = NULL) {
		$this->clients_db->unset_owner($inserted);
		return;
	}
	
	public function set_owner($inserted = NULL,$new = false) {
		
		if($inserted['user-level'] == CLIENT_ADMIN && $inserted['user-own'] != "") {
			$this->clients_db->set_owner($inserted,$new);
		}
		return;
	}
	
	public function client_update($inserted) {
		
		if(isset($inserted['client-default']) && $inserted['client-default'] == 'on') {
			//var_dump($inserted);exit;
			$this->kill_default();
		}
		
		$ret = $this->clients_db->update_client($inserted);
		
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen megbízó módosítás!";
			return false;	
		} else {
			//var_dump($inserted);
			$this->parent->get_component('clientoptions')->clientoptions_update($inserted);
			$_SESSION['HDT_ok_message'] = "Sikeres megbízó módosítás!";
			$this->unlock($inserted['id']);
			return true;
		}
		
	}
	
	public function load_client($id = NULL) {
		return $this->clients_db->get_client($id);
	}
	
	
	public function client_list($filter = NULL) {
		return $this->clients_db->list_clients($filter);
	}
	
	private function action_cell($row) {
		?>
		<div class="inner-cell">
		<a id="client_edit_<?php echo $row['ID'];?>" class="client-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editclient&id=<?php echo $row['ID'];?>">
			<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
		</a>
		<?php
		if($row['Active'] == 0){
			$display = 'inline-block';
		} else {
			$display = 'none';
		}
		?>
		<div style="clear:both;margin-top:10px;display:inline-block;width:100%;"></div>
		<a id="client_delete_<?php echo $row['ID'];?>" class="client-delete-link" data-id="<?php echo $row['ID'];?>" href="javascript:void(0);" style="display:<?php echo $display;?>;">
			<span class="glyphicon glyphicon-remove" aria-hidden="true" style="color:red;"></span>
		</a>
		<div style="clear:both;"></div>
		<?php
		?></div><?php
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {
		return $this->clients_db->check_unique($fieldname,$value);
	}
	
	private function active_cell($row) {
		if($row['Active'] == 1) {
			$checked = 'checked';
		} else {
			$checked = '';
		}
		?>
		<div class="checkbox">
			<input data-id="<?php echo $row['ID'];?>" class="ui-checkbox" id="client-active-<?php echo $row['ID']?>" name="client-active-<?php echo $row['ID']?>" <?php echo $checked;?> type="checkbox" />
			<label class="client-active" for="client-active-<?php echo $row['ID']?>"></label>
		</div>
		<?php
	}
	
	private function cell_name($row) {
		?>
		<p><strong>Név : </strong><?php echo $row['Name'];?></p>
		<p><strong>Előtag : </strong><?php echo $row['Prefix'];?></p>
		<p><strong>Felvive : </strong><?php echo date("Y.m.d H:i:s",strtotime($row['DateOfAdd']));?></p>
		<?php
		//var_dump($row['Owner']);
		$owner = $this->parent->get_component('user')->load_user($row['Owner']);
		//var_dump($owner);
		if($owner == false) {
			$owner['FullName'] = "Még nincs kijelölve!";
		}
		?>
		<p><strong>Adminisztrátor : </strong><?php echo $owner['FullName'];?></p>
		<p><strong>Megjegyzés : </strong><br /><?php echo $row['Comment'];?></p>
		<?php
	}
	
	public function clients_table_prebuild($filter = NULL) {
		
		ob_start();
		?>
		<?php
			if($filter != NULL) {
				$filter_input = serialize($filter);
			} else {
				$filter_input = "";
			}
		?>
		<div class="body">
			<input type="hidden" name="filters" value="<?php echo $filter_input;?>" />
			<table class="table table-bordered table-striped table-hover dataTable clients-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<td>Név/Adatok</td>
						<td>Előtag</td>
						<td>Megjegyzés</td>
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</thead>
				</tbody>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>Név/Adatok</td>
						<td>Előtag</td>
						<td>Megjegyzés</td>
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</tfoot>
				<tbody>
			</table>
		</div><!-- body -->
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function clients_table_rows($post) {
		
		$filter = null;
		
		if($post['filter'] != "NULL") {
			$filter = unserialize($post['filters']);
		}
		
		$result = $this->clients_db->list_clients($filter);
		
		if($result == NULL) {
			echo "List subcontactor error";
			return;
		}
		
		$datas = array();
		
		ob_start();
		
		while($row = mysql_fetch_assoc($result)) {
			?><tr><?php
			?><td><?php $this->action_cell($row);?></td><?php
			?><td data-sort="<?php echo $row['Name'];?>"><?php echo $this->cell_name($row);?></td><?php
			?><td><?php echo $row['Prefix']?></td><?php
			?><td><?php echo $row['Comment']?></td><?php
			?><td><?php $this->active_cell($row);?></td><?php
			?><td><?php echo date("Y.m.d H:i:s",strtotime($row['DateOfAdd']));?></td><?php
			?></tr><?php
		}
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	private function cell_subclients($row = NULL) {
		
		$subclients = $this->parent->get_component('subclients')->subclients_list($row['ID']);
		
		//var_dump($subclients);
		
		foreach($subclients as $row){
			?><p><?php echo $row['Name'];?></p><?php
		}
	}
	
	public function clients_table($filter = NULL) {
		
		//var_dump($_POST);
		
		$result = $this->clients_db->list_clients($filter);
		
		if($result == NULL) {
			echo "List subcontactor error";
			return;
		}
		
		ob_start();
		
		?>
		<div class="body">
		<?php
		if($filter != NULL){
			$filter_str = serialize($filter);
		} else {
			$filter_str = "NULL";
		}
		?>
			<table data-filter="<?php echo $filter_str;?>" class="table table-bordered table-striped table-hover dataTable clients-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<td>Név/Adatok</td>
						<!--<td>Előtag</td>-->
						<td>Almegbízók</td>
						<td>Aktív</td>
						<!--<td>Felvitel dátuma</td>-->
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>Név/Adatok</td>
						<!--<td>Előtag</td>-->
						<td>Almegbízók</td>
						<td>Aktív</td>
						<!--<td>Felvitel dátuma</td>-->
					</tr>
				</tfoot>
				<tbody>
		<?php
		while($row = mysql_fetch_assoc($result)) {
			?><tr><?php
			?><td class="middle-align"><?php $this->action_cell($row);?></td><?php
			?><td data-sort="<?php echo $row['Name'];?>"><?php echo $this->cell_name($row);?></td><?php
			/*?><td><?php echo $row['Prefix']?></td><?php*/
			?><td><?php $this->cell_subclients($row);?></td><?php
			?><td><?php $this->active_cell($row);?></td><?php
			/*?><td><?php echo date("Y.m.d H:i:s",strtotime($row['DateOfAdd']));?></td><?php*/
			?></tr><?php
		}
		?>
				</tbody>
			</table>
		</div><!-- body -->
		<?php //endif;?>		
		<?php
		
		$content = ob_get_contents();
		
		ob_end_clean();

		return $content;
		
	}
	
	public function clients_select($act_clientid = NULL) {
	
		$filter = array(
					"Active = 1",
				);
		
		$result = $this->clients_db->list_clients($filter);
	
		$options = array();
		
		ob_start();
		
		while($row = mysql_fetch_assoc($result)) {

			if($act_clientid == $row['ID']){
				$selected = 'selected';
			} else {
				$selected = '';
			}
			?><option value="<?php echo $row['ID']?>" <?php echo $selected;?>><?php echo $row['Name'];?></option><?php
		}
	
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
				
	}
	
	public function set_client_active($id = NULL, $value = NULL) {
		return $this->clients_db->set_active($id,$value);
	}
	
	public function client_lock($id = NULL) {
		
		return $this->clients_db->lock($id);
	}
	
	public function client_unlock($post){
		return $this->unlock($post['id']);
	}
	
	public function unlock($id = NULL) {
		return $this->clients_db->unlock($id);
	}
	
	public function locked_client($id = NULL) {
		return $this->clients_db->locked($id);
	}
}
?>