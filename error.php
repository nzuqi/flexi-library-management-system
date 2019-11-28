<?php
	require("globals.php");
	
	$ui=new gui;
	$notif=new notification;
	
	$ui->custom_page=true;
	
	if (isset($_SESSION['err_code'])){
		$code=$_SESSION['err_code'];
		unset($_SESSION['err_code']);
	}
	else{
		$code="400";
	}
	
	$ptitle=$pdescr="";
	if ($code=="400"){
		$ptitle=$code." Error: Bad request";
		$pdescr="Bad request.";
	}
	elseif ($code=="401"){
		$ptitle=$code." Error: Authorization required";
		$pdescr="Authorization is required for access.";
	}
	elseif ($code=="403"){
		$ptitle=$code." Error: Forbidden file";
		$pdescr="The file you are trying to access is forbidden.";
	}
	elseif ($code=="404"){
		$ptitle=$code." Error: File not found";
		$pdescr="The requested file was not found.";
	}
	elseif ($code=="500"){
		$ptitle=$code." Error: Internal server error";
		$pdescr="An internal server error occured.";
	}
	else{
		header("Location: ./");
	}
	
	$ui->printTop();
	$ui->printNavbar(); 
?>
	<div class="jumbotron" style="background:url(./images/pattern.png) #913C38;margin-top:-20px;">
		<div class="container">
			<h1 style="color:#FED300;"><?php echo $ptitle; ?></h1>
			<p style="color:#FFF;"><?php echo $pdescr; ?></p>
		</div>
	</div>
	<div class="container">
		<?php
		echo $notif->alertInfo();
		?>
		<div class="row">
			<div class="col-sm-6">
				<h1 class="text-danger">Aaaww :(</h1>
				<p class="text-danger"><strong><?php echo WA_TITLE; ?></strong> encountered the above <strong>error</strong> and redirected you to this page. In order to <strong>fix</strong> future errors such as this one, we have collected the necessary <strong>data</strong> to enable us to fix the error.</p>
				<p class="text-info">It is recommended that you <strong><a href="./" class="text-success">go to the homepage</a></strong>. If the error persists, feel free to contact the <strong>system administrator</strong>.</p>
				<p>&nbsp;</p>
			</div>
		</div>
		<hr>
		<?php $ui->printFooter(); ?>
	</div>
<?php $ui->printBottom(); ?>