<?php

require_once('DbEngines/master.db.php');

require_once('DbEngines/subcontactor.db.php');

require_once('DbEngines/installations.db.php');

require_once('statuses.component.php');

class master {
	
	private $master_db;
	
	private $subcontactor_db;
	
	private $installations_db;
	
	private $statuses;
	
	private $parent;
	
	private $rules = array(
				SUPER_USER,
				ADMIN_USER,
				SUBCON_ADMIN,
			);
	
	public function __construct($parent = null){
		
		$this->parent = $parent;
		
		
		
		$this->master_db = new MasterDB();
		$this->subcontactor_db = new SubcontactorDB();
		$this->installations_db = new InstallationsDB();
		$this->statuses = new statuses();
	}
	
	public function check_rule() {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);

		return in_array($user['UserType'],$this->rules);
	}
	
	public function DrawMenu() {
		ob_start();
		if($this->check_rule()):
		?>
			<li class="<?php echo $this->parent->action == 'addmaster' ? 'active' : '';?><?php echo $this->parent->action == 'listmasters' ? 'active' : '';?><?php echo $this->parent->action == 'editmaster' ? 'active' : '';?>"><a><i class="fa fa-car"></i> Mesterek <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li class="<?php echo $this->parent->action == 'addmaster' ? 'current-page' : '';?>"><a href="<?php echo $this->parent->create_url('addmaster');?>">Új mester</a></li>
					<li class="<?php echo $this->parent->action == 'listmasters' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('listmasters');?>">Mesterek</a></li>
				</ul>
			</li>
		<?php
		endif;
			
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function load_master($id = NULL) {
		return $this->master_db->get_master($id);
	}
	
	public function locked_master($id = NULL) {
		return $this->master_db->locked($id);
	}
	
	public function master_lock($id = NULL) {
		
		return $this->master_db->lock($id);
	}
	
	public function unlock($id = NULL) {
		$this->master_db->unlock($id);
	}
	
	public function masters_select($filter = false) {
		
		$result = $this->master_db->list_masters($filter);
		
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
	
	private function cell_subcon_info($id = NULL) {
		if($id == NULL) {
			echo "No data";
			return;
		}

		$subcon = $this->subcontactor_db->get_subcontactor($id);
		echo '<b>'.$subcon['Name'].'</b>'.'<br />';
		echo "Kapcsolattartó : ".$subcon['ContactPerson'].'<br />';
		echo "E-mail : " . $subcon['Email'].'<br />';
		echo "Tel.: " . $subcon['Phone'].'<br />';
		return;
	}
	
	private function cell_installations($row) {

		$installations_cat = array();
		
		$master_installations = unserialize($row['installations']);
		
		$ret = $this->installations_db->list_installations();
		
		while($row = mysql_fetch_array($ret)) {
			if(is_array($master_installations)) {
				$items = array();
				foreach($master_installations as $inst_id) {
					
					$installation = $this->installations_db->get_item($inst_id);
					if($installation['CatID'] == $row['ID']){
						$items[] = $installation['InstallationName'];
					}
					
				}
				if(count($items) > 0) {
					echo '<b>'.$row['CategoryName'].'</b><br />';
					foreach($items as $item){
						echo '<span style="text-align:right;width:100%;display:inline-block;">'. $item . '</span><br />';
					}
				}
			}
			unset($items);
		}
		
		
		return;
		
	}
	
	public function masters_table($filter = NULL) {
		
		$result = $this->master_db->list_masters($filter);
		
		if($result == NULL) {
			echo "List masters error";
			return;
		}
		
		?>
		<div class="body">
			<table class="table table-bordered table-striped table-hover dataTable master-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<td>Név</td>
						<td>Vállalkozó</td>
						<td>Telefon</td>
						<td>E-mail</td>
						<td>Autó típus</td>
						<td>Rendszám</td>
						<td>Installációk</td>
						<td>Megjegyzés</td>
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>Név</td>
						<td>Vállalkozó</td>
						<td>Telefon</td>
						<td>E-mail</td>
						<td>Autó típus</td>
						<td>Rendszám</td>
						<td>Installációk</td>
						<td>Megjegyzés</td>
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</tfoot>
				<tbody>
		<?php
		while($row = mysql_fetch_assoc($result)) {
			?><tr><?php ?>
			<td><a id="master_edit_<?php echo $row['ID'];?>" class="master-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editmaster&id=<?php echo $row['ID'];?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
			<?php 
			?>
			<td>
				<p><?php echo $row['Name']; ?></p>
				<p>Azonosító: <?php echo str_pad($row['ID'],3,"0",STR_PAD_LEFT);?></p>
			</td>
			<?php
			?><td><?php 
			//echo $row['Subconid']; 
			$this->cell_subcon_info($row['Subconid']);
			?></td><?php
			?><td><?php echo $row['Phone']; ?></td><?php
			?><td><?php echo $row['Email']; ?></td><?php
			?><td><?php echo $row['Cartype']; ?></td><?php
			?><td><?php echo $row['LPNumber']; ?></td><?php
			?><td><?php echo $this->cell_installations($row); ?></td><?php
			?><td><?php echo $row['Comment']; ?></td><?php
			?><td><div class="checkbox"><?php 
			if($row['Active'] == 1){
			?>
				<label class="">
					<div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat master-active-check" id="master-active_<?php echo $row['ID']?>" name="master-active_<?php echo $row['ID']?>" checked="checked" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
				</label>
			<?php } else { ?>
				<label class="">
					<div class="icheckbox_flat-green checked" style="position: relative;"><input class="flat master-active-check" id="master-active_<?php echo $row['ID']?>" name="master-active_<?php echo $row['ID']?>" style="position: absolute; opacity: 0;" type="checkbox"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; border: 0px none; opacity: 0;"></ins></div>
				</label>
			</div></td>
			<?php
			}
			?><td><?php echo $row['DateOfAdd']; ?></td></tr><?php
		}
		?>
				</tbody>
			</table>
		</div>
		<?php
	}
	
	public function list_masters($filter = null){
		$res = $this->master_db->list_masters($filter);
		
		$ret = array();
		
		while($row = mysql_fetch_assoc($res)){
			$ret[] = $row;
		}
		
		return $ret;
	}
	
	public function set_master_active($id = NULL, $value = NULL) {
		return $this->master_db->set_active($id,$value);
	}
	
	public function master_add($inserted) {
		$ret = $this->master_db->add_master($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen felhasználó felvitel!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres felhasználó felvitel!";
			return true;
		}
	}
	
	public function master_update($inserted) {
		$ret = $this->master_db->update_master($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen felhasználó módosítás!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres felhasználó módosítás!";
			$this->unlock($inserted['id']);
			return true;
		}
		
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {
		return $this->master_db->check_unique($fieldname,$value);
	}
	
	public function master_to_mandate($post) {
		
		//var_dump($post);
		
		$filter = array (
						"Active = 1",
						"Subconid = " . $post['subcontactor_id']
						);
		
		$result = $this->master_db->list_masters($filter);
		
		$masters = array();
		
		while($row = mysql_fetch_assoc($result)) {
			$masters[] = $row;
		}
		
		//var_dump($masters);
		
		if(count($masters) == 0) {
			return "Üres lista. Az alvállalkozóhoz nincs mester rendelve, vagy mindegyik inaktiv?";
		} else {
			ob_start();
			?><div class="diolag-content-container"><?php
			foreach($masters as $master) {
				if(isset($post['confirmed_master']) && $post['confirmed_master'] == $master['ID']) {
					$button_style = "btn-success";
				} else {
					$button_style = "btn-default";
				}
				?>
				<div class="container-row"><button data-mandate-id="<?php echo $post['id']?>" data-master-id="<?php echo $master['ID'];?>" type="button" class="btn <?php echo $button_style;?> master-confirm-link" href="javascript:void"><?php echo $master['Name'];?></button></div><p>&nbsp;<p>
				<?php
			}
			?></div>
			<div id="confirm-calendar-container">
			<?php
			if(!isset($post['confirmed_master']) || $post['confirmed_master'] == "NULL"){
				$post['confirmed_master'] = NULL;
			}
			echo $this->parent->get_component('mandates')->draw_calendar(null,null,$post['id'],$post['confirmed_master']);
			?>
			</div>
			<?php
			$content = ob_get_contents();
			ob_end_clean();
			
			return $content;
		}
		
	}
	
	public function login($username = NULL, $password = NULL) {
		
		$master = $this->master_db->login_master($username,$password);
	//var_dump($master);exit;
		if($master != false) {
			//return $master['ID'];
			//var_dump($master);exit;
			//var_dump($master);
			$check = $this->parent->get_component('user_logged')->check_logged($master,true);
			//var_dump($check);
			//exit;
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
						$this->parent->get_component('user_logged')->close_logout($row['Uid'],$row['Session'],true);
					} else {
						$return = false;
					}
					
				}
				//var_dump($return);
				//exit;
				if($return == false) {
					$_SESSION['HDT_login_error'] = "Ez a felhasználó már be van jelentkezve!";
					return false;
				}
			}
			
			$this->parent->get_component('user_logged')->add_log($master,true);
			
			$_SESSION['HDT_master_user'] = $master['ID'];
			
			return $master;
		} else {
			$_SESSION['HDT_login_error'] = "Érvénytelen mester azonosító vagy jelszó (master)!";
			return false;
		}
		
	}
	
}
?>
