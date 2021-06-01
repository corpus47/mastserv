<?php

require_once('DbEngines/subcontactor.db.php');

class subcontactor {
	
	private $subcontactor_db;
	
	private $parent;
	
	private $rules = array(
							SUPER_USER,
							ADMIN_USER,
							//SUBCON_ADMIN,
							);
	
	public function __construct($parent){
		
		$this->parent = $parent;
		
		$this->subcontactor_db = new SubcontactorDB();
	}
	
	public function check_rule() {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);

		return in_array($user['UserType'],$this->rules);
	}
	
	public function DrawMenu() {
		ob_start();
		if($this->check_rule()):
		?>
			<li class="<?php echo $this->parent->action == 'addsubcontactor' ? 'active' : '';?><?php echo $this->parent->action == 'listsubcontactors' ? 'active' : '';?><?php echo $this->parent->action == 'editsubcontactor' ? 'active' : '';?>"><a><i class="fa fa-users"></i> Alvállalkozók <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<li class="<?php echo $this->parent->action == 'addsubcontactor' ? 'current-page' : '';?>"><a href="<?php echo $this->parent->create_url('addsubcontactor');?>">Új alvállalkozó</a></li>
								<li class="<?php echo $this->parent->action == 'listsubcontactors' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('listsubcontactors');?>">Alvállalkozók</a></li>
							</ul>
						</li>
		<?php
		endif;
			
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function load_subcontactor($id = NULL) {
		return $this->subcontactor_db->get_subcontactor($id);
	}
	
	public function set_subcontactor_active($id = NULL, $value = NULL) {
		return $this->subcontactor_db->set_active($id,$value);
	}
	
	public function subcontactor_add($inserted = NULL) {

		//return $this->subcontactor_db->add_subcontactor($inserted);
		$ret = $this->subcontactor_db->add_subcontactor($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen alvállalkozó módosítás!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres alvállalkozó módosítás!";
			return true;
		}

	}
	
	public function subcontactor_update($inserted) {
		$ret = $this->subcontactor_db->update_subcontactor($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen alvállalkozó módosítás!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres alvállalkozó módosítás!";
			$this->unlock($inserted['id']);
			return true;
		}
		
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {
		return $this->subcontactor_db->check_unique($fieldname,$value);
	}
	
	public function subcontactors_select($filter = NULL) {
		
		//$filter[] = " Active = 1";
		
		$result = $this->subcontactor_db->list_subcontactors($filter);
		
		ob_start();
		
		while($row = mysql_fetch_assoc($result)) {
			?>
			<option value="<?php echo $row['ID']?>"><?php echo $row['Name'];?></option>
			<?php
		}
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function subconid_select($act_subconid = NULL,$select_html_name = NULL) {
		
		$result = $this->subcontactor_db->list_subcontactors(array("Active = 1"));
		
		$options = array();
		
		if($select_html_name != NULL) {
			$id = $select_html_name;
			$name = $select_html_name;
		} else {
			$id = "user-subconid";
			$name = "master-subconid";
		}
		
		while($row = mysql_fetch_assoc($result)) {

			$options[$row["ID"]] = '<option value="'.$row["ID"].'">'.$row["Name"].'</option>';
		}

		if(count($options) > 0) {
		//if(count($options) > 1) {
			?>
			<!--<select id="user-subconid" name="master-subconid" class="form-control ch_mark">-->
			<select id="<?php echo $id;?>" name="<?php echo $name;?>" class="form-control ch_mark">
				
			<?php
				if($act_subconid != NULL) {
					echo $options[$act_subconid]."\r\n";
					unset($options[$act_subconid]);
				} else {
				?><option value="">Válasszon!</option><?php
				}
				foreach($options as $option) {
					echo $option . "\r\n";
				}
			?>
			</select>
			<?php
		}
		
		return;
		
	}
	
	private function subcontactor_contact_data_cell($row = NULL) {
		?>
		<p><?php echo $row['ContactPerson']?></p>
		<p><?php echo $row['Phone']?></p>
		<p><a href="mailto:<?php echo $row['Email']?>"><?php echo $row['Email']?></a></p>
		<?php
		if($row['AdminID'] != NULL) {
			$admin_user = $this->parent->get_component('user')->load_user($row['AdminID']);
			//var_dump($admin_user);
		?><strong>Admin felhasználó:</strong> <?php echo $admin_user['Login'];?> (<?php echo $admin_user['FullName'];?>)<?php
		} else {
		?>Nincs admin felhasználó kijelölve!<?php	
		}
	}
	
	private function zipcodes_cell($row) {
		
		if(trim($row['Zips']) != "") {
		
			$zipcodes = substr($row['Zips'],0,100) . "..";
			?><?php echo $zipcodes;?><?php
		
		}
		
	}
	
	private function name_cell($row) {
		?><p><strong><?php echo $row['Name'];?></strong></p><?php
		if($row['Address'] != NULL):
		?><p>Telephely:</p><p><?php echo $row['Address'];?></p><?php
		endif;
	}
	
	public function subcontactor_table($filter = NULL) {
		
		$result = $this->subcontactor_db->list_subcontactors($filter);
		
		if($result == NULL) {
			echo "List subcontactor error";
			return;
		}
		//ob_start();
		?>
		<div class="body">
			<table class="table table-bordered table-striped table-hover dataTable subcontactor-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<td>Név</td>
						<td>Kapcsolattartó</td>
						<!--<td>Telefon</td>
						<td>E-mail</td>-->
						<td>Irányítószámok</td>
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>Név</td>
						<td>Kapcsolattartó</td>
						<!--<td>Telefon</td>
						<td>E-mail</td>-->
						<td>Irányítószámok</td>
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</tfoot>
				<tbody>
		<?php
		while($row = mysql_fetch_assoc($result)) {
			?><tr><?php
				if($_SESSION["HDT_theme"] == "default") {
					?><td><a id="subcontactor_edit_<?php echo $row['ID'];?>" class="subcontactor-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editsubcontactor&id=<?php echo $row['ID'];?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
				<?php } elseif($_SESSION["HDT_theme"] == 'new') { ?>
					<td><a id="subcontactor_edit_<?php echo $row['ID'];?>" class="subcontactor-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=edituser&id=<?php echo $row['ID'];?>"><i class="material-icons">border_color</i></a></td>
				<?php 
				} 
				/*?><td><?php echo $row['Name']; ?></td><?php*/
				?><td><?php $this->name_cell($row); ?></td><?php
				/*?><td><?php echo $row['ContactPerson']; ?></td><?php*/
				?><td><?php $this->subcontactor_contact_data_cell($row);?></td><?php
				/*?><td><?php echo $row['Phone']; ?></td><?php
				?><td><?php echo $row['Email']; ?></td><?php*/
				/*?><td><?php echo $row['Zips']; ?></td><?php*/
				?><td><?php $this->zipcodes_cell($row); ?></td><?php
				
				if($_SESSION["HDT_theme"] == "new"){
				
				?>
					<td><div class="form-group">
					<?php 
					if($row['Active'] == 1){
					?>
						<input type="checkbox" data-id="<?php echo $row['ID']?>" id="subcontactor-active_<?php echo $row['ID']?>" name="subcontactor-active_<?php echo $row['ID']?>" class="subcontactor-active-check filled-in chk-col-light-green" checked="" />
					<?php
					} else {
					?>
						<input type="checkbox" data-id="<?php echo $row['ID']?>" id="subcontactor-active_<?php echo $row['ID']?>" name="subcontactor-active_<?php echo $row['ID']?>" class="subcontactor-active-check filled-in chk-col-light-green" /><label for="subcontactor-active_<?php echo $row['ID']?>"></label></div></td>
					<?php
					}
					?>
				<?php
				} elseif($_SESSION["HDT_theme"] == 'default'){
				?>
					<td><div class="checkbox">
					<?php 
					if($row['Active'] == 1){
						?>
						<label class="">
							<div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat user-active-check" id="subcontactor-active_<?php echo $row['ID']?>" name="subcontactor-active_<?php echo $row['ID']?>" checked="checked" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
						</label>
						<?php
					} else {
						?>
						<label class="">
							<div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat user-active-check" id="subcontactor-active_<?php echo $row['ID']?>" name="subcontactor-active_<?php echo $row['ID']?>" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
						</label></div></td>
						<?php
					}?>
				<?php
				}
				?>
				<td><?php echo $row['DateOfAdd']; ?></td><?php
			?></tr><?php
		}
		?>
				</tbody>
			</table>
		</div><!-- body -->				
		<?php
		//$content = ob_get_contents();
		//ob_end_clean();
		
		//return $content;
		return;
	}
	
	public function list_subcontactors($filter = null) {
		$res = $this->subcontactor_db->list_subcontactors($filter);
		
		$ret = array();
		
		while($row = mysql_fetch_assoc($res)){
			$ret[] = $row;
		}
		
		return $ret;
		
	}
	
	public function login($username = NULL, $password = NULL) {
		if($username == "teszt" && $password == "teszt") {
			return true;
		} else {
			return false;
		}
	}
	
	public function subcontactor_lock($id = NULL) {
		
		return $this->subcontactor_db->lock($id);
	}
	
	public function unlock($id = NULL) {
		$this->subcontactor_db->unlock($id);
	}
	
	public function locked_subcontactor($id = NULL) {
		return $this->subcontactor_db->locked($id);
	}
	
}
?>
