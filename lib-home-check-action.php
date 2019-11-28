<?php
	
	require("globals.php");
	
	$notif=new notification;
	$user=new user;
	
	//insert to db to issue
	function issueBooks($sid,$bid,$dur){
		$sql2="INSERT INTO issue(SID,BID,iDuration,iState) VALUES($sid,$bid,$dur,0);";
		$result2=mysql_query($sql2);
		if ($result2)
			return true;
		else
			return false;
	}
	
	//flag book as lost
	function lostBook($iid){
		$sql2="UPDATE issue SET iState=2 WHERE IID=$iid;";
		$result2=mysql_query($sql2);
		if ($result2)
			return true;
		else
			return false;
	}
	
	//flag book as compensated
	function compensateBook($iid){
		$sql2="UPDATE issue SET iState=3 WHERE IID=$iid;";
		$result2=mysql_query($sql2);
		if ($result2)
			return true;
		else
			return false;
	}
	
	//flag book as returned
	function returnBook($iid){
		$sql2="UPDATE issue SET iState=1 WHERE IID=$iid;";
		$result2=mysql_query($sql2);
		if ($result2)
			return true;
		else
			return false;
	}
	
	//flag book as returned and overdue charges paid
	function ocreturnBook($iid,$oc){
		$sql2="UPDATE issue SET iState=4,iOCharge=$oc WHERE IID=$iid;";
		$result2=mysql_query($sql2);
		if ($result2)
			return true;
		else
			return false;
	}
	
	if(isset($_GET["activity"]) && isset($_GET["id"])){
		
		dbconnect();
		
		if($_GET["activity"]=="read"){
			//update activity
			if($user->updateActivity($_GET["id"],"read")){
				$notif->setInfo(mysql_error()."Successfully checked in the library for reading.","success");
				ulog($_SESSION["CURR_USER_ID"],"Checked in a person for 'reading' successfully...");	//log this activity
			}
			else{
				$notif->setInfo("An error occured while checking in library user for reading.","danger");
				ulog($_SESSION["CURR_USER_ID"],"Incurred an error while checking in a person for 'reading'...");	//log this activity
			}
		}
		elseif($_GET["activity"]=="lost"){
			if(isset($_GET["iid"])){
				if(lostBook($_GET["iid"])){
					$notif->setInfo("Successfully marked the book as 'lost'.","success");
					ulog($_SESSION["CURR_USER_ID"],"Successfully marked a book as 'lost'...");	//log this activity
					header("location: ./lib-home-check-return?id=FL".$_GET["id"]);
					exit();
				}
				else{
					$notif->setInfo("An error occured while marking a library book as 'lost'.","danger");
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while marking a book as 'lost'...");	//log this activity
				}
			}
		}
		elseif($_GET["activity"]=="compensate"){
			if(isset($_GET["iid"])){
				if(compensateBook($_GET["iid"]) && $user->updateActivity($_GET["id"],"return")){
					$notif->setInfo("Successfully marked the book as 'compensated'.","success");
					ulog($_SESSION["CURR_USER_ID"],"Successfully marked a book as 'compensated'...");	//log this activity
					header("location: ./lib-home-check-return?id=FL".$_GET["id"]);
					exit();
				}
				else{
					$notif->setInfo("An error occured while marking a library book as 'compensated'.","danger");
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while marking a book as 'compensated'...");	//log this activity
				}
			}
		}
		elseif($_GET["activity"]=="return"){
			if(returnBook($_GET["iid"]) && $user->updateActivity($_GET["id"],"return")){
				$notif->setInfo("Successfully marked the book as 'returned'.","success");
				ulog($_SESSION["CURR_USER_ID"],"Successfully marked a book as 'returned'...");	//log this activity
				header("location: ./lib-home-check-return?id=FL".$_GET["id"]);
				exit();
			}
			else{
				$notif->setInfo("An error occured while marking a library book as 'returned'.","danger");
				ulog($_SESSION["CURR_USER_ID"],"Incurred an error while marking a book as 'returned'...");	//log this activity
			}
		}
		elseif($_GET["activity"]=="ocreturn"){
			if(ocreturnBook($_GET["iid"],$_GET["oc"]) && $user->updateActivity($_GET["id"],"return")){
				$notif->setInfo("Successfully marked the book as 'returned', and overdue charges of Ksh. ".$_GET["oc"]." cleared.","success");
				ulog($_SESSION["CURR_USER_ID"],"Successfully marked a book as 'returned', and Ksh. ".$_GET["oc"]." cleared...");	//log this activity
				header("location: ./lib-home-check-return?id=FL".$_GET["id"]);
				exit();
			}
			else{
				$notif->setInfo("An error occured while marking a library book as 'returned'.","danger");
				ulog($_SESSION["CURR_USER_ID"],"Incurred an error while marking a book as 'returned'...");	//log this activity
			}
		}
		elseif($_GET["activity"]=="other"){
			//update activity
			if($user->updateActivity($_GET["id"],"other")){
				$notif->setInfo("Successfully checked in the library for other uses.","success");
				ulog($_SESSION["CURR_USER_ID"],"Checked in a person for 'other' successfully...");	//log this activity
			}
			else{
				$notif->setInfo("An error occured while checking in library user for other uses.","danger");
				ulog($_SESSION["CURR_USER_ID"],"Incurred an error while checking in a person for 'other'...");	//log this activity
			}
		}
		header("location: ./lib-home");
		exit();
	}
	
	//======>> ISSUE BOOKS
	$err=$uid=$ddays=$max_issue="";
	
	//upon submit... For issuing books
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		$ddays=$_POST['ddays'];
		$uid=$_POST['uid'];
		$max_issue=$_POST['max_issue'];
		
		//run tests
		if (empty($ddays)){
			$err="Please set the due days prior to expiry of issued book(s).";
			$notif->setInfo($err,"danger");
		}
		elseif (empty($ddays)){
			$err="<b>days</b> prior to expiry of issued book(s).";
			$notif->setInfo($err,"danger");
		}
		elseif (!preg_match('/[0-9]/',$ddays) || preg_match('/[a-zA-Z]/',$ddays)){
			$err="Only <em>numbers</em> are allowed in expiry of issued books.";
			$notif->setInfo($err,'danger');
		}
		
		if($err==""){
		
			dbconnect();
			
			$cnt=$s=0;
			for ($x=1;$x<=$max_issue;$x++){
				if(isset($_SESSION["BORROW_FL".$uid."_B$x"])){
					if(issueBooks($uid,$_SESSION["BORROW_FL".$uid."_B$x"],$ddays)){
						$cnt=$cnt+1;
						//unset values
						unset($_SESSION["BORROW_FL".$uid."_B$x"]);
					}
					$s=$s+1;
				}
			}
			//update activity
			$user->updateActivity($uid,"borrow");
			if ($cnt==$s){
				$notif->setInfo($cnt.mysql_error()." book(s) were successfully recorded as issued. Go to the 'View Issued Books' section for more details.","success");
				ulog($_SESSION["CURR_USER_ID"],"Issued $cnt books successfully...");	//log this activity
				header("location: ./lib-home");
			}
			else{
				$notif->setInfo(($s-$cnt)." books were not successfully recorded as issued, whereas ".$cnt." books were recorded as issued. Go to the 'View Issued Books' section for more details.","danger");
				ulog($_SESSION["CURR_USER_ID"],"Issued $cnt books successfully, ".($s-$cnt)." books failed...");	//log this activity
				header("location: ./lib-home-check-borrow?id=FL".$uid);
			}
			exit();
			
		}
		else{
			header("location: ./lib-home-check-borrow?id=FL".$uid);
			exit();
		}
	}
	
?>