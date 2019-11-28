<?php
	
	require('globals.php');
	
	$notif=new notification;
	
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		
		$quer=mysqli_query($mysqli, "SELECT * FROM libcusts WHERE LID=".trim($_POST["id"])." AND LType='".trim($_POST["type"])."';");
		$res=mysqli_num_rows($quer);
		if ($res==0){
			$notif->setInfo("The ".$_POST["type"]." record does not exist in the system.",'danger');
			header('location: ./lib-students-update');
			exit();
		}
		
		$sql2="DELETE FROM libcusts WHERE LID=".trim($_POST["id"])." LIMIT 1;";
		$result2=mysqli_query($sql2);
		if ($result2){
			ulog($_SESSION["CURR_USER_ID"],"Successfully deleted a ".$_POST["type"]." record and any details attached to the ".$_POST["type"]." in the system...");	//log this activity
			$notif->setInfo("The ".$_POST["type"]." records were successfully deleted.","success");
		}
		else{
			$notif->setInfo("A critical error occured while deleting the ".$_POST["type"]." record. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
		}
		
		header('location: ./lib-students-update');
		exit();
	}
	
	$notif->setInfo("Hey, you should consider reporting this error to the developer, ".D_NAME,"warning");
	header('location: ./lib-students-update');
	exit();
	
?>