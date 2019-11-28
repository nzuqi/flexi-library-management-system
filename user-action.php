<?php
	require("globals.php");
	
	//===create objects===
	$notif=new notification;
	$stats=new stats;
	
	//check if unauthorized person is viewing this, deny request if true
	if ($_SESSION["CURR_USER_AUTH"]!="admin"){
		$notif->setInfo("You are not authorized to view that page.","warning");
		header('location: ./err/?code=401');
		exit();
	}
	
	if (!isset($_GET["id"]) && !isset($_GET["state"])){
		$notif->setInfo("Sorry, bad parameters detected.","warning");
		header('location: ./');
		exit();
	}
	
	$state=$_GET["state"];
	$id=$_GET["id"];
	
	//delete everything about a staff...
	function killStaff(){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$s2="DELETE FROM logs WHERE UID=".$_GET["id"].";";
		$s3="DELETE FROM notifications WHERE NTo=".$_GET["id"].";";
		$re2=mysqli_query($con,$s2);
		$re3=mysqli_query($con,$s3);
		if ($re2 && $re3)
			return true;
		else
			return false;
	}
	
	//make the changes now...
	if ($state=="block")
		$sql="UPDATE users SET uBlock=1 WHERE UID=".$id." LIMIT 1;";
	elseif ($state=="unblock")
		$sql="UPDATE users SET uBlock=0 WHERE UID=".$id." LIMIT 1;";
	elseif ($state=="delete")
		$sql="DELETE FROM users WHERE UID=".$id." LIMIT 1;";
	$result=mysqli_query($mysqli,$sql);
	if ($result){
		if ($state=="delete"){
			//delete ALL user's data
			if (killStaff()){
				$notif->setInfo("User was successful deleted.","success");
				ulog($_SESSION["CURR_USER_ID"],"Successfully deleted a library user...");	//log this activity
				header('location: ./accounts');
				exit();
			}
			else{
				$notif->setInfo("An error occured while deleting the user.","danger");
				ulog($_SESSION["CURR_USER_ID"],"Error incurred while deleting a library user...");	//log this activity
				header('location: ./accounts');
				exit();
			}
		}
		else{
			$notif->setInfo("Action to '<b>$state</b>' user was successful.","success");
			ulog($_SESSION["CURR_USER_ID"],"Performed action '$state' to a library user...");	//log this activity
			header('location: ./accounts');
			exit();
		}
	}
	else{
		$notif->setInfo("A critical error occured, please try again later.","danger");
		ulog($_SESSION["CURR_USER_ID"],"Incurred a critical error while performing unknown actions to a library user...");	//log this activity
		header('location: ./accounts');
		exit();
	}