<?php
	
	require("globals.php");
	
	$notif=new notification;
	$user=new user;
	
	//allow/deny lib user access
	function libcustsAction($id,$a){
		$sql2="UPDATE libcusts SET LBan=$a WHERE LID=$id LIMIT 1;";
		$result2=mysql_query($sql2);
		if ($result2)
			return true;
		else
			return false;
	}
	
	if(isset($_GET["action"]) && isset($_GET["id"])){
		dbconnect();
		if($_GET["action"]=="allow"){
			if(libcustsAction($_GET["id"],0)){
				$notif->setInfo("Successfully allowed library access to the library user.","success");
				ulog($_SESSION["CURR_USER_ID"],"Successfully allowed library access to a library user...");	//log this activity
			}
			else{
				$notif->setInfo("An error occured while allowing a library user access.","danger");
				ulog($_SESSION["CURR_USER_ID"],"Incurred an error allowing a library user access...");	//log this activity
			}
		}
		elseif($_GET["action"]=="deny"){
			if(libcustsAction($_GET["id"],1)){
				$notif->setInfo("Successfully denied library access to the library user.","success");
				ulog($_SESSION["CURR_USER_ID"],"Successfully denied library access to a library user...");	//log this activity
			}
			else{
				$notif->setInfo("An error occured while denying a library user access.","danger");
				ulog($_SESSION["CURR_USER_ID"],"Incurred an error denying a library user access...");	//log this activity
			}
		}
		if(isset($_GET["staff"]))
			header("location: ./lib-students?staff");
		else
			header("location: ./lib-students");
		exit();
	}
	
?>