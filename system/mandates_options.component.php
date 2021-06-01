<?php

require_once('DbEngines/mandates_options.db.php');

class mandates_options {
	
	private $mandates_options_db;
	
	private $parent;
	
	private $rules = array(
							SUPER_USER,
							ADMIN_USER,
							);
	
	public function __construct($parent = NULL){
		
		$this->parent = $parent;
		
		$this->mandates_options_db = new Mandates_optionsDB();
		
	}
	
	public function check_rule() {
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);

		return in_array($user['UserType'],$this->rules);
	}
	
	public function DrawMenu() {
		ob_start();
		if($this->check_rule()):
		
		?>
		<li class="<?php echo $this->parent->action == 'addmandates_option' ? 'active' : '';?><?php echo $this->parent->action == 'listmandates_options' ? 'active' : '';?><?php echo $this->parent->action == 'editmandates_option' ? 'active' : '';?>"><a><i class="fa fa-road"></i> Kiszállási opciók <span class="fa fa-chevron-down"></span></a>
			<ul class="nav child_menu">
				<li class="<?php echo $this->parent->action == 'addmandates_options' ? 'current-page' : '';?>">
					<a href="<?php echo $this->parent->create_url('addmandates_option');?>">Opció hozzáadása</a>
				</li>
				<li class="<?php echo $this->parent->action == 'listmandates_options' ? 'active' : '';?>">
					<a href="<?php echo $this->parent->create_url('listmandates_options');?>">Opciók</a>
				</li>
			</ul>
		</li>
		<?php
		endif;
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function check_unique($fieldname = NULL, $value = NULL) {
	
		return $this->mandates_options_db->check_unique($fieldname,$value);
		
	}
	
	public function mandates_option_add($inserted) {
		$ret = $this->mandates_options_db->add_mandates_option($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen megbízási opció felvitel!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres megbízási opció felvitel!";
			return true;
		}
	}
	
	public function mandates_option_update($inserted) {
		$ret = $this->mandates_options_db->update_mandates_option($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen megbízási opció módosítás!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres megbízási opció módosítás!";
			return true;
		}
	}
	
	public function load_mandates_option($id = NULL) {
		return $this->mandates_options_db->get_mandates_option($id);
	}
	
	private function cell_edit($row) {
	
		ob_start();
		
		?>
			<a id="mandates_options_edit_<?php echo $row['ID'];?>" class="mandates-options-edit-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editmandates_options&id=<?php echo $row['ID'];?>">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
			</a>
		<?php
		
		$content = ob_get_contents();
		ob_end_clean();
				
		return $content;
	
	}
	
	public function mandate_options_table($filter = NULL) {
	
		
		$result = $this->mandates_options_db->list_mandates_options($filter);
		
		?>
		<div class="body">
			<table class="table table-bordered table-striped table-hover dataTable mandates-options-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<td>Megnevezés</td>
						<td>Távolság</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>Megnevezés</td>
						<td>Távolság</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td><?php echo $this->cell_edit($row);?></td>
						<td><?php echo $row['OptionName'];?></td>
						<td><?php echo $row['Distance'];?></td>
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
	
}
