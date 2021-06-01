<?php

require_once('DbEngines/email_contents.db.php');

class email_contents {

	private $parent;
	
	private $email_contents_db;
	
	private $rules = array(
							SUPER_USER,
							ADMIN_USER
							);
	
	public function __construct($parent = NULL) {
	
			 $this->parent = $parent;
			 
			 $this->email_contents_db = new Email_contentsDB($parent);
			 
	}
	
	public function check_rule() {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);

		return in_array($user['UserType'],$this->rules);
	}
	
	public function DrawMenu() {
		ob_start();
		if($this->check_rule()):
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);
		?>
			<?php //if($user['UserType'] == SUPER_USER):?>
				<li><a href="<?php echo $this->parent->create_url('addemail_contents');?>"> Új üzenet </a></li>
			<?php //endif;?>
			<li><a href="<?php echo $this->parent->create_url('listemail_contents');?>"> Üzenetek </a></li>
		<?php
		endif;
			
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	private function action_cell($row) {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);
		
		?>
			<div class="inner-cell">
			<a id="email_contents_edit_<?php echo $row['ID'];?>" class="email_contents-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editemail_contents&id=<?php echo $row['ID'];?>">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
			</a>
			<div style="clear:both;margin-top:10px;display:inline-block;width:100%;"></div>
			<?php //if($user['UserType'] == SUPER_USER):?>
			<a id="email_contents_delete_<?php echo $row['ID'];?>" class="email_contents-delete-link" data-id="<?php echo $row['ID'];?>" href="javascript:void(0);" style="display:<?php //echo $display;?>;">
				<span class="glyphicon glyphicon-remove" aria-hidden="true" style="color:red;"></span>
			</a>
			<div style="clear:both;"></div>
			<?php //endif;?>
			<?php
			?></div><?php
	}
	
	public function add_email_contents($post = NULL) {
		$ret = $this->email_contents_db->add_email_contents($post);

		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen üzenet szöveg felvitel!";
			return false;	
		} else {

			//$email = $this->parent->get_component('email')->mandate_add_email($inserted);
			
			$_SESSION['HDT_ok_message'] = "Sikeres üzenet szöveg felvitel!";
			return true;
		}
	}
	
	public function load_email_contents($id = NULL) {
		
		return $this->email_contents_db->get_email_contents($id);
		
	}
	
	public function update_email_contents($inserted = null) {
		
		$ret = $this->email_contents_db->update_email_contents($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen üzenet módosítás!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres üzenet módosítás!";
			//$this->unlock($inserted['id']);
			return true;
		}
		
	}
	
	public function email_contents_delete($inserted = NULL) {
		
		$ret = $this->email_contents_db->delete_email_contents($inserted);
		
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen üzenet törlés!";
			return false;	
		} else {
			//$this->parent->get_component('clients')->unset_owner($inserted);
			$_SESSION['HDT_ok_message'] = "Az üzenet törölve!";
			return true;
		}
		
	}
	
	public function load_content($hook = null) {
	
		if($hook != null) {
			
			$ret = $this->email_contents_db->load_content($hook);
			
			if(isset($ret['Content'])) {
				return $ret['Content'];
			} else {
				return "Az üzenet üres";
			}
			
		}
	
	}
	
	public function email_contents_table($filter = NULL) {
	
		$result = $this->email_contents_db->list_email_contents();
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);
		
		ob_start();
		//echo __FUNCTION__;
		?>
		<div class="body">
			<table data-filter="<?php //echo $filter_str;?>" class="table table-bordered table-striped table-hover dataTable email_contents-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<td>Leírás</td>
						<?php if($user['UserType'] == SUPER_USER):?>
						<td>Hook</td>
						<?php endif;?>
						<td>Szöveg</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>Leírás</td>
						<?php if($user['UserType'] == SUPER_USER):?>
						<td>Hook</td>
						<?php endif;?>
						<td>Szöveg</td>
					</tr>
				</tfoot>
				<tbody>
				<?php 
				while($row = mysql_fetch_assoc($result)) {
					?><tr><?php
					?><td><?php echo $this->action_cell($row);?></td><?php
					?><td><?php echo $row['Label'];?></td><?php
					if($user['UserType'] == SUPER_USER):
					?><td><?php echo $row['Hook'];?></td><?php
					else:
					?><td>Azt, hogy mely eseményhez kapcsolódjon az üzenet, a fejlesztővel kell megbeszélnie!!!!</td><?php
					endif;
					?><td><?php echo $row['Content'];?></td><?php
					?></tr><?php
				}
				?>
				</tbody>
			</table>
		</div><!-- .body -->
		<?php
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	
	}
	
}
