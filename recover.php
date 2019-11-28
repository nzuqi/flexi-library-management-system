<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	$file=new file;
	
	$ui->custom_page=true;
	//====================
	$ui->printTop();
	$ui->printNavbar();
	
	//check authorization to edit admin details
	function isUserAdmin(){
		if (isset($_SESSION["CURR_USER_AUTH"])){
			if($_SESSION["CURR_USER_AUTH"]=="admin")
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	$uidno=$usecq=$useca=$upass=$rpass=$uerr="";
	
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		$uidno=trim($_POST["idno"]);
		$usecq=$validate->test_input($_POST["secq"]);
		$useca=$validate->test_input($_POST["seca"]);
		
		if (trim($uidno)==""){
			$uerr="<b>ID Number</b> is required.";
			$notif->setInfo($uerr,'danger');
		}
		elseif (strlen($uidno)<7){
			$uerr="<b>ID Number</b> number is too short.";
			$notif->setInfo($uerr,'danger');
		}
		elseif (!$validate->idnoExists($uidno)){
			$uerr="<b>ID Number</b> does not exist in the system.";
			$notif->setInfo($uerr,'danger');
		}
		elseif (!preg_match('/[0-9]/',$uidno) || preg_match('/[a-zA-Z]/',$uidno)){
			$uerr="<b>ID Number</b> number is invalid.";
			$notif->setInfo($uerr,'danger');
		}
		elseif (trim($usecq)=="none"){
			$uerr="<b>Security Question</b> is required.";
			$notif->setInfo($uerr,'danger');
		}
		elseif (trim($useca)==""){
			$uerr="<b>Security Question Answer</b> is required.";
			$notif->setInfo($uerr,'danger');
		}
		
		if ($uerr==""){
			$q="SELECT COUNT(*) AS numrows FROM users WHERE uIDNumber='$uidno' AND uSecQ='$usecq' AND uSecA='$useca';";
			$res=mysqli_query($mysqli,$q) or die ("Query failed validating user details...");
			$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
			if($rw['numrows']==0)
				$notif->setInfo("The details you provided are incorrect! Provide the correct details and try again.","danger");
			else{
				$rpass=randomKey();
				$nwpass=sha1(md5($rpass));
				//reset password
				$sql="UPDATE users SET uPassword='".$nwpass."' WHERE uIDNumber='".$uidno."' LIMIT 1;";
				$result=mysqli_query($mysqli,$sql);
				$to=$user->getUID($uidno);
				if ($result){
					ulog($to,"Recovered password successfully...");	//log this activity
					$notif->notify($to,"You successfully recovered your password. It is highly recommended that you update your security settings and your log in details now.");	//set a notification
					$notif->setInfo("Your password was successfully reset, use the password below to log in. Remember to change your password once you log in successfully.","success");
					if (!isset($_SESSION["CURR_RESET_PASSWORD"]))
						$_SESSION["CURR_RESET_PASSWORD"]=$rpass;
					header('location: recover');
					exit();
				}
				else{
					ulog($to,"Critical error occured while trying to recover password...");	//log this activity
					$notif->notify($to,"Your request to reset your log in password was unsuccssful. If you did not initiate this request, update your security details and report this to the administrator immediately.");	//set a notification
					$notif->setInfo("A critical error occured while reseting your password. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
					header('location: recover');
					exit();
				}
			}
		}
	}
	
	?>
	<div class="container">
		<!--alerts-->
		<?php
		echo $notif->alertInfo();
		?>
		<h1 class="text-danger" style="margin-top:0;"><i class="glyphicon glyphicon-credit-card"></i> Recover Password</h1>
		<?php
		if (isset($_SESSION["CURR_RESET_PASSWORD"])){
			?>
			<p class="text-success">You password reset was successful. Use this password to log in to the system.</p>
			<h3 class="text-warning"><i class="glyphicon glyphicon-credit-card"></i> <?php echo $_SESSION["CURR_RESET_PASSWORD"]; ?></h3>
			<p>&nbsp;</p>
			<p class="text-warning"><i class="glyphicon glyphicon-info-sign"></i> Write this password down to use it to change your system log in details once logged in the system.</p>
			<?php
			if (isset($_SESSION["CURR_RESET_PASSWORD"]))
				unset($_SESSION["CURR_RESET_PASSWORD"]);
		}
		else{
			?>
			<p class="text-info">Provide the details below to reset your password.</p>
			<p class="text-warning"><i class="glyphicon glyphicon-info-sign"></i> Note that this will <strong>reset</strong> your current <strong><?php echo WA_TITLE; ?></strong> login <strong>password</strong> and issue you with a <strong>new</strong> password, generated <strong>automatically</strong> by the system.</p>
			<form role="form" method="POST" autocomplete="off">
				<div class="form-group">
					<label for="idno">ID Number <span class="text-danger">*</span></label>
					<input type="text" name="idno" id="idno" class="form-control" value="<?php echo $uidno; ?>" placeholder="ID Number" />
				</div>
				<div class="form-group">
					<label for="secq">Security Question <span class="text-danger">*</span></label>
					<select name="secq" id="secq" class="form-control">
						<option value="none">--Select Security question--</option>
						<option value="Which hospital was I born?" <?php if ($usecq=="Which hospital was I born?"){ echo "selected"; } ?> >Which hospital was I born?</option>
						<option value="Which model was my first car?" <?php if ($usecq=="Which model was my first car?"){ echo "selected"; } ?> >Which model was my first car?</option>
						<option value="What is my mother's maiden name?" <?php if ($usecq=="What is my mother's maiden name?"){ echo "selected"; } ?> >What is my mother's maiden name?</option>
						<option value="Where did I spend my honeymoon with my spouse?" <?php if ($usecq=="Where did I spend my honeymoon with my spouse?"){ echo "selected"; } ?> >Where did I spend my honeymoon with my spouse?</option>
						<option value="What is the name of my first pet?" <?php if ($usecq=="What is the name of my first pet?"){ echo "selected"; } ?> >What is the name of my first pet?</option>
						<option value="My first boss was called?" <?php if ($usecq=="My first boss was called?"){ echo "selected"; } ?> >My first boss was called?</option>
						<option value="Which town was my first child born?" <?php if ($usecq=="Which town was my first child born?"){ echo "selected"; } ?> >Which town was my first child born?</option>
						<option value="Which model was my first phone?" <?php if ($usecq=="Which model was my first phone?"){ echo "selected"; } ?> >Which model was my first phone?</option>
					</select>
				</div>
				<div class="form-group">
					<label for="seca">Answer <span class="text-danger">*</span></label>
					<input type="text" name="seca" id="seca" class="form-control" value="<?php echo $useca; ?>" placeholder="Answer to the question above" />
				</div>
				<button type="submit" class="btn btn-success btn-lg">Recover Password <i class="glyphicon glyphicon-chevron-right"></i></button>
			</form>
			<?php
		}
		?>
		<p>&nbsp;</p>
		<hr>
		<?php $ui->printFooter(); ?>
	</div>
	<?php
	$ui->printBottom();
?>