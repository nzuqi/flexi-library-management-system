<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=9.1;
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
		
		if (isset($_GET["auth"])){
			if ($_GET["auth"]=="deleteAll"){
				$que="DELETE FROM notifications WHERE NTo=".$_SESSION["CURR_USER_ID"].";";
				$res=mysqli_query($mysqli,$que);
				if($res){
					$notif->setInfo("All notifications were deleted successfully.","success");
					ulog($_SESSION["CURR_USER_ID"],"Successfully deleted own notifications...");	//log this activity
				}
				else{
					$notif->setInfo("An error occured while deleting your notifications. Try again later.","danger");
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while deleting own notifications...");	//log this activity
				}
			}
			elseif ($_GET["auth"]=="deleteOne"){
				$que="DELETE FROM notifications WHERE NTo=".$_SESSION["CURR_USER_ID"]." AND NID=".$_GET["nid"].";";
				$res=mysqli_query($mysqli,$que);
				if($res){
					$notif->setInfo("Your notification was deleted successfully.","success");
					ulog($_SESSION["CURR_USER_ID"],"Successfully deleted own notification...");	//log this activity
				}
				else{
					$notif->setInfo("An error occured while deleting your notification. Try again later.","danger");
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while deleting own notification...");	//log this activity
				}
			}
			elseif ($_GET["auth"]=="markAllUnread"){
				$que="UPDATE notifications SET NRead=0 WHERE NTo=".$_SESSION["CURR_USER_ID"].";";
				$res=mysqli_query($mysqli,$que);
				if($res){
					$notif->setInfo("Your notifications were all marked 'Unread' successfully.","success");
					ulog($_SESSION["CURR_USER_ID"],"Successfully marked own notifications 'Unread'...");	//log this activity
				}
				else{
					$notif->setInfo("An error occured while marking your notifications as 'Unread'. Try again later.","danger");
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while marking own notifications 'Unread'...");	//log this activity
				}
			}
			elseif ($_GET["auth"]=="markUnread"){
				$que="UPDATE notifications SET NRead=0 WHERE NTo=".$_SESSION["CURR_USER_ID"]." AND NID=".$_GET["nid"].";";
				$res=mysqli_query($mysqli,$que);
				if($res){
					$notif->setInfo("Your notification was marked 'Unread' successfully.","success");
					ulog($_SESSION["CURR_USER_ID"],"Successfully marked own notification 'Unread'...");	//log this activity
				}
				else{
					$notif->setInfo("An error occured while marking your notification as 'Unread'. Try again later.","danger");
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while marking own notification 'Unread'...");	//log this activity
				}
			}
			elseif ($_GET["auth"]=="markAllRead"){
				$que="UPDATE notifications SET NRead=1 WHERE NTo=".$_SESSION["CURR_USER_ID"].";";
				$res=mysqli_query($mysqli,$que);
				if($res){
					$notif->setInfo("Your notifications were all marked 'Read' successfully.","success");
					ulog($_SESSION["CURR_USER_ID"],"Successfully marked own notifications 'Read'...");	//log this activity
				}
				else{
					$notif->setInfo("An error occured while marking your notifications as 'Read'. Try again later.","danger");
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while marking own notifications 'Read'...");	//log this activity
				}
			}
			elseif ($_GET["auth"]=="markRead"){
				$que="UPDATE notifications SET NRead=1 WHERE NTo=".$_SESSION["CURR_USER_ID"]." AND NID=".$_GET["nid"].";";
				$res=mysqli_query($mysqli,$que);
				if($res){
					$notif->setInfo("Your notification was marked 'Read' successfully.","success");
					ulog($_SESSION["CURR_USER_ID"],"Successfully marked own notification 'Read'...");	//log this activity
				}
				else{
					$notif->setInfo("An error occured while marking your notification as 'Read'. Try again later.","danger");
					ulog($_SESSION["CURR_USER_ID"],"Incurred an error while marking own notification 'Read'...");	//log this activity
				}
			}
			
			header('Location: notifications');
			exit();
		}
		
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-bell"></span> Notifications<?php echo $notif->displayNotifs(); ?></h1>
			<?php
			//check number of notification entities available
			$quer="SELECT COUNT(NID) FROM notifications WHERE NTo=".$_SESSION["CURR_USER_ID"].";";
			$res=mysqli_query($mysqli,$quer);
			if ($res){					//if the query is successful
				while ($row=mysqli_fetch_array($res)){
					if ($row[0]==0){	//no notifications found
						?>
						<p>&nbsp;</p>
						<p>&nbsp;</p>
						<p class="text-info">You don't have any notifications for now...</p>
						<p>&nbsp;</p>
						<p>&nbsp;</p>
						<?php
					}
					else{				//found
						$squer="SELECT * FROM notifications WHERE NTo=".$_SESSION["CURR_USER_ID"]." ORDER BY nTimeS Desc;";
						$resl=mysqli_query($mysqli,$squer);
						?>
						<p class="text-info"><a class="btn btn-success btn-sm" href="notifications?auth=markAllRead" role="button"><i class="glyphicon glyphicon-ok"></i> Mark all as read</a> &nbsp; <a class="btn btn-danger btn-sm" href="notifications?auth=deleteAll" role="button"><i class="glyphicon glyphicon-trash"></i> Delete all</a></p>
						<p>&nbsp;</p>
						<table class="table table-bordered table-striped table-responsive table-hover" id="render" cellspacing="0">
							<thead>
								<tr>
									<th>Date Modified</th>
									<th>Message</th>
									<th>State</th>
								</tr>
							</thead>
							<tbody>
							<?php
							while ($r=mysqli_fetch_array($resl)){
								?>
								
								<!--books modal-->
								<div id="modal<?php echo $r["NID"]; ?>" class="modal fade" role="dialog">
									<div class="modal-dialog">
										<!--modal content-->
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<?php
												if($r["NRead"]==0){
													?><h4 class="modal-title text-warning"><i class="glyphicon glyphicon-inbox"></i> <?php echo date('l, jS M Y, h:i:s A',strtotime($r["NTimeS"])); ?></h4><?php
												}
												else{
													?><h4 class="modal-title text-success"><i class="glyphicon glyphicon-ok"></i> <?php echo date('l, jS M Y, h:i:s A',strtotime($r["NTimeS"])); ?></h4><?php
												}
												?>
											</div>
											<div class="modal-body">
												<p><?php echo $r["NMsg"]; ?></p>
											</div>
											<div class="modal-footer">
												<?php
												if($r["NRead"]==0){
													?><a class="btn btn-success btn-lg" href="notifications?auth=markRead&nid=<?php echo $r["NID"]; ?>" role="button"><i class="glyphicon glyphicon-ok"></i> Mark as read</a><?php
												}
												else{
													?><a class="btn btn-warning btn-lg" href="notifications?auth=markUnread&nid=<?php echo $r["NID"]; ?>" role="button"><i class="glyphicon glyphicon-inbox"></i> Mark as unread</a><?php
												}
												?>
												&nbsp; <a class="btn btn-danger btn-lg" href="notifications?auth=deleteOne&nid=<?php echo $r["NID"]; ?>" role="button"><i class="glyphicon glyphicon-trash"></i> Delete</a> &nbsp; <button type="button" class="btn btn-default btn-lg" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
											</div>
										</div>
									</div>
								</div>
								<!--==============-->
								
								<tr style="cursor:pointer;" data-toggle="modal" data-target="#modal<?php echo $r["NID"]; ?>" class="<?php if($r["NRead"]==0){ echo "text-success"; } ?>">
									<td><?php echo date('l, jS M Y, h:i:s A',strtotime($r["NTimeS"])); ?></td>
									<td><?php echo smartTrimString($r["NMsg"],50); ?></td>
									<td><?php if($r["NRead"]==1){ echo "Read"; } else { echo "Unread"; } ?></td>
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
				?><p class="text-danger"><strong>MySQLI error:</strong> <?php echo mysqli_error($mysqli); ?></p><?php
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