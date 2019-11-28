<?php

	if (!$validate->isSysActivated()){
		$notif->setInfo(WA_TITLE." is not activated, please activate the system to continue using it. If you don't have the activation key, contact <strong>".D_NAME."</strong> immediately for your key.",'danger');
		header("location: ./preferences");
		exit();
	}
	
	$adminAccAvailable=false;
	if ($validate->adminAccExists())
		$adminAccAvailable=true;
	
	$username=$password=$err="";
	$uname=$uidno=$upass=$uconf=$usecq=$useca=$uclearance=$uun=$uerr="";
	
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		
		if ($adminAccAvailable==false){
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
				$uerr="<b>Your Name</b> is required.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (strlen($uname)<7){
				$uerr="<b>Name</b> number is too short.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (trim($uidno)==""){
				$uerr="<b>ID Number</b> is required.";
				$notif->setInfo($uerr,'danger');
			}
			elseif (strlen($uidno)<7){
				$uerr="<b>ID Number</b> number is too short.";
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
				$uerr="Your <b>password</b> is very weak should. It should contain atleast numbers and letters.";
				$notif->setInfo($uerr,'danger');
			}
			elseif ($upass!=$uconf){
				$uerr="<b>Passwords</b> DO NOT match.";
				$notif->setInfo($uerr,'danger');
			}
			
			if ($uerr==""){
				$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
				$sql="INSERT INTO users(uName,uIDNumber,uSecQ,uSecA,uUsername,uPassword,uAuth,uBlock)
						VALUES('".ucwords(strtolower(mysqli_real_escape_string($con,$uname)))."','".$uidno."','".mysqli_real_escape_string($con,$usecq)."','".mysqli_real_escape_string($con,$useca)."','".mysqli_real_escape_string($con,$uun)."','".sha1(MD5($upass))."','".$uclearance."',0);";
				$result=mysqli_query($con,$sql);
				if ($result){
					$notif->setInfo("Administrator account successfully set up. You can now log in to the system and add more users.","success");
					header('location: '.$curr_page);
					exit();
				}
				else{
					$notif->setInfo("A critical error occured while setting up the administrator account. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
				}
			}
		}
		else{
			$username=$validate->test_input($_POST["username"]);
			$password=trim($_POST["password"]);
			
			//remove whitespaces on username
			$username=preg_replace('/\s+/','',$username);
			
			//Session ID
			$sess_id=session_id();//generateSessionID();
			
			//check for errors
			if (empty($username)){
				$err="<b>Username</b> is required.";
				$notif->setInfo($err,"danger");
			}
			elseif (empty($password)){
				$err="<b>Password</b> is required.";
				$notif->setInfo($err,"danger");
			}
			
			if ($err==""){
				$password=sha1(MD5(trim($password)));
				$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
				$result=mysqli_query($con,"SELECT * FROM users WHERE (uUsername='".strtoupper($username)."' OR uIDNumber='".$username."') AND uPassword='".$password."';");
				$data=mysqli_num_rows($result);
				if($data==1){
					//===========Get the necessary fields now to create sessions==========
					$result2=mysqli_query($con,"SELECT * FROM users WHERE (uUsername='".strtoupper($username)."' OR uIDNumber='".$username."') AND uPassword='".$password."';");
					if ($result2){
						while ($row2=mysqli_fetch_array($result2)){
							$uname=$row2['uName'];
							$uusername=$row2['uUsername'];
							$upassword=$row2['uPassword'];
							$uid=$row2['UID'];
							$uauth=$row2['uAuth'];
							$ublock=$row2['uBlock'];
						}
					}
					else{
						$notif->setInfo("A critical error occured. Please try again, if it insists, consider reporting this to the system administrator.",'danger');
					}
					if ($ublock==1){
						$notif->setInfo("Sorry, you are currently <b>blocked</b> from using the system. Contact the system administrator or your HOD immediately.",'danger');
					}
					else{
						$sql3="UPDATE users SET uSession='".$sess_id."' WHERE UID=".$uid.";";
						$result3=mysqli_query($con,$sql3);
						if ($result3){
							$uname=ucwords(strtolower($uname));
							// Get the user-agent string of the user.
							$user_browser = $_SERVER['HTTP_USER_AGENT'];
							// XSS protection as we might print this value
							$_SESSION['user_id'] = $uid;
							$_SESSION['username'] = $username;
							$_SESSION['login_string'] = $upassword . sha1(md5($user_browser));
							//set sessions
							$user->setUserSessVars($uid,$uname,$uusername,$upassword,$uauth);
							ulog($uid,"Logged in to the system...");	//log this activity
							$notif->setInfo("You successfully logged in as '".$uname."'","success");
							header('location: '.$curr_page);
							exit();
						}
						else{
							$notif->setInfo("A critical error occured. Please try again, if it insists, consider reporting this to the system administrator.",'danger');
						}
					}
				}
				else{
					$notif->setInfo("<b>Username</b> and or <b>password</b> is incorrect.",'danger');
				}
			}
		}
	}
?>
	<div class="container">
		<!--alerts-->
		<?php
		$notif->filename="files/notifications/login_notification.dat";
		echo $notif->lgnNotif();
		echo $notif->alertInfo();
		?>
		<div class="row">
			<?php
			if ($adminAccAvailable==false){
				?>
				<div class="col-sm-12">
					<h1 class="text-danger" style="margin-top:0;"><i class="glyphicon glyphicon-user"></i> Administrator Account</h1>
					<p class="text-danger"><strong><?php echo WA_TITLE; ?></strong> is running for the first time and requires an <strong>administrator</strong> account.</p>
					<!--activate acc form-->
					<form role="form" method="POST">
						<div class="form-group">
							<label for="uname">Name <span class="text-danger">*</span></label>
							<input type="text" name="uname" id="uname" class="form-control input-lg" value="<?php echo $uname; ?>" placeholder="Name e.g. John Doe" />
						</div>
						<div class="form-group">
							<label for="idno">ID Number <span class="text-danger">*</span></label>
							<input type="text" name="idno" id="idno" class="form-control input-lg" value="<?php echo $uidno; ?>" placeholder="ID Number" />
						</div>
						<div class="form-group">
							<label for="uclearance">Clearance <span class="text-danger">*</span></label>
							<select name="uclearance" id="uclearance" class="form-control input-lg">
								<option value="admin">Administrator</option>
							</select>
						</div>
						<div class="form-group">
							<label for="secq">Security Question <span class="text-danger">*</span></label>
							<select name="secq" id="secq" class="form-control input-lg">
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
							<input type="text" name="seca" id="seca" class="form-control input-lg" value="<?php echo $useca; ?>" placeholder="Answer to the question above" />
						</div>
						<div class="form-group">
							<label for="username">Username <span class="text-danger">*</span></label>
							<input type="text" name="username" id="username" class="form-control input-lg" value="<?php echo $uun; ?>" placeholder="Username" />
						</div>
						<div class="form-group">
							<label for="password">Password <span class="text-danger">*</span></label>
							<input type="password" name="password" id="password" class="form-control input-lg" value="" placeholder="Password" />
						</div>
						<div class="form-group">
							<label for="cpassword">Retype Password <span class="text-danger">*</span></label>
							<input type="password" name="cpassword" id="cpassword" class="form-control input-lg" value="" placeholder="Retype Password" />
						</div>
						<p class="text-info"><em>Note that you'll be able to manage other system users amongst other administrative privileges.</em></p>
						<button type="submit" class="btn btn-success btn-lg">Okay, continue <i class="glyphicon glyphicon-chevron-right"></i></button>
					</form>
					<p>&nbsp;</p>
				</div>
				<?php
			}
			else{
				?>
				<div class="col-sm-12">
					<h1 class="text-danger" style="margin-top:0;"><i class="glyphicon glyphicon-lock"></i> Log in</h1>
					<p class="text-danger"><strong><?php echo WA_TITLE; ?></strong> is only available to the <strong>staff</strong> of <strong><?php echo ucwords(strtolower($_SESSION["host_name"])); ?></strong>. You are required to <strong>log in</strong> to access the system.</p>
					<p class="text-info">Use your <strong>username</strong> and <strong>password</strong> to log in to the system.</p>
					<!--login form-->
					<form role="form" method="POST" autocomplete="off">
						<div class="form-group">
							<label for="username">Username:</label>
							<input type="text" name="username" id="username" class="form-control input-lg" value="<?php echo $username; ?>" placeholder="Username or ID Number" />
						</div>
						<div class="form-group">
							<label for="password">Password:</label>
							<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" />
						</div>
						<!--<div class="checkbox">
							<label><input type="checkbox" name="remember"/>Remember me</label>
						</div>-->
						<button type="submit" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-lock"></i> Log in</button>
						&nbsp;<a href="recover" class="btn btn-warning btn-lg">Recover password <i class="glyphicon glyphicon-chevron-right"></i></a>
					</form>
					<p>&nbsp;</p>
				</div>
				<?php
			}
			?>
		</div>
		<hr>
		<?php $ui->printFooter(); ?>
	</div>