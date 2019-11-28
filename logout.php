<?php
	require("globals.php");
	
	$user=new user;
	$notif=new notification;
	$file=new file;
	
	$rURL="./";
	if (isset($_GET['next'])){
		$rURL=$_GET['next'];
	}
	
	if (!$user->login_check($mysqli)){
		$notif->setInfo("You are already logged out, log in to start a new session.","info");
		header("location: ".$rURL);
		exit();
	}
	
	function discardSessionID(){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$quer="UPDATE users SET uSession='' WHERE uSession='".session_id()."';";
		$res=mysqli_query($con,$quer);
		if ($res){
			return true;
		}
		else
			return false;
	}
	
	if (discardSessionID()){
		// Delete the actual cookie. 
		setcookie(session_name(),
		'', time() - 42000, 
		$params["path"], 
		$params["domain"], 
		$params["secure"], 
		$params["httponly"]);
		
		ulog($_SESSION["CURR_USER_ID"],"Logged out of the system...");	//log this activity
		$user->unsetUserSessVars();
		$file->cleariniConfigs();
		$notif->setInfo("You have been logged out. Login to start a new session.","success");
		header("location: ".$rURL);
	}
	else{
		$notif->setInfo("You are not logged in. Login to start a new session.","alert");
		header("location: ".$rURL);
	}