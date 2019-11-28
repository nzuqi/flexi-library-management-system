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
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-wrench"></span> Manage Accounts</h1>
			<p><a class="btn btn-success btn-sm" href="accounts-new" role="button"><i class="glyphicon glyphicon-plus"></i> New User Account</a></p>
			<?php
			//check number of staff entities available
			$quer="SELECT COUNT(*) FROM users WHERE UID<>".$_SESSION['CURR_USER_ID'].";";
			$res=mysqli_query($mysqli,$quer);
			if ($res){					//if the query is successful
				while ($row=mysqli_fetch_array($res)){
					if ($row[0]==0){	//no accounts found
						?>
						<p class="text-info">There are no other accounts available in the system.</p>
						<p class="text-info">Add more user accounts of the system to manage them here.</p>
						<p>&nbsp;</p>
						<p>&nbsp;</p>
						<?php
					}
					else{				//found
						$squer="SELECT * FROM users WHERE UID<>".$_SESSION['CURR_USER_ID'].";";
						$resl=mysqli_query($mysqli,$squer);
						?>
						<table class="table table-bordered table-striped table-responsive table-hover" id="render" cellspacing="0">
							<thead>
								<tr>
									<th>Name</th>
									<th>ID Number</th>
									<th>Status</th>
									<th>Last Activity</th>
								</tr>
							</thead>
							<tbody>
							<?php
							while ($r=mysqli_fetch_array($resl)){
								?>
								<tr style="cursor:pointer;" onclick="javascript:window.location='profile?user=FL<?php echo $r["UID"]; ?>';">
									<td><?php echo $r["uName"]; ?></td>
									<td><?php echo $r["uIDNumber"]; ?></td>
									<td><?php if ($r["uBlock"]==1) echo 'Banned'; else echo 'Authorized'; ?></td>
									<td><?php echo date('D, jS M Y h:i A',strtotime($r["uTimeS"])); ?></td>
								</tr>
								<?php
							}
							?>
							</tbody>
						</table>
						<?php
					}
				}
			}
			else{
				?><p class="text-danger"><strong>MySQL error:</strong> <?php echo mysqli_error($mysqli); ?></p><?php
			}
			?>
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