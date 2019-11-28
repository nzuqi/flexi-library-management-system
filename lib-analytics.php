<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=4;
	//====================
	$ui->printTop();
	$ui->printNavbar();
	
	//globals
	$atu1=$atu2=$atu3=$aborrow=$areturn=$aread=$aother=0;
	
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
		/////////LIBRARY ACTIVITY////////////////////////////////////
		function countActivity($r1,$r2){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(UID) AS numrows FROM activity WHERE (uTimeS BETWEEN '".$r1."' AND '".$r2."');";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		function countSActivity($activity){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(UID) AS numrows FROM activity WHERE DATE(uTimeS)=CURDATE() AND UActivity='".$activity."';";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		$atu1=countActivity(date('Y-m-j').' 09:00:00',date('Y-m-j').' 12:00:00');
		$atu2=countActivity(date('Y-m-j').' 12:00:00',date('Y-m-j').' 16:00:00');
		$atu3=countActivity(date('Y-m-j').' 16:00:00',date('Y-m-j').' 21:00:00');
		
		$aborrow=countSActivity('borrow');
		$areturn=countSActivity('return');
		$aread=countSActivity('read');
		$aother=countSActivity('other');
		/////////////////////////////////////////////////////////////
		
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-stats"></span> Library Analytics</h1>
			<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> Select a tab section below to filter library analytics.</p>
			<ul class="nav nav-tabs">
				<li class="nav-item active"><a href="lib-analytics" class="nav-link"><strong><i class='glyphicon glyphicon-calendar'></i> TODAY</strong></a></li>
				<li class="nav-item"><a href="lib-analytics-monthly" class="nav-link"><strong> MONTHLY</strong></a></li>
				<li class="nav-item"><a href="lib-analytics-yearly" class="nav-link"><strong> YEARLY</strong></a></li>
			</ul>
			<h5><i class="glyphicon glyphicon-time"></i> <?php echo date('D, j M, Y'); ?></h5>
			<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
			<h3><i class="glyphicon glyphicon-transfer"></i> Library Activity</h3>
			<div class="row">
				<div class="col-sm-3">
					<h4 class="text-danger"><span class='label label-danger'><i class="glyphicon glyphicon-time"></i> Time span and frequency</span></h4>
					<p>
						<i class="glyphicon glyphicon-time"></i> <i>0800 hrs ~ 1200 hrs</i> &raquo; <strong><?php echo $atu1; ?> users</strong><br/>
						<i class="glyphicon glyphicon-time"></i> <i>1200 hrs ~ 1600 hrs</i> &raquo; <strong><?php echo $atu2; ?> users</strong><br/>
						<i class="glyphicon glyphicon-time"></i> <i>1600 hrs ~ 2100 hrs</i> &raquo; <strong><?php echo $atu3; ?> users</strong><br/>
						<?php
						if($atu1>0 || $atu2>0 || $atu3>0){
							?><a href="#" class="btn btn-warning disabled"><span class="glyphicon glyphicon-export"></span> Export Data</a><?php
						}
						?>
					</p>
					<h4 class="text-danger"><span class='label label-danger'><i class="glyphicon glyphicon-hourglass"></i> Library user activity</span></h4>
					<p>
						<i class="glyphicon glyphicon-saved"></i> <i>Borrow</i> &raquo; <strong><?php echo $aborrow; ?> times</strong><br/>
						<i class="glyphicon glyphicon-saved"></i> <i>Return</i> &raquo; <strong><?php echo $areturn; ?> times</strong><br/>
						<i class="glyphicon glyphicon-saved"></i> <i>Reading</i> &raquo; <strong><?php echo $aread; ?> times</strong><br/>
						<i class="glyphicon glyphicon-saved"></i> <i>Other</i> &raquo; <strong><?php echo $aother; ?> times</strong><br/>
						<?php
						if($aborrow>0 || $areturn>0 || $aread>0 || $aother>0){
							?><a href="#" class="btn btn-warning disabled"><span class="glyphicon glyphicon-export"></span> Export Data</a><?php
						}
						?>
					</p>
				</div>
				<div class="col-sm-5">
					<?php
					if($atu1==0 && $atu2==0 && $atu3==0){
						?>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> There's no data available.<br/><em>A bar chart will be available here when this data becomes available.</em></p>
						<?php
					}
					else{
						?>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> This bar graph analyses time spans of a selected day in relation to library access frequency.</p>
						<div style="width: 100%"><canvas id="canvas" height="350" width="600"></canvas></div>
						<?php
					}
					?>
				</div>
				<div class="col-sm-4">
					<?php
					if($aborrow==0 && $areturn==0 && $aread==0 && $aother==0){
						?>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> There's no data available.<br/><em>A pie chart will be available here when this data becomes available.</em></p>
						<?php
					}
					else{
						?>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> This pie chart shows library user activity in the selected day.</p>
						<div style="width: 100%"><canvas id="chart-area" height="250" width="300"></canvas></div>
						<?php
					}
					?>
				</div>
			</div>
			<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
			<hr>
			<h3><i class="glyphicon glyphicon-book"></i> Books</h3>
			<div class="row">
				<div class="col-sm-4">
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-share"></span> Most Borrowed book(s)</span></h4>
					<?php
					$q="SELECT COUNT(IID) AS numrows FROM issue WHERE DAY(iTimeS)= DAY(CURDATE());";
					$res=mysqli_query($mysqli,$q) or die ("Query failed counting issued books...");
					$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
					if($rw['numrows']>0){
						?>
						<p>
						<?php
						$q1="SELECT BID FROM issue WHERE DAY(iTimeS)= DAY(CURDATE()) GROUP BY BID ORDER BY COUNT(*) DESC LIMIT 5;";
						$res1=mysqli_query($mysqli,$q1);
						while ($rw1=mysqli_fetch_array($res1)){
							$q2="SELECT * FROM books WHERE BID=".$rw1[0]." LIMIT 1;";
							$res2=mysqli_query($mysqli,$q2);
							while ($rw2=mysqli_fetch_array($res2)){
								?><span class="glyphicon glyphicon-book"></span> <strong><?php echo $rw2['bTitle']; ?></strong> by <em><?php echo $rw2['bAuthor']; ?></em><br/><?php
							}
						}
						?>
						</p>
						<a href="#" class="btn btn-warning disabled"><span class="glyphicon glyphicon-export"></span> Export Data</a>
						<?php
					}
					else{
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> There are no records found...</em></p>
						<?php
					}
					?>
				</div>
				<div class="col-sm-4">
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-time"></span> Overdue book(s)</span></h4>
					<?php
					$ovb=$stats->countOverdueBooks();
					if($ovb>0){
						$owe=$stats->countOverdueBooksCharges();
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> You have <strong><?php echo $ovb; ?></strong> overdue book(s).</em></p>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> <strong>Ksh <?php echo $owe; ?></strong> to be collected from the students as charges for overdue books.</em></p>
						<a href="lib-books-view?cart=overdue" class="btn btn-warning btn"><span class="glyphicon glyphicon-book"></span> Overdue Books</a>
						<?php
					}
					else{
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> There are no overdue books...</em></p>
						<?php
					}
					?>
					<p class="text-warning"><em><span class="glyphicon glyphicon-warning-sign"></span> Preceding stats are not based on any time span.</em></p>
				</div>
				<div class="col-sm-4">
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-share"></span> Lost book(s)</span></h4>
					<?php
					$ovb=$stats->countLostBooks();
					if($ovb>0){
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> You have <strong><?php echo $ovb; ?></strong> lost book(s).</em></p>
						<a href="lib-books-view?cart=lost" class="btn btn-warning btn"><span class="glyphicon glyphicon-book"></span> Lost Books</a>
						<?php
					}
					else{
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> There are no lost books...</em></p>
						<?php
					}
					?>
					<p class="text-warning"><em><span class="glyphicon glyphicon-warning-sign"></span> Preceding stats are not based on any time span.</em></p>
				</div>
			</div>
			<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
			<hr/>
			<h3><i class="glyphicon glyphicon-user"></i> Students & Staff Stats</h3>
			<div class="row">
				<div class="col-sm-4">
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-dashboard"></span> Most Active Student(s)</span></h4>
					<?php
					$q="SELECT COUNT(UID) AS numrows FROM activity WHERE DATE(uTimeS)=CURDATE();";
					$res=mysqli_query($mysqli,$q) or die ("Query failed counting activities...");
					$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
					if($rw['numrows']>0){
						?>
						<p>
						<?php
						$q1="SELECT SID,UActivity FROM activity WHERE DATE(uTimeS)=CURDATE() GROUP BY SID ORDER BY COUNT(*) DESC LIMIT 5;";
						$res1=mysqli_query($mysqli,$q1);
						while ($rw1=mysqli_fetch_array($res1)){
							$q2="SELECT * FROM libcusts WHERE LID=".$rw1[0]." AND LType='student' LIMIT 1;";
							$res2=mysqli_query($mysqli,$q2);
							while ($rw2=mysqli_fetch_array($res2)){
								?><span class="glyphicon glyphicon-user"></span> <?php echo ucwords(strtolower($rw2['LName'])); ?>, <em><?php echo $rw2['LNumb']; ?></em><br/><?php
							}
						}
						?>
						</p>
						<a href="#" class="btn btn-warning disabled"><span class="glyphicon glyphicon-export"></span> Export Data</a>
						<p>&nbsp;</p>
						<?php
					}
					else{
						?>
						<p class="text-info"><em>There are no records found.</em></p>
						<p>&nbsp;</p>
						<?php
					}
					?>
				</div>
				<div class="col-sm-4">
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-ban-circle"></span> Banned Students</span></h4>
					<?php
					$bstu=$stats->countBannedStuds();
					if($bstu>0){
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $bstu; ?></strong> banned student(s)</em> <a href="lib-students-banned" class="btn btn-link btn-sm"><span class="glyphicon glyphicon-user"></span> Banned Students</a></p>
						<?php
					}
					else{
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> There are no banned students.</em></p>
						<?php
					}
					?>
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-ban-circle"></span> Banned Staff</span></h4>
					<?php
					$bsta=$stats->countBannedStaff();
					if($bsta>0){
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $bsta; ?></strong> banned staff</em> <a href="lib-students-banned?staff" class="btn btn-link btn-sm"><span class="glyphicon glyphicon-user"></span> View Banned Staff</a></p>
						<?php
					}
					else{
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> There are no banned staff.</em></p>
						<?php
					}
					?>
					<p class="text-warning"><em><span class="glyphicon glyphicon-warning-sign"></span> Preceding stats are not based on any time span.</em></p>
				</div>
			</div>
			<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
			
			<hr>
			<?php $ui->printFooter(); ?>
		</div>
		<?php
	}
	
	$ui->printBottom();
?>
<script>
	//bar data
	var barChartData = {
		labels : ["8am-12pm","12pm-4pm","4pm-9pm"],
		datasets : [
			{
				fillColor : "rgba(151,187,205,0.5)",
				strokeColor : "rgba(151,187,205,0.8)",
				highlightFill : "rgba(151,187,205,0.75)",
				highlightStroke : "rgba(151,187,205,1)",
				data : [<?php echo $atu1; ?>,<?php echo $atu2; ?>,<?php echo $atu3; ?>]
			}
		]
		
	}
	
	//pie data
	var pieData = [
		{
			value: <?php echo $aborrow; ?>,
			color:"#F7464A",
			highlight: "#FF5A5E",
			label: "Borrow"
		},
		{
			value: <?php echo $areturn; ?>,
			color: "#46BFBD",
			highlight: "#5AD3D1",
			label: "Return"
		},
		{
			value: <?php echo $aread; ?>,
			color: "#FDB45C",
			highlight: "#FFC870",
			label: "Read"
		},
		{
			value: <?php echo $aother; ?>,
			color: "#949FB1",
			highlight: "#A8B3C5",
			label: "Other"
		}

	];
	
	window.onload = function(){
		var bar = document.getElementById("canvas").getContext("2d");
		var pie = document.getElementById("chart-area").getContext("2d");
		window.myBar = new Chart(bar).Bar(barChartData, {
			responsive : true
		});
		window.myPie = new Chart(pie).Pie(pieData);
	}
</script>
