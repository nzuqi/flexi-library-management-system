<?php
	require("../globals.php");
	
	if (isset($_GET['code']))
		$code=$_GET['code'];
	else
		$code="400";
	
	if(isset($_SESSION["err_code"]))
		unset($_SESSION["err_code"]);
	$_SESSION['err_code']=$code;
	
	WriteError("'".$_SERVER['REMOTE_ADDR']."' encountered an error: ".$code,"../err/err.nzk");
	errPage($code,"../error")
	
?>