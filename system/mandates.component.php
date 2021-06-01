<?php
require_once('DbEngines/mandates.db.php');
require_once('DbEngines/parcel.db.php');
require_once('DbEngines/installations.db.php');
require_once('installations.component.php');
require_once('statuses.component.php');
require_once('DbEngines/subcontactor.db.php');
require_once('DbEngines/master.db.php');


class mandates {

	private $mandates_db;
	
	private $parcel_db;
	
	private $statuses;
	
	private $google;
	
	private $parent;
	
	private $converter;
	
	public function __construct($parent = NULL){

		$this->mandates_db = new MandatesDB($parent);

		$this->parcel_db = new ParcelDB();
		$this->installations_db = new InstallationsDB();
		$this->installations = new installations();
		$this->statuses = new statuses();
		$this->subcontactor_db = new SubcontactorDB();
		$this->master_db = new MasterDB();
		$this->google = new Google_tools();
		
		$this->parent = $parent;
		
		$this->converter = new Azaz();
		
		
		
	}
	
	public function load_mandate($id = NULL) {
		return $this->mandates_db->get_mandate($id);
	}
	
	public function mandate_add($inserted = NULL,$files = NULL) {
		
		// Kiosztás alvállalkozóra
		
		$result = $this->subcontactor_db->list_subcontactors();
		
		while($row = mysql_fetch_assoc($result)) {
			$zips= explode(",",$row["Zips"]);
			//var_dump($inserted["mandate-customer-zipcode"]);
			if(in_array(trim($inserted["mandate-customer-zipcode"]),$zips)) {
				//echo $row["Name"];
				$inserted['SubcontactorID'] = $row['ID'];
			}
		}
		
		$ret = $this->mandates_db->add_mandate($inserted,$files);

		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen megbízás felvitel!";
			return false;	
		} else {

			//$email = $this->parent->get_component('email')->mandate_add_email($inserted);
			
			$_SESSION['HDT_ok_message'] = "Sikeres megbízás felvitel!";
			return true;
		}
	}
	
	public function mandate_update($inserted = NULL) {
		$ret = $this->mandates_db->update_mandate($inserted);
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen megbízás módosítás!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "Sikeres megbízás módosítás!";
			$this->unlock($inserted['id']);
			return true;
		}
	}
	
	public function generate_subclient_check($subclients = null) {
		
		ob_start();
		
		if($subclients == null || count($subclients) == 0 ) {
			?>
			<div class="form-group form-float">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-partner-id">Almegbízó</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
					<h4 style="background:red none;color:white;padding:5px;">Ön nincs hozzárendelve egy almegbízóhoz sem! A megbízás nem menthető!</h4>
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 align-left" for="mandate-partner-name"></label>
                    <input type="hidden" class="form-control" id="no-subclient" name="no-subclient" value="" />
				</div>
            </div>
			<?php
		}
		//var_dump($subclients);
		if(count($subclients) == 1){
			?>
			<div class="form-group form-float">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-partner-id">Almegbízó</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
					<h4><?php echo $subclients[0]['Name'];?></h4>
                    <label class="control-label col-md-3 col-sm-3 col-xs-12 align-left" for="mandate-partner-id"></label>
                    <input type="hidden" class="form-control" id="mandate-hdt-partner-id" name="mandate-hdt-partner-id" value="<?php echo $subclients[0]['ID'];?>"/>
					<input type="hidden" class="form-control" id="mandate-partner-id" name="mandate-partner-id" value="<?php echo $subclients[0]['ID'];?>"/>
				</div>
            </div>
			<?php
		} else {
			?>
			<div id="partner-id-select" class="form-group form-float">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="mandate-partner-id">Almegbízó</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<select id="mandate-partner-id" name="mandate-partner-id" class="form-control ch_mark">
					<?php
					foreach($subclients as $subclient) {
						?><option value="<?php echo $subclient['ID'];?>"><?php echo $subclient['Name']?></option><?php
					}
					?>
					</select>
				</div>
			</div>
			<?php
		}
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function confirm_calendar($post = NULL) {
		
		if(isset($post['month'])) {
			$month = $post['month'];
		} else {
			$month = NULL;
		}
		
		if(isset($post['year'])) {
			$year = $post['year'];
		} else {
			$year = NULL;
		}
		
		if(!isset($post['mandate_id'])) {
			$post['mandate_id'] = NULL;
		}
		
		if(!isset($post['master_id'])){
			$post['master_id'] = NULL;
		}
		
		$ret['status'] = 'ok';
		
		$ret['content'] = $this->draw_calendar($month,$year,$post['mandate_id'],$post['master_id']);
		
		return $ret;
	}
	
	public function draw_calendar($month = NULL,$year = NULL, $mandate_id = NULL, $master_id = NULL) {
		//var_dump($master_id);
		
		if($master_id == NULL) {
			ob_start();
			
			$content = ob_get_contents();
			ob_end_clean();
			
			?><div class="alert_div">Válasszon mestert!</div><?php
			
			return $content;
		} else {
			$mandate = $this->mandates_db->get_mandate($mandate_id);
			//var_dump($mandate);
			if(isset($mandate['Kiszallas_Date']) && $mandate['Kiszallas_Date'] !== null) {
				/*?><p><strong>Installáció időpontja:</strong><?php echo date("Y.m.d",strtotime($mandate['Kiszallas_Date']));?></p><?php*/
				$akt_day = strtotime(date("Y-m-d H:i:s",strtotime($mandate['Kiszallas_Date'])));
			} else {
				$akt_day = NULL;
			}
		}
		
		date_default_timezone_set('Europe/Budapest');
		
		$months = array("január","február","március","április","május","június","július","augusztus","szeptember","október","november","december");
		
		$p_month = (int)date('m',time());
		$p_year = (int)date('Y',time());
		
		if($month == NULL) {
			if($akt_day == NULL){
				$month = (int)date('m',time());
			} else {
				$month = (int)date('m',$akt_day);
			}	
		}
		
		if($year == NULL) {
			if($akt_day == NULL){
				$year = (int)date('Y',time());
			} else {
				$year = (int)date('Y',$akt_day);
			}
		}
		
		/*if($month == NULL && $year == NULL && $akt_day !== NULL) {
			$month = (int)date('m',strtotime($akt_day));
			$year = (int)date('Y',strtotime($akt_day));
		}*/
		
		$str_month = (string)$month;
		
		if(strlen($str_month) == 1) {
			$str_month = "0".$str_month;
		}
		
		$datum = (string)$year."-".$str_month."-01";
		
		//var_dump($datum);
		
		$first_day_in_month = date('w',strtotime($datum));
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		
		if($first_day_in_month == 0) {
			$first_day_in_month = 7;
		}
		
		if((int)$month === 1) {
			$prev_month = 12;
			$prev_year = $year-1;
		} else {
			$prev_month = $month-1;
			$prev_year = $year;
		}
		
		if((int)$month === 12) {
			$next_month = 1;
			$next_year = $year+1;
		} else {
			$next_month = $month+1;
			$next_year = $year;
		}
		
		//var_dump($first_day_in_month);
		
		/* draw table */
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

		/* table headings */
		//$headings = array('Vasárnap','Hétfő','Kedd','Szerda','Csütörtök','Péntek','Szombat');
		$calendar .= '<tr class="calendar-row calendar-nav-row">';

		$calendar .= '<td class="calendar-day">';
		if(((int)$prev_month >= (int)$p_month) || ((int)$year > (int)$p_year)):
			$calendar .= '<a class="calendar-step" data-nolock="1" data-master-id="'.$master_id.'" data-mandate-id="'.$mandate_id.'" data-month="'.$prev_month.'" data-year="'.$prev_year.'" title="Előző hónap" href="javascript:void(0);" style="width:100%;text-align:center;display:inline-block;"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>';
		endif;
		$calendar .= '</td>';
		
		$calendar .= '<td class="calendar-day" style="text-align:center;font-size:14px;font-weight:bold;" colspan="5">'.$year.'.'.$months[$month-1].'</td>'.
					 '<td class="calendar-day"><a class="calendar-step" data-master-id="'.$master_id.'" data-mandate-id="'.$mandate_id.'" data-nolock="1" data-month="'.$next_month.'" data-year="'.$next_year.'" title="Következő hónap" href="javascript:void(0);" style="width:100%;text-align:center;display:inline-block;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a></td>'.
					 '</tr>';
		$headings = array('H','K','Sze','Cs','P','Szo','V');
		$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
		
		$days_in_this_week = 1;
		
		$calendar.= '<tr class="calendar-row">';
		
		//Első hét kiírása
		
		$printed_day_in_week = 1;
		
		for($i = 1;$i < $first_day_in_month;$i++) {
			$calendar.= '<td class="calendar-day-np"> </td>';
			$printed_day_in_week++;
		}
		
		for($i = 1;$i <= $days_in_month; $i++) {
			
			$datum = $year."-".$str_month."-".str_pad($i,2,'0',STR_PAD_LEFT);
			$days = $this->parent->get_component('days');
			
			$dayclass = "";
			
			if($printed_day_in_week == 6 || $printed_day_in_week == 7 || $days->check_disabled_day($datum)) {
				//$spanclass = " bg-gray";
				$spanclass=" bg-gray";
			} else {
				//$dayclass = "";
				$spanclass=" bg-green";
			}
			if($days->allyear_disabled_day($datum)){
				$spanclass = " bg-gray";
				//$spanclass=" bg-green";
			}
			if(($printed_day_in_week == 6 || $printed_day_in_week == 7) && $days->working_day($datum)) {
				$spanclass = " bg-green";
				//$spanclass=" bg-green";
			}
			
			//$calendar.= '<td class="calendar-day'.$dayclass.'">';
			//$calendar.= '<div class="day-number">'.$i.'</div>';
			$datum = $year."-".$str_month."-".str_pad($i,2,'0',STR_PAD_LEFT);
			$timestamp = strtotime($datum." 0:0:0");
			$akt_timestamp = strtotime(date("Y-m-d",strtotime("now"))." 0:0:0");
			if($timestamp < $akt_timestamp) {
				//$dayclass = " bg-gray";
				$spanclass=" bg-gray";
			}
			//var_dump($akt_day);
			if($akt_day !== NULL && $timestamp == $akt_day) {
				$dayclass .= " confirm-day-cell";
			}
			
			$calendar.= '<td class="calendar-day'.$dayclass.'">';
			
			//$days = $this->parent->get_component('days');
			//ob_start();
			
			//echo $i;
			//var_export($dayclass);
			//$days->check_disabled_day($datum);
			//var_dump($year);var_dump($p_year);
			//$content = ob_get_contents();
			//ob_end_clean();
			//$calendar .= '<span class="badge '.$spanclass.'">'.$content.'</span>';
			$calendar .= $this->draw_calendar_day($spanclass,$i,$datum,$master_id,$mandate_id);
			
			//$calendar.= str_repeat('<p>&nbsp;</p>',2);
			$calendar.= '</td>';
			$printed_day_in_week++;
			if($printed_day_in_week == 8) {
				$calendar.= '</tr>';
				$printed_day_in_week = 1;
				if($i+1 <= $days_in_month){
					$calendar.= '<tr class="calendar-row">';
				}
			}
			
		}
		
		/* final row */
		$calendar.= '</tr>';

		/* end the table */
		$calendar.= '</table>';
		
		/* all done, return result */
		return $calendar;
		
	}
	
	private function draw_calendar_day($spanclass = NULL,$day_number = NULL, $datum = NULL, $master_id = NULL, $mandate_id = NULL) {
		
		ob_start();
		
		if($spanclass == " bg-gray") {
			?><span class="badge bg-gray"><?php echo $day_number;?></span><?php
		} else {
			
			$mandate = $this->mandates_db->get_mandate($mandate_id);
			
			$mandate_installations = unserialize($mandate['Mandate_installations']);
			
			$mandate_req_time = 0;
			
			foreach($mandate_installations as $key=>$item) {
				$installation = $this->parent->get_component('installations')->get_installation_item($key);
				//var_export($installation);
				$mandate_req_time += $installation['Req_Time'];
			}
			
			//$statuses = $this->parent->get_component('statuses')->list_statuses();
			//var_dump($statuses);
			$datum = date("Y-m-d H:i:s",strtotime($datum));
			//var_dump($datum);
			$filter = array(
					"MasterID = ".$master_id,
					"Master_status = " . MANDATE_GET_MASTER,
					"Kiszallas_Date = '" . $datum ."'"
			);
				
			$res = $this->mandates_db->list_mandates($filter);
			//$mandates = array();
			$req_time = 0;
			while($row = mysql_fetch_assoc($res)) {
				//$mandates[] = $row;
				//var_dump($row);
				$installations = unserialize($row['Mandate_installations']);
				foreach($installations as $key=>$item){
					$installation = $this->parent->get_component('installations')->get_installation_item($key);
					//var_export($installation);
					$req_time += $installation['Req_Time'];
				}
				
			}
			//var_export($req_time);
			$clickspan = " confirmed-span";
			if($req_time > 0 && $req_time < 8) {
				$spanclass = " bg-yellow";
			} elseif($req_time > 7) {
				$spanclass = " bg-red";
				$clickspan = "";
			} elseif($mandate_req_time > (8 - $req_time)) {
				$spanclass = " bg-red";
				$clickspan = "";
			}
			?><span title="Szabad: <?php echo (8-$req_time);?> munkaóra.<?php echo "\r\n";?>Jelenlegi megbízás: <?php echo $mandate_req_time;?> munkaórát tartalmaz." data-datum="<?php echo $datum;?>" data-master-id="<?php echo $master_id?>" data-mandate-id="<?php echo $mandate_id;?>" class="badge<?php echo $spanclass;?><?php echo $clickspan;?>"><?php echo $day_number;?></span><?php
		}

		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	private function action_cell($row = NULL) {
		ob_start();
		?><div class="action-cell-container"><?php
		echo $this->alert_cell($row);
		
		if(isset($_SESSION['mode'])) {
			$mode = "&mode=".$_SESSION['mode'];
			//unset($_SESSION['mode']);
		} else {
			$mode = "";
		}
		?>
		<?php if(!$this->parent->get_component('user')->is_subcon_admin($_SESSION['HDT_uid'])):?>
		<a title="Szerkesztés" id="mandate_edit_<?php echo $row['ID'];?>" class="mandate-edit-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" href="http://<?php echo ROOT_URL?>/?m=masterservice&act=editmandate&id=<?php echo $row['ID'];?><?php echo $mode;?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
		<div style="width:100%;clear:both;"></div>
		<?php endif;?>
		<?php
		if(($row['MasterID'] == NULL || $row['MasterID'] == 0 ) && $row['SubcontactorID'] != NULL):
		?>
		<a title="Kiosztás mesterre" id="mandate_confirm_<?php echo $row['ID']?>" class="mandate-confirm-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" data-subcontactor="<?php echo $row['SubcontactorID'];?>" href="javascript:void(0);"><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span></a>
		<div style="width:100%;clear:both;"></div>
		<?php
		endif;
		?>
		<?php if($row['Master_status'] == MANDATE_GET_MASTER):
		?>
		<a title="Visszahelyezés a várakozók közé" id="mandate_unconfirm_<?php echo $row['ID']?>" class="mandate-unconfirm-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" href="#"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span></a>
		<div style="width:100%;clear:both;"></div>
		<?php
		endif;
		?>
		<?php
		$history = $this->parent->get_component('statushistory');
		if(count($history->get_history($row['ID'])) > 0):
		?>
		<?php if($this->parent->get_component('user')->is_admin($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_super($_SESSION['HDT_uid'])):?>
		<a title="Status history" id="mandate_history_<?php echo $row['ID']?>" class="mandate-history-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" data-serial="<?php echo $row['Mandate_serial'];?>" href="javascript:void(0);"><span  class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></a>
		<div style="width:100%;clear:both;"></div>
		<?php endif;?>
		<?php
		endif;
		?>
		<?php if(!$this->parent->get_component('user')->is_subcon_admin($_SESSION['HDT_uid'])):?>
		<a title="Csatolt fájlok" href="javascript:void(0);" data-id="<?php echo $row['ID']?>" class="file-upload-button table-edit-cell-link"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span></a>
		<div style="width:100%;clear:both;"></div>
		<?php endif;?>
		<?php if($row['screen_width'] <= 768):?>
		<a title="További infó" id="cell_info_<?php echo $row['ID']?>" class="cell-info-link table-edit-cell-link" data-id="<?php echo $row['ID'];?>" href="#"><span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a>
		<div style="width:100%;clear:both;"></div>
		<?php endif;?>
		<?php if($this->parent->get_component('user')->is_admin($_SESSION['HDT_uid']) || $this->parent->get_component('user')->is_super($_SESSION['HDT_uid'])):?>
			<?php if($row['Master_status'] == INSTALLATION_SUCCESS):?>
				<a title="Feladás számlázásra" href="javascript:void(0);" data-id="<?php echo $row['ID']?>" class="billed-button table-edit-cell-link"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></a>
				<div style="width:100%;clear:both;"></div>
			<?php endif;?>
		<?php endif;?>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	private function hdt_data_cell($row = NULL) {
		ob_start();
		?><?php echo $row['Mandate_serial'];?><?php
		
		if(!isset($row['HDT_order_id']) || $row['HDT_order_id'] == NULL) {
			?><p>Nincs fuvarhoz rendelve</p><?php
		} else {
			?><p>Fuvarszám :<strong><?php echo $row['HDT_order_id'];?></strong></p><?php
		}
		if(!isset($row['PartnerID']) || $row['PartnerID'] == NULL) {
			?><p>Nincs partnerhez rendelve</p><?php
		} else {
			//$partner = $this->parcel_db->get_partner($row['PartnerID']);
			$subclient = $this->parent->get_component('subclients')->load_subclient($row['PartnerID']);
			?><p>Almegbízó :<strong><?php echo $subclient['Name'];?></strong></p><?php
		}
		if($row['SubcontactorID'] != NULL) {
			$subcontactor = $this->subcontactor_db->get_subcontactor($row['SubcontactorID']);
			//var_dump($subcontactor);
			?><p>Aut. kiosztott alv.: <b><?php echo $subcontactor["Name"]?></b></p><?php
		} else {
			?><p style="color:red;">Nincs alvállalkozóra kiosztva</p><?php
		}
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	private function customer_data_cell($row) {
		ob_start();
		?><p>Név: <strong><?php echo $row['CustomerName'];?></strong></p><?php
		?><p>Cím: <?php echo $row['CustomerZipcode'];?> <?php echo $row['CustomerCity']?> <?php echo $row['CustomerAddress'];?></p><?php
		?><p>Tel.: <?php echo $row['CustomerPhone'];?></p><?php
		?><p>E-mail: <?php echo $row['CustomerEmail'];?></p><?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	private function  mandate_data_cell($row) {
		ob_start();
		?><p>Kiszállítás: <?php echo date('Y.m.d',strtotime($row['Kiszallitas']));?></p><?php
		if($row['Kiszallitva'] == 0):
		?><p>Még nincs kiszállítva!</p><?php
		else:
		?><p>A futár lezárta a fuvart!</p><?php
		endif;
		if($row['Kiszallas_Date'] == NULL):
		?><p>Nincs installációs dátum egyeztetve</p><?php
		else:
		?><p>Installáció dátuma: <strong><?php echo date('Y.m.d',strtotime($row['Kiszallas_Date']));?></strong></p><?php
		endif;
		?>
		<!--<p>Még nincsenek csatolmányok</p>
		<p style="width:100%;text-align:center;"><a href="javascript:void(0);" class="btn btn-primary file-upload-button" data-id="<?php echo $row['ID']?>">File feltöltés</a></p>-->
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	private function installations_data_cell($row) {
		
		ob_start();
		
		//$details = $this->mandates_db->get_details($row['ID']);
		
		//$to_address = $row['CustomerZipcode']." ".$row['CustomerCity']." ".$row["CustomerAddress"];
		
		$netto_cost = 0;
		
		$products = unserialize($row['Mandate_products']);
		
		
		/*if(count($products) > 0) {
		
		?><p><b>Termékek:</b></p><?php
		?><ol><?php
			foreach($products as $product) {
				?><li><?php echo $product;?></li><?php
			}
		?></ol><?php
		} else {
			?><p>Nincs termék!</p><?php
		}*/
		
		$installations = unserialize($row['Mandate_installations']);
		
		//var_dump($installations);
		
		$insts = array();
		
		foreach($installations as $key=>$value) {
			
			//$inst_costs = explode('|',$value);
			
			$val = $value;
			//var_dump($val);
			$inst = $this->parent->get_component('installations')->get_installation_item($key);
			//var_dump($inst);
			$inst['Value'] = $val;
			if(isset($inst['CatID'])){
				$insts[$inst['CatID']][] = $inst;
			}
		}
		
		//var_dump($insts);
		
		if(count($insts) > 0){
			foreach($insts as $key=>$items){
				
				$piece_array = explode('|',$items[0]['Value']);
				
				if(isset($piece_array[1])) {
					$piece = $piece_array[1];
				} else {
					$piece = 1;
				}
				
				$installation_cat = $this->installations->load_installation($key);
				?><p><strong><?php echo $installation_cat['CategoryName'];?></strong>&nbsp;-&nbsp;<?php echo $piece;?>&nbsp;db</p><?php
				?><ol><?php
				foreach($items as $item){
					$cost_array = explode('|',$item['Value']);
					//var_dump($cost_array);
					if(isset($cost_array[2])) {
						$cost = $cost_array[2];
					} else {
						$to_address = $row['CustomerZipcode']." ".$row['CustomerCity']." ".$row["CustomerAddress"];
						$cost = $this->parent->get_component('clientoptions')->get_option($item['ID'],$row['PartnerID'],$to_address,$installations[$item['ID']]);
					}
					
					$netto_cost += $cost
					?><li><?php echo $item['InstallationName'];?> - <strong><?php echo $cost;?></strong> Ft</li><?php
				}
				?></ol><?php
			}
		} else {
			?><p>Nincs termék!</p><?php
		}
		?><p><strong>Netto összesen: <?php echo $netto_cost;?> Ft</strong></p><?php
		?><p><strong>Brutto összesen: <?php echo $netto_cost*1.27;?> Ft</strong></p><?php
		?><p><?php echo $this->parent->get_component('converter')->toString($netto_cost*1.27);?> forint</p><?php
		/*if(count($installations) > 0) {
		
		?><p><b>Installációk:</b></p><?php
		?><ol><?php
			foreach($installations as $key=>$value) {
				$inst = $this->installations_db->get_item($key);
				?><li><?php echo $inst['InstallationName'];?></li><?php
			}
		?></ol><?php
		} else {
			?><p>Nincs termék!</p><?php
		}*/
		
		/*foreach($details as $item) {
			//var_dump($item);
			?><p>Termék: <?php echo $item['ProductName'];?></p><?php
			
			$installation = $this->installations_db->get_item($item['InstallationID']);
			//var_dump($installation);
			$installation_cat = $this->installations_db->get_installation($installation['CatID']);
			//var_dump($installation_cat);
			
			?><p>Installáció : <?php echo $installation_cat['CategoryName'] . ' -> ' . $installation['InstallationName'];?></p><?php
			?><p>&nbsp;</p><?php
		}*/
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	private function status_cell($row,$master_board = false) {
		//var_dump($row);
		ob_start();
		//var_export($row['screen_width']);
		if($row['Master_status'] == 0) {
			?><p>A megbízás még nincs kiosztva</p><?php
		} else {
			$status = $this->statuses->get_status($row['Master_status']);
			//var_dump($status);
			?>
			<?php if(!$master_board):?>
			<div>
			<p><?php //echo $status['label'];?></p>
			<?php 
			$master = $this->master_db->get_master($row['MasterID']);
			?>
			<p><b>Mester:</b></p>
			<p><?php echo $master['Name'];?></p>
			<p><i class="fa fa-phone"></i>&nbsp;<?php echo $master['Phone'];?></p>
			</div>
			<?php endif;?>
			<?php
			$statuses = $this->parent->get_component('statuses');
			$status = $statuses->get_status($row['Master_status']);
			?>
			<span title="Kattintással változtatás" class="status-button" style="background-color:<?php echo $status['color'];?>" data-id="<?php echo $row['ID'];?>" data-status="<?php echo $row["Master_status"]?>">
				<?php echo $status['label'];?>
				<!--<p class="change-message">Módosítás kattintással</p>-->
			</span>
			<?php
		}
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function save_mandate_comments($post = null) {
		
		//var_dump($post);
		
		$res = $this->mandates_db->save_comments($post);
		
		if($res !== false) {
			$ret['status'] = 'ok';
		} else {
			$ret['status'] = 'error';
		}
		
		return $ret;
		
	}
	
	public function single_mandate_to_master($mandate_id = NULL) {
	
		$mandate = $this->load_mandate($mandate_id);
		
		ob_start();
		//var_export($mandate);
		
		?>
		<div class="body">
			<h4><strong>Kiszállási cím adatok:</strong></h4>
			<h5><strong>Név: </strong><?php echo $mandate['CustomerName'];?></h5>
			<?php $address = $mandate['CustomerZipcode'] . " " . $mandate['CustomerCity'] . " " . $mandate['CustomerAddress'];?>
			<h5><strong>Cím:</strong>&nbsp;<?php echo $address;?></h5>
			<?php					
				$geocodes = $this->google->get_geocodes($address);
				//var_dump($geocodes);					
			?>
			<p><a class="btn btn-success master-detail-link address-map-link web-link" data-container-id="<?php echo $mandate['ID'];?>" data-lat="<?php echo $geocodes['lat'];?>" data-lng="<?php echo $geocodes['lng'];?>" href="javascript:void(0);">Térkép</a></p>
			<p><a style="display:none;" class="btn btn-success master-detail-link ios-mobile-link" data-container-id="<?php echo $mandate['ID'];?>" data-lat="<?php echo $geocodes['lat'];?>" data-lng="<?php echo $geocodes['lng'];?>" href="maps://maps.google.com/maps?daddr=<?php echo $geocodes['lat'];?>,<?php echo $geocodes['lng'];?>&amp;ll=">Térkép</a></p>
			<!--<p><a style="display:none;" class="btn btn-success master-detail-link android-mobile-link" data-container-id="<?php echo $mandate['ID'];?>" data-lat="<?php echo $geocodes['lat'];?>" data-lng="<?php echo $geocodes['lng'];?>" href="geo:<?php echo $geocodes['lat'];?>,<?php echo $geocodes['lng'];?>?z=zoom">Térkép</a></p>-->
			<p><a style="display:none;" class="btn btn-success master-detail-link android-mobile-link" data-container-id="<?php echo $mandate['ID'];?>" data-lat="<?php echo $geocodes['lat'];?>" data-lng="<?php echo $geocodes['lng'];?>" href="geo:0,0?q=<?php echo $geocodes['lat'];?>,<?php echo $geocodes['lng'];?>?">Térkép</a></p>
			<p>
			<div id="google-maps-container-<?php echo $mandate['ID'];?>" style="display:none;width:100%;height:400px;" class="maps-container"></div>
			</p>
			<p><a class="btn btn-success master-detail-link" href="tel:<?php echo $mandate['CustomerPhone'];?>">Telefon : <?php echo $mandate['CustomerPhone'];?></a></p>
			<!-- Feladatok -->
			<div class="row">
				<div class="x_panel">
					<div class="x_content">
						<h4>Installációk</h4>
						<p>&nbsp;</p>
						<?php echo $this->installations_data_cell($mandate);?>
					</div>
				</div>
			</div>
			<?php
				$subclient = $this->parent->get_component('subclients')->load_subclient($mandate['PartnerID']);
				//var_dump($subclient['Master_cash']);
			?>
			<?php if($subclient['Master_cash'] == 1):?>
			<p>Az installáció díját a mester veszi át a helyszínen!</p>
			<?php endif;?>
			<!-- státusz -->
			<div class="row">
				<div class="x_panel">
					<div class="x_content">
						<h4>Státusz</h4>
						<p>&nbsp;</p>
						<?php echo $this->status_cell($mandate,true);?>
					</div>
				</div>
			</div>
			<div class="row">
				<div id="filelist_container"></div>
			</div>
			<!--<input type="hidden" name="uploadform-mandate-id" value="<?php echo $mandate['ID']?>" />
			<p><a href="javascript:void(0);" class="proba-gomb btn btn-primary">Próba</a></p>-->
			<div class="row">
				
					<div class="x_panel">
						<div class="x_content">
							<form id="file-upload-form" action="" class="edit-form form-horizontal form-label-left" method="post" enctype="multipart/form-data">
							<div class="form-group form-float">
								<p>Fájl feltöltése:</p>
								
									<input type="file" class="btn btn-default" name="fileToUpload" id="fileToUpload">
									<input type="hidden" name="uploadform-mandate-id" value="<?php echo $mandate['ID']?>" />
									<input type="hidden" name="attachment-type" value="2" />
								
							</div>
							<div class="form-group form-float">
								<div class="error-msg"></div>
							</div>
							<div class="form-group form-float">
								
									<input type="submit" class="btn btn-primary" value="Filefeltöltés" name="file-upload-form-submit">
								
							</div>
							</form>
						</div>
					</div>
				
			</div>
			<!-- Comments -->
			<?php
			?>
			<div class="row">
				<div class="x_panel">
					<div class="x_content">
						<form id="mandate-comments-form" action="" class="edit-form form-horizontal form-label-left" method="post">
							<h5>Installáló megjegyzése:</h5>
							<div class="form-group form-float">
								<textarea name="master-comment" data-id="<?php echo $mandate['ID']?>" style="min-height:250px;" class="form-control"><?php echo $mandate['master_comment'];?></textarea>
							</div>
							<h5>Megrendelő megjegyzése:</h5>
							<div class="form-group form-float">
								<textarea name="customer-comment" data-id="<?php echo $mandate['ID']?>" style="min-height:250px;" class="form-control"><?php echo $mandate['customer_comment'];?></textarea>
							</div>
							<div class="form-group form-float">
								<input type="button" data-id="<?php echo $mandate['ID']?>" class="btn btn-primary" value="Mentés" name="mandate-comments-form-submit" />
							</div>
						</form>
					</div>
				</div>
			</div>
			
			
			<p><a class="btn btn-success master-detail-link handwrite-dialog-open" data-id="<?php echo $mandate['ID'];?>" href="javascript:void(0);">Aláírás</a></p>
			
			<p><a class="btn btn-primary master-detail-link" href="<?php echo $this->parent->create_url('master');?>">Vissza a listához</a></p>
		</div>
		<?php
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function mandate_table_to_master($filter) {
		
		$result = $this->mandates_db->list_mandates($filter);
		
		if($result == NULL) {
			echo "List mandates error";
			return;
		}
		
		ob_start();
		?>
		<div class="body">
			<table class="table table-bordered table-striped table-hover dataTable mandate-table-to-master">
				<thead>
					<tr>
						<td>Megbízások</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>Megbízások</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					while($row = mysql_fetch_assoc($result)) {
						?>
						<tr>
							<td>
								<h5><strong>Megbízás sz.: </strong><?php echo $row['Mandate_serial'];?></h5>
								<h4><strong>Kiszállási cím adatok:</strong></h4>
								<h5><strong>Név: </strong><?php echo $row['CustomerName'];?></h5>
								<h5><strong>Cím:</strong></h5>
								<?php $address = $row['CustomerZipcode'] . " " . $row['CustomerCity'] . " " . $row['CustomerAddress'];?>
								<p><?php echo $address;?></p>
								<?php
								
								$geocodes = $this->google->get_geocodes($address);
								
								?>
								<p><a class="btn btn-success master-detail-link address-map-link" data-container-id="<?php echo $row['ID'];?>" data-lat="<?php echo $geocodes['lat'];?>" data-lng="<?php echo $geocodes['lng'];?>" href="javascript:void(0);">Térkép</a></p>
								<p>
								<div id="google-maps-container-<?php echo $row['ID'];?>" style="display:none;width:100%;height:400px;" class="maps-container"></div>
								<!--<script>
								  function initMap() {
									// Create a map object and specify the DOM element for display.
									var map = new google.maps.Map(document.getElementById('google-maps-container-<?php echo $row['ID'];?>'), {
									  center: {lat: <?php echo $geocodes['lat']?>, lng: <?php echo $geocodes['lng']?>},
									  scrollwheel: false,
									  zoom: 15
									});
									marker = new google.maps.Marker({
                						map: map,
                						position: new google.maps.LatLng(<?php echo $geocodes['lat']; ?>, <?php echo $geocodes['lng']; ?>)
            						});
								  }
								</script>-->
								
								</p>
								<?php //var_dump($this->parent->create_url('master')); ?>
								<p><a class="btn btn-success master-detail-link" href="tel:<?php echo $row['CustomerPhone'];?>">Telefon : <?php echo $row['CustomerPhone'];?></a></p>
								<p><a class="btn btn-primary master-detail-link" href="<?php echo $this->parent->create_url('master') . "&id=" . $row["ID"]?>">Lépés a megbízásra</a></p>
								<?php

								?>
							</td>
						</tr>
						<?php
					?>
					<?php
					}
					?>
				</tbody>
			</table>	
		</div>
		
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	private function serial_cell($row) {
		
		$qrcode = $this->parent->get_component('qrcode');
		$pdf = $this->parent->get_component('pdf');
		
		//var_dump($qrcode->generate_mandate_master_qr($row['ID']));
		
		ob_start();
		?><p style="text-align:center;"><?php echo $row['Mandate_serial'];?></p><?php
		if($row['MasterID'] != NULL && $row['MasterID'] != 0 ) :
		?><p style="text-align:center;"><?php echo $qrcode->generate_mandate_master_qr($row['ID']);?></p><?php
		endif;
		?><!--<p style="text-align:center;width:100%;"><a href="javascript:void(0);" data-mandate-id="<?php echo $row['ID'];?>" class="btn btn-primary worksheet-link">Munkalap</a></p>--><?php
		//echo $pdf->generate_pdf_link($row['ID']);
		?><!-- <p style="text-align:center;width:100%;"><?php //echo $pdf->generate_pdf_link($row['ID']);?></p>--><?php
		?><p style="text-align:center;width:100%;"><a href="javascript:void(0);" data-mandate-id="<?php echo $row['ID'];?>" class="btn btn-primary worksheet-link">Munkalap</a></p><?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;	
		
	}
	
	public function filter_form($filter = NULL){
		
		$mandates = array();
		
		if(isset($_SESSION['HDT_form_filter'])) {
			$filter = $_SESSION['HDT_form_filter'];
		}
		
		$result = $this->mandates_db->list_mandates($filter);
		
		while($row = mysql_fetch_assoc($result)) {
			$mandates[] = $row;
		}
		
		// Szűrés partnerre
		
		$partners_in_list = array();
		
		foreach($mandates as $mandate) {
			if(!in_array($mandate['PartnerID'],$partners_in_list)) {
				$partners_in_list[] = $mandate['PartnerID'];
			}
		}
		
		$filter_partner = "";
		
		foreach($partners_in_list as $row) {
			if($filter_partner == "") {
				$filter_partner .= " ID = " . $row;
			} else {
				$filter_partner .= " OR ID = " . $row;
			}
		}
		
		// Szűrés alvállalkozóra
		
		$subcontactor_in_list = array();
		
		foreach($mandates as $mandate) {
			if(!in_array($mandate['SubcontactorID'],$subcontactor_in_list)) {
				$subcontactor_in_list[] = $mandate['SubcontactorID'];
			}
		}
		
		$filter_subcontactor = "";
		
		foreach($subcontactor_in_list as $row) {
			if($filter_subcontactor == "") {
				$filter_subcontactor .= " ID = " . $row;
			} else {
				$filter_subcontactor .= " OR ID = " . $row;
			}
		}
		//var_dump($filter_subcontactor);
		
		
		
		// Szűrés mesterre
		
		$masters_in_list = array();
		
		foreach($mandates as $mandate) {
			if(!in_array($mandate['MasterID'],$masters_in_list)) {
				$masters_in_list[] = $mandate['MasterID'];
			}
		}
		
		$filter_master = "";
		
		foreach($masters_in_list as $row) {
			if($filter_master == "") {
				$filter_master .= " ID = " . $row;
			} else {
				$filter_master .= " OR ID = " . $row;
			}
		}

		
		ob_start();
		?>
		<div class="x_panel">
		<div class="x_title">
			<h2>Szűrés</h2>
			<ul class="nav navbar-right panel_toolbox">
                <li>
					<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
                <?php //if(isset($_POST['mandate-filter'])):?>
                <!-- <li>
					<a href="http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>&erasefilter" title="Szűrő törlése" class=erase-filter-link"><i class="fa fa-close"></i></a>
                </li>-->
                <?php //endif;?>
            </ul>
			<div class="clearfix"></div>
		</div>
			<div class="x_content closed_x_content">
				<?php //var_dump($_POST);?>
				<form id="mandate-filterform" class="edit-form form-horizontal form-label-left" method="post" novalidate="novalidate" action="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>">
					<div class="form-group form-float">
						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="partner-id">Partner: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
							<select name="mandate-filter[PartnerID]" class="form-control">
								<option value="" default>Nincs szűrve erre</option>
								<?php //echo $this->parent->get_component('parcel')->partner_select(array($filter_partner),true);?>
								<?php echo $this->parent->get_component('subclients')->filter_subclient_select(array($filter_partner,true));?>
							</select>
						</div>
					</div>
					<div class="form-group form-float">
						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="subcontactor-id">Alvállalkozó: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
							<select name="mandate-filter[SubcontactorID]" class="form-control">
								<option value="" default>Nincs szűrve erre</option>
								<?php echo $this->parent->get_component('subcontactor')->subcontactors_select(array($filter_subcontactor));?>
							</select>
						</div>
					</div>
					<div class="form-group form-float">
						<label class="control-label col-md-3 col-sm-3 col-xs-12" for="master-id">Mester: </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
							<select name="mandate-filter[MasterID]" class="form-control">
								<option value="" default>Nincs szűrve erre</option>
								<?php echo $this->parent->get_component('master')->masters_select(array($filter_master));?>
							</select>
						</div>
					</div>
					<div class="ln_solid"></div>
					<div class="form-group form-float">
						<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
							<button id="mandate-form-submit" class="btn btn-success" type="submit">Szűrés</button>
						</div>
					</div>
				</form>
			</div><!-- x_content -->
		</div><!-- x_panel -->
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function ordering_buttons() {
		
		ob_start();
		?>
		<div class="x_panel">
		<div class="x_title">
			<h2>Rendezés</h2>
			<ul class="nav navbar-right panel_toolbox">
                <li>
					<a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
			<div class="clearfix"></div>
		</div>
		<div class="x_content closed_x_content">
		<?php
		$act_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$btn_style = "btn-default";
		preg_match("/\/\?(.*?)\&order/i",$act_url,$gets);
		if(!isset($gets[1])) {
			preg_match("/\/\?(.*?)$/i",$act_url,$gets);
		}
		$act_url = 'http://' . ROOT_URL . '/?' . $gets[1];
		
		$direction = "";
		
		
		
		if($_GET['order'] == 'ID'){
			$btn_style = "btn-success";
			if($_GET['direction'] == 'ASC') {
				$direction = '&desc';
				$title = "Csökkenő sorrendben";	
			} else {
				$direction = "";
				$title = "";
			}	
		} else {
			$btn_style = "btn-default";
			$title = "";
			$direction = "";			
		}
			
		?><a title="<?php echo $title;?>" href="<?php echo $act_url . '&order=ID' . $direction;?>" class="btn <?php echo $btn_style;?>">Azonosító</a><?php
		
		
		if($_GET['mode'] != 'unconfirmed') {
			
			if($_GET['order'] == 'MasterID'){
				$btn_style = "btn-success";
				if($_GET['direction'] == 'ASC') {
					$direction = '&desc';
					$title = "Csökkenő sorrendben";	
				} else {
					$direction = "";
					$title = "";
				}	
			} else {
				$btn_style = "btn-default";
				$title = "";
				$direction = "";			
			}
			
			
			
			?><a title="<?php echo $title;?>" href="<?php echo $act_url . '&order=MasterID' . $direction;?>" class="btn <?php echo $btn_style;?>">Mester</a><?php
			
			if($_GET['order'] == 'Master_status'){
				$btn_style = "btn-success";
				if($_GET['direction'] == 'ASC') {
					$direction = '&desc';
					$title = "Csökkenő sorrendben";	
				} else {
					$direction = "";
					$title = "";
				}	
			} else {
				$btn_style = "btn-default";
				$title = "";
				$direction = "";			
			}
			
			
			
			?><a title="<?php echo $title;?>" href="<?php echo $act_url . '&order=Master_status' . $direction;?>" class="btn <?php echo $btn_style;?>">Státusz</a><?php
		}
		if($_GET['order'] == 'CustomerZipcode'){
			$btn_style = "btn-success";
			if($_GET['direction'] == 'ASC') {
				$direction = '&desc';
				$title = "Csökkenő sorrendben";	
			} else {
				$direction = "";
				$title = "";
			}	
		} else {
			$btn_style = "btn-default";
			$title = "";
			$direction = "";			
		}
		
		?><a title="<?php echo $title;?>" href="<?php echo $act_url . '&order=CustomerZipcode' . $direction;?>" class="btn <?php echo $btn_style;?>">Irányítószám</a><?php
		
		if($_GET['order'] == 'Kiszallitas'){
			$btn_style = "btn-success";
			if($_GET['direction'] == 'ASC') {
				$direction = '&desc';
				$title = "Csökkenő sorrendben";	
			} else {
				$direction = "";
				$title = "";
			}	
		} else {
			$btn_style = "btn-default";
			$title = "";
			$direction = "";			
		}
		
		?><a title="<?php echo $title;?>" href="<?php echo $act_url . '&order=Kiszallitas' . $direction;?>" class="btn <?php echo $btn_style;?>">Kiszállítás</a><?php
		?>
		</div><!-- x_content -->
		</div><!-- x_panel -->
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	private function routing($filter = NULL) {
		
		$mandates = array();
		
		if(isset($_SESSION['HDT_form_filter'])) {
			$filter = $_SESSION['HDT_form_filter'];
		}
		
		$result = $this->mandates_db->list_mandates($filter);
		
		while($row = mysql_fetch_assoc($result)) {
			$mandates[] = $row;
		}
		
		$masters_in_list = array();
		
		foreach($mandates as $mandate) {
			if(!in_array($mandate['MasterID'],$masters_in_list)) {
				$masters_in_list[] = $mandate['MasterID'];
			}
		}
		
		ob_start();
		$points = array();
		foreach($masters_in_list as $master_id) {
			$master = $this->parent->get_component('master')->load_master($master_id);
			$subcontactor = $this->parent->get_component('subcontactor')->load_subcontactor($master['Subconid']);
			$points[] = $subcontactor['Address'];
			foreach($mandates as $mandate) {
				if($mandate['MasterID'] == $master['ID']) {
					$points[] = $mandate['CustomerZipcode']." ".$mandate['CustomerCity']." ".$mandate['CustomerAddress'];
				}
			}
			?>
			<div class="x_panel">
				<div class="x_title">
					<h2><?php echo $master['Name']?> útvonala</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li>
							<a data-addresses="<?php echo serialize($points);?>" data-id="<?php echo $master['ID'];?>" class="collapse-link"><i class="fa fa-chevron-down"></i></a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div id="routing-content-<?php echo $master['ID'];?>" class="x_content closed_x_content"></div><!-- x_content -->
			</div><!-- x_panel -->
			<?php
		}
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	private function alert_cell($row = NULL) {
		
		ob_start();
		
		$alerts = array();
		
		$warnings = array();
		
		// Szállítás lezárás érkezett a parcel-ből
		
		if($row['Kiszallitva'] == 1 && $row['Master_status'] < 1) {
			$alerts[] = "A fuvar rezárult, de a megbízás még nincs kiosztva mesterre!";
		}
		
		if($row['is_parcel_storno'] == 1) {
			$alerts[] = "A fuvart stornózták!";
		}
		
		if($row['Kiszallitva'] == 1 && $row['Master_status'] < 2) {
			$warnings[]= "A fuvar lezárult, de még nincs időpont egyeztetve!";
		}
		
		if(count($alerts) > 0):

		?><span style="width:100%;text-align:center;display:inline-block;"><a data-toggle="modal" data-target=".alert-modal-<?php echo $row['ID']?>" title="Kattintson a megnyitáshoz" class="alert-link badge bg-red"><?php echo count($alerts);?></a></span><?php

		?>
		<div class="modal fade alert-modal-<?php echo $row['ID']?>" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="ModalLabel-<?php echo $row['ID'];?>">Figyelmeztetés</h4>
                    </div>
                    <div class="modal-body">
                         <?php
							foreach($alerts as $alert) {
								echo '<p>'.$alert;?></p><?php
							}
						?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Bezárás</button>
                    </div>
                </div>
            </div>
		</div>
		<?php
		endif;
		?><!--<div style="clear:both;min-height:10px;"></div>--><?php
		if(count($alerts) == 0 && count($warnings) > 0):
		?><span style="width:100%;text-align:center;display:inline-block;"><a data-toggle="modal" data-target=".warning-modal-<?php echo $row['ID']?>" title="Kattintson a megnyitáshoz" class="alert-link badge bg-yellow"><?php echo count($warnings);?></a></span><?php
		?>
		<div class="modal fade warning-modal-<?php echo $row['ID']?>" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="ModalLabel-<?php echo $row['ID'];?>">Várjon!</h4>
                    </div>
                    <div class="modal-body">
                         <?php
							foreach($warnings as $warning) {
								echo '<p>'.$warning;?></p><?php
							}
						?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Bezárás</button>
                    </div>
                </div>
            </div>
		</div>
		<?php
		endif;
		
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
		return;
		
	}
	
	public function load_compact_view($post = NULL) {
		
		$mandate = $this->load_mandate($post['id']);
		
		ob_start();
		//var_dump($mandate);
		?>
		<table style="width:auto;min-width:600px;">
			<tr>
				<td style="vertical-align:top;padding:10px;"><?php echo $this->serial_cell($mandate);?></td>
				<td style="vertical-align:top;padding:10px;"><?php echo $this->customer_data_cell($mandate);?></td>
				<td style="vertical-align:top;padding:10px;"><?php echo $this->installations_data_cell($mandate);?></td>
			</tr>
		</table>
		<?php
		
		$content = ob_get_contents();
		ob_end_clean();
		
		$ret = array();
		
		$ret['status'] = 'OK';
		$ret['content'] = $content;
		
		return $ret;
		
	}
	
	public function mandates_table_source($post = NULL) {
		
		$response = array();
		
		if(isset($_SESSION['HDT_form_filter'])) {
			$filter = $_SESSION['HDT_form_filter'];
		}
		
		if(isset($post['order'][0])) {
			for($i = 0;$i < count($filter);$i++){
				if(strpos($filter[$i],'ORDER BY') !== false){
					switch($post['order'][0]['column']){
						case 0:
							$field = 'ID';
							break;
						case 1:
							$field = 'Master_status';
							break;
						case 2:
							$field = 'ID';
							break;
						case 3:
							$field = 'ID';
							break;
						case 4:
							$field = 'CustomerCity';
							break;
						case 5:
							$field = 'Kiszallas_Date';
							break;
						default:
							$field = '';
							break;
					}
					if($field != '') {
						$filter[$i] = "ORDER BY `".$field."` ".strtoupper($post['order'][0]['dir']);
					}
				}
			}
		}
		
		$where = $filter[0];
		
		$filter[0] = "(".$where.")";
		//var_dump($filter);
		
		$result = $this->mandates_db->list_mandates();
		
		$response['recordsTotal'] = mysql_num_rows($result);
		
		if(isset($post['search']) && $post['search']['value'] != "") {
			
			$searched_sql = $this->mandates_db->searched_sql($post['search']['value']);
			
			//var_dump($searched_sql);
			
			$filter[0] .= " AND (".$searched_sql.")";

		}
		
		$result = $this->mandates_db->list_mandates($filter);
		
		$response['recordsFiltered'] = mysql_num_rows($result);
		
		
		if( isset($post['start']) && $post['length'] != -1) {
		
			$limit = "LIMIT " . $post['start'] . ", " . $post['length'];
			
			$filter[] = $limit;
		
		}
		//var_dump($filter);return;
		$result = $this->mandates_db->list_mandates($filter);
		
		
		
		$response['draw'] = $post['draw'];
		
		$response['data'] = array();
		
		// Compact mandate list
		
		$user = $this->parent->get_component('user')->load_user($_SESSION['HDT_uid']);
		
		if($post['screen_width'] > 768 && $user['List_style'] == 1 ) {
			
			$compactlist = $this->parent->get_component('compactmandatelist');
			
			while($row = mysql_fetch_assoc($result)) {
				
				$data = array();
				
				$data[] = $compactlist->id_cell($row);
				$data[] = $compactlist->data_cell($row);
				$data[] = $compactlist->action_cell($row);
				
				$response['data'][] = $data;
			
			}
			
			if(isset($_SESSION['mode'])) {
				unset($_SESSION['mode']);
			}
			
			return $response;	
			
		}
		
		while($row = mysql_fetch_assoc($result)) {
			
			if(isset($post['screen_width'])){
				$row['screen_width'] = $post['screen_width'];
			}
			if($post['screen_width'] > 768 ){
				$data = array();
				
				$data[] = $row['ID'];
				//$data[] = "";
				$data[] = $this->status_cell($row);
				//$data[] = "";
				$data[] = $this->serial_cell($row);
				//$data[] = "";
				$data[] = $this->hdt_data_cell($row);
				//$data[] = "";
				$data[] = $this->customer_data_cell($row);
				//$data[] = "";
				$data[] = $this->installations_data_cell($row);
				//$data[] = "";
				$data[] = $this->mandate_data_cell($row);
				//$data[] = "";
				$data[] = $this->action_cell($row);
			} else {
				$data = array();
				$data[] = $this->mobile_cell($row);
				//$data[] = $row['ID'];
				$data[] = $this->action_cell($row);
			}
			
			/*if(isset($post['search']['value']) && $post['search']['value'] != "") {
				foreach($data as $row) {
					//var_dump(mb_strtoupper($row,'UTF-8'));
					$pos = strpos(mb_strtoupper($row,'UTF-8'),mb_strtoupper($post['search']['value'],'UTF-8'));
					var_dump($pos);
					if($pos > 0) {
						$addind = true;
					} else {
						$adding = false;
					}
				}
				if($adding === true) {
					$response['data'][] = $data;
				}
			} else {*/
				$response['data'][] = $data;
			//}	
		}
		
		//var_dump($response);
		
		if(isset($_SESSION['mode'])) {
			unset($_SESSION['mode']);
		}
		
		return $response;
	}
	
	private function mobile_cell($row = NULL) {
		
		ob_start();
		?>
		<p>ID: <?php echo $row['ID']?></p>
		<p>Azonosító: <?php echo $row['Mandate_serial']?></p>
		<?php
		if($row['Master_status'] == 0) {
		?><p>A megbízás még nincs kiosztva</p><?php
		} else {
			$status = $this->statuses->get_status($row['Master_status']);
			//var_dump($status);
			?>
					
			<div>
			<p><?php //echo $status['label'];?></p>
			<?php 
			$master = $this->master_db->get_master($row['MasterID']);
			?>
			<!--<p><b>Mester:</b></p>
			<p><?php //echo $master['Name'];?></p>
			<p><i class="fa fa-phone"></i>&nbsp;<?php echo $master['Phone'];?></p>
			</div>-->
			<?php
			$statuses = $this->parent->get_component('statuses');
			$status = $statuses->get_status($row['Master_status']);
			?>
			<span title="Kattintással változtatás" class="status-button" style="background-color:<?php echo $status['color'];?>" data-id="<?php echo $row['ID'];?>" data-status="<?php echo $row["Master_status"]?>">
			<?php echo $status['label'];?>
			<!--<p class="change-message">Módosítás kattintással</p>-->
			</span>
			<?php
		}
		?>
		<div id="info-cell-<?php echo $row['ID']?>" class="mobile-table-info-cell" >
		<p>&nbsp;</p>
		<p><strong>HDT adatok</strong></p>
		<p>&nbsp;</p>
		<?php 
		if(!isset($row['HDT_order_id']) || $row['HDT_order_id'] == NULL) {
			?><p>Nincs fuvarhoz rendelve</p><?php
		} else {
			?><p>Fuvarszám :<strong><?php echo $row['HDT_order_id'];?></strong></p><?php
		}
		if(!isset($row['PartnerID']) || $row['PartnerID'] == NULL) {
			?><p>Nincs partnerhez rendelve</p><?php
		} else {
			//$partner = $this->parcel_db->get_partner($row['PartnerID']);
			$subclient = $this->parent->get_component('subclients')->load_subclient($row['PartnerID']);
			?><p>Almegbízó :<strong><?php echo $subclient['Name'];?></strong></p><?php
		}
		if($row['SubcontactorID'] != NULL) {
			$subcontactor = $this->subcontactor_db->get_subcontactor($row['SubcontactorID']);
			//var_dump($subcontactor);
			?><p>Aut. kiosztott alv.: <b><?php echo $subcontactor["Name"]?></b></p><?php
		} else {
			?><p style="color:red;">Nincs alvállalkozóra kiosztva</p><?php
		}
		?>
		<p>&nbsp;</p>
		<p><strong>Installáció helyének adatai:</strong></p>
		<p>&nbsp;</p>
		<?php
		?><p>Név: <strong><?php echo $row['CustomerName'];?></strong></p><?php
		?><p>Cím: <?php echo $row['CustomerZipcode'];?> <?php echo $row['CustomerCity']?> <?php echo $row['CustomerAddress'];?></p><?php
		?><p>Tel.: <?php echo $row['CustomerPhone'];?></p><?php
		?><p>E-mail: <?php echo $row['CustomerEmail'];?></p><?php
		?>
		
		<p>&nbsp;</p>
		<p><strong>A kiszállítás adatai:</strong></p>
		<p>&nbsp;</p>
		<?php
		?><p>Kiszállítás: <?php echo date('Y.m.d',strtotime($row['Kiszallitas']));?></p><?php
		if($row['Kiszallitva'] == 0):
		?><p>Még nincs kiszállítva!</p><?php
		else:
		?><p>A futár lezárta a fuvart!</p><?php
		endif;
		if($row['Kiszallas_Date'] == NULL):
		?><p>Nincs kiszállás egyeztetve</p><?php
		else:
		?><p>Egyeztetett dátum: <?php echo date('Y.m.d',strtotime($row['Kiszallas_Date']));?></p><?php
		endif;
		?>
		<a style="width:100%;" data-id="<?php echo $row['ID'];?>" class="btn btn-default info-cell-close" href="javascript:void(0);">Bezár</a>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function mandate_table($filter = NULL) {
		
		//var_dump($this->mandates_db->connection);exit;
		
		$result = $this->mandates_db->list_mandates($filter);
		
		if($result == NULL) {
			echo "List mandates error";
			return;
		}
		
		//ob_start();
		
		?>
		<!--<pre><?php $this->statuses->list_statuses();?></pre>-->
		<div class="body">
			<?php
			if(mysql_num_rows($result) != 0) {
				
				/*echo $this->filter_form($filter);
				?><div class="clearfix"></div><?php
				echo $this->ordering_buttons();
				?><div class="clearfix"></div><?php
				if($_GET['mode'] == 'confirmed'):
				echo $this->routing($filter);
				?><div class="clearfix"></div><?php
				endif;*/
			}
			?>
			<table class="table table-bordered table-striped table-hover dataTable mandate-table">
				<thead>
					<tr>
						<td>Id</td>
						<td>&nbsp;</td>
						<td>Státusz</td>
						<td>Azonosító</td>
						<td>HDT adatok</td>
						<td>Vevő adatai</td>
						<td>Termékek/Installációk</td>
						<td>Megbízás adatai</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>Id</td>
						<td>&nbsp;</td>
						<td>Státusz</td>
						<td>Azonosító</td>
						<td>HDT adatok</td>
						<td>Vevő adatai</td>
						<td>Termékek/Installációk</td>
						<td>Megbízás adatai</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				while($row = mysql_fetch_assoc($result)) {
					?>
					<tr>
						<td class="middle-align" data-sort="<?php echo $row['ID'];?>"><span style="display:inline-block;width:100%;height:100%;vertical-align:middle;text-align:center;"><?php echo $row['ID'];?></span></td>
						<td><?php echo $this->action_cell($row);?></td>
						<td class="status-cell"><?php echo $this->status_cell($row);?></td>
						<td><?php echo $this->serial_cell($row);?></td>
						<td><?php //echo $this->hdt_data_cell($row);?></td>
						<td data-sort="<?php echo $row['CustomerZipcode']?>"><?php echo $this->customer_data_cell($row);?></td>
						<td><?php echo $this->installations_data_cell($row);?></td>
						<td><?php echo $this->mandate_data_cell($row);?></td>
					<?php
					
					?>
					</td>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
		<?php
		
		//$content = ob_get_contents();
		//ob_end_clean();
		
		//return $content;
		return;
	}
	
	public function mandate_product_fields($post = NULL) {
		
		if($post == NULL || !isset($post['id'])) {
			$ret['status'] = 'error';
			return $ret;
		}
		
		return $ret;
		
		$items = $this->mandates_db->get_details($post['catid']);
		
		/*$content = '<div class="form-group form-float">
						<div class="col-md-3 col-sm-3 col-xs-12"></div>
						<div class="col-md-3 col-sm-3 col-xs-12">Termék</div>
						<div class="col-md-3 col-sm-3 col-xs-12">Installáció</div>
				   </div>';*/
		
		$content = '<div class="form-group form-float">
						<div class="col-md-3 col-sm-3 col-xs-12"></div>
						<div class="col-md-3 col-sm-3 col-xs-12">Termék</div>
				   </div>';
		
		$i = 1;
		foreach($items as $item) {

			$content .= '<div id="item-row-'.$i.'" class="form-group form-float">
				<div class="col-md-3 col-sm-3 col-xs-12"></div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<input placeholder="Megnevezés" type="text" class="form-control item-name" id="mandate-product-name-'.$i.'" name="mandate-product-name['.$i.']" value="'.$item['ProductName'].'" required />
				</div>
				<div class="col-md-3 col-sm-3 col-xs-12">
					<span id="select-container-'.$i.'">'.$this->installations->installations_select($i++,$item['InstallationID']).'</span> 
				</div>
			</div>';

		}

		$ret['status'] = 'true';
		$ret['content'] = $content;
		$ret['count'] = $i;
		
		return $ret;
		
	}
	
	public function mandate_lock($id = NULL) {
		
		return $this->mandates_db->lock($id);
	}
	
	public function mandate_unlock($post){
		return $this->unlock($post['id']);
	}
	
	public function unlock($id = NULL) {
		$this->mandates_db->unlock($id);
	}
	
	public function locked_mandate($id = NULL) {
		return $this->mandates_db->locked($id);
	}
	
	public function confirm($post) {
		
		$post['status'] = MANDATE_GET_MASTER;
		
		$statushistory = $this->parent->get_component('statushistory');

		$mandate = $this->mandates_db->get_mandate($post['id']);
		
		$history['mandate_id'] = $mandate['ID'];
		
		$history['old_status'] = $mandate['Master_status'];
		
		$history['new_status'] = $post['status'];
		
		$ret = $this->mandates_db->confirm($post);

		//var_dump($mandate);
		
		if($ret != false) {
			$statushistory->statuschange_add($history);
			
			$mandate = $this->mandates_db->get_mandate($post['id']);
			
			$this->parent->get_component('email')->change_status_email($mandate['ID']);
			
		}
		
		return $ret;
	}
	
	public function unconfirm($post) {
		
		$ret = $this->mandates_db->unconfirm($post);
		
		if($ret == false) {
			$_SESSION['HDT_error_message'] = "Sikertelen megbízás kiosztás visszavétel!";
			return false;	
		} else {
			$_SESSION['HDT_ok_message'] = "A mester és a státusz törlődött. A megbízás visszakerült a várakozók közé!";
			return true;
		}
		
	}
	
	public function load_statuses($post) {
		
		if(isset($post['master'])) {
			$master = true;
		} else {
			$master = false;
		}
		
		$statuses = $this->parent->get_component('statuses')->list_statuses(true,$master);
		//var_dump($master);return;
		
		$mandate = $this->mandates_db->get_mandate($post['id']);
		
		$subclient = $this->parent->get_component('subclients')->load_subclient($mandate['PartnerID']);
		
		if($subclient['Master_cash'] == NULL || $subclient['Master_cash'] == 0){
			unset($statuses[CASH_ON_MASTER]);
		}
		
		ob_start();
		//var_dump($statuses);
		?>
		<div class="diolag-content-container">
			<p style="display:inline-block;min-height:10px;margin:0;"></p>
		<?php
		foreach($statuses as $key=>$status) {
			if($key != $post['act_status']) {
				?>
				<span class="change-status-button status-button" data-id="<?php echo $post['id']?>" data-status="<?php echo $key?>" style="background-color:<?php echo $status['color'];?>;"><?php echo $status['label']?></span>
				<p style="display:inline-block;min-height:10px;margin:0;"></p>
				<?php
			}
		}
		?></div><?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function change_status($post) {
		
		//$mandate = $this->mandates_db->get_mandate($post['id']);
		
		$statushistory = $this->parent->get_component('statushistory');

		$mandate = $this->mandates_db->get_mandate($post['id']);
		
		$history['mandate_id'] = $mandate['ID'];
		
		$history['old_status'] = $mandate['Master_status'];
		
		$history['new_status'] = $post['new_status'];
		
		$ret = $this->mandates_db->update_mandate_status($mandate['ID'],$post['new_status']);
		
		if($ret != false) {
			$statushistory->statuschange_add($history);
			
			$this->parent->get_component('email')->change_status_email($mandate['ID']);
			
			$ret = $post['new_status'];
		} else {
			return $ret;
		}
		
	}
	
	public function delivery_has_arrived($post) {
		
		$ret = $this->mandates_db->has_arrived($post);
		
		if($ret == false) {
			return 'Error';
		} else {
			return 'Ok';
		}
	}
	
	public function delivery_has_storno($post) {
		
		$ret = $this->mandates_db->has_storno($post);
		
		if($ret == false) {
			return 'Error';
		} else {
			return 'Ok';
		}
	}
	
	public function load_geocords($post) {
		
		$master = $this->parent->get_component('master')->load_master($post['id']);
		$subcontactor = $this->parent->get_component('subcontactor')->load_subcontactor($master['Subconid']);
		$filter = array(
						"MasterID = " . $post['id'],
						"Master_status > 0",
						"Master_status < 5",
		);
		$res = $this->mandates_db->list_mandates($filter);
		$mandates = array();
		while($row = mysql_fetch_assoc($res)) {
			$mandates[] = $row;
		}
		$points = array();
		$points[] = $this->google->get_geocodes($subcontactor['Address']);
		foreach($mandates as $mandate) {
			$address = $mandate['CustomerZipcode']." ".$mandate['CustomerCity']." ".$mandate['CustomerAddress'];
			$points[] = $this->google->get_geocodes($address);
		}
		
		return json_encode($points);
		
		exit;
		
	}
	
	public function load_routing($post) {
		
		ob_start();
		
		$master = $this->parent->get_component('master')->load_master($post['id']);
		$subcontactor = $this->parent->get_component('subcontactor')->load_subcontactor($master['Subconid']);
		//$google = $this->parent->get_component('google');
		
		$filter = array(
						"MasterID = " . $post['id'],
						"Master_status > 0",
						"Master_status < 5",
		);
		$res = $this->mandates_db->list_mandates($filter);
		$mandates = array();
		while($row = mysql_fetch_assoc($res)) {
			$mandates[] = $row;
		}
		$points = array();
		//var_dump($mandates);
		$points[] = $this->google->get_geocodes($subcontactor['Address']);
		foreach($mandates as $mandate) {
			$address = $mandate['CustomerZipcode']." ".$mandate['CustomerCity']." ".$mandate['CustomerAddress'];
			//var_dump($this->google->get_geocodes($address));
		}
		?>
				<div id="routing-map-container-<?php echo $post['id']?>" class="col-md-9 col-sm-12 col-xs-12 routing-map-container"></div>
				<div class="col-md-3 col-sm-12 col-xs-12">
				<p>Indulás innen:<?php echo $subcontactor['Address'];?></p>
				<ul id="routing-sortable-<?php echo $master['ID'];?>">
				<?php
				foreach($mandates as $mandate) {
					if($mandate['MasterID'] == $master['ID']) {
						$address = $mandate['CustomerZipcode']." ".$mandate['CustomerCity']." ".$mandate['CustomerAddress'];		
						//$geocodes = $this->google->get_geocodes($address);
						//var_dump($geocodes);
						?><li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><?php echo $address;?></li><?php
						//$points[] = $this->google->get_geocodes($address);
					}
				}
				?>
				</ul>
				</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	public function installations_for_pdf($mandate) {
		
		$installations = unserialize($mandate['Mandate_installations']);
		
		$insts = array();
		
		foreach($installations as $key=>$value) {
			//$val = $value;
			$inst = $this->parent->get_component('installations')->get_installation_item($key);
			//$inst['Value'] = $val;
			$insts[$inst['CatID']][] = $inst;
			//$insts[] = $inst['CatID'];
		}
		
		return $insts;
		
	}
	
	public function installations_for_update($mandate = null) {
		
		ob_start();
		
		$installations = unserialize($mandate['Mandate_installations']);
		
		?>
		<table id="products-list" class="installations-table">
           	<thead>
             	<tr>
	          		<td>Termék</td>
	          		<td>Installáció</td>
	          		<td>&nbsp;</td>
	         	</tr>
           	</thead>
           	<tbody>
		<?php
		
		$insts = array();
		//var_dump($installations);
		foreach($installations as $key=>$value) {
			$val = $value;
			$inst = $this->parent->get_component('installations')->get_installation_item($key);
			$inst['Value'] = $val;
			$insts[$inst['CatID']][] = $inst;
		}
		
		//var_dump($insts);
		$netto_cost = 0;
		foreach($insts as $key=>$row){
		$random = $this->generateRandomString(5);
		$installation_cat = $this->installations->load_installation($key);
		//var_dump($key);
		//var_dump($installations);
		//var_dump($row);
		//var_dump($installation_cat);
		
		$cost_array = explode('|',$installations[$row[0]['ID']]);
		
		//var_dump($cost_array);
		
		if(!isset($cost_array[1])) {
			$cost_array[1] = 1;
		}
		
		$catname_label = $installation_cat['CategoryName'] . " - " . $cost_array[0] . " Ft - " . $cost_array[1] . " db";
		
		?>
		<tr id="installation-row-<?php echo $random;?>">
			<!--<td><?php //echo $installation_cat['CategoryName'];?> <?php //echo $installations[$row[0]['ID']];?> Ft</td>-->
			<td><?php echo $catname_label;?></td>
			<td>
			<ul>
			<?php
			foreach($row as $item){
				
				// Calculate cost
				
				//var_dump($installations[$item['ID']]);
				
				$item_cost_array = explode('|',$installations[$item['ID']]);
				
				$to_address = $mandate['CustomerZipcode']." ".$mandate['CustomerCity']." ".$mandate["CustomerAddress"];
				
				//
				if(isset($item_cost_array[2])) {
					if(isset($item_cost_array[1])){
						$cost = $item_cost_array[2] * $item_cost_array[1];
					} else {
						$cost = $item_cost_array[2];
					}
				} else {
					$cost = $this->parent->get_component('clientoptions')->get_option($item['ID'],$mandate['PartnerID'],$to_address,$installations[$item['ID']]);
				}
				$netto_cost = $netto_cost + $cost;
				if(!isset($item_cost_array[1])) {
					$item_cost_array[1] = '1';
				}
				if(!isset($item_cost_array[2])) {
					$item_cost_array[2] = $cost;
				}
				?><li><?php echo $item['InstallationName'];?> <?php echo $cost;?> Ft</li><input type="hidden" data-id="<?php echo $item['ID'];?>" name="installation[<?php echo $item['ID'];?>]" value="<?php echo $item_cost_array[0];?>|<?php echo $item_cost_array[1];?>|<?php echo $item_cost_array[2];?>" />
				<input data-id="<?php echo $item['ID'];?>" type="hidden" name="installation-cost[<?php echo $item['ID'];?>]" value="<?php echo $cost;?>" />
				<?php
			}
			?>
			</ul>
			<input type="hidden" name="product-value[<?php echo $installation_cat['ID']?>]" value="<?php echo $cost_array[0];//echo $installations[$item['ID']];?>" />
			
			<input type="hidden" name="product-piece[<?php echo $installation_cat['ID']?>]" value="<?php echo $cost_array[1];?>" />
			
			<input type="hidden" name="installation-product[<?php echo $installation_cat['ID']?>]" value="<?php echo $random;?>" />
			</td>
			<td><a class="installation-row-delete" data-nolock="" data-row_id="<?php echo $random?>" href="javascript:void(0);" style="width:100%;text-align:center;display:inline-block;"><span class="glyphicon glyphicon-remove" aria-hidden="true" style="color:red;"></span></a></td>
		</tr>
		<?php
		}
		//var_dump($netto_cost);exit;
		?>
			</tbody>
        </table>
         <div class="ln_solid"></div>
        <table style="border:none;width:100%;">
			<tr>
				<td style="width:50%;text-align:right;">
					<h5>Szolgáltatások díja (netto): <span id="summa-cost-netto"><?php echo $netto_cost;?></span>&nbsp;Ft</h5>
					<!-- <h5>ÁFA (27%): <span id="summa-cost-afa">0</span>&nbsp;Ft</h5>
					<h5><strong>Fizetendő: <span id="summa-cost">0</span>&nbsp;Ft</strong></h5>-->
				</td>
			</tr>
		</table>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
		
	}
	
	public function get_installation_cost($post = NULL) {
		
		$clientoptions = $this->parent->get_component('clientoptions')->get_option($post['inst_id'],$post['subcli_id'],$post['to'],$post['prod_val'],$post['prod_piece']);

		return $clientoptions;
	}
	
	private function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	public function file_upload($post = null,$files = null) {
		
		$ret = "";
		
		//var_dump($post);
	
		return $ret;
	}
	
}
?>
