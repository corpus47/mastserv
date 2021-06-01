<?php

require_once('DbEngines/days.db.php');

class days {
	
	private $days_db;
	
	private $parent;
	
	private $rules = array(
							SUPER_USER,
							ADMIN_USER
							);
	
	public function __construct($parent) {
		
		$this->days_db = new DaysDB($parent);
		
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
			<li class="<?php echo $this->parent->action == 'adday' ? 'active' : '';?><?php echo $this->parent->action == 'listdays' ? 'active' : '';?><?php echo $this->parent->action == 'editday' ? 'active' : '';?>"><a><i class="fa fa-calendar"></i> Fenttartott dátumok <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li class="<?php echo $this->parent->action == 'adday' ? 'current-page' : '';?>"><a href="<?php echo $this->parent->create_url('adday');?>">Új dátum</a></li>
					<li class="<?php echo $this->parent->action == 'listdays' ? 'active' : '';?>"><a href="<?php echo $this->parent->create_url('listdays');?>">Dátumok</a></li>
				</ul>
			</li>
		<?php
		endif;
			
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function working_day($checked_day = NULL) {
		
		$datum = $this->days_db->get_datum($checked_day);
		
		if($datum !== FALSE && $datum['WorkingDay'] == 1) {
			return true;
		} else {
			
			return false;
		}
		
		return false;
	}
	
	public function allyear_disabled_day($checked_day = NULL) {
		
		$allyear_days = array();
		
		$ret = $this->days_db->get_allyear_disabled();
		
		while($row = mysql_fetch_assoc($ret)) {
			$allyear_days[] = $row;
		}
		
		foreach($allyear_days as $datum){
			$p_month = date('m',strtotime($datum['Datum']));
			$p_day = date('d',strtotime($datum['Datum']));
				
			$month = date('m',strtotime($checked_day));
			$day = date('d',strtotime($checked_day));
			
			if($month == $p_month && $day == $p_day) {
				return true;
			}
		}
		
		return false;
		
	}
	
	public function check_disabled_day($checked_day = NULL) {
		
		$datum = $this->days_db->get_datum($checked_day);
		
		//return $datum;
		
		if($datum !== FALSE && $datum['WorkingDay'] == 0) {
			return true;
		} else {
			
			return false;
		}
		
		/*if ($datum !== FALSE && $datum['AllYear'] == 1) {
			//if($datum['AllYear'] == 1) {
			$p_month = date('m',strtotime($datum['Datum']));
			$p_day = date('d',strtotime($datum['Datum']));
				
			$month = date('m',strtotime($checked_day));
			$day = date('d',strtotime($checked_day));
			var_dump($p_month);
			var_dump($p_day);
			if($month == $p_month && $day == $p_day) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}*/
	
	}
	
	public function day_add($inserted = NULL) {

		$ret = $this->days_db->add_day($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen dátum beállítás!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres dátum beállítás!";
			return true;
		}
	}
	
	public function load_day($id = NULL) {
		return $this->days_db->load_day($id);
	}
	
	public function day_update($inserted = NULL) {
		$ret = $this->days_db->update_day($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen dátum módosítás!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres dátum módosítás!";
			return true;
		}
	}
	
	public function days_table($filter = NULL) {
		
		$res = $this->days_db->list_days($filter);
		
		?>
		<div class="body">
			<table class="table table-bordered table-striped table-hover dataTable day-table">
				<thead>
					<tr>
						<td>&nbsp;</td>
						<td>Fenntartott dátum</td>
						<td>Minden évben imétlődik</td>
						<td>Hétvégi munkanap</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>Fenntartott dátum</td>
						<td>Minden évben imétlődik</td>
						<td>Hétvégi munkanap</td>
					</tr>
				</tfoot>
				<tbody>
		<?php
		
		while($row = mysql_fetch_assoc($res)){
			//var_dump($row);
			?><tr><?php
			?><td>
			<a id="master_edit_<?php echo $row['ID'];?>" class="master-edit-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editday&id=<?php echo $row['ID'];?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
			</td><?php
			?><td><?php echo date("Y.m.d",strtotime($row['Datum']));?></td><?php
			?><td>
			<?php 
			if($row['AllYear'] == 1) {
				echo 'Igen';
			} else {
				echo 'Nem';
			}
			?></td><?php
			?><td><?php
			//echo $row['WorkingDay'];
			if($row['WorkingDay'] == 1) {
				echo 'Igen';
			} else {
				echo 'Nem';
			}
			?></td><?php
			?></tr><?php
		}
		
	}
	
}