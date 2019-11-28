<?php
	require('globals.php');
	$notif=new notification;
	$user=new user;
	require_once './plugins/PHPExcel/Classes/PHPExcel.php';
	
	if (isset($_GET["user"])){
		
		//check if there's the required parameter passed
		if (!isset($_GET['user'])){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.","warning");
			header('location: ./');
			exit();
		}
		//check if there's the required parameter passed
		if (substr($_GET['user'],0,2)!="FL"){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.","warning");
			header('location: ./');
			exit();
		}
		
		$uid=substr($_GET['user'],2,strlen($_GET['user']));
		
		//check if the UID exists in 'users'
		$q="SELECT COUNT(UID) AS numrows FROM users WHERE UID=$uid;";
		$res=mysqli_query($mysqli,$q) or die ("Query failed checking 'uid' on 'users'");
		$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
		if ($rw==0){
			$notif->setInfo("The requested profile could not be found on the database.","warning");
			header('location: ./err/?code=404');
			exit();
		}
		
		$uname=$user->getUName($uid);
		$uidno=$user->getUIdNumb($uid);
		
		$sql = mysqli_query($mysqli,"SELECT * FROM logs WHERE UID=$uid ORDER BY lTimeS Desc");
		$n = 1;
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set properties
		$objPHPExcel->getProperties()->setCreator(WA_TITLE)
				 ->setLastModifiedBy(WA_TITLE)
				 ->setTitle(ucwords(strtolower($_SESSION["host_name"]))." Document")
				 ->setSubject(ucwords(strtolower($_SESSION["host_name"])))
				 ->setDescription("This document was generated by the ".WA_TITLE." for ".ucwords(strtolower($_SESSION["host_name"])))
				 ->setKeywords(ucwords(strtolower($_SESSION["host_name"])))
				 ->setCategory(ucwords(strtolower($_SESSION["host_name"]))." Documents");
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', strtoupper($_SESSION["host_name"]))
		            ->setCellValue('A2', 'LIBRARY USER ACTIVITY LOGS')
		            ->setCellValue('A3', strtoupper($uname).", ID ".$uidno);
		
		$BStyle = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
				
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A4', 'NO')
					->setCellValue('B4', 'TIME')
					->setCellValue('C4', 'ACTIVITY');
			$no=5;
		while($data = mysqli_fetch_assoc($sql)){
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$no, $n)
				->setCellValue('B'.$no, date('l, jS M Y, h:i:s a',strtotime($data["lTimeS"])))
				->setCellValue('C'.$no, $data['lMessage']);
			$n++;
			$no++;
		}
		$objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:C3');
		$objPHPExcel->getActiveSheet()->getStyle('A1:C4')->getFont()->setBold(true);
		//autosize column width
		foreach(range('A','C') as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		//set borders
		$objPHPExcel->getActiveSheet()->getStyle('A4:C'.($no-1))->applyFromArray($BStyle);
		
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('User Activity Logs');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		ob_end_clean();
		
		// Redirect output to a client's web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="UserActivityLogs.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
	else{
		$notif->setInfo('Missing parameters detected while trying to export user log files.','danger');
		header('location: ./');
		exit();
	}