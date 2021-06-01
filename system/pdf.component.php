<?php
require_once('tools/fpdf/fpdf.php');

class Worksheet extends FPDF {
	
	private $mandate_id;
	
	private $mandate;
	
	private $parent;
	
	public function __construct($mandate_id = NULL, $parent = NULL) {
		
		$this->mandate_id = $mandate_id;
		
		$this->parent = $parent;
		
		$this->mandate = $this->load_mandate();
		
		parent::__construct();
	}
	
	private function load_mandate() {
		
		if($this->mandate_id != NULL) {
			
			$mandate_component = $this->parent->get_component('mandates');
			
			return $mandate_component->load_mandate($this->mandate_id);
		}
		
		
	}
	
	public function Header(){
                $X = 10;
                $Y = 10;
                $this->SetXY($X,$Y);
                $this->SetFont('arial','B',17);
                $this->Cell(0,0,self::ConvertText('Munkalap'),0,0,'l');
                $X = 72;
                $this->SetXY($X,$Y);
                $this->SetFont('arial','B',11);
                $this->Cell(0,0,self::ConvertText('Megrendelés szám: '.$this->mandate['Mandate_serial']),0,0,'l');
                $this->Ln(5);
        }

    public function Footer(){
        $this->SetFont('arial','',7);
        $this->SetY(-15);
        $this->Cell(0,20,self::ConvertText($this->PageNo().' oldal   -   Home Delivery Team - www.homedt.hu'),0,0,'l');
    }
		
	public static function ConvertText($param){
        return iconv('UTF-8','ISO-8859-2//IGNORE',$param);
    }
		
	private function DrawQrCodes(){
          /*if($this->enableQR){
               QRcode::png(Yii::app()->params['BASE_URL'].'index.php?r=qr/read&id='.$this->data->orders_id, 'qr/'.$this->data->orders_id.'.png', 'H', 10,10);
               // méret módosítása
               $this->Image('qr/'.$this->data->orders_id.'.png',172,5,35,35);		
               // for scanner	
               // Todo: Csupor Béla		
               $qr_x = 35;
               if(Yii::app()->params['NEW_DIRECTORY_STRUCT'] && $this->SubDirName($this->data->orders_id) != ''){
                    QRcode::png($this->SubDirName($this->data->orders_id),'qr_scan/'.$this->SubDirName($this->data->orders_id).'.png','H',10,10);

                    $this->Image('qr_scan/'.$this->SubDirName($this->data->orders_id).'.png',175,$qr_x,30,30);
                    $qr_x += 25;
               }
               QRcode::png($this->data->orders_id,'qr_scan/'.$this->data->orders_id.'.png','H',10,10);
               $this->Image('qr_scan/'.$this->data->orders_id.'.png',175,$qr_x,30,30);
               // todo end
          }*/

		$qrcode = $this->parent->get_component('qrcode');

		$image_path = $qrcode->generate_mandate_master_qr($this->mandate_id,true);
		$this->image($image_path,172,5,35,35);
	}
	
	private function DrawAddressArea(){
               // HDT logo, cégadatok
               $this->rect(73,15,98,20);
               // HDT logo es szoveg
               $this->SetFont('arial','',9);
               
               $uri = str_replace('system','',dirname(__FILE__));
               
               $this->Image($uri.'images'.DIRECTORY_SEPARATOR.'hdt_logo_pdf.jpg',11,17,26,15);
               
               $this->SetFont('arial','B',8);
               $X = 80;
               $Y = 19;
               $this->SetXY($X, $Y);
               $this->Cell(0, 0, self::ConvertText('Installációt végző cég:'),0,0,'l');
               $X = 125;
               $this->SetXY($X, $Y);
               
               $this->Cell(0,0, 'HOME DELIVERY TEAM',0,0,'l');
               $this->SetFont('arial','B',6);	
               $Y += 4;
               $X = 80;
               $this->SetXY($X, $Y);
               $this->SetFont('arial','',6);
               $this->Cell(0,0,self::ConvertText('GE Logisztika KFT'),0,0,'l');
               $Y += 4;
               $this->SetXY($X, $Y);
               $this->Cell(0,0,self::ConvertText('1116 BUDAPEST'),0,0,'l');
               $Y += 4;
               $this->SetXY($X, $Y);
               $this->Cell(0,0,self::ConvertText('Hunyadi J. út 156.'),0,0,'l');
               $Y = 24;
               $X = 125;
               $this->SetXY($X, $Y);
               $this->Cell(0,0,self::ConvertText('Telefon: 06/20-557-4499'),0,0,'l');
               $Y += 4;
               $this->SetXY($X, $Y);
               $this->Cell(0,0,self::ConvertText('Email: info@homedt.hu'),0,0,'l');
               $Y += 4;
               $this->SetXY($X, $Y);
               $this->Cell(0,0,self::ConvertText('Web: http://www.homedeliveryteam.hu'),0,0,'l');
			  
			   //$partner = $this->parent->get_component('parcel')->load_partner($this->mandate['PartnerID']);
			   $partner = $this->parent->get_component('subclients')->load_subclient($this->mandate['PartnerID']);
			   //return;
			   //$partner_group = $this->parent->get_component('parcel')->load_client($partner['partner_group_id']);
			   $partner_group = $this->parent->get_component('clients')->load_client($partner['ClientID']);
			   
               // HDT logo, cégadatok vége
               // Szállító cég és vevő adatai

               // függőleges vonal

               // Szállító adatok
               $X = 12;
               $Y = 41;
               $this->SetXY($X,$Y);
               $this->SetFont('arial','BU',10);
               $this->Cell(0,0,self::ConvertText('PARTNER'),0,0,'l');

               // Ha SAMSUNG
               /*if($this->data->partner_group_id == self::SAMSUNG_PARTNER_GROUP){
                    $Y += 5;
                    $this->SetXY($X,$Y);
                    $this->SetFont('arial','BI',10);
                    $this->Cell(0,0,'SAMSUNG'. '('.$partnercsoport_columns->attributes['partner_group_prefix'].')',0,0,'l');
               }*/
               // samsung
               $Y += 7;
               $this->SetXY($X,$Y);
               $this->SetFont('arial','BI',10);
               //$this->Cell(0,0,GlobalPDF::ConvertText(THayerUtils::replaceLongLettersReverse($partnercsoport_columns->attributes['partner_group_name'])). '('.$partnercsoport_columns->attributes['partner_group_prefix'].')',0,0,'l');
			   //$this->Cell(0,0,$this->ConvertText($partner_group['partner_group_name']). '('.$partner_group['partner_group_prefix'].')',0,0,'l');
			   $this->Cell(0,0,$this->ConvertText($partner_group['Name']). '('.$partner_group['Prefix'].')',0,0,'l');
               $this->SetFont('arial','',9);
               //$this->data->partner_name = GlobalPDF::ConvertText(THayerUtils::replaceLongLettersReverse($this->data->partner_name.' ('.$this->data->partner_code.')'));
			   //$partner_name = $this->ConvertText($partner['partner_name'].' ('.$partner['partner_code'].')');
			   $partner_name = $this->ConvertText($partner['Name'].' ('.$partner['Prefix'].')');
               // Partner meghegyzés
               /*if($partner_columns->partner_comment != NULL){
                    $Y += 5;
                    $this->SetXY($X,$Y);
                    $this->Cell(0,0,$this->ConvertText(Yii::t('default','app._viewOrder.megjegyzes')),0,0,'l');
                    foreach($this->WordWrapStringinArray($partner_columns->partner_comment,50) as $partner_comment_line){
                         $Y += 4;
                         $this->SetXY(0,0,$this->ConvertText($partner_comment_line),0,0,'l');
                    }
               }*/
               $Y += 5;
               $this->SetXY($X,$Y);
               $this->SetFont('arial','U',9);
               $this->Cell(0,0,'Alpartner: ',0,0,'l');
               $this->SetFont('arial','',9);
               foreach($this->WordWrapStringinArray($partner_name,50) as $partner_name_line){			
                    $Y += 4;
                    $this->SetXY($X,$Y);
                    $this->Cell(0,0,$partner_name_line);
               }
               /*if($this->data->order_foreign_id !== '' && $this->data->order_foreign_id !== null){
                    $Y += 4;
                    $this->SetXY($X,$Y);
                    $this->SetFont('arial','B',9);
                    $this->Cell(0,0,GlobalPDF::ConvertText(Yii::t('default', 'app._components_pdf_globalparcelpdf.bolti_azonosito')),0,0,'1');
                    $this->SetFont('arial','',9);
                    $Y += 4;
                    $this->SetXY($X,$Y);
                    $this->Cell(0,0,GlobalPDF::ConvertText($this->data->order_foreign_id),0,0,'1');
               }*/
               $Y += 5;
               $this->SetXY($X,$Y);
               //$this->Cell(0,0,$this->ConvertText('Telefonszám : '. $partner['partner_telephone']),0,0,'l');
			   $this->Cell(0,0,$this->ConvertText('Telefonszám : '. $partner['Telephone']),0,0,'l');
			   
               /*$Y += 5;
               $this->SetXY($X,$Y);		
               $user = Users::model()->findByPk($this->data->orders_user_id);    
               $this->Cell(0,0,GlobalPDF::ConvertText(Yii::t('default','app._components_pdf_globalparcelpdf.rogzito').' '.THayerUtils::replaceLongLettersReverse($this->data->user_fullname)));

               if($user->user_telephone != NULL){
                    $Y += 4;
                    $this->SetXY($X,$Y);
                    $this->Cell(0,0,GlobalPDF::ConvertText(Yii::t('default','app.telefonszam').': '.THayerUtils::replaceLongLettersReverse($user->user_telephone)));
               }*/
               $temp_Y = $this->GetY();
               /*unset($user);*/
    	
               // Címett adatok
		
               $X = 97;
               $Y = 41;
               $this->SetXY($X,$Y);
               $this->SetFont('arial','BU',10);
               $this->Cell(0,0,self::ConvertText('MEGRENDELŐ'),0,0,'l');
               $Y += 5;
               $this->SetXY($X,$Y);
               $this->SetFont('arial','BU',9);
               $this->Cell(0,0,self::ConvertText('Számlázási cím :  '),0,0,'l');
               $this->SetFont('arial','',9);
			   
               $mandate_name = $this->WordWrapStringinArray($this->ConvertText($this->mandate['CustomerName']),35);
			   
               foreach($mandate_name as $mandate_name_line){
                    $Y += 5;
                    $this->SetXY($X,$Y);
                    $this->Cell(0,0,$mandate_name_line,0,0,'l');
               }
			   
               $mandate_city_str = $this->mandate['CustomerZipcode'] . ' ' . $this->mandate['CustomerCity'];
               $mandate_city = $this->WordWrapStringinArray($this->ConvertText($mandate_city_str),50);
               foreach($mandate_city as $mandate_city_line){
                    $Y += 5;
                    $this->SetXY($X,$Y);
                    $this->Cell(0,0,$mandate_city_line,0,0,'l');
               }

               $street_str = $this->ConvertText($this->mandate['CustomerAddress']);
               $street = $this->WordWrapStringinArray($street_str,50);
               foreach($street as $street_line){
                    $Y += 5;
                    $this->SetXY($X,$Y);
                    $this->Cell(0,0,$street_line,0,0,'l');
               }

               $Y += 5;
               $this->SetXY($X,$Y);
               $this->Cell(0,0,$this->ConvertText('Telefonszám : '.$this->mandate['CustomerPhone']),0);    	
               // Számlázásdi címmel kapcsolatos megjegyzés
    	
               /*$comment = THayerUtils::replaceLongLettersReverse($this->data->orders_address);
               $str_array = $this->WordWrapStringinArray($comment,60);
               $Y += 5;
               $X = 97;
               $this->SetXY($X,$Y);
               $this->SetFont('arial','BI',7);
               $this->Cell(0,0,GlobalPDF::ConvertText(Yii::t('default','app._viewOrder.megjegyzes').' '));
               $this->SetFont('arial','I',7);
               foreach($str_array as $string_line){
                    $Y += 3;
                    $X = 97;
                    $this->SetXY($X,$Y);
                    $this->Cell(0,0,$this->ConvertText(THayerUtils::replaceLongLettersReverse($string_line)),0,0,'l');
               }*/
			   
               /*$X = 97;
               $Y += 5;
               $this->SetXY($X,$Y);
               $this->SetFont('arial','BU',9);
               if($this->data->orders_need_invoice_form == 1) {
                    $this->Cell(0,0,$this->ConvertText(Yii::t('default','app._driver_vieworder.szallitasi_cim').':'.Yii::t('default', 'app._components_pdf_globalpdf._ugyanaz_mint_a_szállítási_cim')),0,0,'l');
               } else {
                    // Ide a zsállítási cím, ha nem ugyanaz
                    $this->Cell(0,0,$this->ConvertText(Yii::t('default','app._driver_vieworder.szallitasi_cim').':'),0,0,'l');
                    $this->SetFont('arial','',9);
                    $orders_invoice_name = $this->WordWrapStringinArray($this->ConvertText($this->data->orders_invoice_name),35);
                    foreach($orders_invoice_name as $orders_invoice_name_line){
                         $Y += 5;
                         $this->SetXY($X,$Y);
                         $this->Cell(0,0,$orders_invoice_name_line,0,0,'l');
                    }
                    $orders_invoice_city = $this->WordWrapStringinArray($this->ConvertText($this->data->orders_invoice_zipcode.' '.$this->data->orders_invoice_city),50);
                    foreach($orders_invoice_city as $orders_invoice_city_line){
                         $Y += 5;
                         $this->SetXY($X,$Y);
                         $this->Cell(0,0,$orders_invoice_city_line,0,0,'l');
                    }
                         $orders_invoice_street = $this->WordWrapStringinArray($this->ConvertText($this->data->orders_inv_address_street.' '.$this->data->orders_inv_address_streetnumber),50);
           		foreach($orders_invoice_street as $orders_invoice_street_line){
           		     $Y += 5;
           		     $this->SetXY($X,$Y);
           		     $this->Cell(0,0,$orders_invoice_street_line,0,0,'l');
           		}
           		$Y += 5;
           		$this->SetXY($X,$Y);
           		$this->Cell(0,0,$this->ConvertText(Yii::t('default','app._viewOrder.telefon').' '.$this->data->orders_invoice_telephone),0,0,1);
           		$Y += 5;
           		$X = 97;
           		$this->SetXY($X,$Y);
           		$this->SetFont('arial','BI',7);
           		$this->Cell(0,0,GlobalPDF::ConvertText(Yii::t('default','app._viewOrder.megjegyzes').' '));
           		$comment = THayerUtils::replaceLongLettersReverse($this->data->orders_invoice_address);
           		$str_array = $this->WordWrapStringinArray($comment,60);
           		$this->SetFont('arial','I',7);
           		foreach($str_array as $string_line){
           		     $Y += 3;
           		     $X = 97;
           		     $this->SetXY($X,$Y);
           		     $this->Cell(0,0,iconv('UTF-8','ISO-8859-2',$string_line),0,0,'l');
           		}
               }*/
               // Itt kellene kirajzolni a szállításo adatok köré a téglalapot
               if($this->GetY() > $temp_Y){
                    $temp_Y = $this->GetY();
               }
               $Y = ($temp_Y - 37)+3;
               $this->rect(11,37,160,$Y);
               // vonal
               $Y = 37 + $Y;
               $this->Line(95,37,95,$Y);
			   
			   return;
			   
    }
	
	private function InstallationsArea() {
		
		$X = 10;
		
		$Y = $this->GetY()+10;
		
		$this->SetXY($X,$Y);
		
		$this->SetFont('arial','B',9);
		
		$this->Cell(0,0,$this->ConvertText('Megrendelt szolgáltatások'),0,0,'l');
		
		$this->line(10,$Y+5,195,$Y+5);
		
		//$Y = $this->GetY()+10;
		
		//$this->SetXY($X,$Y);
		
		//$this->SetFont('arial','',9);
		
		//$this->Cell(0,0,$this->ConvertText('Under construction'),0,0,'l');
		$installations = $this->parent->get_component('mandates')->installations_for_pdf($this->mandate);
		
		$mandate = $this->mandate;
		
		$to_address = $mandate['CustomerZipcode']." ".$mandate['CustomerCity']." ".$mandate["CustomerAddress"];
		
		$netto_cost = 0;
		
		$insts = unserialize($mandate['Mandate_installations']);
		
		//var_dump($insts);
		
		$topY = $Y+5;
		
		$cimsor = 0;
		
		foreach($installations as $key=>$items){
			$installation_cat = $this->parent->get_component('installations')->load_installation($key);
			
			
			
			//$this->line(10,$Y+5,195,$Y+5);
			
			//$this->SetXY($X,$Y);
			//$this->SetFont('arial','B',9);
			//$this->Cell(0,0,$this->ConvertText($installation_cat['CategoryName']),0,0,'l');
			$this->SetFont('arial','B',9);
			$Y = $this->GetY()+10;
			
			if($cimsor++ == 0) {
			
				$pX = $this->GetX();
				
				$this->SetXY($X,$Y);
				$this->Cell(0,0,$this->ConvertText('Termék',0,0,'l'));
				
				$X = 72;
				
				$this->SetXY($X,$Y);
				
				$this->Cell(0,0,$this->ConvertText('Installáció',0,0,'l'));
				
				$X = 142;
				
				$this->SetXY($X,$Y);
				
				$this->Cell(0,0,$this->ConvertText('Darab',0,0,'l'));
				
				$X = 160;
				
				$this->SetXY($X,$Y);
				
				$this->Cell(0,0,$this->ConvertText('Ár',0,0,'l'));
				
				$this->line(10,$Y+5,195,$Y+5);
			
			}
			
			$Y = $this->GetY()+10;
			
			$this->SetX($pX);
			
			$p_X = $X;
			
			//var_dump($p_X);
			//$X += 30;
			$X = 10;
			
			$i = 0;
			
			$i_max = count($items);
			
			foreach($items as $item){
				
				//var_dump($item);
				//var_dump($insts[$item['ID']]);
				
				$itemcost = explode('|',$insts[$item['ID']]);
				
				//var_dump($itemcost);
				
				if(isset($itemcost[2])) {
					$cost = $itemcost[2];
				} else {
					$cost = $this->parent->get_component('clientoptions')->get_option($item['ID'],$mandate['PartnerID'],$to_address,$insts[$item['ID']]);
					if(isset($itemcost[1])) {
						$cost = $cost * $itemcost['1'];
					}
				}
				
				$netto_cost += $cost;
				
				$this->SetFont('arial','',9);
				
				// Termék kiírása
				
				if($i++ == 0) {
				
					$X = 10;
					
					$this->SetXY($X,$Y);
					$this->SetFont('arial','',9);
					$this->Cell(0,0,$this->ConvertText($installation_cat['CategoryName']),0,0,'l');
				
				}
				
				$X = 72;
				
				$this->SetXY($X,$Y);
				//$this->Cell(0,0,$this->ConvertText($item['InstallationName'] . ' - ' . $cost . ' Ft'),0,0,'l');
				$this->Cell(0,0,$this->ConvertText($item['InstallationName']),0,0,'l');
				
				$X = 142;
				
				if(isset($itemcost[1])) {
					$piece = $itemcost[1];
				} else {
					$piece = 1;
				}
				
				$this->SetXY($X,$Y);
				$this->Cell(0,0,$this->ConvertText($piece),0,0,'l');
				
				$X = 160;
				
				$cost_str = number_format($cost,0,'.',' ');
				
				$this->SetXY($X,$Y);
				$this->Cell(0,0,$this->ConvertText($cost_str . ' Ft'),0,0,'l');
				
				//$this->line(10,$Y+5,195,$Y+5);
				
				if($i < $i_max) {
					$this->line(70,$Y+5,195,$Y+5);
				} else {
					$this->line(10,$Y+5,195,$Y+5);
				}
				
				
				
				$Y = $this->GetY()+10;
			}
			$X = $p_X;
		}
		
		$bottomY = $Y-5;
		
		//$this->line(10,$Y+5,195,$Y+5);
		
		$this->line(10,$topY,10,$bottomY);
		$this->line(70,$topY,70,$bottomY);
		$this->line(140,$topY,140,$bottomY);
		$this->line(155,$topY,155,$bottomY);
		$this->line(195,$topY,195,$bottomY);
		
		$X = 123;
		
		$Y = $this->GetY()+15;
		$this->SetXY($X,$Y);
		$this->SetFont('arial','',12);
		//$this->Cell(0,0,$this->ConvertText('Netto összesen: '.$netto_cost.' Ft'),0,0,'l');
		$this->Cell(0,0,$this->ConvertText('Netto összesen:'),0,0,'l');
		
		$X = 160;
		
		$netto_cost_str = number_format($netto_cost,0,'.',' ');
		
		$this->SetXY($X,$Y);
		$this->Cell(0,0,$this->ConvertText($netto_cost_str.' Ft'),0,0,'l');
		
		$X = 119;
		
		$brutto = $netto_cost * 1.27;
		$brutto = number_format($brutto,0,'.',' ');
		
		$Y = $this->GetY()+10;
		$this->SetXY($X,$Y);
		$this->SetFont('arial','B',12);
		//$this->Cell(0,0,$this->ConvertText('Brutto összesen: '.$brutto.' Ft'),0,0,'l');
		$this->Cell(0,0,$this->ConvertText('Brutto összesen:'),0,0,'l');
		
		$X = 160;
		
		$this->SetXY($X,$Y);
		$this->Cell(0,0,$this->ConvertText($brutto.' Ft'),0,0,'l');
		
		
		return;
		
	}
	
	private function CommentsArea() {
		
		if(($this->h - $this->GetY()+8) < 70){
            $this->addPage('P','A4');
            $Y = 20;
        } else {
            $Y = $this->GetY()+8;
        }
		
		$this->SetFont('arial','B',9);
		
		$X = 10;
		//$Y = $this->GetY();
		
		$this->SetXY($X,$Y+10);
		
		$this->Cell(0,0,$this->ConvertText('Installáló megjegyzése'),0,0,'l');
		
		$Y = $this->GetY()+4;
		
		$this->Rect(11,$Y,185,45);
		
		$this->SetXY(10,$Y+40);
		
		if(($this->h - $this->GetY()+8) < 70){
            $this->addPage('P','A4');
            $Y = 20;
        } else {
            $Y = $this->GetY()+8;
        }
		
		$this->SetFont('arial','B',9);
		
		$X = 10;
		//$Y = $this->GetY();
		
		$this->SetXY($X,$Y+10);
		
		$this->Cell(0,0,$this->ConvertText('Megrendelő megjegyzése'),0,0,'l');
		
		$Y = $this->GetY()+4;
		
		$this->Rect(11,$Y,185,45);
		
		$this->SetXY(10,$Y+40);
		
		if(($this->h - $this->GetY()+8) < 100){
            $this->addPage('P','A4');
            $Y = 20;
        } else {
            $Y = $this->GetY()+8;
        }
		
		$this->SetFont('arial','B',9);
		
		$X = 10;
		//$Y = $this->GetY();
		
		$this->SetXY($X,$Y+10);
		
		$this->Cell(0,0,$this->ConvertText('Átvevő / Vevő tanúsítása'),0,0,'l');
		
		$Y = $this->GetY()+4;
		
		$this->SetXY($X,$Y+10);
		
		$this->SetFont('arial','',9);
		
		$signature = $this->parent->get_component('uploadfiles')->get_signature($this->mandate['ID']);
		
		if($signature != false) {
			
			$this->Cell(0,0,$this->ConvertText('Installáció dátuma :' . $signature['date']),0,0,'l');
		
		} else {
		
			$this->Cell(0,0,$this->ConvertText('Installáció dátuma :.....................................................'),0,0,'l');
		
		}
		
		$Y = $this->GetY();
		
		$this->SetXY(125,$Y);
		
		$this->SetFont('arial','',9);
		
		
		
		if($signature != false) {
			$this->Cell(0,0,$this->ConvertText('Vásárló aláírása :'),0,0,'l');
			$Y = $this->GetY();
		
			$this->SetXY(65,$Y+200);
			
			$this->image($signature['image'],150,30,60,35);
		} else {
			$this->Cell(0,0,$this->ConvertText('Vásárló aláírása :.....................................................'),0,0,'l');
		}
		
		/*$Y = $this->GetY();
		
		$this->SetXY(65,$Y+20);
		
		$this->SetFont('arial','',9);
		
		$this->Cell(0,0,$this->ConvertText('Ügyfél aláírása :.....................................................'),0,0,'l');*/
		
		$Y = $this->GetY();
		
		$this->SetXY(10,$Y+20);
		
		$this->SetFont('arial','BU',8);
		
		/*$this->Cell(0,0,$this->ConvertText('A munkavégzést igazoló aláírással a fent megjelölt munka teljesítését és a felhasznált anyagok mennyiségét elismerem, az elvégzett munkát átveszem.
'),0,0,'l');*/
		$this->MultiCell( 185, 5, $this->ConvertText('A munkavégzést igazoló aláírással a fent megjelölt munka teljesítését és a felhasznált anyagok mennyiségét elismerem, az elvégzett munkát átveszem.'), 0);
		
		return;
	}
	
	private function SignatureArea($installation = NULL){
          //Aláírások és jogi rendelkezések

          $X = 11;
          // Ha már nem fér ki az oldalra
          if(($this->h - $this->GetY()+8) < 70){
               $this->addPage('P','A4');
               $Y = 20;
          } else {
               $Y = $this->GetY()+8;
          }
          $this->SetFont('arial','B',9);
          $this->Rect(11,$Y,185,45);
          $this->Line(105,$Y,105,$Y+45);
          $Y_top = $Y;
          if($installation){
               $Y += 5;
               $this->SetXY($X,$Y);
               $this->Cell(0,0,$this->ConvertText('Installációs feljegyzések'),0,0,'l');
               $Y += 35;
               $this->SetXY($X+50,$Y);
               $this->SetFont('arial','',9);
               $this->Cell(0,0,'............................................',0,0,'l');
               $Y += 5;
               $this->SetXY($X+70,$Y);
               $this->Cell(0,0,$this->ConvertText('vevő aláírása'),0,0,'l');
          }
          // Vevő tanusítása
          /*$X = 107;
          $Y = $Y_top + 5;
          $this->SetFont('arial','B',9);
          $this->SetXY($X,$Y);
          $this->Cell(80,0,$this->ConvertText('Átvátel / Vevő tanusítása'),0,0,'C');
          $Y += 5;
          $this->SetFont('arial','I',7);
          $this->SetXY($X,$Y);
          $this->Cell(80,0,$this->ConvertText(Yii::t('default', 'app._components_pdf_globalparcelpdf.vasarlo_a_termeket_garanciajeggyel')),0,0,'C');
          $Y += 3;
          $this->SetXY($X,$Y);
          $this->Cell(80,0,$this->ConvertText(Yii::t('default', 'app._components_pdf_globalparcelpdf.magyar_nyelvu_leirassal_serulesmentesen_atvette')),0,0,'C');*/
          $this->SetFont('arial','',9);
          /*$Y += 7;
          $this->SetXY($X,$Y);
          $this->Cell(80,0,$this->ConvertText('Vásárló aláírása'.' ..........................................................'),0,0,'C');*/
          $Y += 7;
          $this->SetXY($X,$Y);
          $this->Cell(80,0,$this->ConvertText('Vásárló aláírása:'.' ..........................................................'),0,0,'C');
          /*$Y += 7;
          $this->SetXY($X,$Y);
          $this->SetFont('arial','I',8);
          $this->Cell(80,0,$this->ConvertText(Yii::t('default', 'app._components_pdf_globalparcelpdf.a_vasarlo_nem_kerte_a_termek_kibontasat_ellenorzeset')),0,0,'C');*/
          //$Y += 7;
          //$this->SetXY($X,$Y);
          //$this->SetFont('arial','',9);
          //$maradek = $this->h - $this->GetY();
          //$maradek += 8;
          //$this->Cell(80,0,$this->ConvertText('Vásárló aláírása'.' ..........................................................'),0,0,'C'); 
    }
	
	// Szöveg tördelése szóköznél
	
    private function WordWrapStringinArray($string = '',$limit){
     
        $string_array = explode(' ',$string);

        $return_array = array();
        $line = '';
        for($i = 0; $i < count($string_array);$i++){
            $word = $string_array[$i];
            if(strlen($line.' '.$word) > $limit){
               $return_array[] = trim($line);
               $line = '';
               $i--;
            } else {
               $line = trim($line) . ' ' . trim($word);
            }
        }
        $return_array[] = $line;
        return $return_array;
    }
	
	public function addWorksheetPage() {
		$this->AddPage('P','A4');
		$this->DrawQrCodes();
		$this->DrawAddressArea();
		$this->InstallationsArea();
			  /*$this->DrawCOD();
			  $this->DrawItemsArea();
			  $installation = $this->OptionsArea();*/
		$this->CommentsArea();
			  //$this->SignatureArea();
			  //$this->ClauseArea();
	}
	
	public function generate() {
		
		$this->addWorksheetPage();
		
		$params = $this->parent->get_component('params');
		
		$pdf_filename =  str_replace("/","_",$this->mandate['Mandate_serial']) . ".pdf";
		
		$filepath = $params['pdf_path'] . $pdf_filename;
		

		
		$this->Output($filepath,'F');
		
		if(file_exists($filepath)) {
			//return $filepath;
			return $params['pdf_uri'] . $pdf_filename;
			return "itt";
		} else {
			return false;
		}
		
	}
		
}

class HistoryReport extends FPDF {
	
	private $history;
	
	private $mandate_id;
	
	private $parent;
	
	private $params;
	
	public function __construct($mandate_id = NULL, $history = NULL, $parent = NULL,$params = NULL) {
		
		$this->history = $history;
		
		$this->mandate_id = $mandate_id;
		
		$this->parent = $parent;
		
		$this->params = $params;
		
		parent::__construct();
		
	}
	
	public function Header() {
		
		$mandate = $this->parent->get_component('mandates')->load_mandate($this->mandate_id);
		
		$X = 10;
        $Y = 10;
        $this->SetXY($X,$Y);
        $this->SetFont('arial','B',17);
        $this->Cell(0,0,self::ConvertText('Státusz history'),0,0,'l');
        $X = 72;
        $this->SetXY($X,$Y);
        $this->SetFont('arial','B',11);
        $this->Cell(0,0,self::ConvertText('Megrendelés szám: '.$mandate['Mandate_serial']),0,0,'l');
        $this->Ln(5);
	}
	
	public function Footer() {
		$this->SetFont('arial','',7);
        $this->SetY(-15);
        $this->Cell(0,20,self::ConvertText($this->PageNo().' oldal   -   Home Delivery Team - www.homedt.hu'),0,0,'l');
	}
	
	public static function ConvertText($param){
        return iconv('UTF-8','ISO-8859-2//IGNORE',$param);
    }
	
	// Szöveg tördelése szóköznél
	
    private function WordWrapStringinArray($string = '',$limit){
     
        $string_array = explode(' ',$string);

        $return_array = array();
        $line = '';
        for($i = 0; $i < count($string_array);$i++){
            $word = $string_array[$i];
            if(strlen($line.' '.$word) > $limit){
               $return_array[] = trim($line);
               $line = '';
               $i--;
            } else {
               $line = trim($line) . ' ' . trim($word);
            }
        }
        $return_array[] = $line;
        return $return_array;
    }
	
	private function drawTable() {
		$X = 10;
		$Y = $this->GetY() + 10;
		
		$statuses = $this->parent->get_component('statuses')->list_statuses(true);
		
		foreach($this->history as $row) {
			$Y += 4;
			$this->SetXY($X,$Y);
			$this->SetFont('arial','',10);
			
			if($row['user_type'] == 0) {
				$user = $this->parent->get_component('user')->load_user($row['user_id']);
				//var_dump($user);
				//echo '<p>Felhasználó : '.$user['FullName'].' ( Masterservice )</p>';
				$username = $user['FullName'].' ( Masterservice )';
			} elseif($row['user_type'] == 2) {
				//var_dump($his->parent->get_component('master'));exit;
				$master = $this->parent->get_component('master')->load_master($row['user_id']);
				//echo '<p>Mester : '.$master['FullName'].'</p>';
				$username = $master['Name'].' ( Mester )';
			} elseif($row['user_type'] == 1) {
				// Parcel user
				$username = 'Parcel felhasználó';
			}
			
			//$row_str = $username." ".date("Y.m.d H:i:s",strtotime($row['changed_time']))." ".$statuses[$row['old_status']]['label']." ".$statuses[$row['new_status']]['label']." ".$row['IP'];
			$this->Cell( 0, 0,self::ConvertText('Felhasználó : '.$username . " Időpont: ".date("Y.m.d H:i:s",strtotime($row['changed_time'])),0,0,'l'));
			$Y += 4;
			$this->SetXY($X,$Y);
			$this->Cell( 0, 0,self::ConvertText('Régi státusz : '.$statuses[$row['old_status']]['label']." Új státusz : ".$statuses[$row['new_status']]['label']),0,0,'l');
			$Y += 4;
			$this->SetXY($X,$Y);
			$this->Cell( 0, 0,self::ConvertText('IP : '.$row['IP'],0,0,'l'));
			$Y += 4;
			$this->SetXY($X,$Y);
			//$this->Ln(5);
			$Y += 4;
		}
	}
	
	public function addHistoryPage(){
		
		$this->AddPage('P','A4');
		$this->drawTable();
		
	}
	
	public function generate() {
		
		$this->addHistoryPage();
		
		$params['pdf_path'] = $_SESSION['pdf_path'];
		$params['pdf_uri'] = $_SESSION['pdf_uri'];
		
		$mandate = $this->parent->get_component('mandates')->load_mandate($this->mandate_id);
		
		$pdf_filename =  "Hist_".str_replace("/","_",$mandate['Mandate_serial']) . ".pdf";
		
		$filepath = $params['pdf_path'] . $pdf_filename;
		
		//var_dump($filepath);exit;
		
		$this->Output($filepath,'F');
		
		if(file_exists($filepath)) {
			//return $filepath;
			return 'http://'.$params['pdf_uri'] . $pdf_filename;
		} else {
			return false;
		}
	}
	
}

class Pdf_tool{
	
	private $parent;
	
	private $pdf = NULL;
	
	public function __construct($parent = NULL) {
		
		if($parent != NULL) {
			$this->parent = $parent;
		}

		//$this->generate_mandate_worksheet();
		
		//exit;
	}
	
	
	
	public function generate_mandate_worksheet($mandate_id = NULL) {

		if($this->pdf != NULL) {
			$this->pdf = NULL;
		}
		
		$this->pdf = new Worksheet($mandate_id,$this->parent);
		
		
		return $this->pdf->generate();
		
	}
	
	public function generate_history_pdf_link($mandate_id = NULL,$history = NULL) {
		
		if($this->pdf != NULL) {
			$this->pdf = NULL;
		}
		
		$this->pdf = new HistoryReport($mandate_id,$history,$this->parent);
		
		return $this->pdf->generate();
		
	}
	
	public function generate_pdf_link($mandate_id = NULL) {
		ob_start();
		//echo $this->generate_mandate_worksheet($mandate_id);
		$link = $this->generate_mandate_worksheet($mandate_id);
		?>
		<a target="_BLANK" href="http://<?php echo $link;?>" data-mandate-id="<?php echo $mandate_id;?>" class="btn btn-primary worksheet-link">Munkalap</a>
		<?php
		$content = ob_get_contents();

		ob_end_clean();

		return $content;
	}
	
	public function generate_ajax_pdf_link($mandate_id) {
		
		$return['status'] = 'ok';
		$return['content'] = $this->generate_mandate_worksheet($mandate_id);
		
		return $return;
	}
	
}
?>
