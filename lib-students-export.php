<?php
	require('globals.php');
	require_once './plugins/PHPExcel/Classes/PHPExcel.php';
	
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		$dtype="";
		$dtype=$_POST["dtype"];
		
		dbconnect();
		
		if ($dtype=='staff')
			$sql = mysql_query("SELECT * FROM libcusts WHERE LType='staff' ORDER BY LName ASC");
		else
			$sql = mysql_query("SELECT * FROM libcusts WHERE LType='student' ORDER BY LName ASC");
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
		            ->setCellValue('A2', 'LIBRARY '.strtoupper($dtype).' RECORDS');
		
		$BStyle = array(
				  'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				  )
				);
		
		if($dtype=='staff'){	
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A4', 'NO')
						->setCellValue('B4', 'NUMBER')
						->setCellValue('C4', 'NAME');
				$no=5;
			while($data = mysql_fetch_assoc($sql)){
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$no, $n)
					->setCellValue('B'.$no, $data['LNumb'])
					->setCellValue('C'.$no, $data['LName']);
				$n++;
				$no++;
			}
			$objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:C4')->getFont()->setBold(true);
			//autosize column width
			foreach(range('A','C') as $columnID) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
			}
			//set borders
			$objPHPExcel->getActiveSheet()->getStyle('A4:C'.($no-1))->applyFromArray($BStyle);
		}
		else{
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A4', 'NO')
						->setCellValue('B4', 'NUMBER')
						->setCellValue('C4', 'NAME')
						->setCellValue('D4', 'CLASS');
				$no=5;
			while($data = mysql_fetch_assoc($sql)){
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$no, $n)
					->setCellValue('B'.$no, $data['LNumb'])
					->setCellValue('C'.$no, $data['LName'])
					->setCellValue('D'.$no, "Form ".$data["LForm"]." ".ucwords(strtolower($data["LStream"])));
				$n++;
				$no++;
			}
			$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
			$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
			$objPHPExcel->getActiveSheet()->getStyle('A1:D4')->getFont()->setBold(true);
			//autosize column width
			foreach(range('A','D') as $columnID) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
					->setAutoSize(true);
			}
			//set borders
			$objPHPExcel->getActiveSheet()->getStyle('A4:D'.($no-1))->applyFromArray($BStyle);
		}
		
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle(strtoupper($dtype));
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		ob_end_clean();
		
		// Redirect output to a client�s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.ucwords(strtolower($dtype)).' Records.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		//exit;
	}
	
	
?>