<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$email=new email;
	$notif=new notification;
	$assignment=new assignment;
	$stats=new stats;
	
	$ui->active_menu=21;
	$ui->page_title="Update Profile :: ".WA_TITLE;
	//====================
	
	$ui->printTop();
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
		
		//check if the right parameters are set
		//if we're updating the details
		if (isset($_GET['details'])){
			$utitle=$uname=$udesig=$uemail=$uphone=$uusername=$err="";
			$upic=$_SESSION["CURR_USER_PIC"];
			
			//if the form has been submitted
			if ($_SERVER["REQUEST_METHOD"]=="POST"){
				//if the user submitting is a student...
				if ($_SESSION['CURR_USER_AUTH']=="student"){
					//get POST data
					$uemail=trim($_POST['uemail']);
					$uphone=trim($_POST['uphone']);
					
					//run some tests here...
					if (empty($uemail)){
						$err="<b>Email</b> required.";
						$notif->setInfo($err,'danger');
					}
					elseif (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$uemail)){
						$err="Invalid <b>email</b> format.";
						$notif->setInfo($err,'danger');
					}
					elseif ($validate->emailExistsWithOtherUser($uemail,$_SESSION['CURR_USER_ID'])){
						$err="<b>Email</b> belongs to another user.";
						$notif->setInfo($err,'danger');
					}
					elseif ($validate->validEmail($uemail)==false){
						$err="<b>Email</b> is invalid. Check your email for errors and try again";
						$notif->setInfo($err,'danger');
					}
					elseif (trim($uphone)==""){
						$err="<b>Phone</b> is required.";
						$notif->setInfo($err,'danger');
					}
					elseif (strlen($uphone)<10){
						$err="<b>Phone</b> number is too short.";
						$notif->setInfo($err,'danger');
					}
					elseif (!preg_match('/[0-9]/',$uphone) || preg_match('/[a-zA-Z]/',$uphone)){
						$err="<b>Phone</b> number is invalid.";
						$notif->setInfo($err,'danger');
					}
					//if there are no errors, time to update our 'users' table
					if ($err==""){
						$sql="UPDATE users SET uEmail='".$uemail."' WHERE UID=".$_SESSION['CURR_USER_ID']." LIMIT 1;";
						$result=mysqli_query($mysqli,$sql);
						if ($result){
							$notif->setInfo("Your details have been successfully updated.","success");
						}
						else{
							$notif->setInfo("An error occured, please try again.","danger");
						}
					}
				}
				else{
					//get POST data
					$utitle=$validate->test_input($_POST['utitle']);
					$uname=$validate->test_input($_POST['uname']);
					$udesig=$validate->test_input($_POST['udesig']);
					$uemail=trim($_POST['uemail']);
					$uphone=trim($_POST['uphone']);
					$uusername=$validate->test_input($_POST['uusername']);
					
					//run some tests here...
					if (!empty($utitle)){
						if (!preg_match("/^[a-zA-Z]*$/",$utitle)){
							$err="Only <em>letters</em> are allowed to be used as title. If you've used '.', remove it and try again.";
							$notif->setInfo($err,'danger');
						}
					}
					elseif (empty($uemail)){
						$err="<b>Email</b> required.";
						$notif->setInfo($err,'danger');
					}
					elseif (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$uemail)){
						$err="Invalid <b>email</b> format.";
						$notif->setInfo($err,'danger');
					}
					elseif ($validate->emailExistsWithOtherUser($uemail,$_SESSION['CURR_USER_ID'])){
						$err="<b>Email</b> belongs to another user.";
						$notif->setInfo($err,'danger');
					}
					elseif ($validate->validEmail($uemail)==false){
						$err="<b>Email</b> is invalid. Check your email for errors and try again";
						$notif->setInfo($err,'danger');
					}
					elseif (trim($uphone)==""){
						$err="<b>Phone</b> is required.";
						$notif->setInfo($err,'danger');
					}
					elseif (strlen($uphone)<10){
						$err="<b>Phone</b> number is too short.";
						$notif->setInfo($err,'danger');
					}
					elseif (!preg_match('/[0-9]/',$uphone) || preg_match('/[a-zA-Z]/',$uphone)){
						$err="<b>Phone</b> number is invalid.";
						$notif->setInfo($err,'danger');
					}
					elseif (empty($uname)){
						$err="<b>Name</b> required.";
						$notif->setInfo($err,'danger');
					}
					elseif (strlen($uname)<5){
						$err="<b>Name</b> too short.";
						$notif->setInfo($err,'danger');
					}
					elseif (empty($udesig)){
						$err="<b>Designation</b> required.";
						$notif->setInfo($err,'danger');
					}
					elseif (strlen($udesig)<5){
						$err="<b>Designation</b> too short.";
						$notif->setInfo($err,'danger');
					}
					elseif (empty($uusername)){
						$err="<b>Username</b> required.";
						$notif->setInfo($err,'danger');
					}
					elseif ($uusername != $_SESSION["CURR_USER_UN"]){
						if ($validate->usernameExists($uusername)){
							$err="<b>Username</b> already exists.";
							$notif->setInfo($err,'danger');
						}
					}
					elseif (!preg_match("/^[a-zA-Z]*$/",$uusername)){
						$err="Only <em>letters</em> are allowed to be used as username.";
						$notif->setInfo($err,'danger');
					}
					
					//if there are no errors, time to update our 'users' table
					if ($err==""){
						$sql="UPDATE users SET uTitle='$utitle',uName='$uname',uDesignation='$udesig',uEmail='$uemail',uPhone='$uphone',uUsername='$uusername' WHERE UID=".$_SESSION['CURR_USER_ID']." LIMIT 1;";
						$result=mysqli_query($mysqli,$sql);
						if ($result){
							$notif->setInfo("Your details have been successfully updated.", "success");
							$stats->setStat($_SESSION['CURR_USER_ID'],'profileu');
						}
						else{
							$notif->setInfo("An error occured, please try again.","danger");
						}
					}
				}
			}
			//if not, load values from the db...
			else{
				$sql22="SELECT * FROM users WHERE UID=".$_SESSION["CURR_USER_ID"]." LIMIT 1;";
				$result22=mysqli_query($mysqli,$sql22);
				while ($row22=mysqli_fetch_array($result22)){
					$utitle=$row22['uTitle'];
					$uname=$row22['uName'];
					$udesig=$row22['uDesignation'];
					$uemail=$row22['uEmail'];
					$uphone=$row22['uPhone'];
					$uusername=$row22['uUsername'];
					$upic=$row22['uPic'];
					
				}
			}
			?>
			<div class="jumbotron" style="background:url(./images/pattern.png) #913C38;">
				<div class="container">
					<h1 style="color:#FED300;">Update Details</h1>
					<a href="updatepd?password" class="btn btn-warning btn-sm"><i class="glyphicon glyphicon-lock"></i> Change Password</a>
				</div>
			</div>
			<div class="container">
				<!--alerts-->
				<?php
				echo $notif->printImportant();
				echo $notif->alertInfo();
				?>
				<!--pathing-->
				<div class="well well-sm"><a href="./" style="color:#913C38;"><span class="glyphicon glyphicon-home" style="color:#913C38;"></span> Home</a> // <a href="./profile?user=<?php echo "MZ".$_SESSION['CURR_USER_ID']; ?>" style="color:#913C38;">Profile</a> // Update Details</div>
				
				<div class="row">
					<div class="col-sm-3">
						<img class="img-thumbnail img-responsive" src="files/thumbs/users/<?php if (strlen(trim($_SESSION["CURR_USER_PIC"]))>0 && file_exists('files/thumbs/users/'.$_SESSION["CURR_USER_PIC"])) { echo $_SESSION["CURR_USER_PIC"]; } else { echo "_blank.png"; } ?>" title="<?php echo $_SESSION["CURR_USER_NAME"]; ?>" alt="<?php echo $_SESSION["CURR_USER_NAME"]; ?>" border="0" width="250px" height="250px" />
						<p>&nbsp;</p>
					</div>
					<div class="col-sm-9">
						<form role="form" method="POST" action="updatepd?details" autocomplete="off">
							<p class="text-info"><i><strong>Manza MTC</strong> will use your <strong>email</strong> and <strong>phone</strong> to communicate with you.</i></p>
							<p class="text-warning"><i>Your changes will take effect fully the next time you <b>log in.</b></i></p>
							<?php
							if($_SESSION["CURR_USER_AUTH"]!="student"){
								?>
								<div class="form-group">
									<label for="utitle">Title</label>
									<input type="text" name="utitle" id="utitle" class="form-control" value="<?php echo $utitle; ?>" />
								</div>
								<div class="form-group">
									<label for="uname">Name</label>
									<input type="text" name="uname" id="uname" class="form-control" value="<?php echo $uname; ?>" />
								</div>
								<?php
							}
							?>
							<div class="form-group">
								<label for="uemail">Email</label>
								<input type="text" name="uemail" id="uemail" class="form-control" value="<?php echo $uemail; ?>" />
							</div>
							<div class="form-group">
								<label for="uphone">Phone</label>
								<input type="text" name="uphone" id="uphone" class="form-control" value="<?php echo $uphone; ?>" />
							</div>
							<?php
							if($_SESSION["CURR_USER_AUTH"]!="student"){
								?>
								<div class="form-group">
									<label for="udesig">Designation</label>
									<input type="text" name="udesig" id="udesig" class="form-control" value="<?php echo $udesig; ?>" />
								</div>
								<div class="form-group">
									<label for="uusername">Username</label>
									<input type="text" name="uusername" id="uusername" class="form-control" value="<?php echo $uusername; ?>" style="text-transform:uppercase;" />
								</div>
								<?php
							}
							?>
							<button class="btn btn-success btn-sm" type="submit"><i class="glyphicon glyphicon-pencil"></i> Update</button> &nbsp; <a href="updatepd?password" class="btn btn-warning btn-sm">Change Password</a>
						</form>
					</div>
				</div>
				<hr>
				<?php $ui->printFooter(); ?>
			</div>
			<?php
		}
		//if we're updating the passowrd
		elseif (isset($_GET['password'])){
			$old_password=$new_password=$confirm=$err="";
			//if the form has been submitted
			if ($_SERVER["REQUEST_METHOD"]=="POST"){
				//get POST data
				$old_password=trim($_POST['uold']);
				$new_password=trim($_POST['unew']);
				$confirm=trim($_POST['uconfirm']);
				$err="";
				//Password
				if (empty($old_password)){
					$err="<b>Old password</b> required.";
					$notif->setInfo($err,'danger');
				}
				elseif (empty($new_password)){
					$err="<b>New password</b> required.";
					$notif->setInfo($err,'danger');
				}
				elseif (empty($confirm)){
					$err="<b>Confirm</b> your new password.";
					$notif->setInfo($err,'danger');
				}
				elseif (strlen($new_password)<7){
					$err="<b>New password</b> MUST be more than 7 characters.";
					$notif->setInfo($err,'danger');
				}
				elseif (!preg_match('/[A-Za-z]/',$new_password) || !preg_match('/[0-9]/',$new_password)){
					$err="Your <b>password</b> is very weak should. It should contain atleast numbers and letters.";
					$notif->setInfo($err,'danger');
				}
				elseif ($new_password!=$confirm){
					$err="Passwords <b>DO NOT</b> match.";
					$notif->setInfo($err,'danger');
				}
				elseif (sha1(md5($old_password))!=$_SESSION['CURR_USER_PASS']){
					$err="<b>Old password</b> is incorrect.";
					$notif->setInfo($err,'danger');
				}
				
				if ($err==""){
					$sql="UPDATE users SET uPassword='".sha1(md5($confirm))."' WHERE UID=".$_SESSION['CURR_USER_ID']." LIMIT 1;";
					$result=mysqli_query($mysqli,$sql);
					if ($result){
						//setInfo("Your password was changed successfully");
						$stats->setStat($_SESSION['CURR_USER_ID'],'profileu');
						header("Location: logout");
					}
					else{
						$notif->setInfo("An error occured, please try again.","danger");
					}
				}
			}
		
			?>
			<div class="jumbotron" style="background:url(./images/pattern.png) #913C38;">
				<div class="container">
					<h1 style="color:#FED300;">Change Password</h1>
					<a href="updatepd?details" class="btn btn-warning btn-sm"><i class="glyphicon glyphicon-pencil"></i> Update Details</a>
				</div>
			</div>
			<div class="container">
				<!--alerts-->
				<?php
				echo $notif->printImportant();
				echo $notif->alertInfo();
				?>
				<!--pathing-->
				<div class="well well-sm"><a href="./" style="color:#913C38;"><span class="glyphicon glyphicon-home" style="color:#913C38;"></span> Home</a> // <a href="./profile?user=<?php echo "MZ".$_SESSION['CURR_USER_ID']; ?>" style="color:#913C38;">Profile</a> // Update Password</div>
				
				<div class="row">
					<div class="col-sm-3">
						<img class="img-thumbnail img-responsive" src="files/thumbs/users/<?php if (strlen(trim($_SESSION["CURR_USER_PIC"]))>0 && file_exists('files/thumbs/users/'.$_SESSION["CURR_USER_PIC"])) { echo $_SESSION["CURR_USER_PIC"]; } else { echo "_blank.png"; } ?>" title="<?php echo $_SESSION["CURR_USER_NAME"]; ?>" alt="<?php echo $_SESSION["CURR_USER_NAME"]; ?>" border="0" width="250px" height="250px" />
						<p>&nbsp;</p>
					</div>
					<div class="col-sm-9">
						<form role="form" method="POST" action="updatepd?password" autocomplete="off">
							<div class="form-group">
								<label for="uold">Old Password</label>
								<input type="password" name="uold" id="uold" class="form-control" value="<?php echo $old_password; ?>" />
							</div>
							<div class="form-group">
								<label for="unew">New Password</label>
								<input type="password" name="unew" id="unew" class="form-control" value="<?php echo $new_password; ?>" />
							</div>
							<div class="form-group">
								<label for="uconfirm">Retype Password</label>
								<input type="password" name="uconfirm" id="uconfirm" class="form-control" value="<?php echo $confirm; ?>" />
							</div>
							<button class="btn btn-success btn-sm" type="submit"><i class="glyphicon glyphicon-lock"></i> Change</button> &nbsp; <a href="updatepd?details" class="btn btn-warning btn-sm">Update Details</a>
						</form>
					</div>
				</div>
				<hr>
				<?php $ui->printFooter(); ?>
			</div>
			<?php
		}
		else{
			header('location: updatepd?details');
			exit();
		}
	}
	
	$ui->printBottom();
?>