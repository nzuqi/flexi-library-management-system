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
	if (!$user->verify()){
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
		
		//connect to db
		dbconnect();
		
		//check if the UID exists in 'users'
		$q="SELECT COUNT(LID) AS numrows FROM libcusts WHERE LID=$uid;";
		$res=mysql_query($q) or die ("Query failed checking 'lid' on 'libcusts'");
		$rw=mysql_fetch_array($res,MYSQL_ASSOC);
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
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-check"></span> Check In</h1>
			<div class="row">
				<?php
				$owe=$stats->countOverdueBooksUserCharges($uid);
				$sqlf="SELECT * FROM libcusts WHERE LID=$uid LIMIT 1;";
				$resultf=mysql_query($sqlf);
				while($r=mysql_fetch_array($resultf)){
					if($r['LBan']==1){
						?>
						<div class="col-sm-12">
							<h3 class="text-success" style="margin-top:0;"><span class="glyphicon glyphicon-user"></span> <?php echo ucwords(strtolower(trim($r['LName']))); ?>, <?php echo $r['LNumb']; ?> (<?php echo ucwords($r['LType']); ?>)</h3>
							<p class="text-danger">This <?php echo $r['LType']; ?>, <?php echo ucwords(strtolower(trim($r['LName']))); ?>, has been banned from accessing the library resources.</p>
							<h1 class="text-danger"><span class="glyphicon glyphicon-ban-circle"></span> BANNED</h1>
							<p class="text-info"><span class="glyphicon glyphicon-info-sign"></span> Visit the banned users section to update <?php echo ucwords(strtolower(trim($r['LName']))); ?>'s status.</p>
							<p><a href="lib-users-banned" class="btn btn-success btn-lg">View Banned Users <i class="glyphicon glyphicon-chevron-right"></i></a> &nbsp; <a href="./lib-home-check-return?id=FL<?php echo $uid; ?>" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-check"></i> Return Books</a> &nbsp; <a href="lib-home" class="btn btn-danger btn-lg"><i class="glyphicon glyphicon-remove"></i> Cancel</a></p>
							<p>&nbsp;</p>
						</div>
						<p>&nbsp;</p>
						<?php
					}
					else{
						?>
						<div class="col-sm-12">
							<h3 class="text-success" style="margin-top:0;"><span class="glyphicon glyphicon-user"></span> <?php echo ucwords(strtolower(trim($r['LName']))); ?>, <?php echo $r['LNumb']; ?> (<?php echo ucwords($r['LType']); ?>)</h3>
							<?php
							if($owe>0){
								?>
								<!--<h3 class="text-danger"><span class="glyphicon glyphicon-alert"></span> Overdue Charges: <em><u>Ksh <?php echo $owe; ?></u></em></h3>-->
								<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-alert"></span> Overdue Charges: <em><strong>Ksh <?php echo $owe; ?></strong></em><br/><span class="glyphicon glyphicon-info-sign"></span> It is advisable that this <?php echo ucwords($r['LType']); ?> returns the overdue books and pays the charged amount of <em><strong>Ksh <?php echo $owe; ?></strong></em> before being checked in the library.</div>
								<?php
							}
							?>
							<p>This <?php echo $r['LType']; ?>, <?php echo ucwords(strtolower(trim($r['LName']))); ?>, has visited the library to:</p>
							<a href="./lib-home-check-borrow?id=FL<?php echo $uid; ?>" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-check"></i> Borrow Books</a> &nbsp; <a href="./lib-home-check-action?id=<?php echo $uid; ?>&activity=read" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-check"></i> Read in the Library</a> &nbsp; <a href="./lib-home-check-return?id=FL<?php echo $uid; ?>" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-check"></i> Return Books</a> &nbsp; <a href="./lib-home-check-action?id=<?php echo $uid; ?>&activity=other" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-check"></i> Other...</a> &nbsp; <a href="lib-home" class="btn btn-danger btn-lg"><i class="glyphicon glyphicon-remove"></i> Cancel</a>
							<p>&nbsp;</p>
						</div>
						<?php
					}
				}
				?>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-stats"></span> Library Stats</h1>
					<p>Brief analysis for the current user.</p>
				</div>
				<div class="col-sm-4">
					<h3><span class="glyphicon glyphicon-book"></span> Books Stats</h3>
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-share"></span> Most Borrowed book(s)</span></h4>
					<?php
					$q="SELECT COUNT(IID) AS numrows FROM issue WHERE SID=$uid;";
					$res=mysql_query($q) or die ("Query failed counting issued books...");
					$rw=mysql_fetch_array($res,MYSQL_ASSOC);
					if($rw['numrows']>0){
						?>
						<p>
						<?php
						$q1="SELECT BID FROM issue WHERE MONTH(iTimeS)= MONTH(CURDATE()) AND SID=$uid GROUP BY BID ORDER BY COUNT(*) DESC LIMIT 3;";
						$res1=mysql_query($q1);
						while ($rw1=mysql_fetch_array($res1)){
							$q2="SELECT * FROM books WHERE BID=".$rw1[0]." LIMIT 1;";
							$res2=mysql_query($q2);
							while ($rw2=mysql_fetch_array($res2)){
								?><span class="glyphicon glyphicon-book"></span> <strong><?php echo $rw2['bTitle']; ?></strong> by <em><?php echo $rw2['bAuthor']; ?></em><br/><?php
							}
						}
						?>
						</p>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> Preceding stats are based on current month.</em></p>
						<a href="#" class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-stats"></span> More Analytics</a>
						<p>&nbsp;</p>
						<?php
					}
					else{
						?>
						<p class="text-info"><em>This user has not borrowed any books.</em></p>
						<p>&nbsp;</p>
						<?php
					}
					?>
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-time"></span> Overdue book(s)</span></h4>
					<?php
					$ovb=$stats->countOverdueBooksUser($uid);
					if($ovb>0){
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> This user has <strong><?php echo $ovb; ?></strong> overdue book(s).</em></p>
						<p class="text-warning"><em><span class="glyphicon glyphicon-alert"></span> Owed <strong>Ksh <?php echo $owe; ?></strong> by the library.</em></p>
						<a href="#" class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-book"></span> Overdue Books</a>
						<?php
					}
					else{
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> This user has no overdue books.</em></p>
						<?php
					}
					?>
				</div>
				<div class="col-sm-4">
					<h3><span class="glyphicon glyphicon-calendar"></span> Library Access</h3>
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-transfer"></span> Recent Activity</span></h4>
					<?php
					$q="SELECT COUNT(UID) AS numrows FROM activity WHERE SID=$uid;";
					$res=mysql_query($q) or die ("Query failed counting activities...");
					$rw=mysql_fetch_array($res,MYSQL_ASSOC);
					if($rw['numrows']>0){
						?>
						<p>
						<?php
						$q1="SELECT * FROM activity WHERE MONTH(UTimeS)= MONTH(CURDATE()) AND SID=$uid ORDER BY UTimeS DESC LIMIT 10;";
						$res1=mysql_query($q1);
						while ($rw1=mysql_fetch_array($res1)){
							?><span class="glyphicon glyphicon-time"></span> <strong><?php echo date('D, j M y, h:i a ',strtotime($rw1['UTimeS'])); ?></strong>, activity &raquo; <em><?php echo $rw1['UActivity']; ?></em><br/><?php
						}
						?>
						</p>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> Preceding stats are based on current month.</em></p>
						<a href="#" class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-stats"></span> More Analytics</a>
						<p>&nbsp;</p>
						<?php
					}
					else{
						?>
						<p class="text-info"><em>No library activities associated with this user.</em></p>
						<p>&nbsp;</p>
						<?php
					}
					?>
				</div>
				<div class="col-sm-4">
					<h3><span class="glyphicon glyphicon-stats"></span> Overall Books Stats</h3>
					<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> Hover on a pie section for details.</em></p>
					<canvas id="books-pie" width="230" height="230" />
					<p>&nbsp;</p>
				</div>
			</div>
			
			<hr>
			
			<?php $ui->printFooter(); ?>
		</div>
		<?php
	}
	
	$ui->printBottom();
?>
<script>
	var pieData = [{
        value: <?php echo $stats->countOverdueBooksUser($uid) ?>,
        color: "#F7464A",
        highlight: "#FF5A5E",
        label: "Overdue Books"
    }, {
        value: <?php echo $stats->userBooksBorrowed($uid) ?>,
        color: "#46BFBD",
        highlight: "#5AD3D1",
        label: "Borrowed Books"
    }, {
        value: <?php echo $stats->userBooksReturned($uid) ?>,
        color: "#FDB45C",
        highlight: "#FFC870",
        label: "Returned Books"
    }, {
        value: <?php echo $stats->userBooksLost($uid) ?>,
        color: "#949FB1",
        highlight: "#A8B3C5",
        label: "Lost Books"
    }, {
        value: <?php echo $stats->userBooksPaid($uid) ?>,
        color: "#4D5360",
        highlight: "#616774",
        label: "Paid Books"
    }];
	
	window.onload = function() {
        var ctx = document.getElementById("books-pie").getContext("2d");
        window.myPie = new Chart(ctx).Pie(pieData);
    };
</script>