<?php

require_once('DbEngines/parcel.db.php');

class parcel {
	
	private $parcel_db;
	
	private $parent;
	
	public function __construct($parent = NULL){
		
		$this->parcel_db = new ParcelDB();
		
		$this->parent = $parent;
	}
	
	public function load_user($id = NULL) {
		return $this->parcel_db->get_user($id);
	}
	
	public function get_partner($user_id = NULL) {

		if($user_id != NULL) {
		
			$user = $this->load_user($user_id);
			
			$partner = $this->parcel_db->get_partner($user['Partner']);
			
			return $partner;
		
		}
	
	}
	
	public function load_client($id = NULL) {
	
		if($id != NULL) {
		
			return $this->parcel_db->get_client($id);
		
		}
	
	}
	
	public function load_subclients($id = NULL) {
		
		if($id != NULL) {
			
			$filter = array("partner_group_id = " . $id);
			
			$result = $this->parcel_db->list_partners($filter);
			
			ob_start();
			while($row = mysql_fetch_assoc($result)){
				/*?><p><?php echo $row['partner_name'];?></p><?php*/
				?>
				<div class="checkbox">
					<label>
						<input id="import-client-<?php echo $row['partner_id'];?>" name="import-client[<?php echo $row['partner_id'];?>]" class="ui-checkbox" type="checkbox">
						<label class="import-client" for="import-client-<?php echo $row['partner_id'];?>"><?php echo $row['partner_name'];?></label>
					</label>
				</div>
				<?php
			}
			$content = ob_get_contents();
			ob_end_clean();
			
			return $content;
		
		}
		
	}
	
	public function load_partner($id = NULL) {
	
		if($id != NULL) {
		
			return $this->parcel_db->get_partner($id);
		
		}
	
	}
	
	public function partner_select($filter = NULL, $options = false) {
		
		$result = $this->parcel_db->list_partners($filter);
		
		//$result = $this->parent->get_component('subclients')->subclients_db->list_subclients($filter);
		
		if($result == NULL) {
			echo "List user error";
			return;
		}
		
		ob_start();
		if($options == false):
		?>
		<select id="" name="mandate-hdt-partner-id" class="form-control select2_single" />
		<?php
		endif;
		while($partner = mysql_fetch_assoc($result)) {
			?><option value="<?php echo $partner['partner_id']?>"><?php echo $partner['partner_name'];?></option><?php
		}
		if($options == false):
		?>
		</select>
		<?php
		endif;
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	private function is_imported($partner_group_id) {
		
		$filter = array("Parcel_user = ".$partner_group_id);
		
		$result = $this->parent->get_component('clients')->client_list($filter);
		
		$clients = array();
		
		while($row = mysql_fetch_assoc($result)) {
			$clients[] = $row;
		}
		
		return $clients;
		
	}
	
	public function parcelclients_select_for_import($filter = NULL) {
		
		$result = $this->parcel_db->list_clients($filter);
		
		$res = array();
		
		ob_start();
		
		while($row = mysql_fetch_assoc($result)) {

			$client = $this->is_imported($row['partner_group_id']);
			if(count($client) == 0) {
				?><option value="<?php echo $row['partner_group_id'];?>"><?php echo $row['partner_group_name'];?></option><?php
			}

		}
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function clients_table($filter = NULL) {
		
		//$filter = "SELECT * FROM partner_group WHERE partner_group_inactive = 0";
		$filter = array("partner_group = 0");
		
		$result = $this->parcel_db->list_clients($filter);
		
		if($result == NULL) {
			echo "List user error";
			return;
		}
		?>
		<div class="body">
			<table class="table table-bordered table-striped table-hover dataTable clients-table">
				<thead>
					<tr>
						<td></td>
						<td>Előtag</td>
						<td>Név</td>
						<td>Partnerek</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td></td>
						<td>Előtag</td>
						<td>Név</td>
						<td>Partnerek</td>
					</tr>
				</tfoot>
				<tbody>
		<?php
		while($row = mysql_fetch_assoc($result)) {
			?><tr><?php
				?><td><?php echo $this->cell_edit_client($row);?></td><?php
				?><td><?php echo $row['partner_group_prefix']; ?></td><?php
				?><td><?php echo $row['partner_group_name']; ?></td><?php
				?><td><?php echo $this->cell_list_partners($row); ?></td><?php
			?></tr><?php
		}
		?>
		</tbody>
			</table>
		</div><!-- body -->
		<?php
		
		return;
		
	}
	
	private function cell_edit_client($row = NULL) {
		
		if($row == NULL) {
			echo "Üres";
			return;
		}
		
		?>
		<a id="client_edit_<?php echo $row['partner_group_id'];?>" class="client-edit-link" data-id="<?php echo $row['partner_group_id'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editclient&id=<?php echo $row['partner_group_id'];?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
		<?php	
		
	}
	
	private function cell_list_partners($row = NULL) {
	
		if($row == NULL) {
			echo "Üres";
			return;
		}
		//$filter = "SELECT * FROM partners WHERE partner_group_id = " . $row["partner_group_id"];
		$filter = array("partner_group_id = ".$row["partner_group_id"]);
		
		$result = $this->parcel_db->list_partners($filter);
		
		while($row = mysql_fetch_assoc($result)) {
			?><p><?php echo $row['partner_code']?> <?php echo $row['partner_name'];?></p><?php
		}
	} 
	
	public function users_table($filter = NULL) {
		
		$result = $this->parcel_db->list_users($filter);
		
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
						<td>Login</td>
						<td>Teljes név</td>
						<td>Telefon</td>
						<td>E-mail</td>
						<td>Rendszám</td>
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>Login</td>
						<td>Teljes név</td>
						<td>Telefon</td>
						<td>E-mail</td>
						<td>Rendszám</td>
						<td>Aktív</td>
						<td>Felvitel dátuma</td>
					</tr>
				</tfoot>
				<tbody>
		<?php
		while($row = mysql_fetch_assoc($result)) {
			?><tr><?php
				?><td><?php echo $row['Login']; ?></td><?php
				?><td><?php echo $row['FullName']; ?></td><?php
				?><td><?php echo $row['Phone']; ?></td><?php
				?><td><?php echo $row['Email']; ?></td><?php
				?><td><?php echo $row['LPNumber']; ?></td><?php
				?><td><?php echo $row['Active']; ?></td><?php
				?><td><?php echo $row['DateOfAdd']; ?></td><?php
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
	
	public function login($username = NULL, $password = NULL) {
	
		$user = $this->parcel_db->login_user($username,$password);

		if($user !== false) {
			return $user['user_id'];
		} else {
			$_SESSION['HDT_login_error'] = "Érvénytelen felhasználónév vagy jelszó (parcel)!";
			return false;
		}
	}
	
	public function userinfo_content() {
		
		ob_start();
		
		$user = $this->load_user($_SESSION['HDT_uid']);
		
		$partner = $this->parcel_db->get_partner($user['Partner']);
		
		?>
		<p>HDT felhasználó</p>
		<p><strong>Partner:</strong></p>
		<p><?php echo $partner['partner_name'];?></p>
		<?php
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
}
