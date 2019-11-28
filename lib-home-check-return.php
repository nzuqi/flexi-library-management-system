<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=1;
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
		
		//check if there's the required parameter passed
		if (!isset($_GET['id'])){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.","warning");
			header('location: ./lib-home');
			exit();
		}
		//check if there's the required parameter passed
		if (substr($_GET['id'],0,2)!="FL"){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.","warning");
			header('location: ./lib-home');
			exit();
		}
		
		//$uid=$validate->decrypt($_GET['user'],WA_SALT);
		$uid=substr($_GET['id'],2,strlen($_GET['id']));
		
		//check if the UID exists in 'users'
		$q="SELECT COUNT(LID) AS numrows FROM libcusts WHERE LID=$uid;";
		$res=mysqli_query($mysqli,$q) or die ("Query failed checking 'lid' on 'libcusts'");
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
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-check"></span> Return Books</h1>
			<div class="row">
				<?php
				$owe=$stats->countOverdueBooksUserCharges($uid);
				$sqlf="SELECT * FROM libcusts WHERE LID=$uid LIMIT 1;";
				$resultf=mysqli_query($mysqli,$sqlf);
				while($r0=mysqli_fetch_array($resultf)){
					?>
					<div class="col-sm-12">
						<h3 class="text-success" style="margin-top:0;"><span class="glyphicon glyphicon-user"></span> <?php echo ucwords(strtolower(trim($r0['LName']))); ?>, <?php echo $r0['LNumb']; ?> (<?php echo ucwords($r0['LType']); ?>)</h3>
						<?php
						if($owe>0){
							?>
							<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-alert"></span> Overdue Charges: <em><strong>Ksh <?php echo $owe; ?></strong></em><br/><span class="glyphicon glyphicon-info-sign"></span> It is advisable that this <?php echo ucwords($r0['LType']); ?> returns the overdue books and pays the charged amount of <em><strong>Ksh <?php echo $owe; ?></strong></em> before being checked in the library.</div>
							<?php
						}
						?>
						<a href="./lib-home-check?id=FL<?php echo $uid; ?>" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-chevron-left"></i> Go Back</a>
						<?php
						$res=mysqli_query($mysqli,"SELECT COUNT(IID) FROM issue WHERE SID=$uid AND (iState=0 OR iState=2);");
						while($r=mysqli_fetch_array($res)){
							$cnt=$r[0];
						}
						if ($cnt>0){
							$sql_res=mysqli_query($mysqli,"SELECT * FROM issue WHERE SID=$uid AND (iState=0 OR iState=2);");
							?>
							<!-- &nbsp; <a href="#" class="btn btn-warning btn-sm"><i class="glyphicon glyphicon-export"></i> Export Records</a>-->
							<p><?php echo ucwords(strtolower(trim($r0['LName']))); ?> has <strong><?php echo $cnt; ?></strong> book(s) waiting to be cleared.</p>
							<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> Click on any book entity for more options.</p>
							<div class="list-group">
							<?php
							while($row=mysqli_fetch_array($sql_res)){
								$q2="SELECT * FROM books WHERE BID=".$row['BID']." LIMIT 1;";
								$res2=mysqli_query($mysqli,$q2);
								while ($rw2=mysqli_fetch_array($res2)){
									?>
									
									<!--books modal-->
									<div id="modal<?php echo $rw2["BID"]; ?>" class="modal fade" role="dialog">
										<div class="modal-dialog">
											<!--modal content-->
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title text-success"><?php echo $rw2["bAccNo"].", ".$rw2["bTitle"]." <em>by</em> ".$rw2["bAuthor"]; ?></h4>
												</div>
												<div class="modal-body">
													<p><strong>Title:</strong> <?php echo $rw2["bTitle"]; ?></p>
													<p><strong>Author:</strong> <?php echo $rw2["bAuthor"]; ?></p>
													<p><strong>Cartegory:</strong> <?php echo $rw2["bCartegory"]; ?></p>
													<?php
													if($stats->isBookLost($row['IID'])){
														?><p class="text-danger">This book was marked as lost on <strong><?php echo date('D, j M y, h:i a ',strtotime($row["iTimeS"])); ?></strong></p><?php
													}
													else{
														?>
														<p><strong>Issued on:</strong> <?php echo date('D, j M y, h:i a ',strtotime($row["iTimeS"])); ?></p>
														<p class="text-<?php if($stats->isBookOverdue($row['IID'])) echo 'danger'; else echo 'success'; ?>"><strong>Due Date:</strong> <?php echo date('D, j M y, h:i a ',strtotime($row["iTimeS"])+(86400*$row["iDuration"])); ?></p>
														<?php
													}
													?>
												</div>
												<div class="modal-footer">
													<?php
													if($stats->isBookOverdue($row['IID'])){
														?><a href="./lib-home-check-action?id=<?php echo $uid; ?>&activity=ocreturn&iid=<?php echo $row['IID']; ?>&oc=<?php echo $stats->countOverdueBookUserCharges($row['IID']); ?>" class="btn btn-warning"><i class="glyphicon glyphicon-usd"></i> Returned, Overdue Charges (Ksh <?php echo $stats->countOverdueBookUserCharges($row['IID']); ?>) Paid</a> &nbsp; <?php
													}
													else{
														if($stats->isBookLost($row['IID'])){
															?><a href="./lib-home-check-action?id=<?php echo $uid; ?>&activity=compensate&iid=<?php echo $row['IID']; ?>" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Compensated</a> &nbsp; <?php
														}
														else{
															?><a href="./lib-home-check-action?id=<?php echo $uid; ?>&activity=return&iid=<?php echo $row['IID']; ?>" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Returned</a> &nbsp; <?php
														}
													}
													if(!$stats->isBookLost($row['IID'])){
														?><a href="./lib-home-check-action?id=<?php echo $uid; ?>&activity=lost&iid=<?php echo $row['IID']; ?>" class="btn btn-danger"><i class="glyphicon glyphicon-flag"></i> Flag As Lost</a> &nbsp; <?php
													}
													?>
													<button type="button" class="btn btn-default" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
												</div>
											</div>
										</div>
									</div>
									<!--==============-->
									
									<a class="list-group-item" href="#" data-toggle="modal" data-target="#modal<?php echo $rw2["BID"]; ?>">
										<span class="glyphicon glyphicon-book"></span> <?php echo $rw2['bAccNo']; ?> <strong><?php echo $rw2['bTitle']; ?></strong> by <em><?php echo $rw2['bAuthor']; ?></em>
										<?php
										if($row['iState']==0){
											if($stats->isBookOverdue($row['IID'])){
												?><span class="badge">Overdue: Ksh <?php echo $stats->countOverdueBookUserCharges($row['IID']); ?></span><?php
											}
										}
										else{
											?><span class="badge">Reported As Lost</span><?php
										}
										?>
									</a>
									<?php
								}
							}
							?>
							</div>
							<?php
						}
						else{
							?><p>&nbsp;</p><p class="text-success"><i class="glyphicon glyphicon-ok"></i> Apparently, <?php echo ucwords(strtolower(trim($r0['LName']))); ?> has cleared with the library.</p><?php
						}
						?>
						<p>&nbsp;</p>
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
<script>
	
</script>