<?php
	
	require("globals.php");
	
	$notif=new notification;
	
	//promote students
	function promoteStuds(){
		$sql2="UPDATE libcusts SET LForm=LForm+1 WHERE LForm<>4;";
		$result2=mysqli_query($mysqli, $sql2);
		if ($result2)
			return true;
		else
			return false;
	}
	
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		$ptype=$_POST['ptype'];
		if($ptype=='promote'){
			if(promoteStuds()){
				$notif->setInfo("Successfully promoted students.","success");
				ulog($_SESSION["CURR_USER_ID"],"Successfully promoted students...");	//log this activity
			}
			else{
				$notif->setInfo("An error occured while promoting students.","danger");
				ulog($_SESSION["CURR_USER_ID"],"Incurred an error while promoting students...");	//log this activity
			}
		}
		
		header("location: ./lib-students-update");
		exit();
	}
	
?>