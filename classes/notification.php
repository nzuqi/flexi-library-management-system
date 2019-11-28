<?php
	/*
	This class handles all notifications within the system
	
	*/
	class notification{
		
		//for notifications stored in files
		public $filename;
		
		//read file with notifications line by line
		private function readFLBL(){
			$content="";
			if (!file_exists($this->filename)){
				$content="";
				return $content;
				exit();
			}
			$file=fopen($this->filename,"r");
			while(!feof($file)){
				$content.=fgets($file)."<br/>";
			}
			fclose($file);
			return $content;
		}
		
		//login notification
		public function lgnNotif(){
			if (strlen(trim($this->readFLBL()))>5)
				echo '<div class="alert alert-info fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.$this->readFLBL().'</div>';
		}
		
		public function setInfo($info,$alert){
			if(isset($_SESSION["INFO"]))
				unset($_SESSION["INFO"]);
			$_SESSION["INFO"]='<div class="alert alert-'.$alert.' fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.$info.'</div>';
		}
		
		public function alertInfo(){
			if(isset($_SESSION["INFO"])){
				echo $_SESSION["INFO"];
				unset($_SESSION["INFO"]);
			}
		}
		
		//!important notification
		public function printImportant(){
			$d='';
			$c=$this->countNotifs();
			if($c==1)
				$d='<div class="alert alert-info fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>You have '.$c.' unread notification.</div>';
			if($c>1)
				$d='<div class="alert alert-info fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>You have '.$c.' unread notifications.</div>';
			echo $d;
		}
		
		public function notify($to,$msg){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$que="INSERT INTO notifications(NTo,NMsg,NRead) VALUES($to,'$msg',0);";
			$res=mysqli_query($con,$que);
			if($res)
				return true;
			else
				return false;
		}
		
		//count notifications
		public function countNotifs(){
			$c=0;
			if(isset($_SESSION['CURR_USER_ID'])){
				$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
				$q="SELECT COUNT(*) AS numrows FROM notifications WHERE NTo=".$_SESSION['CURR_USER_ID']." AND NRead=0;";
				$res=mysqli_query($con,$q) or die ("Query failed counting notifications...");
				$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
				$c=$rw['numrows'];
			}
			return $c;
		}
		
		//display notifications
		public function displayNotifs(){
			$d='';
			$c=$this->countNotifs();
			if($c>0)
				$d=' ('.$c.')';
			return $d;
		}
		
	}