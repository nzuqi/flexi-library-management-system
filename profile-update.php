<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=9.2;
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
		
		//check if there's the required parameter passed
		if (!isset($_GET['user'])){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.","warning");
			header('location: ./lib-home');
			exit();
		}
		//check if there's the required parameter passed
		if (substr($_GET['user'],0,2)!="FL"){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.","warning");
			header('location: ./lib-home');
			exit();
		}
		
		//$uid=$validate->decrypt($_GET['user'],WA_SALT);
		$uid=substr($_GET['user'],2,strlen($_GET['user']));
		
		$uname=$uidno=$cpass=$upass=$uconf=$usecq=$useca=$uclearance=$uun=$uerr="";
		$curr_pass=$_SESSION['CURR_USER_PASS'];
		
		if ($_SERVER["REQUEST_METHOD"]=="POST"){
			$uname=$validate->test_input($_POST["uname"]);
			$uidno=trim($_POST["idno"]);
			$usecq=$validate->test_input($_POST["secq"]);
			$useca=$validate->test_input($_POST["seca"]);
			//$uclearance=trim($_POST["uclearance"]);
			$uun=$validate->test_input($_POST["username"]);
			$cpass=trim($_POST["password"]);
			$upass=trim($_POST["npassword"]);
			$uconf=trim($_POST["cpassword"]);
			
			//run some tests
			if (trim($uname)==""){
				$uerr="Your <b>name</b> is required.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (strlen($uname)<7){
				$uerr="Your <b>name</b> is too short.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (trim($uidno)==""){
				$uerr="Your <b>ID Number</b> is required.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (strlen($uidno)<7){
				$uerr="Your <b>ID Number</b> number is too short.";
				$notif->setInfo($uerr,'danger');
			}
			elseif ($validate->idnoExistsWithOtherUser($uidno,$_SESSION['CURR_USER_ID'])){
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
			elseif ($validate->usernameExistsWithOtherUser($uun,$_SESSION['CURR_USER_ID'])==true){
				$uerr="<b>Username</b> is not available, please choose another username and try again.";
				$notif->setInfo($uerr,'danger');
			}
			elseif ($curr_pass!=sha1(md5($cpass))){
				$uerr="<b>Current password</b> is incorrect.";
				$notif->setInfo($uerr,'danger');
			}
			
			//if user is changing password
			elseif (strlen(trim($upass))>0){
				if (strlen($upass)<7){
					$uerr="<b>New Password</b> is too short.";
					$notif->setInfo($uerr,'danger');
				}
				elseif (!preg_match('/[A-Za-z]/',$upass) || !preg_match('/[0-9]/',$upass)){
					$uerr="<b>New password</b> is very weak should. It should contain atleast numbers and letters.";
					$notif->setInfo($uerr,'danger');
				}
				elseif ($upass!=$uconf){
					$uerr="<b>Passwords</b> DO NOT match.";
					$notif->setInfo($uerr,'danger');
				}
			}
			
			if ($uerr==""){
				if (strlen(trim($upass))==0)
					$sql="UPDATE users SET uName='".ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname)))."',uIDNumber='".$uidno."',uSecQ='".mysqli_real_escape_string($mysqli,$usecq)."',uSecA='".mysqli_real_escape_string($mysqli,$useca)."',uUsername='".mysqli_real_escape_string($mysqli,$uun)."' WHERE UID=".$_SESSION["CURR_USER_ID"].";";
				else
					$sql="UPDATE users SET uName='".ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname)))."',uIDNumber='".$uidno."',uSecQ='".mysqli_real_escape_string($mysqli,$usecq)."',uSecA='".mysqli_real_escape_string($mysqli,$useca)."',uUsername='".mysqli_real_escape_string($mysqli,$uun)."',uPassword='".sha1(MD5($upass))."' WHERE UID=".$_SESSION["CURR_USER_ID"].";";
				$result=mysqli_query($mysqli,$sql);
				if ($result){
					$msg="Hello ".ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname))).", you updated your ".WA_TITLE." profile. If this was not you, please report this activity to the administrator or to, ".D_NAME." for further security analysis.";
					$notif->notify($user->getUID($uidno),$msg);
					ulog($_SESSION["CURR_USER_ID"],"Updated personal profile details...");	//log this activity
					$notif->setInfo(ucwords(strtolower(mysqli_real_escape_string($mysqli,$uname))).", your profile details weres successfully updated. Some changes will reflect the next time you log in to the system.","success");
					header('location: ./logout');
					exit();
				}
				else{
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while updating prsonal profile...");	//log this activity
					$notif->setInfo("A critical error occured while your profile. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
				}
			}
		}
		else{
			$sqlf="SELECT * FROM users WHERE UID=$uid LIMIT 1;";
			$resultf=mysqli_query($mysqli,$sqlf);
			while($r=mysqli_fetch_array($resultf)){
				$uname=$r['uName'];$uidno=$r['uIDNumber'];
				//$upass=$uconf=
				$usecq=$r['uSecQ'];$useca=$r['uSecA'];
				$uclearance=$r['uAuth'];$uun=$r['uUsername'];
			}
		}
		
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-pencil"></span> Update Profile</h1>
			<p>Change the details below to update your profile.</p>
			<form role="form" action="#" method="POST" autocomplete="off">
				<div class="form-group">
					<label for="uname">Name <span class="text-danger">*</span></label>
					<input type="text" name="uname" id="uname" class="form-control" value="<?php echo $uname; ?>" placeholder="User's name e.g. John Doe" />
				</div>
				<div class="form-group">
					<label for="idno">ID Number <span class="text-danger">*</span></label>
					<input type="text" name="idno" id="idno" class="form-control" value="<?php echo $uidno; ?>" placeholder="User's ID Number" />
				</div>
				<!--
				<div class="form-group">
					<label for="uclearance">Clearance <span class="text-danger">*</span></label>
					<select name="uclearance" id="uclearance" class="form-control">
						<option value="librarian">Librarian</option>
						<option value="admin" disabled>Administrator</option>
					</select>
				</div>
				-->
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
					<label for="password">Current Password <span class="text-danger">*</span></label>
					<input type="password" name="password" id="password" class="form-control" value="" placeholder="Current Password" />
				</div>
				<p>&nbsp;</p>
				<h3>Change Password</h3>
				<p class="text-info"><em>If you're changing your password, fill in the fields below. Otherwise, leave them blank to continue using the same password.</em></p>
				<div class="form-group">
					<label for="npassword">New Password</label>
					<input type="password" name="npassword" id="npassword" class="form-control" value="" placeholder="New Password" />
				</div>
				<div class="form-group">
					<label for="cpassword">Retype Password</label>
					<input type="password" name="cpassword" id="cpassword" class="form-control" value="" placeholder="Retype Password" />
				</div>
				<p>&nbsp;</p>
				<button type="submit" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-floppy-disk"></i> Update Profile</button>
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