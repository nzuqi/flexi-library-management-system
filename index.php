<?php
	require("globals.php");
	$ui=new gui;
	$user=new user;
	$notif=new notification;
	$ui->printTop();
	?>
	<div class="container">
		<?php echo $notif->alertInfo(); ?>
		<div class="row">
			<div class="col-sm-12">
				<center>
					<img class="img img-responsive" src="./images/splash-logo.png" title="" border="0" width="435px" height="170px" />
					<p>&nbsp;</p>
					<?php
					if ($user->login_check($mysqli) && isset($_SESSION["CURR_USER_NAME"])){
						?>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> You are logged in as <strong><?php echo $_SESSION["CURR_USER_NAME"]; ?></strong>.</p>
						<p>
							<a class="btn btn-warning" href="./lib-home" role="button"><i class="glyphicon glyphicon-book"></i> Continue</a> &nbsp; 
							<a class="btn btn-warning" href="./preferences" role="button"><i class="glyphicon glyphicon-cog"></i> Preferences</a> &nbsp; 
							<a class="btn btn-danger" href="logout" role="button"><i class="glyphicon glyphicon-log-out"></i> Log out</a>
						</p>
						<?php
					}
					else{
						?>
						<p class="text-danger"><i class="glyphicon glyphicon-alert"></i> You will be required to log in to access the system.</p>
						<p>
							<a class="btn btn-success" href="./lib-home" role="button"><i class="glyphicon glyphicon-book"></i> Continue</a> &nbsp; 
							<a class="btn btn-warning" href="./preferences" role="button"><i class="glyphicon glyphicon-cog"></i> Preferences</a>
						</p>
						<?php
					}
					?>
					<p>&nbsp;</p>
					<?php $ui->printFooter(); ?>
				</center>
			</div>
		</div>
	</div>
	<?php
	$ui->printBottom();
	
	//header('location: ./lib-home');