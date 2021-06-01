<?php 
class CompactMandateList {
	
	private $parent;
	
	private $statuses;
	
	public function __construct($parent = NULL) {
		
		$this->parent = $parent;
		
		if($parent != NULL) {
			
			$this->statuses = $this->parent->get_component('statuses');
			
		} else {
			
			$this->statuses = NULL;
			
		}
		
	}
	
	public function id_cell($row = NULL) {
		
		if($row == NULL) {
			
			return "Hiányzó adat!";
			
		} else {
			
			ob_start();
			
			$status = $this->statuses->get_status($row['Master_status']);
			
			?><a title="<?php echo $status['label'];?>" class="compact_id_cell status-button" style="background-color:<?php echo $status['color'];?>;" data-id="<?php echo $row['ID'];?>" data-status="<?php echo $row["Master_status"]?>"><?php echo $row['ID'];?></a><?php
			
			$content = ob_get_contents();
			ob_end_clean();
		
			return $content;
			
		}
		
	}
	
	public function data_cell($row = NULL) {
		
		if($row == NULL) {
				
			return "Hiányzó adat!";
				
		} else {
				
			ob_start();
			
			?>
			<div class="compact_data_cell_container">
				<p><strong><?php echo $row["Mandate_serial"];?></strong></p>
				<?php 
				if(!isset($row['HDT_order_id']) || $row['HDT_order_id'] == NULL) {
					?><p>Nincs fuvarhoz rendelve&nbsp;|&nbsp;</p><?php
				} else {
					?><p>Fuvarszám :<strong><?php echo $row['HDT_order_id'];?></strong>&nbsp;|&nbsp;</p><?php
				}
				if(!isset($row['PartnerID']) || $row['PartnerID'] == NULL) {
					?><p>Nincs partnerhez rendelve&nbsp;|&nbsp;</p><?php
				} else {
					//$partner = $this->parcel_db->get_partner($row['PartnerID']);
					$subclient = $this->parent->get_component('subclients')->load_subclient($row['PartnerID']);
					?><p>Almegbízó :<strong><?php echo $subclient['Name'];?></strong>&nbsp;|&nbsp;</p><?php
				}
				if($row['SubcontactorID'] != NULL) {
					$subcontactor = $this->parent->get_component('subcontactor')->load_subcontactor($row['SubcontactorID']);
					//var_dump($subcontactor);
					?><p>Aut. kiosztott alv.: <b><?php echo $subcontactor["Name"]?></b>&nbsp;|&nbsp;</p><?php
				} else {
					?><p style="color:red;">Nincs alvállalkozóra kiosztva&nbsp;|&nbsp;</p><?php
				}
				?>
				</br>
				<p><?php echo $row['CustomerZipcode']?>&nbsp;<?php echo $row['CustomerCity'];?>&nbsp;</p>
				<?php 
				if($row['Master_status'] == 0) {
					?><p>A megbízás még nincs kiosztva</p><?php
				} else {
					$master = $this->parent->get_component('master')->load_master($row['MasterID']);
					?><p><b>Mester:</b>&nbsp;<?php echo $master['Name'];?>&nbsp;</p><?php
				}
				if($row['Kiszallas_Date'] == NULL):
					?><p>Nincs installációs dátum egyeztetve</p><?php
				else:
					?><p>Installáció dátuma: <strong><?php echo date('Y.m.d',strtotime($row['Kiszallas_Date']));?></strong></p><?php
				endif;
				?>
			</div>
			<?php
			
			$content = ob_get_contents();
			ob_end_clean();
			
			return $content;
				
		}
		
	}
	
	public function action_cell($row = NULL) {
		
		if($row == NULL) {
				
			return "Hiányzó adat!";
				
		} else {
				
			ob_start();
			
			?><div class="compact-action-cell-container"><?php
					//echo $this->alert_cell($row);
					
					?><a title="Részletek" id="compact_mandate_view_<?php echo $row['ID'];?>" class="compact-mandate-view-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" href="javascript:void(0);"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a><?php
			
					if(isset($_SESSION['mode'])) {
						$mode = "&mode=".$_SESSION['mode'];
						//unset($_SESSION['mode']);
					} else {
						$mode = "";
					}
					?>
					<?php if(!$this->parent->get_component('user')->is_subcon_admin($_SESSION['HDT_uid'])):?>
					<a title="Szerkesztés" id="mandate_edit_<?php echo $row['ID'];?>" class="mandate-edit-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editmandate&id=<?php echo $row['ID'];?><?php echo $mode;?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
					
					<?php endif;?>
					<?php
					if(($row['MasterID'] == NULL || $row['MasterID'] == 0 ) && $row['SubcontactorID'] != NULL):
					?>
					<a title="Kiosztás mesterre" id="mandate_confirm_<?php echo $row['ID']?>" class="mandate-confirm-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" data-subcontactor="<?php echo $row['SubcontactorID'];?>" href="javascript:void(0);"><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span></a>
					
					<?php
					endif;
					?>
					<?php if($row['Master_status'] == MANDATE_GET_MASTER):
					?>
					<a title="Visszahelyezés a várakozók közé" id="mandate_unconfirm_<?php echo $row['ID']?>" class="mandate-unconfirm-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" href="#"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span></a>
					
					<?php
					endif;
					?>
					<?php
					$history = $this->parent->get_component('statushistory');
					if(count($history->get_history($row['ID'])) > 0):
					?>
					<?php if($this->parent->get_component('user')->is_admin($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_super($_SESSION['HDT_uid'])):?>
					<a title="Status history" id="mandate_history_<?php echo $row['ID']?>" class="mandate-history-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" data-serial="<?php echo $row['Mandate_serial'];?>" href="javascript:void(0);"><span  class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></a>
					
					<?php endif;?>
					<?php
					endif;
					?>
					<?php if(!$this->parent->get_component('user')->is_subcon_admin($_SESSION['HDT_uid'])):?>
					<a title="Csatolt fájlok" href="javascript:void(0);" data-id="<?php echo $row['ID']?>" class="file-upload-button table-edit-cell-link"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span></a>
					
					<?php endif;?>
					<?php /*if($row['screen_width'] <= 768):?>
					<a title="További infó" id="cell_info_<?php echo $row['ID']?>" class="cell-info-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" href="#"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a>
					<div style="width:100%;clear:both;"></div>
					<?php endif;*/?>
					<?php if($this->parent->get_component('user')->is_admin($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_super($_SESSION['HDT_uid'])):?>
						<?php if($row['Master_status'] == INSTALLATION_SUCCESS):?>
							<a title="Feladás számlázásra" href="javascript:void(0);" data-id="<?php echo $row['ID']?>" class="billed-button table-edit-cell-link"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></a>
							
						<?php endif;?>
					<?php endif;?>
					</div>
					<?php
		
			$content = ob_get_contents();
			ob_end_clean();
			
			return $content;
				
		}
	}
	
}
?>