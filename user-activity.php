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
		
		if (isset($_GET["action"])){
			if ($_GET["action"]=="clearAll"){
				$que="DELETE FROM logs WHERE UID=$uid;";
				$res=mysqli_query($mysqli,$que);
				if($res){
					$notif->setInfo("All logs were deleted successfully.","success");
					ulog($_SESSION["CURR_USER_ID"],"Successfully deleted user logs...");	//log this activity
				}
				else{
					$notif->setInfo("An error occured while deleting the logs. Try again later.","danger");
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while deleting user logs...");	//log this activity
				}
			}
			else{
				$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.","warning");
			}
			header('location: ./user-activity?user=FL'.$uid);
			exit();
		}
		
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
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-tasks"></span> User Activity</h1>
			<div class="row">
				<?php
				$sqlf="SELECT * FROM users WHERE UID=$uid LIMIT 1;";
				$resultf=mysqli_query($mysqli,$sqlf);
				while($r=mysqli_fetch_array($resultf)){
					?>
					<div class="col-sm-12">
						<h4><span class="glyphicon glyphicon-user"></span> <?php echo ucwords(strtolower(trim($r['uName']))); ?>, <?php echo $r['uIDNumber']; ?></h4>
						<?php
						$q3="SELECT COUNT(*) AS numrows FROM logs WHERE UID=$uid;";
						$res3=mysqli_query($mysqli,$q3) or die ("Query failed fetching users data...");
						$rw3=mysqli_fetch_array($res3,MYSQLI_ASSOC);
						$x=$rw3['numrows'];
						if ($x>0){
							?>
							<p><a href="#" onClick="JavaScript:var p=confirm('Are you sure you want to clear all the activity for <?php echo ucwords(strtolower(trim($r["uName"]))); ?>');if(p==true){ window.location='user-activity?user=FL<?php echo $uid; ?>&action=clearAll'; };" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i> Clear All</a> &nbsp; <a href="user-activity-export?user=FL<?php echo $uid; ?>" class="btn btn-warning btn-sm"><i class="glyphicon glyphicon-export"></i> Export Data</a></p>
							<p class="text-info"><span class="glyphicon glyphicon-info-sign"></span> Exporting log files can take a longer time, depending on the amount of data. So, be patient.</p>
							<h3 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-blackboard"></span> All Activity</h3>
							<?php
							$sqlf2="SELECT * FROM logs WHERE UID=$uid ORDER BY lTimeS Desc;";
							$resultf2=mysqli_query($mysqli,$sqlf2);
							while($r2=mysqli_fetch_array($resultf2)){
								?>
								<h5><em><?php echo date('l, jS M Y, h:i:s a, ',strtotime($r2["lTimeS"])); ?></em> <?php echo $r2["lMessage"]; ?></h5>
								<?php
							}
							?>
							<p>&nbsp;</p>
							<p class="text-info">******* END *******</P>
							<?php
						}
						else{
							?>
							<p><a href="profile?user=FL<?php echo $uid; ?>" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-chevron-left"></i> Go Back</a></p>
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