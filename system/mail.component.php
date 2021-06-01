<?php
require_once('tools/phpmailer/PHPMailerAutoload.php');

class Email {
	
	private $parent;
	
	private $phpmailer;
	
	const EMAIL_HOST = 'smtp.gmail.com';

	//const EMAIL_HOST = 'mail.homedt.hu';

	const EMAIL_PORT = 587;
	const EMAIL_USERNAME = 'csuporbela@gmail.com';
	//const EMAIL_USERNAME = 'csupor.bela@homedt.hu';
	const EMAIL_PASSWORD = 'asy3848mt';
	//const EMAIL_PASSWORD = 'bela_4875';
	const EMAIL_FROM = 'info@homedt.hu';
	const EMAIL_FROMNAME = 'HDT Masterservice';
	
	private $content_header;
							
	private $content_footer;
	
	public function __construct($parent = NULL) {
		
		$this->parent = $parent;
		
		$this->phpmailer = new PHPMailer;
		$this->phpmailer->CharSet = 'UTF-8';
		if($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '195.228.35.4') {
			$this->phpmailer->isSMTP();
			//$this->phpmailer->SMTPDebug = 2;
			$this->phpmailer->Debugoutput = 'html';
			$this->phpmailer->Host = self::EMAIL_HOST;
			$this->phpmailer->Port = self::EMAIL_PORT;
			$this->phpmailer->SMTPAuth = true;
			$this->phpmailer->Username = self::EMAIL_USERNAME;
			$this->phpmailer->Password = self::EMAIL_PASSWORD;
			$this->phpmailer->SMTPSecure = 'tls';
		}
		
		$this->phpmailer->From = self::EMAIL_FROM;
		$this->phpmailer->FromName = self::EMAIL_FROMNAME;
		$this->phpmailer->WordWrap = 50;                                 // Set word wrap to 50 characters
		$this->phpmailer->isHTML(true);
		
		$this->content_header = '<div style="background:transparent none;padding:0;border:1px solid #2A3F54;">'
								.'<div style="background:#2A3F54 none;display:inline-block;padding:10px 0;margin:0;text-align:left;vertical-align:middle;width:100%;height:auto;">'
									.'<img style="margin-left:10px;display:inline-block;vertical-align:middle;height:30px;width:auto;" src="cid:hdt-logo" />'
								.'</div>'
								.'<div style="margin:0;padding:0;">'
							.'<div style="margin:0;padding:10px;">';
		
		$this->content_footer = '</div>'."\r\n"
							.'<div style="margin:0;padding:5px 10px;color:#ffffff;background:#2A3F54 none;">'."\r\n"
								.'<p style="font-size:10px;">Ez egy automatikusan generált üzenet. Kérjük, ne válaszoljon rá!'.'</p>'."\r\n"
							.'</div>'."\r\n"
						.'</div>'."\r\n"
					.'</div>'."\r\n"
				.'</div>'."\r\n";
		
		return;
		
	}
	
	public function build_email($act = NULL,$addresses = NULL) {
		if(method_exists($this,$act)) {
			return $this->$act($addresses);
		} else {
			return false;
		}
	}
	
	public function change_status_email($mandate_id = null) {
		
		if($mandate_id == NULL) {
				return;
		}
		
		$mandate = $this->parent->get_component('mandates')->load_mandate($mandate_id);
		
		$tracking = $this->parent->get_component('mandate_tracking')->get_track($mandate['ID']);
		
		$track_url = 'http://'.ROOT_URL.'?m=masterservice&act=mandate_tracking&uname='.$tracking['Uname'].'&passw='.$tracking['Passw'];
		
		$this->phpmailer->addAddress($mandate['CustomerEmail']);
		
		$img_url = $this->parent->get_component('params')['theme_path'];
		
		$img_1_url = $img_url.'img'.DIRECTORY_SEPARATOR.'hdt_logo_new.png';
		$img_2_url = $img_url.'img'.DIRECTORY_SEPARATOR.'hdt_szlogen.png';
		
		$this->phpmailer->AddEmbeddedImage($img_1_url,'hdt-logo','hdt_logo_new.png');
		$this->phpmailer->AddEmbeddedImage($img_2_url,'hdt-szlogen','hdt_szlogen.png');
		
		$this->phpmailer->Subject = 'HDT Masterservice - Értesítés státusz megváltozásáról';
		
		$this->phpmailer->Body = $this->content_header;
		
		$statuses = $this->parent->get_component('statuses')->list_statuses(true);
		
		//var_dump($statuses);
		
		//$allKeys = array_keys($statuses);
		
		//var_dump($statuses[$mandate['Master_status']]); exit;
		
		$content = $this->parent->get_component('email_contents')->load_content($statuses[$mandate['Master_status']]['hook']);
		
		
		
		$replaced = array();
		
		$replaced['CUSTOMER-NAME'] = $mandate['CustomerName'];
		$replaced['CUSTOMER-ADDRESS'] = $mandate['CustomerZipcode']." ".$mandate['CustomerCity']." ".$mandate['CustomerAddress'];
		$replaced['CUSTOMER-PHONENUM'] = $mandate['CustomerPhone'];
		$replaced['CUSTOMER-TRACKURL'] = '<a href="'.$track_url.'">HDT Masterszerviz megbízáskövetés</a>';
		$replaced['MANDATE-SERIAL'] = $mandate['Mandate_serial'];
		$replaced['STATUS-LABEL'] = $statuses[$mandate['Master_status']]['label'];
		$replaced['INVOICE-CONTENT'] = $this->invoice_content($mandate);
		
		
		$content = $this->compose_content($replaced,$content);
		
		
		
		$this->phpmailer->Body .= $content;
		
		$this->phpmailer->Body .= $this->content_footer;
		
		$this->phpmailer->CreateBody();
		$ret = $this->send_email();
		//var_dump($ret);
		//exit;
		return;
		
	}
	
	
	
	
	private function invoice_content($mandate = null){

		ob_start();
		//var_dump($mandate);
		$netto_cost = 0;
		
		$products = unserialize($mandate['Mandate_products']);

		$installations = unserialize($mandate['Mandate_installations']);

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

		/*if(count($insts) > 0){
			foreach($insts as $key=>$items){
				
				$piece_array = explode('|',$items[0]['Value']);
				
				if(isset($piece_array[1])) {
					$piece = $piece_array[1];
				} else {
					$piece = 1;
				}
				
				//$installation_cat = $this->installations->load_installation($key);
				$installation_cat = $this->parent->get_component('installations')->load_installation($key);
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
		*/
		?>
		<?php
		if(count($insts) > 0){
			?>
			<p>Az Ön számlája:</p>
			<table style="border-collapse:collapse;border: 1px solid black;width:100%;max-width:600px;">
				<tr style="border-collapse:collapse;border: 1px solid black;">
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><strong>Számla kiállító adatai</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><strong>Vevő adatai</strong></td>
				</tr>
				<tr style="border-collapse:collapse;border: 1px solid black;">
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;width:50%;vertical-align:top;">
						<?php 
							$subclient = $this->parent->get_component('subclients')->load_subclient($mandate['PartnerID']);
							//var_export($subclient);
							echo '<strong>' . $subclient['Name'] . '</strong><br />';
							echo $subclient['Zipcode'] . ' ' . $subclient['City'] . '<br />';
							echo $subclient['Address'] . '<br />';
							echo 'Telefon: ' . $subclient['Telephone'] . '<br />';
							echo 'E-mail:' . $subclient['Email'];
						?>
					</td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;width:50%;vertical-align:top;">
						<?php 
							echo '<strong>' . $mandate['CustomerName'] . '</strong><br />';
							echo $mandate['CustomerZipcode'] . ' ' . $mandate['CustomerCity'] . '<br />';
							echo $mandate['CustomerAddress'];
						?>
					</td>
				</tr>
			</table>
			
			<table style="border-collapse:collapse;border: 1px solid black;width:100%;max-width:600px;">
				<tr style="border-collapse:collapse;border: 1px solid black;">
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;text-align:center;"><strong>Fizetés módja</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;text-align:center;"><strong>Számla kelte</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;text-align:center;"><strong>Teljesítés időpontja</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;text-align:center;"><strong>Fizetési határidő</strong></td>
				</tr>
				<tr style="border-collapse:collapse;border: 1px solid black;">
					<?php 
						if($subclient['Master_cash']) {
							$fizmod = 'készpénz';
						} else {
							$fizmod = 'átutalás';
						}
					?>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;text-align:center;"><strong><?php echo $fizmod;?></strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;text-align:center;"><strong><?php echo date('Y.m.d',time());?></strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;text-align:center;"><strong><?php echo date('Y.m.d',time());?></strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;text-align:center;"><strong><?php $date = strtotime("+7 day"); echo date('Y.m.d', $date);?></strong></td>
				</tr>
			</table>
			
			<table style="border-collapse:collapse;border: 1px solid black;width:100%;max-width:600px;">
				<tr style="border-collapse:collapse;border: 1px solid black;">
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><strong>Termék</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><strong>Menny/Mee</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><strong>Installáció (Művelet)</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><strong>Ár (netto)</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><strong>ÁFA (%)</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><strong>ÁFA ért.</strong></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><strong>Bruttó</strong></td>
				</tr>
				
			<?php
			foreach($insts as $key=>$items){
				$piece_array = explode('|',$items[0]['Value']);
				
				if(isset($piece_array[1])) {
					$piece = $piece_array[1];
				} else {
					$piece = 1;
				}
				$installation_cat = $this->parent->get_component('installations')->load_installation($key);
				?>

				<?php
				$installations_cell = "";
				$costs_cell = "";
				$afas_cell = "";
				$brutto_cell = "";
				foreach($items as $item){
					$cost_array = explode('|',$item['Value']);
					//var_dump($cost_array);
					if(isset($cost_array[2])) {
						$cost = $cost_array[2];
					} else {
						$to_address = $row['CustomerZipcode']." ".$row['CustomerCity']." ".$row["CustomerAddress"];
						$cost = $this->parent->get_component('clientoptions')->get_option($item['ID'],$row['PartnerID'],$to_address,$installations[$item['ID']]);
					}
					
					$netto_cost += $cost;
					
					$installations_cell .= $item['InstallationName'] . "<br />";
					$costs_cell .= $cost . "<br />";
					$afas_cell .= ($cost*1.27)-$cost . "<br />";
					$brutto_cell .= $cost*1.27 . "<br />";
					?>
					
					<?php
				}
				?>
				<tr style="border-collapse:collapse;border: 1px solid black;">
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><?php echo $installation_cat['CategoryName'];?></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><?php echo $piece . ' db';?></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><?php echo $installations_cell;?></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><?php echo $costs_cell;?></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;">27</td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><?php echo $afas_cell;?></td>
					<td style="border-collapse:collapse;border: 1px solid black;padding:5px;"><?php echo $brutto_cell;?></td>
				</tr>
				<?php
			}
			?>
			<tr style="border-collapse:collapse;border: 1px solid black;">
				<td colspan="7" style="border-collapse:collapse;border: 1px solid black;padding:5px;text-align:right;">
					<p><strong>Netto összesen: <?php echo $netto_cost;?> Ft</strong></p>
					<p><strong>Brutto összesen: <?php echo $netto_cost*1.27;?> Ft</strong></p>
					<p>Azaz: <?php $brutto = $netto_cost*1.27; echo $this->parent->get_component('converter')->toString($brutto);?> forint</p>
				</td>
			</td>
			</table>
			<p>Kérjük, digitális aláírásával véglegesítse a szolgáltatást!</p>
			<?php
		} else {
			?><p>Nincs termék!</p><?php
		}
		?>
		<?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	public function mandate_add_email($mandate_data = NULL) {
		//var_dump($mandate_data);
		
		//$track_url = 'http://'.ROOT_URL.'?m=masterservice&act=mandate_tracking';
		
		
		
		$tracking = $this->parent->get_component('mandate_tracking')->get_track($mandate_data['id']);
		
		$track_url = 'http://'.ROOT_URL.'?m=masterservice&act=mandate_tracking&uname='.$tracking['Uname'].'&passw='.$tracking['Passw'];
		
		$this->phpmailer->addAddress($mandate_data['mandate-customer-email']);
		
		$img_url = $this->parent->get_component('params')['theme_path'];
		
		$img_1_url = $img_url.'img'.DIRECTORY_SEPARATOR.'hdt_logo_new.png';
		$img_2_url = $img_url.'img'.DIRECTORY_SEPARATOR.'hdt_szlogen.png';
		
		$this->phpmailer->AddEmbeddedImage($img_1_url,'hdt-logo','hdt_logo_new.png');
		$this->phpmailer->AddEmbeddedImage($img_2_url,'hdt-szlogen','hdt_szlogen.png');
		
		$this->phpmailer->Subject = 'HDT Masterservice - Értesítés megbízás rögzítéséről';
		/*$this->phpmailer->Body = ''
				.'<div style="background:transparent none;padding:0;border:1px solid #2A3F54;">'
					.'<div style="background:#2A3F54 none;display:inline-block;padding:10px 0;margin:0;text-align:left;vertical-align:middle;width:100%;height:auto;">'
						.'<img style="margin-left:10px;display:inline-block;vertical-align:middle;height:30px;width:auto;" src="cid:hdt-logo" />'
						//.'&nbsp;<p style="font-family:\'Verdana\',\'sans-serif\';display:inline-block;vertical-align:middle;width:auto;margin:0;padding:0 0 0 25px;font-size:18px;font-weight:bold;color:#ffffff">Mesterszervíz</p>'
					.'</div>'."\r\n"
					.'<div style="margin:0;padding:0;">'."\r\n"
						.'<div style="margin:0;padding:10px;">'."\r\n"
							.'<h2>Tisztelt '.$mandate_data['mandate-customer-name'].'!</h2>'."\r\n"
							.'<p>Mesterszervíz megbízás érkezett a rendszerünkbe az alábbi adatokkal:'.'</p>'."\r\n"
							.'<p>Név: '.$mandate_data['mandate-customer-name'].'</p>'."\r\n"
							.'<p>Az installáció helye :'.$mandate_data['mandate-customer-zipcode']." ".$mandate_data['mandate-customer-city']." ".$mandate_data['mandate-customer-address'].'</p>'."\r\n"
							.'<p>Telefonszám: '.$mandate_data['mandate-customer-phonenum'].'</p>'."\r\n"
							.'<p>Az installáéció pontos időpontjának egyeztetése céljából kollégánk hamarosan keresni fogja!</p>'."\r\n"
							.'<p>A nyomkövetéshez erre a linkre kattintva léphet be: <a href="'.$track_url.'">HDT Masterszerviz megbízáskövetés</a>'."\r\n"
							.'<p>Azonosító: ' . $tracking['Uname']."\r\n"
							.'<p>Jelszó: ' . $tracking['Passw']."\r\n"
							.'<p style="margin:0;padding:20px 0 0 0;text-align:center;"><img style="height:auto;width:100%;max-width:600px;"src="cid:hdt-szlogen" /></p>'."\r\n"
							.'</div'."\r\n"
							.'<div style="margin:0;padding:5px 10px;color:#ffffff;background:#2A3F54 none;">'."\r\n"
								.'<p style="font-size:10px;">Ez egy automatikusan generált üzenet. Kérjük, ne válaszoljon rá!'.'</p>'."\r\n"
							.'</div>'."\r\n"
						.'</div>'."\r\n"
					.'</div>'."\r\n"
				.'</div>'."\r\n";*/
		
		$this->phpmailer->Body = $this->content_header;
		
		$content = $this->parent->get_component('email_contents')->load_content(__FUNCTION__);
		
		//var_dump($content); exit;
		
		$replaced = array();
		
		$replaced['CUSTOMER-NAME'] = $mandate_data['mandate-customer-name'];
		$replaced['CUSTOMER-ADDRESS'] = $mandate_data['mandate-customer-zipcode']." ".$mandate_data['mandate-customer-city']." ".$mandate_data['mandate-customer-address'];
		$replaced['CUSTOMER-PHONENUM'] = $mandate_data['mandate-customer-phonenum'];
		$replaced['CUSTOMER-TRACKURL'] = '<a href="'.$track_url.'">HDT Masterszerviz megbízáskövetés</a>';
		
		$content = $this->compose_content($replaced,$content); 
		
		$this->phpmailer->Body .= $content;
		
		$this->phpmailer->Body .= $this->content_footer;
		
		$this->phpmailer->CreateBody();
		//try{
		$ret = $this->send_email();
		//} catch (phpmailerException $e) {
		//	var_dump($e->errorMessage()); //Pretty error messages from PHPMailer
		//} catch (Exception $e) {
		//	var_dump($e->getMessage()); //Boring error messages from anything else!
		//}
		//var_dump($_SERVER['HTTP_HOST']);
		//var_dump($ret);
		//exit;
		return;
		
	}
	
	private function compose_content($replaced = array(),$content = null) {
	
		foreach($replaced as $key=>$tag) {
			$content = preg_replace('/\[\!\{'.$key.'\}\!\]/',$tag,$content);
		}
		
		return $content;
		
	}
	
	public function user_register_email($user_data = NULL) {
		
		//var_dump($user_data);
		
		$this->phpmailer->addAddress($user_data['user-email']);
		$this->phpmailer->Subject = 'HDT Masterservice - Értesítés felhasználói regisztrációról';
		$this->phpmailer->Body = '<p>Tisztelt '.$user_data['user-fullname'].'!</p>'
								 .'<p>A Homedt adminisztrátora felhasználói regisztrációt hozott létre az Ön adataival.'."\r\n"
								 .'<p>Az Ön felhasználóneve: '.$user_data['user-name']."\r\n"
								 .'<p>Jelszava :'.$user_data['pwd-one']."\r\n"
								 .'<p>Felhasználói szintje: '."\r\n"
								 .'<p>A felületre az alábbi linkre kattintva léphet be adatai beírásával: <a href="http://'.ROOT_URL.'">'.ROOT_URL.'</a>'."\r\n"
								 .'<p>Ez egy automatikusan generált üzenet. Kérjük, ne válaszoljon rá!';
		$ret = $this->send_email();
		
		return;
		
	}
	
	public function proba_email($addresses = NULL) {
		$ret = false;
		if(is_array($addresses)) {
			foreach($addresses as $address) {
				$this->phpmailer->addAddress($address);
				$this->phpmailer->Subject = 'HDT Masterservice - próba e-mail';
				$this->phpmailer->Body    = 'Ez egy próba e-mail innen: <a href="http://bela.homedt.hu"><b>HDT Masterservice</b></a>. Ellenőrizd, helyesen jelennek-e meg a karakterek! éáőúöüóíÉÁŐÚÖÜÓÍ';
				$this->phpmailer->AltBody = 'Ez egy próba e-mail innen: HDT Masterservice (Nem HTML formátum)';
				$ret = $this->send_email();
			}
		}
		if($ret == false) {
			return 'Hiba történt a küldés során. Mailer error:' . $this->phpmailer->ErrorInfo;
		} else {
			return 'Ok';
		}
	}
	
	private function send_email() {
		if(!$this->phpmailer->send()) {
			//return 'Message could not be sent. Mailer Error: ' . $this->phpmailer->ErrorInfo;
			return false;
		} else {
			return true;
		}
	}
	
}
?>
