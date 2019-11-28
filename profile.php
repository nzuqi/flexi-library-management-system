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
		
		//check if the UID exists in 'users'
		$q="SELECT COUNT(UID) AS numrows FROM users WHERE UID=$uid;";
		$res=mysqli_query($mysqli,$q) or die ("Query failed checking 'uid' on 'users'");
		$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
		if ($rw==0){
			$notif->setInfo("The requested profile could not be found on the database.","warning");
			header('location: ./err/?code=404');
			exit();
		}
		
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-user"></span> Profile</h1>
			<p><a href="./accounts" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-chevron-left"></i> View More Accounts</a></p>
			
			<div class="row">
				<?php
				$sqlf="SELECT * FROM users WHERE UID=$uid LIMIT 1;";
				$resultf=mysqli_query($mysqli,$sqlf);
				while($r=mysqli_fetch_array($resultf)){
					?>
					<div class="col-sm-3">
						<h3 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-blackboard"></span> General Information</h3>
						<h4><span class="badge">Name</span> <?php echo ucwords(strtolower(trim($r['uName']))); ?></h4>
						<h4><span class="badge">ID Number</span> <?php echo $r['uIDNumber']; ?></h4>
						<h4><span class="badge">Username</span> <?php echo $r['uUsername']; ?></h4>
						<h5><span class="badge">Last Activity</span> <?php echo date('D j, M Y',strtotime($r["uTimeS"])); ?></h5>
						<p>&nbsp;</p>
						<?php
						if ($_SESSION["CURR_USER_ID"]==$r["UID"]){
							?><a href="profile-update?user=FL<?php echo $uid; ?>" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-pencil"></i> Update Profile</a><?php
						}
						if (($_SESSION["CURR_USER_AUTH"]=='admin' || $_SESSION["CURR_USER_AUTH"]=='dev') && $_SESSION["CURR_USER_ID"]!=$r["UID"]){
							if ($r["uBlock"]==1){
								?><button class="btn btn-success btn-sm" onclick="javascript:window.location='user-action?state=unblock&id=<?php echo $uid; ?>';"><i class="glyphicon glyphicon-ok-circle"></i> Unblock User</button><?php
							}
							else{
								?><button class="btn btn-danger btn-sm" onclick="javascript:window.location='user-action?state=block&id=<?php echo $uid; ?>';"><i class="glyphicon glyphicon-ban-circle"></i> Block User</button><?php
							}
							?>
							<button class="btn btn-danger btn-sm" onclick="javascript:var c=confirm('Are you sure you want to delete <?php echo ucwords(strtolower(trim($r['uName']))); ?> as a system user?\n\nThis action is irreversible. All records related to <?php echo ucwords(strtolower(trim($r['uName']))); ?> will be deleted.');if (c==true){ window.location='user-action?state=delete&id=<?php echo $uid; ?>'; };"><i class="glyphicon glyphicon-trash"></i> Delete User</button>
							<?php
						}
						?>
						<p>&nbsp;</p>
					</div>
					<div class="col-sm-9">
						<p>&nbsp;</p>
						<h3 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-tasks"></span> Recent Activity</h3>
						<?php
						$q3="SELECT COUNT(*) AS numrows FROM logs WHERE UID=$uid;";
						$res3=mysqli_query($mysqli,$q3) or die ("Query failed fetching exams data...");
						$rw3=mysqli_fetch_array($res3,MYSQLI_ASSOC);
						$x=$rw3['numrows'];
						if ($x>0){
							$sqlf2="SELECT * FROM logs WHERE UID=$uid ORDER BY lTimeS Desc LIMIT 10;";
							$resultf2=mysqli_query($mysqli,$sqlf2);
							while($r2=mysqli_fetch_array($resultf2)){
								?>
								<h5><em><?php echo date('l, jS M Y, h:i:s a, ',strtotime($r2["lTimeS"])); ?></em> <?php echo $r2["lMessage"]; ?></h5>
								<?php
							}
							?>
							<p>&nbsp;</p>
							<a href="user-activity?user=FL<?php echo $uid; ?>" class="btn btn-warning btn-lg">View All Activity <i class="glyphicon glyphicon-chevron-right"></i></a>
							<p>&nbsp;</p>
							<?php
						}
						else{
							?>
							<p>&nbsp;</p>
							<p class="text-info"><span class="glyphicon glyphicon-info-sign"></span> There's no data related to <strong><?php echo ucwords(strtolower(trim($r['uName']))); ?></strong> in the system logs.</p>
							<p>&nbsp;</p>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
			<hr>
			<?php $ui->printFooter(); ?>
		</div>
		<?php
	}
	
	$ui->printBottom();
?>