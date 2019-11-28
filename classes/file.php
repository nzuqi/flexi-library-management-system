<?php
	
	class file{
		
		//Write error to file
		public function WriteError($err_string){
			if (!file_exists(ERROR_LOG_PATH)){
				$file=fopen(ERROR_LOG_PATH,"w");
				fclose($file);
			}
			$file=fopen(ERROR_LOG_PATH,"a");
			fwrite($file,"\r\n");
			fwrite($file,date("d-m-Y H:i:s")."\r\n");
			fwrite($file,$err_string);
			fwrite($file,"\r\n");
			fclose($file);
		}
		
		//Update INI
		public function overwriteINI($akey,$iname,$ipost,$icode,$itown,$max_books,$ocharge){
			$file=fopen(INI_PATH,"w");
			fwrite($file,"[Flexi]\r\n");
			fwrite($file,"\r\n");
			fwrite($file,";Institution details\r\n");
			fwrite($file,"host_name=".$iname."\r\n");
			fwrite($file,"host_box=".$ipost."\r\n");
			fwrite($file,"host_postalcode=".$icode." \r\n");
			fwrite($file,"host_ctown=".$itown."\r\n");
			fwrite($file,"\r\n");
			fwrite($file,";Activation details\r\n");
			fwrite($file,"act_key=".$akey."\r\n");
			fwrite($file,"\r\n");
			fwrite($file,";Library settings\r\n");
			fwrite($file,"max_books=".$max_books."\r\n");
			fwrite($file,"overdue_charges=".$ocharge."\r\n");
			fwrite($file,"\r\n");
			fclose($file);
		}
		
		//INI configs
		public function iniConfigs(){
			$this->cleariniConfigs();
			$config_array=parse_ini_file(INI_PATH, "Lib");
			foreach($config_array as $Val){
				if(!isset($_SESSION["host_name"]))
					$_SESSION["host_name"]=$Val["host_name"];
				if(!isset($_SESSION["host_box"]))
					$_SESSION["host_box"]=$Val["host_box"];
				if(!isset($_SESSION["host_postalcode"]))
					$_SESSION["host_postalcode"]=$Val["host_postalcode"];
				if(!isset($_SESSION["host_ctown"]))
					$_SESSION["host_ctown"]=$Val["host_ctown"];
				if(!isset($_SESSION["act_key"]))
					$_SESSION["act_key"]=$Val["act_key"];
				if(!isset($_SESSION["LIB_MAX_BOOKS"]))
					$_SESSION["LIB_MAX_BOOKS"]=$Val["max_books"];
				if(!isset($_SESSION["LIB_OVERDUE_CHARGES"]))
					$_SESSION["LIB_OVERDUE_CHARGES"]=$Val["overdue_charges"];
			}
		}
		
		public function createINI(){
			$file=fopen(INI_PATH,"w");
			fwrite($file,"[Flexi]\r\n");
			fwrite($file,"\r\n");
			fwrite($file,";Institution details\r\n");
			fwrite($file,"host_name= \r\n");
			fwrite($file,"host_box= \r\n");
			fwrite($file,"host_postalcode= \r\n");
			fwrite($file,"host_ctown= \r\n");
			fwrite($file,"\r\n");
			fwrite($file,";Activation details\r\n");
			fwrite($file,"act_key= \r\n");
			fwrite($file,"\r\n");
			fwrite($file,";Library settings\r\n");
			fwrite($file,"max_books=5\r\n");
			fwrite($file,"overdue_charges=5\r\n");
			fwrite($file,"\r\n");
			fclose($file);
		}
		
		//clear INI configs
		public function cleariniConfigs(){
			if(isset($_SESSION["host_name"]))
				unset($_SESSION["host_name"]);
			if(isset($_SESSION["host_box"]))
				unset($_SESSION["host_box"]);
			if(isset($_SESSION["host_postalcode"]))
				unset($_SESSION["host_postalcode"]);
			if(isset($_SESSION["host_ctown"]))
				unset($_SESSION["host_ctown"]);
			if(isset($_SESSION["act_key"]))
				unset($_SESSION["act_key"]);
			if(isset($_SESSION["LIB_MAX_BOOKS"]))
				unset($_SESSION["LIB_MAX_BOOKS"]);
			if(isset($_SESSION["LIB_OVERDUE_CHARGES"]))
				unset($_SESSION["LIB_OVERDUE_CHARGES"]);
		}
		
		//Write log to file
		public function WriteLog($log_string){
			if (!file_exists(LOG_PATH)){
				$file=fopen(LOG_PATH,"w");
				fclose($file);
			}
			$file=fopen(LOG_PATH,"a");
			fwrite($file,"\r\n");
			fwrite($file,date("d-m-Y H:i:s")."\r\n");
			fwrite($file,$log_string);
			fwrite($file,"\r\n");
			fclose($file);
		}
		
		//safe delete files
		public function safeDeleteDFile($file_path,$file){
			$c=0;
			$q="SELECT COUNT(DID) AS numrows FROM downloads WHERE dFileN='$file';";
			$res=mysql_query($q) or die ("Query failed...");
			$rw=mysql_fetch_array($res,MYSQL_ASSOC);
			$c=$rw['numrows'];
			if ($c==1){
				if ($this->deleteFile($file_path))
					return true;
				else
					return false;
			}
			else
				return false;
		}
		
		//delete files
		public function deleteFile($file_path){
			if (file_exists($file_path)){
				if(unlink($file_path))
					return true;
				else
					return false;
			}
			else
				return false;
		}
		
		//create thumbs/resize upload images
		public function createThumb($path1, $path2, $file_type, $new_w, $new_h, $squareSize = ''){
			/* read the source image */
			$source_image = FALSE;
			
			if (preg_match("/jpg|JPG|jpeg|JPEG/", $file_type)) {
				$source_image = imagecreatefromjpeg($path1);
			}
			elseif (preg_match("/png|PNG/", $file_type)) {
				
				if (!$source_image = @imagecreatefrompng($path1)) {
					$source_image = imagecreatefromjpeg($path1);
				}
			}
			elseif (preg_match("/gif|GIF/", $file_type)) {
				$source_image = imagecreatefromgif($path1);
			}		
			if ($source_image == FALSE) {
				$source_image = imagecreatefromjpeg($path1);
			}

			$orig_w = imageSX($source_image);
			$orig_h = imageSY($source_image);
			
			if ($orig_w < $new_w && $orig_h < $new_h) {
				$desired_width = $orig_w;
				$desired_height = $orig_h;
			} else {
				$scale = min($new_w / $orig_w, $new_h / $orig_h);
				$desired_width = ceil($scale * $orig_w);
				$desired_height = ceil($scale * $orig_h);
			}
					
			if ($squareSize != '') {
				$desired_width = $desired_height = $squareSize;
			}

			/* create a new, "virtual" image */
			$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
			// for PNG background white----------->
			$kek = imagecolorallocate($virtual_image, 255, 255, 255);
			imagefill($virtual_image, 0, 0, $kek);
			
			if ($squareSize == '') {
				/* copy source image at a resized size */
				imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $orig_w, $orig_h);
			} else {
				$wm = $orig_w / $squareSize;
				$hm = $orig_h / $squareSize;
				$h_height = $squareSize / 2;
				$w_height = $squareSize / 2;
				
				if ($orig_w > $orig_h) {
					$adjusted_width = $orig_w / $hm;
					$half_width = $adjusted_width / 2;
					$int_width = $half_width - $w_height;
					imagecopyresampled($virtual_image, $source_image, -$int_width, 0, 0, 0, $adjusted_width, $squareSize, $orig_w, $orig_h);
				}

				elseif (($orig_w <= $orig_h)) {
					$adjusted_height = $orig_h / $wm;
					$half_height = $adjusted_height / 2;
					imagecopyresampled($virtual_image, $source_image, 0,0, 0, 0, $squareSize, $adjusted_height, $orig_w, $orig_h);
				} else {
					imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $squareSize, $squareSize, $orig_w, $orig_h);
				}
			}
			
			if (@imagejpeg($virtual_image, $path2, 90)) {
				imagedestroy($virtual_image);
				imagedestroy($source_image);
				return TRUE;
			} else {
				return FALSE;
			}
		}
		
	}