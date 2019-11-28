<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=9.3;
	//====================
	$ui->printTop();
	
	// if(isset($_SESSION["CURR_SUB_SYSTEM"]))
	// 	$ui->printNavbar($_SESSION["CURR_SUB_SYSTEM"]);
	// else
		$ui->printNavbar();
	
	//check if the user is logged in
	if (!$user->login_check($mysqli)){
		//if the user is not logged in,
		//set current page name, just to make sure that we'll stick to this page even after loging in :)
		$curr_page=basename(__FILE__,".php");
		//load the login page
		include("login.php");
	}
	else{
		//if the user is logged in, format the page...
		
		$uname=$uidno=$upass=$uconf=$usecq=$useca=$uclearance=$uun=$uerr="";
	
		if ($_SERVER["REQUEST_METHOD"]=="POST"){
			$uname=$validate->test_input($_POST["uname"]);
			$uidno=trim($_POST["idno"]);
			$usecq=$validate->test_input($_POST["secq"]);
			$useca=$validate->test_input($_POST["seca"]);
			$uclearance=trim($_POST["uclearance"]);
			$uun=$validate->test_input($_POST["username"]);
			$upass=trim($_POST["password"]);
			$uconf=trim($_POST["cpassword"]);
			
			//run some tests
			if (trim($uname)==""){
				$uerr="<b>User's Name</b> is required.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (strlen($uname)<7){
				$uerr="<b>User's Name</b> number is too short.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (trim($uidno)==""){
				$uerr="User's <b>ID Number</b> is required.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (strlen($uidno)<7){
				$uerr="User's <b>ID Number</b> number is too short.";
				$notif->setInfo($uerr,'danger');
			}
			elseif ($validate->idnoExists($uidno)){
				$uerr="<b>ID Number</b> number rejected, it's probably in use by another user.";
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
			elseif (trim($uun)==""){
				$uerr="<b>Username</b> is required.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (strlen($uun)<7){
				$uerr="<b>Username</b> is too short.";
				$notif->setInfo($uerr,'danger');
			}
			elseif ($validate->usernameExists($uun)==true){
				$uerr="<b>Username</b> is not available, please choose another username and try again.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (strlen($upass)<7){
				$uerr="<b>Password</b> is too short.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (strlen($upass)<7){
				$uerr="<b>Password</b> is too short.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (!preg_match('/[A-Za-z]/',$upass) || !preg_match('/[0-9]/',$upass)){
				$uerr="User's <b>password</b> is very weak should. It should contain atleast numbers and letters.";
				$notif->setInfo($uerr,'danger');
			}
			elseif ($upass!=$uconf){
				$uerr="<b>Passwords</b> DO NOT match.";
				$notif->setInfo($uerr,'danger');
			}
			
			if ($uerr==""){
				$sql="INSERT INTO users(uName,uIDNumber,uSecQ,uSecA,uUsername,uPassword,uAuth,uBlock)
						VALUES('".ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname)))."','".$uidno."','".mysqli_real_escape_string($mysqli,$usecq)."','".mysqli_real_escape_string($mysqli,$useca)."','".mysqli_real_escape_string($mysqli,$uun)."','".sha1(MD5($upass))."','".$uclearance."',0);";
				$result=mysqli_query($mysqli,$sql);
				if ($result){
					$msg="Hello ".ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname))).", welcome to ".WA_TITLE.". It is highly recommended that you change your login details after you log in for the first time. If you encounter any issues, feel free to contact our programming team here at ".D_NAME." and we'll get you fixed! Enjoy using the system.";
					$notif->notify($user->getUID($uidno),$msg);
					ulog($_SESSION["CURR_USER_ID"],"Added '".ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname)))."' into the system...");	//log this activity
					$notif->setInfo(ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname)))."'s account was successfully set up. ".ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname)))." can now login to the system using the details you have provided. Advice the user to change them once logged in.","success");
					header('location: accounts');
					exit();
				}
				else{
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while adding '".ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname)))."' into the system...");	//log this activity
					$notif->setInfo("A critical error occured while setting up the ".ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname)))."'s account. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
				}
			}
		}
		
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-plus"></span> New Account</h1>
			<p>&nbsp;</p>
			<form role="form" action="accounts-new" method="POST" autocomplete="off">
				<div class="form-group">
					<label for="uname">Name <span class="text-danger">*</span></label>
					<input type="text" name="uname" id="uname" class="form-control" value="<?php echo $uname; ?>" placeholder="User's name e.g. John Doe" />
				</div>
				<div class="form-group">
					<label for="idno">ID Number <span class="text-danger">*</span></label>
					<input type="text" name="idno" id="idno" class="form-control" value="<?php echo $uidno; ?>" placeholder="User's ID Number" />
				</div>
				<div class="form-group">
					<label for="uclearance">Clearance <span class="text-danger">*</span></label>
					<select name="uclearance" id="uclearance" class="form-control">
						<option value="lib-user">Library User</option>
						<option value="admin">Administrator</option>
					</select>
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
				<div class="form-group">
					<label for="username">Username <span class="text-danger">*</span></label>
					<input type="text" name="username" id="username" class="form-control" value="<?php echo $uun; ?>" placeholder="Username" />
				</div>
				<div class="form-group">
					<label for="password">Password <span class="text-danger">*</span></label>
					<input type="password" name="password" id="password" class="form-control" value="" placeholder="Password" />
				</div>
				<div class="form-group">
					<label for="cpassword">Retype Password <span class="text-danger">*</span></label>
					<input type="password" name="cpassword" id="cpassword" class="form-control" value="" placeholder="Retype Password" />
				</div>
				<button type="submit" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-floppy-disk"></i> Create Account</button> &nbsp; <a class="btn btn-default btn-lg" href="accounts" role="button"><i class="glyphicon glyphicon-chevron-left"></i> Manage Accounts</a>
			</form>
			<hr>
			<?php $ui->printFooter(); ?>
		</div>
		<?php
	}
	
	$ui->printBottom();
?>
<!-- dataTables plugin -->
<script src="./plugins/dataTables/jquery.dataTables.min.js"></script>
<script src="./plugins/dataTables/dataTables.bootstrap.min.js"></script>
<script>
	$(document).ready(function(){
		$('#render').DataTable();
	});
</script>