<?php
	//run ONLY if this is the first time the site is running
	
	$db_name="flexi";
	
	include("conn.php");
	
	//create database if it does not exists
	function createDB($db){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$result=mysqli_query($con,"CREATE DATABASE IF NOT EXISTS ".$db.";");
		if ($result)
		{
			return "Database <strong>".$db."</strong> was created successfully...";
		}
		else
		{
			return mysql_error();
		}
	}
	
	//tables to be created in the database
	
	//check if a table exists
	function TableExists($table){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$exists=mysqli_query($con,"SELECT 1 FROM ".$table." LIMIT 0;");
		if ($exists)
			return true;
		else
			return false;
	}
	
	//tables to be created
	//Table users
	function CreateTableUsers(){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$table="users";
		$sql="CREATE TABLE ".$table." (
			UID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			uName VarChar(100) NOT NULL,
			uIDNumber VarChar(20) NOT NULL,
			uAuth VarChar(10) NOT NULL,
			uBlock INT(1) NOT NULL,
			uSecQ longtext NOT NULL,
			uSecA longtext NOT NULL,
			uUsername VarChar(20) NOT NULL,
			uPassword text NOT NULL,
			uSession text NOT NULL DEFAULT '',
			uTimeS TIMESTAMP NOT NULL
			)";
		if (!TableExists($table)){
			$result=mysqli_query($con,$sql);
			if ($result)
				return true;
			else
				return false;
		}
		else{
			return true;
		}
	}
	//Table libcusts
	function CreateTableLibCusts(){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$table="libcusts";
		$sql="CREATE TABLE ".$table." (
			LID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			LAYear VarChar(4) NOT NULL,
			LName VarChar(50) NOT NULL,
			LNumb VarChar(50) NOT NULL,
			LStream VarChar(50) NOT NULL,
			LBan INT(1) NOT NULL,
			LForm INT(2) NOT NULL DEFAULT 1,
			LType VarChar(10) NOT NULL,
			LTimeS TIMESTAMP NOT NULL
			)";
		if (!TableExists($table)){
			$result=mysqli_query($con,$sql);
			if ($result)
				return true;
			else
				return false;
		}
		else{
			return true;
		}
	}
	//Table notifications
	function CreateTableNotifications(){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$table="notifications";
		$sql="CREATE TABLE ".$table." (
			NID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			NMsg text NOT NULL DEFAULT '',
			NTo INT NOT NULL,
			NRead INT NOT NULL,
			NTimeS TIMESTAMP NOT NULL
			)";
		if (!TableExists($table)){
			$result=mysqli_query($con,$sql);
			if ($result)
				return true;
			else
				return false;
		}
		else{
			return true;
		}
	}
	//Table books
	function CreateTableBooks(){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$table="books";
		$sql="CREATE TABLE ".$table." (
			BID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			bTitle text NOT NULL DEFAULT '',
			bAuthor text NOT NULL DEFAULT '',
			bPublisher text NOT NULL DEFAULT '',
			bBlurb longtext NOT NULL DEFAULT '',
			bEdition VarChar(10) NOT NULL DEFAULT '',
			bAccNo VarChar(50) NOT NULL DEFAULT '',
			bPoPub VarChar(50) NOT NULL DEFAULT '',
			bSubject VarChar(100) NOT NULL DEFAULT '',
			bCartegory VarChar(100) NOT NULL DEFAULT '',
			bYoPub VarChar(4) NOT NULL,
			bReserve INT(1) NOT NULL,
			bTimeS TIMESTAMP NOT NULL
			)";
		if (!TableExists($table)){
			$result=mysqli_query($con,$sql);
			if ($result)
				return true;
			else
				return false;
		}
		else{
			return true;
		}
	}
	//Table issue
	function CreateTableIssue(){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$table="issue";
		$sql="CREATE TABLE ".$table." (
			IID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			SID INT(12) NOT NULL,
			BID INT(12) NOT NULL,
			iDuration INT(12) NOT NULL,
			iState INT(1) NOT NULL,
			iTimeS TIMESTAMP NOT NULL
			)";
		if (!TableExists($table)){
			$result=mysqli_query($con,$sql);
			if ($result)
				return true;
			else
				return false;
		}
		else{
			return true;
		}
	}
	//Table logs
	function CreateTableLogs(){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$table="logs";
		$sql="CREATE TABLE ".$table." (
			LID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			UID INT(12) NOT NULL,
			lMessage longtext NOT NULL DEFAULT '',
			lTimeS TIMESTAMP NOT NULL
			)";
		if (!TableExists($table)){
			$result=mysqli_query($con,$sql);
			if ($result)
				return true;
			else
				return false;
		}
		else{
			return true;
		}
	}
	//Table activity
	function CreateTableActivity(){
		$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
		$table="activity";
		$sql="CREATE TABLE ".$table." (
			UID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			SID INT(12) NOT NULL,
			UActivity text NOT NULL DEFAULT '',
			UTimeS TIMESTAMP NOT NULL
			)";
		if (!TableExists($table)){
			$result=mysqli_query($con,$sql);
			if ($result)
				return true;
			else
				return false;
		}
		else{
			return true;
		}
	}
	
	//connect to db now
	$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
	if (!$con){
		echo mysqli_error($con)."<br/>";
		exit();
	}
	
	echo createDB($db_name)."<br/>";
	
	mysqli_select_db($con,$db_name);
	
	if (CreateTableLibCusts()==true && CreateTableUsers()==true && CreateTableNotifications()==true && CreateTableBooks()==true && CreateTableIssue()==true && CreateTableLogs()==true && CreateTableActivity()==true){
		echo "Tables created successfully...<br/><br/>";
		echo "<em>You will be redirected in 5 seconds.</em><br/><br/>";
		echo "<strong><a href='http://martin.co.ke' target='_blank'>www.martin.co.ke</a></strong>";
		header('refresh: 5; url=./lib-home');
		//exit();
	}
	else
		echo "An error occured while creating tables...<br/>";
?>