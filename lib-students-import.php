<?php
	
	require("globals.php");
	
	$notif=new notification;
	
	//declare variables
	$sfile=$err="";
	
	//upon submit...
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		
		//run some tests
		if (empty($_FILES["file"]["name"])){
			$err="Please select an Excel workbook file to import data from...";
			$notif->setInfo($err,"warning");
		}
		if (!empty($_FILES["file"]["name"])){
			$allowedExts = array("xls");
			$temp = explode(".", $_FILES["file"]["name"]);
			$extension = end($temp);
			
			if ($_FILES["file"]["size"] < 10000000 && in_array($extension, $allowedExts)) {
				if ($_FILES["file"]["error"] > 0) {
					$err=$_FILES["file"]["error"];
					$notif->setInfo($err,"danger");
				}
				else{
					include './plugins/phpexcelreader/excel_reader.php';
					$excel = new PhpExcelReader;
					$excel->read($_FILES['file']['tmp_name']);
					
					$sql="INSERT INTO libcusts (LAYear,LName,LNumb,LBan,LType) VALUES ";
					
					$sheet=$excel->sheets[0];
					$x = 2;
					while($x <= $sheet['numRows']){
						$cellNo = isset($sheet['cells'][$x][1]) ? $sheet['cells'][$x][1] : '';
						$cellName = isset($sheet['cells'][$x][2]) ? $sheet['cells'][$x][2] : '';
						$cellYear = isset($sheet['cells'][$x][3]) ? $sheet['cells'][$x][3] : '';
						
						if ($x==$sheet['numRows']){
							if (isset($_GET["staff"]))
								$sql.="('$cellYear','".mysqli_real_escape_string($mysqli, $cellName)."','$cellNo',0,'staff');";
							else
								$sql.="('$cellYear','".mysqli_real_escape_string($mysqli, $cellName)."','$cellNo',0,'student');";
						}
						else{
							if (isset($_GET["staff"]))
								$sql.="('$cellYear','".mysqli_real_escape_string($mysqli, $cellName)."','$cellNo',0,'staff'),";
							else
								$sql.="('$cellYear','".mysqli_real_escape_string($mysqli, $cellName)."','$cellNo',0,'student'),";
						}
						$x++;
					}
						
					//echo $sql;
					$result=mysqli_query($mysqli, $sql);
					if ($result){
						if (isset($_GET["staff"]))
							ulog($_SESSION["CURR_USER_ID"],"Successfully imported staff records to the system...");	//log this activity
						else
							ulog($_SESSION["CURR_USER_ID"],"Successfully imported student records to the system...");	//log this activity
						$notif->setInfo("You successfully imported the data into the system, you can view them from the 'View All Students & Staff' page. If this has caused a system instability, please contact ".D_NAME." immediately.","success");
					}
					else{
						//echo mysql_error();
						if (isset($_GET["staff"]))
							ulog($_SESSION["CURR_USER_ID"],"Failed to import staff records to the system...");	//log this activity
						else
							ulog($_SESSION["CURR_USER_ID"],"Failed to import student records to the system...");	//log this activity
						$notif->setInfo("A critical error occured while importing your file. Please try again, if it insists, consider reporting this to ".D_NAME." immediately.","danger");
					}
				}
			}
			else{
				$err="Invalid file detected, select an Excel workbook in 97 - 2003 format and try again.";
				$notif->setInfo($err,"danger");
			}
		}
		if (isset($_GET["staff"]))
			header('location: ./lib-students-new?staff');
		else
			header('location: ./lib-students-new');
		exit();
	}
	$notif->setInfo("Hey, you should consider reporting this error to the developer, ".D_NAME,"warning");
	if (isset($_GET["staff"]))
		header('location: ./lib-students-new?staff');
	else
		header('location: ./lib-students-new');
	exit();
	
?>