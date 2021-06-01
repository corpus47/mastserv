<?php
require_once('DbEngines/subclients.db.php');

class subclients {
	
	private $subclients_db;
	
	private $parent;
	
	private $rules = array(
							SUPER_USER,
							ADMIN_USER,
							CLIENT_ADMIN,
							);
	
	public function __construct($parent = NULL) {
	
		$this->parent = $parent;
	
		$this->subclients_db = new SubclientsDB();
	
	}
	
	public function check_rule() {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);

		return in_array($user['UserType'],$this->rules);
	}
	
	public function DrawMenu() {
		ob_start();
		if($this->check_rule()):
		?>
			<li class="<?php echo $this->parent->action == 'addsubclient' ? 'active' : '';?><?php echo $this->parent->action == 'editsubclient' ? 'active' : '';?><?php echo $this->parent->action == 'listsubclients' ? 'active' : '';?>">
				<a href="<?php echo $this->parent->create_url('listsubclients');?>"><i class="fa fa-users"></i> Almegbízók <span class="fa fa-chevron-down"></a>
				<?php
				$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);
				?>
				<?php if($user['UserType'] <= ADMIN_USER):?>
				<ul class="nav child_menu">
					<li class="<?php echo $this->parent->action == 'addsubclient' ? 'current-page' : '';?>">
						<a href="<?php echo $this->parent->create_url('addsubclient');?>">Almegbízó hozzáadása</a>
					</li>
					<li class="<?php echo $this->parent->action == 'listsubclients' ? 'active' : '';?>">
						<a href="<?php echo $this->parent->create_url('listsubclients');?>">Almegbízók</a>
					</li>
				</ul>
				<?php endif;?>
			</li>
		<?php
		endif;
			
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function delete_subclients($client_id = NULL) {
		
		if($client_id != NULL) {
		
			$filter = array("ClientID = " . $client_id);
			$result = $this->subclients_db->list_subclients($filter);
			
			while($row = mysql_fetch_assoc($result)) {
				//var_dump($row);
				$row['id'] = $row['ID'];
				$this->subclient_delete($row);
			}
		
		}
		exit;
		return;
	}
	
	public function subclient_add($inserted = NULL,$simple = false) {
	
		$ret = $this->subclients_db->add_subclient($inserted);
		
		if($simple == false){
		
			if($ret == false) {
				$_SESSION['HDT_error_message'] = "Sikertelen almegbízó felvitel!";
				return false;
			} else {
				$_SESSION['HDT_ok_message'] = "Sikeres almegbízó felvitel!";
				return true;
			}
		} else {
			
			return $ret;
			
		}
	
	}
	
	public function subclient_update($inserted) {
		$ret = $this->subclients_db->update_subclient($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen almegbízó módosítás!";
			return false;
		} else {
			//var_dump($inserted);exit;
			$this->parent->get_component('clientoptions')->subclientoptions_update($inserted);
			$_SESSION['HDT_ok_message'] = "Sikeres almegbízó módosítás!";
			$this->unlock($inserted['id']);
			return true;
		}
	
	}
	
	public function subclient_delete($inserted = NULL) {
	
		$ret = $this->subclients_db->delete_subclient($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen almegbízó törlés!";
			return false;
		} else {
			$_SESSION['HDT_ok_message'] = "Az almegbízó törölve!";
			return true;
		}
	
	}
	
	public function subclient_users($subclient_id = null, $type = null) {
		
		ob_start();
		
		$subclient = $this->load_subclient($subclient_id);
		
		$subclient_users = unserialize($subclient['Users']);
		
		$client = $this->parent->get_component('clients')->load_client($subclient['ClientID']);
		
		$owner = $this->parent->get_component('user')->load_user($client['Owner']);
		
		//var_dump($owner);
		
		if($this->parent->get_component('user')->is_admin($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_super($_SESSION['HDT_uid'])) {
			
			$filter = array(
							"Active = 1",
							);
			
		} else {
		
			$filter = array(
							"Active = 1",
							"Partner_ID = " .$owner['ID']
							);
		}
		
		switch($type){
			/*case 'admin':
				$filter[] = "UserType = " . SUBCLIENT_ADMIN;
				break;*/
			case 'admin':
				$filter[] = "UserType = " . SUBCLIENT_ADMIN;
				break;
			case 'user':
				$filter[] = "UserType = " . SUBCLIENT_USER;
				break;
			case 'import':
				$filter[] = "UserType = " . IMPORT_USER;
				break;
			case 'export':
				$filter[] = "UserType = " . EXPORT_USER;
				break;
			default:
				break;
		}
		//var_dump($filter);
		$users = $this->parent->get_component('user')->list_users($filter);
		//var_dump($users);
		foreach($users as $row) {
			if(isset($subclient_users[$type]) && in_array($row['ID'],$subclient_users[$type])) {
				$checked = 'checked';
			} else {
				$checked = '';
			}
			?>
			<div class="checkbox">
				<label>
					<input id="subclient-<?php echo $type;?>-user-<?php echo $row['ID']?>" name="subclient-<?php echo $type;?>-user[<?php echo $row['ID']?>]" class="ui-checkbox" <?php echo $checked;?> type="checkbox">
					<label class="subclient-<?php echo $type;?>-user" for="subclient-<?php echo $type;?>-user-<?php echo $row['ID']?>"><?php echo $row["FullName"];?></label>
				</label>
			</div>
			<?php
		}
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function load_subclient($id = NULL) {
		return $this->subclients_db->get_subclient($id);
	}
	
	private function action_cell($row) {
		?>
			<div class="inner-cell">
			<a id="subclient_edit_<?php echo $row['ID'];?>" class="subclient-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editsubclient&id=<?php echo $row['ID'];?>">
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
			<a id="subclient_delete_<?php echo $row['ID'];?>" class="subclient-delete-link" data-id="<?php echo $row['ID'];?>" href="javascript:void(0);" style="display:<?php echo $display;?>;">
				<span class="glyphicon glyphicon-remove" aria-hidden="true" style="color:red;"></span>
			</a>
			<div style="clear:both;"></div>
			<?php
			?></div><?php
	}
	
	private function cell_name($row) {
		?>
			<p><strong>Név : </strong><?php echo $row['Name'];?></p>
			<p><strong>Előtag : </strong><?php echo $row['Prefix'];?></p>
			<p><strong>Cím : </strong><?php echo $row['Zipcode'];?> <?php echo $row['City'];?> <?php echo $row['Address'];?></p>
			<p><strong>Telefon : </strong><?php echo $row['Telephone'];?></p>
			<p><strong>Email : </strong><a href="mailto:<?php echo $row['Email'];?>"><?php echo $row['Email'];?></ahref><a></p>
			<p><strong>Felvive : </strong><?php echo date("Y.m.d H:i:s",strtotime($row['DateOfAdd']));?></p>
			<p><strong>Megjegyzés : </strong><br /><?php echo $row['Comment'];?></p>
			<?php
	}
	
	private function active_cell($row) {
		if($row['Active'] == 1) {
			$checked = 'checked';
		} else {
			$checked = '';
		}
		?>
			<div class="checkbox">
				<input data-id="<?php echo $row['ID'];?>" class="ui-checkbox" id="subclient-active-<?php echo $row['ID']?>" name="subclient-active-<?php echo $row['ID']?>" <?php echo $checked;?> type="checkbox" />
				<label class="subclient-active" for="subclient-active-<?php echo $row['ID']?>"></label>
			</div>
			<?php
		}
	
	public function subclients_table($filter = NULL) {
	
		if($this->parent->get_component('user')->is_super($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_admin($_SESSION['HDT_uid'])) {
			$filter = null;
			//$filter = array("ORDER BY ID DESC");
		} else {
			// Kliens kikeresése
			$client = $this->parent->get_component('clients')->check_owner($_SESSION["HDT_uid"]);
			//var_dump($client);
			$filter = array("ClientID = " . $client['ID']);
		}
	
		$result = $this->subclients_db->list_subclients($filter);

		if($result == NULL) {
			echo "Az almegbízó lista üres";
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
				<table data-filter="<?php echo $filter_str;?>" class="table table-bordered table-striped table-hover dataTable subclients-table">
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
			<?php
			while($row = mysql_fetch_assoc($result)) {
				?><tr><?php
				?><td data-sort="<?php echo $row['ID']?>" class="middle-align"><?php $this->action_cell($row);?></td><?php
				?><td data-sort="<?php echo $row['Name'];?>"><?php echo $this->cell_name($row);?></td><?php
				?><td><?php echo $row['Prefix']?></td><?php
				?><td><?php echo $row['Comment']?></td><?php
				?><td><?php $this->active_cell($row);?></td><?php
				?><td><?php echo date("Y.m.d H:i:s",strtotime($row['DateOfAdd']));?></td><?php
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
	
	public function check_subclient_user($user = null,$filter = null) {
		
		
		$res = $this->subclients_db->list_subclients($filter);
		
		$level = '';
		
		switch($user['UserType']) {
			case SUBCLIENT_ADMIN:
				$level = 'admin';
				break;
			case SUBCLIENT_USER:
				$level = 'user';
				break;
			case IMPORT_USER:
				$level = 'import';
				break;
			case EXPORT_USER:
				$level = 'export';
				break;
			default:
				$level = false;
				break;
		}
		
		if($level == false) {
			return $level;
		}
		
		$subclients = array();
		
		while($row = mysql_fetch_assoc($res)) {

			$users = unserialize($row['Users']);
			//var_dump($users);
			if(isset($users[$level]) && in_array($user['ID'],$users[$level])) {
				$subclients[] = $row;
			}
		}
		
		return $subclients;
		
	}
	
	public function filter_subclient_select($filter = NULL,$options = false) {
		
		//var_dump($filter);
		
		$result = $this->subclients_db->list_subclients($filter);
		
		ob_start();
		/*if($options === false):
		?>
		<select id="" name="mandate-hdt-partner-id" class="form-control select2_single" />
		<?php
		endif;*/
		while($partner = mysql_fetch_assoc($result)) {
			?><option value="<?php echo $partner['ID']?>"><?php echo $partner['Name'];?></option><?php
		}
		/*if($options === false):
		?>
		</select>
		<?php
		endif;*/
		$content = ob_get_contents();
		ob_end_clean();
				
		return $content;
		
	}
	
	public function subclients_select($client_id = null,$act_subclient = null) {
		
		$subclient_list = $this->subclients_list($client_id);
		
		//var_dump($client_list);
		ob_start();
		foreach($subclient_list as $subclient) {
			?><option value="<?php echo $subclient['ID']?>"><?php echo $subclient['Name']?></option><?php
		}
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function subclients_list($client_id = NULL, $all = false){
		
		if($all == true) {
			$filter = array("Active = 1");
		} else {
			$filter = array(
							"ClientID = " . $client_id,
							"Active = 1"
							);
		}
		
		$result = $this->subclients_db->list_subclients($filter);
		
		$list = array();
		
		while($row = mysql_fetch_assoc($result)) {
			$list[] = $row;
		}
		return $list;
	}
	
	public function get_subclients_for_user($user_id = null) {
	
		$user = $this->parent->get_component('user')->load_user($user_id);
		
		$ret = array();
		
		$subclients = $this->subclients_list(null,true);
		
		if($user['UserType'] == SUBCLIENT_ADMIN || $user['UserType'] == SUBCLIENT_USER) {
			
			foreach($subclients as $row) {
				$subc_user = unserialize($row['Users']);
				foreach($subc_user as $prow){
					if(in_array($user['ID'],$prow) == true) {
						$ret[] = $row['ID'];
					}
				}
			}
			
			return $ret;
			
		} elseif($user['UserType'] == CLIENT_ADMIN) {
			
			$filter = array('Partner_ID = ' . $user['ID']);
			
			$users = $this->parent->get_component('user')->list_users($filter);
			
			foreach($users as $user_row) {
				foreach($subclients as $row) {
					$subc_user = unserialize($row['Users']);
					$p_id = null;
					foreach($subc_user as $prow){
						if(in_array($user_row['ID'],$prow) == true && $row['ID'] != $p_id) {
							$ret[] = $row['ID'];
						}
						$p_id = $row['ID'];
					}
				}
			}
			
			return $ret;
		
		}
	
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {
		return $this->subclients_db->check_unique($fieldname,$value);
	}
	
	public function set_subclient_active($id = NULL, $value = NULL) {
		return $this->subclients_db->set_active($id,$value);
	}
	
	public function subclient_lock($id = NULL) {
	
		return $this->subclients_db->lock($id);
	}
	
	public function subclient_unlock($post){
		return $this->unlock($post['id']);
	}
	
	public function unlock($id = NULL) {
		return $this->subclients_db->unlock($id);
	}
	
	public function locked_subclient($id = NULL) {
		return $this->subclients_db->locked($id);
	}
}
?>
