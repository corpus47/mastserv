<?php

require_once('DbEngines/uploadfiles.db.php');

class uploadfiles {
	
	private $parent;
	
	private $uploadfiles_db;
	
	private $rules = array(
							SUPER_USER,
							ADMIN_USER,
							);
							
	const MANDATE_BILL = 0; // A megbízást bizonyító számla
	
	const ACCOUNT_BILL = 1; // A megbízást lezáró számla
	
	const MASTERS_PICT = 2; // A mester által feltöltött képek

	public function __construct($parent = NULL) {
	
		$this->parent = $parent;
		
		$this->uploadfiles_db = new UploadfilesDB($parent);
	
	}
	
	public function files_table($post = null) {
		
		$filter = array("MandateID = " . $post['id']);
		
		ob_start();
		
		$files = $this->uploadfiles_db->list_files($filter);
		?><h4>Csatolt fájlok</h4><?php
		?><table class="table table-bordered table-striped filelist-table"><?php
		
		while($row = mysql_fetch_assoc($files)){
			?><tr><?php
			$filetags = explode('_',$row['FileName']);
			$dirs = array();
			preg_match('/^([0-9]{4})([0-9]{2})/',$filetags[1],$dirs);
			
			$dir_base = $this->parent->get_component('params')['uploads_path'];
			
			//$link = $_SERVER['HTTP_HOST'] . '/masterservice/uploads/' . $dirs[1] .'/'.$dirs[2].'/'.$row['FileName'];
			
			$link = ROOT_URL . '/uploads/' . $dirs[1] .'/'.$dirs[2].'/'.$row['FileName'];
			
			//var_export(preg_match('/\.pdf$/',$row['FileName'],$found));
			
			/*if(preg_match('/\.pdf$/',$row['FileName'],$found) == 0) {
					$class = 'class="image-popup-fit-width"';
			} else {
					$class = '';
			}
			
			?><td><a <?php echo $class;?> href="http://<?php echo $link;?>" target="_BLANK"><?php echo $row['FileName'];?></td><?php*/
			
			if(preg_match('/\.(pdf|doc|docx|)$/',$row['FileName'],$found) == 0) {
					$fancybox = 'data-fancybox="images"';
			} else {
					$fancybox = '';
			}
			switch($row['AttachmentType']) {
				case 0:
					$f_type = 'Vásárlási számla';
					break;
				case 1:
					$f_type = 'Lezáró számla';
					break;
				case 2:
					$f_type = 'Helyszíni fotó';
					break;
				case 3:
					$f_type = 'Aláírás';
					break;
			}
			?><td><a <?php echo $fancybox;?> href="http://<?php echo $link;?>" target="_BLANK"><?php echo $row['FileName'];?></a> - <?php echo $f_type;?></td><?php
			
			if(isset($_SESSION['HDT_master_user'])){
				if($row['UploadUser'] == $_SESSION['HDT_master_user']){
					?><td style="text-align:center;"><a class="attachment-delete" data-id="<?php echo $row['ID'];?>" href="javascript:void(0);">Törlés</a></td><?php
				} else {
					?><td>&nbsp;</td><?php
				}	
			} else {
				?><td style="text-align:center;"><a class="attachment-delete" data-id="<?php echo $row['ID'];?>" href="javascript:void(0);">Törlés</a></td><?php
			}
			?></tr><?php
		}
		
		?></table><?php
		
		$content = ob_get_contents();
		ob_end_clean();
		
		$ret['status'] = 'ok';
		$ret['content'] = $content;
		
		return $ret;
	
	}
	
	public function delete_attachment_file($post = NULL) {
		
		ob_start();
		
		$res = $this->uploadfiles_db->get_file($post['id']);
		
		$file = mysql_fetch_assoc($res);
		
		$filetags = explode('_',$file['FileName']);
		$dirs = array();
		preg_match('/^([0-9]{4})([0-9]{2})/',$filetags[1],$dirs);
			
		$dir_base = $this->parent->get_component('params')['uploads_path'];
			
		//$link = $_SERVER['HTTP_HOST'] . '/masterservice/uploads/' . $dirs[1] .'/'.$dirs[2].'/'.$row['FileName'];
			
		//$link = ROOT_URL . '/uploads/' . $dirs[1] .'/'.$dirs[2].'/'.$file['FileName'];
		
		$uploads_path = $this->parent->get_component('params')['uploads_path'];
		
		$path = $uploads_path . $dirs[1] . DIRECTORY_SEPARATOR . $dirs[2] . DIRECTORY_SEPARATOR . $file['FileName']; 
		
		if(file_exists($path)) {
			
			$status = true;
			
			$status = $this->uploadfiles_db->delete_file($post['id']);
			$status = unlink($path);
			
		}
		
		if($status == true) {
			$ret['status'] = 'ok';
			$ret['content'] = $file['FileName'];
			$ret['mandate'] = $file['MandateID'];
		} else {
			$ret['status'] = 'error';
			$ret['content'] = $file['FileName'];
			$ret['mandate'] = $file['MandateID'];
		}
		
		$content = ob_get_contents();
		ob_end_clean();
		
		
		
		return $ret;
	
	}
	
	public function get_signature($mandate_id = null) {
	
		$filter = array('MandateID = ' . $mandate_id,'AttachmentType = 3');
		
		$res = $this->uploadfiles_db->list_files($filter);
		
		$signature = mysql_fetch_assoc($res);
		
		if($signature == false) {
			return false;
		}
		
		$ret['date'] = date("Y.m.d",strtotime($signature['UploadDate']));
		
		$dir_base = $this->parent->get_component('params')['uploads_path'];
		
		$year = date('Y',strtotime($signature['UploadDate']));
		
		$month = date('m',strtotime($signature['UploadDate']));
		
		$subdir = (string)$year . DIRECTORY_SEPARATOR . str_pad((string)$month,2,'0',STR_PAD_LEFT);
		
		$ret['image'] = $dir_base.$subdir.DIRECTORY_SEPARATOR.$signature['FileName'];
		
		return $ret;
	
	}
	
	public function check_upload_file($post = NULL) {
		
		//  5MB maximum file size 
		$MAXIMUM_FILESIZE = 5 * 1024 * 1024; 
		//  Valid file extensions (images,pdf) 
		$rEFileTypes = "/^\.(jpg|jpeg|gif|png|pdf|doc|docx|){1}$/i";
		
		if($post['file_size'] > $MAXIMUM_FILESIZE) {
			$ret['status'] = 'error';
			$ret['content'] = 'Túl nagy fájlméret (Max 5MB engedélyezett)!';
				
			return $ret;
		}
		
		preg_match('/\.(jpg|jpeg|gif|png|pdf|doc|docx|)$/i',$post['file_name'],$found);
		
		if(count($found) == 0) {
			$ret['status'] = 'error';
			$ret['content'] = 'Nem engedélyezett file formátum! Engedélyezett: jpg, jpeg, png, gif, pdf, doc, docx';
				
			return $ret;
		}
		
		$ret['status'] = 'ok';
		$ret['content'] = '';
		
		return $ret;
	
	}
	
	public function save_handwrite_image($post = null) {
		
		//var_dump($post);
		
		$parts = explode(',',$post['DataUrl']);
		
		$data = base64_decode($parts[1]);
		
		$dir_base = $this->parent->get_component('params')['uploads_path'];
		
		$year = date('Y',time());
		
		$month = date('m',time());
		
		$subdir = (string)$year . DIRECTORY_SEPARATOR . str_pad((string)$month,2,'0',STR_PAD_LEFT);
		
		$file_name = $post['id'] . '_' . date('Ymd',time()) . '_3.png';
		
		//var_dump($dir_base.$subdir.DIRECTORY_SEPARATOR.$file_name);
		
		$fp = fopen($dir_base.$subdir.DIRECTORY_SEPARATOR.$file_name, 'w');  
		$fp_ret = fwrite($fp, $data);  
		fclose($fp);
		
		if($fp_ret !== false) {
		
			$this->uploadfiles_db->add_file($file_name,$post['id'],3);
		
			$ret['status'] = 'ok';
			$ret['content'] = 'Aláírás feltöltve';
			
		} else {
			
			$ret['status'] = 'error';
			$ret['content'] = 'File írási hiba';
			
		}
		
		return $ret;
		
	}
	
	public function addmandate_upload($files = NULL, $inserted = NULL) {
		
		$files = $files['fileToUpload'];
		
		//var_export($files);exit;
		
		$dir_base = $this->parent->get_component('params')['uploads_path'];
		
		$year = date('Y',time());
		
		$month = date('m',time());
		
		$subdir = (string)$year . DIRECTORY_SEPARATOR . str_pad((string)$month,2,'0',STR_PAD_LEFT);
		
		$ext = pathinfo($files['name'], PATHINFO_EXTENSION);
		
		$file_name = $inserted['id'] . '_' . date('Ymd',time()) . '_0'.'.'.$ext;
		
		//var_export($file_name);exit;
		
		if(move_uploaded_file($files['tmp_name'],$dir_base.$subdir.DIRECTORY_SEPARATOR.$file_name)) {
			$this->uploadfiles_db->add_file($file_name,$inserted['id'],0);
		}
	}
	
	public function upload_file($post = null,$files = null) {
		
		//var_export($files);
		
		//var_export($post);
		
		//  5MB maximum file size 
		$MAXIMUM_FILESIZE = 5 * 1024 * 1024; 
		//  Valid file extensions (images,pdf) 
		$rEFileTypes = "/^\.(jpg|jpeg|gif|png|pdf|doc|docx|){1}$/i";
		
		$dir_base = $this->parent->get_component('params')['uploads_path'];
		
		$year = date('Y',time());
		
		$month = date('m',time());
		
		$subdir = (string)$year . DIRECTORY_SEPARATOR . str_pad((string)$month,2,'0',STR_PAD_LEFT);
		
		$ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
		
		if($files['file']['size'] > $MAXIMUM_FILESIZE) {
			$ret['status'] = 'error';
			$ret['content'] = 'Túl nagy fájlméret (Max 5MB engedélyezett)!';
				
			return $ret;
		}
		
		preg_match('/\.(jpg|jpeg|gif|png|pdf|doc|)$/i',$files['file']['name'],$found);
		
		if(count($found) == 0) {
			$ret['status'] = 'error';
			$ret['content'] = 'Nem engedélyezett file formátum! Engedélyezett: jpg, jpeg, png, gif, pdf, doc, docx';
				
			return $ret;
		}
		
		if (!file_exists($dir_base.$subdir)) {
			if(!mkdir($dir_base.$subdir, 0777, true)){
				$ret['status'] = 'error';
				$ret['content'] = 'Könyvtár létrehozás meghiúsúlt!';
				
				return $ret;
			}
		}
		
		$file_name = "";
		
		if(isset($post['attachment_type'])) {
			$file_name = $post['mandate_id'] . '_' . date('Ymd',time()) . '_' . $post['attachment_type'].'.'.$ext;
		} else {
			$ret['status'] = 'error';
			$ret['content'] = 'Nincs csatolmány típus!';
				
			return $ret;
		}
		
		if($file_name != "") {
			if(!move_uploaded_file($files['file']['tmp_name'],$dir_base.$subdir.DIRECTORY_SEPARATOR.$file_name)) {
				$ret['status'] = 'error';
				$ret['content'] = 'File feltöltési hiba!';
				
				return $ret;
			} else {
				$ret['status'] = 'ok';
				$ret['content'] = $dir_base.$subdir.DIRECTORY_SEPARATOR.$file_name;
				
				$this->uploadfiles_db->add_file($file_name,$post['mandate_id'],$post['attachment_type']);
				
				return $ret;
			}
		}
				
		$ret['status'] = 'error';
		$ret['content'] = 'Ismeretlen file feltöltési hiba!';
				
		return $ret;
	}

}
?>
