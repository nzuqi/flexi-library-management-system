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
					
					$sql="INSERT INTO books(bTitle,bAuthor,bPublisher,bBlurb,bEdition,bAccNo,bPoPub,bCartegory,bSubject,bYoPub,bReserve) VALUES ";
					$sheet=$excel->sheets[0];
					$x = 2;
					while($x <= $sheet['numRows']){
						$cellBaccNo = isset($sheet['cells'][$x][1]) ? $sheet['cells'][$x][1] : '';
						$cellBTitle = isset($sheet['cells'][$x][2]) ? $sheet['cells'][$x][2] : '';
						$cellBAuthor = isset($sheet['cells'][$x][3]) ? $sheet['cells'][$x][3] : '';
						$cellBDescription = isset($sheet['cells'][$x][4]) ? $sheet['cells'][$x][4] : '';
						$cellBEdition = isset($sheet['cells'][$x][5]) ? $sheet['cells'][$x][5] : '';
						$cellBCartegory = isset($sheet['cells'][$x][6]) ? $sheet['cells'][$x][6] : '';
						$cellBSubject = isset($sheet['cells'][$x][7]) ? $sheet['cells'][$x][7] : '';
						$cellBPublisher = isset($sheet['cells'][$x][8]) ? $sheet['cells'][$x][8] : '';
						$cellBPoPub = isset($sheet['cells'][$x][9]) ? $sheet['cells'][$x][9] : '';
						$cellBYoPub = isset($sheet['cells'][$x][10]) ? $sheet['cells'][$x][10] : '';
						$cellBReserve = isset($sheet['cells'][$x][11]) ? $sheet['cells'][$x][11] : 0;
						
						if ($x==$sheet['numRows'])
							$sql.="('".mysqli_real_escape_string($mysqli,$cellBTitle)."','".mysqli_real_escape_string($mysqli,$cellBAuthor)."','".mysqli_real_escape_string($mysqli,$cellBPublisher)."','".mysqli_real_escape_string($mysqli,$cellBDescription)."','".mysqli_real_escape_string($mysqli,$cellBEdition)."','$cellBaccNo','".mysqli_real_escape_string($mysqli,$cellBPoPub)."','".mysqli_real_escape_string($mysqli,$cellBCartegory)."','".mysqli_real_escape_string($mysqli,$cellBSubject)."','$cellBYoPub',$cellBReserve);";
						else
							$sql.="('".mysqli_real_escape_string($mysqli,$cellBTitle)."','".mysqli_real_escape_string($mysqli,$cellBAuthor)."','".mysqli_real_escape_string($mysqli,$cellBPublisher)."','".mysqli_real_escape_string($mysqli,$cellBDescription)."','".mysqli_real_escape_string($mysqli,$cellBEdition)."','$cellBaccNo','".mysqli_real_escape_string($mysqli,$cellBPoPub)."','".mysqli_real_escape_string($mysqli,$cellBCartegory)."','".mysqli_real_escape_string($mysqli,$cellBSubject)."','$cellBYoPub',$cellBReserve),";
						
						$x++;
					}
						
					//echo $sql;
					$result=mysqli_query($mysqli,$sql);
					if ($result){
						ulog($_SESSION["CURR_USER_ID"],"Successfully imported books records to the system...");	//log this activity
						$notif->setInfo("You successfully imported the data into the system, you can view them from the 'View All Books' page. If this has caused a system instability, please contact ".D_NAME." immediately.","success");
					}
					else{
						ulog($_SESSION["CURR_USER_ID"],"Failed to import books records to the system...");	//log this activity
						$notif->setInfo(mysqli_error($mysqli)."A critical error occured while importing your file. Please try again, if it insists, consider reporting this to ".D_NAME." immediately.","danger");
					}
				}
			}
			else{
				$err="Invalid file detected, select an Excel workbook in 97 - 2003 format and try again.";
				$notif->setInfo($err,"danger");
			}
		}
		header('location: ./lib-books-new');
		exit();
	}
	$notif->setInfo("Hey, you should consider reporting this error to the developer, ".D_NAME,"warning");
	header('location: ./lib-books-new');
	exit();