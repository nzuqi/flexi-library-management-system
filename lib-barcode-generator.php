<?php
	
	require("globals.php");
	require('./plugins/fpdf/fpdf.php');
	
	$notif=new notification;
	$user=new user;
	
	//check if the user is logged in
	if (!$user->login_check($mysqli)){
		//if the user is not logged in,
		$notif->setInfo('Log in to the library sub-system to generate barcodes for new books.','danger');
		header('location: ./');
		exit();
	}
	
	include "./plugins/Barcode39.php";
	
	Class PDF extends FPDF{
	// Page header
	
		function Header()
		{
			$this->AddFont('CalibriL','');
			$this->SetFont('CalibriL','',10);
			// Move to the right
			//$this->Cell(120);
			// Title
			$this->Cell(0,0,'FLEXI/LIB-FILES/BARCODE39',0,0,'R');
			// Line break
			$this->Ln(20);
		}
		
		// Page footer
		function Footer(){
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			$this->AddFont('CalibriL','');
			$this->SetFont('CalibriL','',11);
			$this->Cell(0,0,'','T',2,'C');
			// Page number
			$this->Cell(0,10,WA_TITLE,0,0,'C');
		}
	}
	
	//generate random numbers
	function gNumber(){
		$r=rand(1111111111,9999999999);
		return $r;
	}
	
	function uNumber($r){
		$validate=new validate;
		do{
			$n=$r;
		}
		while($validate->booknoExists($r));
		return $r;
	}
	
	$bc=array();
	
	//new numbers
	for($i=1;$i<=14;$i++){
		//$bc[$i]=uNumber(gNumber());
		$bc[$i]=new Barcode39(uNumber(gNumber()));
		$bc[$i]->draw('./files/barcodes/bc'.$i.'.gif');
	}
	
	// set Barcode39 object
	//$bc = new Barcode39 ("0921239821");
	// display new barcode
	//$bc->draw();
	
	$pdf = new PDF();
	
	$pdf->AddPage();
	$pdf->AddFont('CalibriL','');
	$pdf->AddFont('Calibri','');
	$pdf->AddFont('CalibriB','');
	$pdf->AddFont('CalibriLI','');
	
	$pdf->SetFont('CalibriB','',14);
	$pdf->Cell(0,8,strtoupper($_SESSION["host_name"]),0,2,'C');
	$pdf->SetFont('CalibriL','',12);
	$pdf->Cell(0,5,'LIBRARY BARCODES FOR NEW BOOKS',0,2,'C');
	
	$pdf->Image('./files/barcodes/bc1.gif',35,60);
	$pdf->Image('./files/barcodes/bc2.gif',120,60);
	
	$pdf->Image('./files/barcodes/bc3.gif',35,90);
	$pdf->Image('./files/barcodes/bc4.gif',120,90);
	
	$pdf->Image('./files/barcodes/bc5.gif',35,120);
	$pdf->Image('./files/barcodes/bc6.gif',120,120);
	
	$pdf->Image('./files/barcodes/bc7.gif',35,150);
	$pdf->Image('./files/barcodes/bc8.gif',120,150);
	
	$pdf->Image('./files/barcodes/bc9.gif',35,180);
	$pdf->Image('./files/barcodes/bc10.gif',120,180);
	
	$pdf->Image('./files/barcodes/bc11.gif',35,210);
	$pdf->Image('./files/barcodes/bc12.gif',120,210);
	
	$pdf->Image('./files/barcodes/bc13.gif',35,240);
	$pdf->Image('./files/barcodes/bc14.gif',120,240);
	
	$pdf->Output('D',"barcodes".date('jmYhis').".pdf");