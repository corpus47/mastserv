<?php
require_once('DbEngines/status_history.db.php');

class statushistory {
	
	private $statushistory_db;
	
	private $parent;
	
	public function __construct($parent) {
		
		$this->statushistory_db = new Status_history();
		
		$this->parent = $parent;
		
	}
	
	public function statuschange_add($inserted = NULL) {
		
		$ret = $this->statushistory_db->add_history($inserted);

		if($ret == false) {
			//$_SESSION['HDT_error_message'] = "Sikertelen history felvitel!";
			die("Sikertelen history felvitel!");
			//return false;	
		} else {
			//$_SESSION['HDT_ok_message'] = "Sikeres history felvitel!";
			return true;
		}
	}
	
	public function get_history($mandate_id = NULL) {
		
		return $this->statushistory_db->history($mandate_id);
		
	}
	
	/*public function history_to_pdf($mandate_id = NULL, $history = NULL) {
		
		$pdf = $this->parent->get_component('pdf');
		
		return $pdf->generate_history_pdf_link($mandate_id,$history);
		
	}*/
	
	public function load_history($post) {
		
		$history = $this->statushistory_db->history($post['id']);
		
		$pdf = $this->parent->get_component('pdf');
		
		ob_start();
		
		if(count($history) == 0 ) {
			?>
			Nincs státusz history!
			<?php
		} else {
			?>
			<div class="button-container">
				<!--<a class="btn btn-success" href="javascript:void(0);">Excel (csv)</a>-->
				<a target="blank" class="btn btn-primary" href="<?php echo $pdf->generate_history_pdf_link($post['id'],$history);?>">Pdf</a>
			</div>
			<table class="table table-bordered table-striped table-hover dataTable history-table">
			<thead>
				<tr>
					<td>Események</td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td>Események</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			
			$statuses = $this->parent->get_component('statuses')->list_statuses(true);
			
			foreach($history as $row) {
				?>
				<tr>
					<td>
				<?php
					//var_dump($row);
					$old_status = $statuses[$row['old_status']];
					$new_status = $statuses[$row['new_status']];
					echo "<p>Módosítás időpontja: " . date("Y.m.d H:i:s",strtotime($row['changed_time']))."</p>";
					echo '<p style="color:'.$old_status['color'].';">Régi státusz: ' . $old_status['label']."</p>";
					echo '<p style="color:'.$new_status['color'].';">Új státusz: ' . $new_status['label']."</p>";
					if($row['user_type'] == 0) {
						$user = $this->parent->get_component('user')->load_user($row['user_id']);
						//var_dump($user);
						echo '<p>Felhasználó : '.$user['FullName'].' ( Masterservice )</p>';
					} elseif($row['user_type'] == 2) {
						$master = $this->parent->get_component('master')->load_master($row['user_id']);
						echo '<p>Mester : '.$user['Name'].'</p>';
					} elseif($row['user_type'] == 1) {
						// Parcel user
					}
					echo "<p>IP: " . $row['IP']."</p>";
				?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
			</table>
			<?php
		}
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
}
?>
