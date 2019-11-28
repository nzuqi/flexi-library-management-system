<?php
	ob_start();
	session_start();
	error_reporting(E_ALL);

	//constants
	define("INI_PATH","./flexi.ini");
	define("WA_REVISION","5<sup>th</sup> January, 2015");
	define("WA_VERSION","1.0.4");
	define("WA_TITLE","Flexi Library Management System");
	define("WA_TAG","Flexibility at it's best");
	define("WA_SALT","@nzuqi");
	define("D_NAME","Martin Nzuki");
	define("D_SUPPORT","hello@martin.co.ke");
	define("D_PROTOCOL","http://");
	define("D_ADMIN","");
	define("D_ADMIN_NAME","Administrator");
	define("TIMEZONE","Africa/Nairobi");
	
	//set default timezone
	date_default_timezone_set(TIMEZONE);
	
	//conection configuration
	require("conn.php");
	
	//include all classes here...
	include("classes/notification.php");
	include("classes/gui.php");
	include("classes/file.php");
	include("classes/validate.php");
	include("classes/user.php");
	include("classes/stats.php");
	
	//connectionn string
	$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);

	//MySQL query
	function query($sql,$db_name=null,$db_con_err=null) {
		$link = new mysqli(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$stmt = $link->prepare($sql) or die('Error, please contact the administrator!');
		$stmt->execute();
		$meta = $stmt->result_metadata();

		while ($field = $meta->fetch_field()) {
			$parameters[] = &$row[$field->name];
		}

		$results = array();
		call_user_func_array(array($stmt, 'bind_result'), $parameters);

		while ($stmt->fetch()) {
			foreach($row as $key => $val) {
				$x[$key] = $val;
			}
			$results[] = $x;
		}

		return $results;
		$results->close();
		$link->close();
	}

	//count query
	function count_query($query) {
		$link = new mysqli(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		if($stmt = $link->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($result);
			$stmt->fetch();
			return $result;
			$stmt->close();
		}
		$link->close();
	}

	//Write error to file
	function WriteError($err_string,$log_path){
		if (!file_exists($log_path)){
			$file=fopen($log_path,"w");
			fclose($file);
		}
		$file=fopen($log_path,"a");
		fwrite($file,"\r\n");
		fwrite($file,date("d-m-Y H:i:s")."\r\n");
		fwrite($file,$err_string);
		fwrite($file,"\r\n");
		fclose($file);
	}
	
	//set err redirect session
	function errPage($code,$path){
		if(isset($_SESSION["err_code"]))
			unset($_SESSION["err_code"]);
		$_SESSION['err_code']=$code;
		header("Location: ".$path);
	}
	
	//random key
	function randomKey(){
		$d=date("d");
		$m=date("m");
		$y=date("Y");
		$t=time();
		$dmt=$d+$m+$y+$t;
		$ran=rand(0,10000000);
		$dmtran=$dmt+$ran;
		$un=uniqid();
		$dmtun=$dmt.$un;
		$mdun=sha1($dmtran.$un);
		$sort=substr($mdun,32);
		return $sort;
	}
	
	//trim strings according to the given length & add trailing dots
	function smartTrimString($str,$len){
		$str=trim($str);
		if (strlen($str)>$len)
			return substr($str,0,$len)."..";
		else 
			return $str;
	}
	
	function ulog($id,$msg){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$que="INSERT INTO logs(UID,lMessage) VALUES($id,'".mysqli_real_escape_string($con,$msg)."');";
		$stmt = $con->query($que) or die($con->error);
		//$stmt->close;
		return true;
	}
	
	//set current sub-system session
	function setSubSys($sub){
		if(!isset($_SESSION["CURR_SUB_SYSTEM"]))
			$_SESSION["CURR_SUB_SYSTEM"]=$sub;
	}
	
	//reset sub-system session
	function resetSubSys(){
		if(isset($_SESSION["CURR_SUB_SYSTEM"]))
			unset($_SESSION["CURR_SUB_SYSTEM"]);
	}
	
	//INI configuration
	$file=new file;
	if (!file_exists(INI_PATH)){
		$file->createINI();
		header("location: ./pioneer");
	}
	$file->iniConfigs();