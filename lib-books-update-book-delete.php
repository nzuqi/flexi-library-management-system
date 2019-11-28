<?php
	
	require('globals.php');
	
	$notif=new notification;
	
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		
		dbconnect();
		
		$quer=mysql_query("SELECT * FROM books WHERE BID=".trim($_POST["id"]).";");
		$res=mysql_num_rows($quer);
		if ($res==0){
			$notif->setInfo("The book record does not exist in the system.",'danger');
			header('location: ./lib-books-update');
			exit();
		}
		
		$sql2="DELETE FROM books WHERE BID=".trim($_POST["id"])." LIMIT 1;";
		$result2=mysql_query($sql2);
		if ($result2){
			ulog($_SESSION["CURR_USER_ID"],"Successfully deleted a book record and any details attached to the book in the system...");	//log this activity
			$notif->setInfo("The book record were successfully deleted.","success");
		}
		else{
			$notif->setInfo("A critical error occured while deleting the record. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
		}
		
		header('location: ./lib-books-update');
		exit();
	}
	
	$notif->setInfo("Hey, you should consider reporting this error to the developer, ".D_NAME,"warning");
	header('location: ./lib-books-update');
	exit();
	
?>