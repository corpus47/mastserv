<?php

require_once('DbEngines/user.db.php');

class user {
	
	private $user_db;
	
	private $parent;
	
	private $rules = array(
							SUPER_USER,
							ADMIN_USER,
							CLIENT_ADMIN,
							);
	
	public function __construct($parent = NULL){
		
		$this->user_db = new UserDB();
		
		$this->parent = $parent;
	}
	
	public function check_rule() {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);

		return in_array($user['UserType'],$this->rules);
	}
	
	public function DrawMenu() {
		ob_start();
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);
		if($this->check_rule()):
		
		?>
		<li class="<?php echo $this->parent->action == 'adduser' ? 'active' : '';?><?php echo $this->parent->action == 'listusers' ? 'active' : '';?><?php echo $this->parent->action == 'edituser' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('listusers');?>"><i class="fa fa-user"></i> Felhasználók <span class="fa fa-chevron-down"></span></a>
			<?php //if($user['UserType'] <= ADMIN_USER):?>
			<ul class="nav child_menu">
				<li class="<?php echo $this->parent->action == 'adduser' ? 'current-page' : '';?>"><a href="<?php echo $this->parent->create_url('adduser');?>">Új felhasználó</a></li>
				<li class="<?php echo $this->parent->action == 'listusers' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('listusers');?>">Felhasználók</a></li>
			</ul>
			<?php //endif;?>
		</li>
		<?php
		endif;
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function load_user($id = NULL) {
		return $this->user_db->get_user($id);
	}
	
	public function is_super($id = NULL) {
		if($id == NULL) {
			return false;
		}
		
		$type = $this->user_level($id);
		// Superuser
		if((int)$type == SUPER_USER) {
			return true;
		} else {
			return false;
		}
		
	}
	
	public function is_admin($id = NULL) {
		// admin
		if($id == NULL) {
			return false;
		}
		
		$type = $this->user_level($id);
		
		if((int)$type == ADMIN_USER) {
			return true;
		} else {
			return false;
		}
	}
	
	public function is_client_admin($id = NULL) {
		// admin
		if($id == NULL) {
			return false;
		}
		
		$type = $this->user_level($id);
		
		if((int)$type == CLIENT_ADMIN) {
			return true;
		} else {
			return false;
		}
	}
	
	public function is_subcon_admin($id = NULL) {
		//admin for subcontactor
		if($id == NULL) {
			return false;
		}
		
		$type = $this->user_level($id);
		
		if((int)$type == SUBCON_ADMIN) {
			return true;
		} else {
			return false;
		}
	}
	
	public function user_level($id = NULL) {
		
		if($id == NULL) {
			return false;
		}
		
		$user = $this->user_db->get_user($id);
		
		return $user['UserType'];
	}
	
	public function user_access($user = NULL) {
	
		$logged_user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);
		
		if($logged_user['UserType'] == SUPER_USER || $logged_user['UserType'] == ADMIN_USER || $user['Partner_ID'] == $logged_user['ID']) {
			return true;
		} else {
			return false;
		}
	}
	
	public function locked_user($id = NULL) {
		return $this->user_db->locked($id);
	}
	
	public function user_lock($id = NULL) {
		
		return $this->user_db->lock($id);
	}
	
	public function unlock($id = NULL) {
		$this->user_db->unlock($id);
	}
	
	public function user_unlock($post){
		return $this->unlock($post['id']);
	}
	
	public function subconid_select($act_subconid = NULL) {
		
		$filter = array("Active = 1 AND UserType = 2");
		
		$result = $this->user_db->list_users($filter);
		
		
		$options = array();
		
		while($row = mysql_fetch_assoc($result)) {
			var_dump($row);
			$options[$row["ID"]] = '<option value="'.$row["ID"].'">'.$row["FullName"].'</option>';
		}
		if(count($options) > 0) {
			?>
			<select id="user-subconid" name="master-subconid" class="form-control ch_mark"><?php
				if($act_subconid != NULL) {
					echo $options[$act_subconid]."\r\n";
					unset($options[$act_subconid]);
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
	
	public function user_level_select($user_level = NULL) {
		
		$level = $this->user_level($_SESSION["HDT_uid"]);

		if(!in_array($level,$this->rules)){
			return "";
		}
		
		$p_user_levels = unserialize(USER_LEVELS);
		
		$user_levels = array();
		
		switch($level){
			
			case CLIENT_ADMIN:

				//$user_levels[SUBCLIENT_ADMIN] = $p_user_levels[SUBCLIENT_ADMIN];
				$user_levels[SUBCLIENT_USER] = $p_user_levels[SUBCLIENT_USER];
				$user_levels[IMPORT_USER] = $p_user_levels[IMPORT_USER];
				$user_levels[EXPORT_USER] = $p_user_levels[EXPORT_USER];
				
				break;
			
			
			case SUBCON_ADMIN:

				/*$user_levels = array(
									2 => 'Alvállalkozó adminisztrátor',
									3 => 'Exportáló felhasználó',
									4 => 'Importáló felhasználó',
									);*/
									
				break;
			
			default:
				for($i = $level; $i < count($p_user_levels);$i++) {
					$user_levels[$i] = $p_user_levels[$i];
				}
				break;
			
		}
		//var_dump($user_levels);
		?>
		<select id="user-level" name="user-level" class="form-control ch_mark">
		<?php
			if($user_level != NULL && isset($user_levels[$user_level])) {
				?><option value="<?php echo $user_level;?>"><?php echo $user_levels[$user_level];?></option><?php
				unset($user_levels[$user_level]);
			}
			foreach($user_levels as $key=>$value) {
				?><option value="<?php echo $key;?>"><?php echo $value;?></option><?php
			}
		?></select><?php
		
		return;
		
	}
	
	private function cell_userinfo($row = NULL) {
		
		/*echo "<p><strong>" . $row["FullName"] . "</strong></p>" . "Tel.:" . $row["Phone"] . "<br />E-mail: <a href=\"mailto:".$row["Email"]."\">" . $row["Email"] . "</a>";*/
		?><p><strong><?php echo $row["FullName"];?></strong></p><?php
		?><p>Tel.: <?php echo $row["Phone"];?></p><?php
		?><p>E-mail: <a href="mailto:<?php echo $row["Email"];?>"><?php echo $row["Email"];?></a></p><?php
		$user_levels = unserialize(USER_LEVELS);
		?><p>Szint: <?php echo $user_levels[$row["UserType"]];?></p><?php
	
	}
	
	private function cell_edit($row) {
	
		ob_start();
		?><div class="inner-cell"><?php
		/*if((int)$row['UserType'] < (int)$this->user_level($_SESSION["HDT_uid"])) {
			?><?php
					
		} else {*/
	
			if($_SESSION["HDT_theme"] == "new") {
			?>
				<a id="user_edit_<?php echo $row['ID'];?>" class="user-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=edituser&id=<?php echo $row['ID'];?>"><i class="material-icons">border_color</i></a>
			<?php 
			} elseif($_SESSION["HDT_theme"] == 'default') { ?>
				<a id="user_edit_<?php echo $row['ID'];?>" class="user-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=edituser&id=<?php echo $row['ID'];?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
			<?php
				
			}
		//}
		if($row['Active'] == 0){
			$display = 'inline-block';
		} else {
			$display = 'none';
		}
		?>
		<div style="clear:both;margin-top:10px;display:inline-block;width:100%;"></div>
		<a id="user_delete_<?php echo $row['ID'];?>" class="user-delete-link" data-id="<?php echo $row['ID'];?>" href="javascript:void(0);" style="display:<?php echo $display;?>;">
			<span class="glyphicon glyphicon-remove" aria-hidden="true" style="color:red;"></span>
		</a>
		<div style="clear:both;"></div>		
		</div><?php		
		$content = ob_get_contents();
		ob_end_clean();
				
		return $content;
	}
	
	private function active_cell($row) {
		if($row['Active'] == 1) {
			$checked = 'checked';
		} else {
			$checked = '';
		}
		?>
		<div class="checkbox">
			<input data-id="<?php echo $row['ID'];?>" class="ui-checkbox" id="user-active-<?php echo $row['ID']?>" name="user-active-<?php echo $row['ID']?>" <?php echo $checked;?> type="checkbox" />
			<label class="user-active" for="user-active-<?php echo $row['ID']?>"></label>
		</div>
		<?php
	}
	
	public function list_users($filter = null) {
		if($filter == null) {
			return $filter;
		}
		$ret = $this->user_db->list_users($filter);
		
		if($ret == false){
			return array();
		}
		
		$users = array();
		
		
		while($row = mysql_fetch_assoc($ret)) {
			$users[] = $row;
		}
		return $users;
	}
	
	public function users_table($filter = NULL) {
		
		if($this->is_super($_SESSION['HDT_uid']) || $this->is_admin($_SESSION['HDT_uid'])) {
			$filter = array("UserType >= ".$this->user_level($_SESSION["HDT_uid"]));
		} else {
			$filter = array("Partner_ID = ".$_SESSION["HDT_uid"]." OR ID = ".$_SESSION["HDT_uid"]);
		}
		
		
		$result = $this->user_db->list_users($filter);
		
		if($result == NULL) {
			echo "List user error";
			return;
		}
		//ob_start();
		?>
		<div class="body">
			<table class="table table-bordered table-striped table-hover dataTable user-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<td>Login</td>
						<td>Felhasználói információk</td>
						<!--<td>Telefon</td>
						<td>E-mail</td>-->
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>Login</td>
						<td>Felhasználói információk</td>
						<!--<td>Telefon</td>
						<td>E-mail</td>-->
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</tfoot>
				<tbody>
		<?php
		while($row = mysql_fetch_assoc($result)) {
			?><tr>
				<td class="middle-align"><?php echo $this->cell_edit($row);?></td>
				<td><strong><?php echo $row['Login'];?></strong></td><?php
				
				?><!--<td><?php echo $row['FullName']; ?></td>--><?php
				?><td><?php echo $this->cell_userinfo($row); ?></td><?php
				?><!--<td><?php echo $row['Phone']; ?></td>--><?php
				?><!--<td><?php echo $row['Email']; ?></td>-->
				<td><?php
				echo $this->active_cell($row);
				/*if($_SESSION["HDT_theme"] == "new") {
					
					?>
					<td><div class="form-group">
					<?php 
					if($row['Active'] == 1){
					?>
					<input type="checkbox" data-id="<?php echo $row['ID']?>" id="user-active_<?php echo $row['ID']?>" name="user-active_<?php echo $row['ID']?>" class="user-active-check filled-in chk-col-light-green" checked="" />
					<?php
					} else {
					?>
					<input type="checkbox" id="user-active_<?php echo $row['ID']?>" name="user-active_<?php echo $row['ID']?>" class="user-active-check filled-in chk-col-light-green" /><label for="user-active_<?php echo $row['ID']?>"></label>
					<?php 
					}
					?></div></td>
					<?php
				
				} elseif($_SESSION["HDT_theme"] == 'default') { 
				
					?>
					<td><div class="checkbox">
					<?php 
					if($row['Active'] == 1){
						?>
						<label class="">
							<div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat user-active-check" id="user-active_<?php echo $row['ID']?>" name="user-active_<?php echo $row['ID']?>" checked="checked" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
						</label>
						<?php
					}else{
						?>
						<label class="">
							<div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat user-active-check" id="user-active_<?php echo $row['ID']?>" name="user-active_<?php echo $row['ID']?>" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
						</label></div></td>
						<?php
					}
					?>
					<?php
				
				}*/
				
				?></td>
				<td><?php echo date("Y.m.d H:i:s",strtotime($row['DateOfAdd'])); ?></td><?php
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
	
	public function users_select($act_userid = NULL) {
		ob_start();
		
		$filter = array("Active = 1");
		
		$result = $this->user_db->list_users($filter);
		
		while($row = mysql_fetch_assoc($result)) {

			if($act_userid == $row['ID']){
				$selected = 'selected';
			} else {
				$selected = '';
			}
			if($_SESSION['HDT_uid'] == $row['ID']){
				?><option value="<?php echo $row['ID']?>" <?php echo $selected;?>>Saját felhasználó</option><?php
			} else {
			?><option value="<?php echo $row['ID']?>" <?php echo $selected;?>><?php echo $row['FullName']." - ".$row['Login'];?></option><?php
			}
		}
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function set_user_active($id = NULL, $value = NULL) {
		return $this->user_db->set_active($id,$value);
	}
	
	public function user_add($inserted) {
		$ret = $this->user_db->add_user($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen felhasználó felvitel!";
			return false;	
		} else {
			// E-mail küldése
			$this->parent->get_component('clients')->set_owner($inserted,true);
			$email = $this->parent->get_component('email')->user_register_email($inserted);
			$_SESSION['HDT_ok_message'] = "Sikeres felhasználó felvitel!";
			return true;
		}
	}
	
	public function user_delete($inserted = NULL) {

		$ret = $this->user_db->delete_user($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen felhasználó törlés!";
			return false;	
		} else {
			$this->parent->get_component('clients')->unset_owner($inserted);
			$_SESSION['HDT_ok_message'] = "A felhasználó törölve!";
			return true;
		}

	}
	
	public function user_update($inserted) {

		$ret = $this->user_db->update_user($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen felhasználó módosítás!";
			return false;	
		} else {
			//var_dump($inserted);exit;
			if($inserted['user-own'] < 0 ){
				$this->parent->get_component('clients')->unset_owner($inserted);
			} else {
				$this->parent->get_component('clients')->set_owner($inserted,false);
			}
			$_SESSION['HDT_ok_message'] = "Sikeres felhasználó módosítás!";
			$this->unlock($inserted['id']);
			return true;
		}
		
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {
		return $this->user_db->check_unique($fieldname,$value);
	}
	
	
	
	public function login($username = NULL, $password = NULL, $servicelogin = NULL) {
		
		/*if($username == "teszt" && $password == "teszt") {
			return true;
		} else {
			return false;
		}*/
		
		if($servicelogin == true) {
			return $this->user_db->login_service($username,$password);
		}
		
		$user = $this->user_db->login_user($username,$password);
		//var_dump($user);exit;
		if($user != false) {
			
			$check = $this->parent->get_component('user_logged')->check_logged($user);
			//var_dump($check);
			if($check != false && is_array($check)) {
				
				$return = true;
				
				foreach($check as $row){
					//$check['LastCheck'] = date("Y.m.d H:i:s",strtotime($check['LastCheck']));
					if($row['LastCheck'] == NULL) {
						continue;
					}			
					$last_check = strtotime($row['LastCheck'])+(60*3);
					
					$expire_time = time();
					
					//var_dump(strtotime($row['LastCheck']));
					
					//var_dump($expire_time);
					//var_dump(date("Y.m.d H:i:s"), 1);
					//var_dump($last_check);
					//var_dump(date("Y.m.d H:i:s", $last_check+(60*3)));
					//$different = $expire_time - $last_check;
					
					//var_dump($different % 60);
					
					if(time() > $last_check){
						$this->parent->get_component('user_logged')->close_logout($row['Uid'],$row['Session']);
					} else {
						$return = false;
					}
					
				}
				if($return == false) {
					$_SESSION['HDT_login_error'] = "Ez a felhasználó már be van jelentkezve!";
					return false;
				}
			}
			
			$this->parent->get_component('user_logged')->add_log($user);
			
			return $user;
		} else {
			$_SESSION['HDT_login_error'] = "Érvénytelen felhasználónév vagy jelszó (master)!";
			return false;
		}
	}
	
	public function userinfo_content() {
		
		ob_start();
		
		$user = $this->load_user($_SESSION['HDT_uid']);
		
		var_dump($user);
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
}
?>
