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
	$aborrow=$areturn=$aread=$aother=0;
	
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
		function countSActivity($activity,$month){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(UID) AS numrows FROM activity WHERE MONTH(uTimeS)=$month AND UActivity='".$activity."';";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		if(isset($_GET['month'])){
			$aborrow=countSActivity('borrow',$_GET['month']);
			$areturn=countSActivity('return',$_GET['month']);
			$aread=countSActivity('read',$_GET['month']);
			$aother=countSActivity('other',$_GET['month']);
		}
		else{
			$aborrow=countSActivity('borrow',date('m'));
			$areturn=countSActivity('return',date('m'));
			$aread=countSActivity('read',date('m'));
			$aother=countSActivity('other',date('m'));
		}
		/////////////////////////////////////////////////////////////
		
		function convMonth($month){
			if($month==1) return 'January'; elseif($month==2) return 'February'; elseif($month==3) return 'March'; elseif($month==4) return 'April'; elseif($month==5) return 'May'; elseif($month==6) return 'June'; elseif($month==7) return 'July'; elseif($month==8) return 'August'; elseif($month==9) return 'September'; elseif($month==10) return 'October'; elseif($month==11) return 'November'; elseif($month==12) return 'December';
		}
		
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
				<li class="nav-item"><a href="lib-analytics" class="nav-link"><strong>TODAY</strong></a></li>
				<li class="nav-item active"><a href="lib-analytics-monthly" class="nav-link"><strong><i class='glyphicon glyphicon-calendar'></i> MONTHLY</strong></a></li>
				<li class="nav-item"><a href="lib-analytics-yearly" class="nav-link"><strong> YEARLY</strong></a></li>
			</ul>
			<!--HEADER-->
			<p>&nbsp;</p>
			<div class="well well-sm">
				<em><i class="glyphicon glyphicon-calendar"></i> Choose month:</em>&nbsp;
				<?php
				$q0="SELECT DISTINCT MONTH(UTimeS) FROM activity WHERE YEAR(UTimeS)= YEAR(CURDATE()) ORDER BY MONTH(UTimeS) Asc;";
				$res0=mysqli_query($mysqli,$q0);
				while ($rw0=mysqli_fetch_array($res0)){
					if(isset($_GET['month'])){
						if($_GET['month']==$rw0[0]){
							?><strong><?php echo convMonth($rw0[0]); ?></strong>&nbsp;&nbsp;<?php
						}
						else{
							?><a href="lib-analytics-monthly?month=<?php echo $rw0[0]; ?>"><?php echo convMonth($rw0[0]); ?></a>&nbsp;&nbsp;<?php
						}
					}
					else{
						?><a href="lib-analytics-monthly?month=<?php echo $rw0[0]; ?>"><?php echo convMonth($rw0[0]); ?></a>&nbsp;&nbsp;<?php
					}
				}
				?>
			</div>
			<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
			<h3><i class="glyphicon glyphicon-transfer"></i> Library Activity</h3>
			<div class="row">
				<div class="col-sm-3">
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
					if(isset($_GET['month']))
						$q="SELECT COUNT(IID) AS numrows FROM issue WHERE MONTH(iTimeS)=".$_GET['month'].";";
					else
						$q="SELECT COUNT(IID) AS numrows FROM issue WHERE MONTH(iTimeS)=MONTH(CURDATE());";
					$res=mysqli_query($mysqli,$q) or die ("Query failed counting issued books...");
					$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
					if($rw['numrows']>0){
						?>
						<p>
						<?php
						if(isset($_GET['month']))
							$q1="SELECT BID FROM issue WHERE MONTH(iTimeS)=".$_GET['month']." GROUP BY BID ORDER BY COUNT(*) DESC LIMIT 5;";
						else
							$q1="SELECT BID FROM issue WHERE MONTH(iTimeS)=MONTH(CURDATE()) GROUP BY BID ORDER BY COUNT(*) DESC LIMIT 5;";
						$res1=mysqli_query($q1);
						while ($rw1=mysql_fetch_array($res1)){
							$q2="SELECT * FROM books WHERE BID=".$rw1[0]." LIMIT 1;";
							$res2=mysql_query($mysqli,$q2);
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
					if(isset($_GET['month']))
						$q="SELECT COUNT(UID) AS numrows FROM activity WHERE MONTH(uTimeS)=".$_GET['month'].";";
					else
						$q="SELECT COUNT(UID) AS numrows FROM activity WHERE MONTH(uTimeS)=MONTH(CURDATE());";
					$res=mysqli_query($mysqli,$q) or die ("Query failed counting activities...");
					$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
					if($rw['numrows']>0){
						?>
						<p>
						<?php
						if(isset($_GET['month']))
							$q1="SELECT SID,UActivity FROM activity WHERE MONTH(uTimeS)=".$_GET['month']." GROUP BY SID ORDER BY COUNT(*) DESC LIMIT 5;";
						else
							$q1="SELECT SID,UActivity FROM activity WHERE MONTH(uTimeS)=MONTH(CURDATE()) GROUP BY SID ORDER BY COUNT(*) DESC LIMIT 5;";
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
		var pie = document.getElementById("chart-area").getContext("2d");
		window.myPie = new Chart(pie).Pie(pieData);
	}
</script>
