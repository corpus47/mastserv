<?php

require_once('phpmailer/PHPMailerAutoload.php');

if($_SERVER['HTTP_HOST'] === 'localhost') {
    
    define('_USER','root');
    
    define('_PASSWD', 'sc1959op');
    
    define('_DB', 'hdt');
    
} elseif($_SERVER['HTTP_HOST'] === '195.228.35.4') {
    
    define('_USER','root');
    
    define('_PASSWD', 'sc1959op');
    
    define('_DB', 'hdt');
    
} else {
    
    define('_USER','dev_mester');
    
    define('_PASSWD', 'Dev_admin1');
    
    define('_DB', 'dev_mesterszervizt');
    
}

class Support{
	
	const HOST = 'localhost';
	
	const USER = _USER;
	
	const PASSWD = _PASSWD;
	
	const DB = _DB;
	
	/*if($_SERVER['HTTP_HOST'] === 'localhost') {
	    
	    const USER = 'root';
	    
	    const PASSWD = 'sc1959op';
	    
	    const DB = 'hdt';
	    
	} elseif($_SERVER['HTTP_HOST'] === '195.228.35.4') {
	    
	    const USER = 'root';
	    
	    const PASSWD = 'sc1959op';
	    
	    const DB = 'hdt';
	    
	} else {
	
    	const USER = 'dev_mester';
    	
    	const PASSWD = 'Dev_admin1';
    	
    	const DB = 'dev_mesterszerviz';
	
	}*/
	
	private $conn = NULL;
	
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
	
	public function __construct(){
		
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
									.'<img style="margin-left:10px;display:inline-block;vertical-align:middle;height:30px;width:auto;" src="cid:hdt-logo" /><span style="color:#ffffff;"><strong>MASTERSERVICE</strong>&nbsp;SUPPORT</span>'
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
		
		//$this->send_email('37');
		//exit;
		
		if($this->isAjax()){
			//var_dump($_POST);
			//var_export($_FILES['attachment-file']);

			$this->addBug();

		}
	}
	
	private function send_email($id = NULL) {
		
		//var_dump($id);
		
		//exit;
		
		if($id === NULL) {
			
			$ret['status'] = 'error';
			$ret['message'] = 'ID is null';
			
			echo json_encode($ret);
			
			die();
			
		}
		//var_dump($id);
		
		$this->connect();
			
		try {
			
			$stmt = $this->conn->prepare('SELECT * FROM ms_bugs WHERE ID=:ID');
			$stmt->bindValue(':ID',$id,PDO::PARAM_INT);
			$stmt->execute();	
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			//var_dump($row);
			
			$this->disconnect();
			
			if($row === false) {
				
				
				$ret['status'] = 'error';
				$ret['massage'] = 'Bug report not found 2';
				
				echo json_encode($ret);
				
				die();
			}		
				
		} catch(PDOException $ex) {
			//echo $ex->getMessage();
			
			$ret['status'] = 'error';
			$ret['message'] = $ex->getMessage();
			
			die();
		}
			
		$this->disconnect();
		
		// Create e-mail
		
		$this->phpmailer->addAddress('csuporbela@gmail.com');
		
		$this->phpmailer->Subject = 'HDT Masterservice - Értesítés megbízás hibáról';
		
		$this->phpmailer->Body = $this->content_header;
		
		$this->phpmailer->Body .= '<p>Url: ' . $row['Url'] . '</p><br />';
		
		$this->phpmailer->Body .= '<p>Leírás: ' . $row['Comment'] . '</p><br />';
		
		$this->phpmailer->Body .= $this->content_footer;
		
		$this->phpmailer->CreateBody();
		//try{
		$eret = $this->phpmailer->send();
		
	}
	
	private function addBug() {

		if(isset($_FILES['attachment-file']) && $_FILES['attachment-file']['type'] !== 'image/jpeg' && $_FILES['attachment-file']['type'] !== 'image/jpg' && $_FILES['attachment-file']['type'] !== 'image/png' && $_FILES['attachment-file']['type'] !== 'image/gif' && $_FILES['attachment-file']['type'] !== '') {
			
			$ret['status'] = 'error';
			$ret['message'] = 'Nem képet töltött fel';

		} else {
			
			$this->connect();
			
			try {
			
				$stmt = $this->conn->prepare('INSERT INTO ms_bugs (Uid,Url,Comment,Time) VALUES (:uid,:url,:comment,NOW())');
				
				//$this->conn->beginTransaction();
				
				$stmt->execute(array(
					'uid' => $_SESSION['HDT_supuid'],
					'url' => $_POST['bug-url'],
					'comment' => $_POST['bug-description']
				));	

				//$this->conn->commit();
				
				$last_id = $this->conn->lastInsertId();
				//var_dump($last_id);
			} catch(PDOException $ex) {
				
				//$this->conn->rollback();
				
				$ret['status'] = 'error';
				$ret['message'] = $ex->getMessage();
				
				echo json_encode($ret);
				
				die();
			}
			
			$this->disconnect();

			$max_file_size = 1024*768; // 200kb
			$valid_exts = array('jpeg', 'jpg', 'png', 'gif');
			// thumbnail sizes
			//$sizes = array(100 => 100, 150 => 150, 250 => 250);
			$sizes = array(1024 => 768);
			
			//var_dump($_FILES);
			
			if (isset($_FILES['attachment-file'])) {
				//var_dump($_FILES);
				//var_dump($max_file_size);
				//var_dump($_FILES['attachment-file']['size']);
			  if( $_FILES['attachment-file']['size'] > $max_file_size ){
			  	//echo 'Itt';
			    // get file extension
			    $ext = strtolower(pathinfo($_FILES['attachment-file']['name'], PATHINFO_EXTENSION));
				
			    if (in_array($ext, $valid_exts)) {
			      /* resize image */
			      foreach ($sizes as $w => $h) {
			        $files[] = $this->resize($w, $h, $last_id);
			      }

			    } else {
			      $msg = 'Unsupported file';
			    }
			  } else{
			    //$msg = 'Please upload image smaller than 200KB';
				
				$path = 'uploads/' . $last_id;
				
				switch ($_FILES['attachment-file']['type']) {
					case 'image/jpeg':
						$path .= '.jpg';
						//var_dump($path);
						//imagejpeg($tmp, $path, 100);
						break;
					case 'image/png':
						$path .= '.png';
						//imagepng($tmp, $path, 0);
						break;
					case 'image/gif':
						$path .= '.gif';
						//imagegif($tmp, $path);
						break;
					default:
						//exit;
						break;
				}
				
				if (move_uploaded_file($_FILES["attachment-file"]["tmp_name"], $path)) {
					
					$ret['status'] = 'ok';
					//$ret['message'] = $path;
					$ret['message'] = 'picture-saved';
					
					//echo json_encode($ret);
					
					//die();
					
				} elseif($_FILES["attachment-file"]['name'] == ""){
					
					$ret['status'] = 'ok';
					$ret['message'] = 'bug-saved';
					
				} else {
					
					$ret['status'] = 'error';
					$ret['message'] = 'File feltöltés hiba';
					
				}
				
			  }
			}
			
			$this->send_email($last_id);
			
			echo json_encode($ret);
			
			die();

		}
		
		//if($ret['status'] == 'ok') {
		
		//}
		
		

		exit;

	}

	private function resize($width, $height ,$id = NULL){
		
		/* Get original image x y*/

		list($w, $h) = getimagesize($_FILES['attachment-file']['tmp_name']);
		
		/* calculate new image size with ratio */
		$ratio = max($width/$w, $height/$h);
		$h = ceil($height / $ratio);
		$x = ($w - $width / $ratio) / 2;
		$w = ceil($width / $ratio);
		
		/* new file name */
		//$path = 'uploads/'.$width.'x'.$height.'_'.$_FILES['attachment-file']['name'];
		$path = 'uploads/' . $id;
		//var_dump($path);
		/* read binary data from image file */
		$imgString = file_get_contents($_FILES['attachment-file']['tmp_name']);
		/* create image from string */
		$image = imagecreatefromstring($imgString);
		$tmp = imagecreatetruecolor($width, $height);
		imagecopyresampled($tmp, $image,
		0, 0,
		$x, 0,
		$width, $height,
		$w, $h);
		/* Save image */
		switch ($_FILES['attachment-file']['type']) {
			case 'image/jpeg':
				$path .= '.jpg';
				//var_dump($path);
				imagejpeg($tmp, $path, 100);
			    break;
			case 'image/png':
				$path .= '.png';
			    imagepng($tmp, $path, 0);
			    break;
			case 'image/gif':
				$path .= '.gif';
			    imagegif($tmp, $path);
			    break;
			default:
			    exit;
			    break;
		}
		return $path;
		/* cleanup memory */
		imagedestroy($image);
		imagedestroy($tmp);
	}

	private function isAjax() {
	        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}

	public function check_logged() {
		
		if(isset($_SESSION['HDT_uid'])) {
			return $_SESSION['HDT_uid'];
		} elseif(isset($_SESSION['HDT_supuid'])){
			return $_SESSION['HDT_supuid'];
		} elseif(isset($_POST['loginname']) && isset($_POST['loginpwd'])) {
			return true;
		} else {
			return false;
		}
		
	}
	
	private function connect(){
		try{
			$this->conn = new PDO('mysql:host='.self::HOST.';dbname='.self::DB.';charset=utf8', self::USER, self::PASSWD);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $ex){
			echo $ex->getMessage();
			$this->conn = NULL;
		} 
		return;
		
	}
	
	private function disconnect(){
		
		$this->pdo = NULL;
		
		return;
		
	}
	
	public function pdo_test(){
		
		$this->connect();
		
		//var_dump($this->pdo);
		
		$this->disconnect();
		
	}
	
	public function check_login() {
	
		if(isset($_POST['loginname']) && isset($_POST['loginpwd'])) {
			
			$this->connect();
			
			try {
			
				$stmt = $this->conn->prepare('SELECT * FROM sys_users WHERE Login=:Login');
				$stmt->bindValue(':Login',$_POST['loginname'],PDO::PARAM_STR);
				$stmt->execute();	
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
				
				if($row === false) {
					$this->disconnect();
					return null;
				}
				
				$password = sha1($_POST['loginpwd'] . $row['Salt']);
				
				if($password === $row['Pwd']) {
					$_SESSION['HDT_supuid'] = $row['ID'];
					header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
					die();
				}
				
				
			} catch(PDOException $ex) {
				echo $ex->getMessage();
				die();
			}
			
			$this->disconnect();	
			
		} else {
			return null;
		}
	
	}
	
	public function new_buginfo() {
		
	}
	
	public function get_user($id = NULL){
		
		$this->connect();

		try {
			$stmt = $this->conn->prepare("SELECT * FROM sys_users WHERE ID=:ID");
			$stmt->bindValue(':ID', $id, PDO::PARAM_INT);
			$stmt->execute();	
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

		} catch(PDOException $ex){
			echo $ex->getMessage();
			$row = array();
		} 
		
		
		
		$this->disconnect();
		
		return $row;
		
	}
}
