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
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-home"></span> Library Home</h1>
			<div class="row">
				<div class="col-sm-12">
					<p class="text-info" style="margin-top:10px;">Search for <strong>students</strong> and <strong>staff</strong>, click on the search results for more actions.</p>
					<form role="form" method="GET" autocomplete="off" action="#" name="srch" onSubmit="return false;" >
						<div class="form-group">
							<input type="text" name="username" id="q" class="form-control input-lg" value="" placeholder="Type here..." style="border-radius:0 !important;" onkeyup="JavaScript:searchStudents()" />
						</div>
					</form>
					<div class="search-res col-sm-12" id="search-res">
						<p>&nbsp;</p><p><img src="./images/loading.gif"> Searching, please wait...</p>
					</div>
					<p>&nbsp;</p>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<h3><span class="glyphicon glyphicon-calendar"></span> Monthly Calendar</h3>
					<!--<h5><span class="glyphicon glyphicon-calendar"></span> <?php echo date('D, j M, Y'); ?></h5>-->
					<table cellpadding="0" cellspacing="0" class="calendar">
						<tr>
							<th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th>
						</tr>
						<?php
						$td=cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
						$timestamp=mktime(0,0,0,date('m'),1,date('Y'));
						$maxday=date("t",$timestamp);
						$thismonth=getdate($timestamp);
						$startday=$thismonth['wday'];
						$url="lib-analytics?filter=today";
						for($i=0; $i<($maxday+$startday); $i++){
							if(($i%7)==0)
								echo "<tr>";
							if($i<$startday)
								echo "<td></td>";
							else{
								$d=($i-$startday+1);
								if ($d==date('j'))
									echo "<td id='curr' title='Today&apos;s Library Activities' onclick=\"javascript:window.location='$url';\">".$d."</td>";
								else
									echo "<td>".$d."</td>";
								
							}
							if(($i%7)==6)
								echo "</tr>";
						}
						?>
					</table>
					<p>&nbsp;</p>
					<p>Hello <strong><?php echo ucwords(strtolower($_SESSION["CURR_USER_NAME"])); ?></strong>,<br/>Today is <strong><?php echo date('D, j M, Y'); ?></strong>, welcome to <strong><?php echo WA_TITLE; ?></strong>.</p>
					<a href="lib-analytics" class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-stats"></span> Library Analytics</a>
				</div>
				
				<div class="col-sm-4">
					<h3><span class="glyphicon glyphicon-stats"></span> Books Stats</h3>
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-share"></span> Most Borrowed book(s)</span></h4>
					<?php
					$q="SELECT COUNT(IID) AS numrows FROM issue;";
					$res=mysqli_query($mysqli,$q) or die ("Query failed counting issued books...");
					$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
					if($rw['numrows']>0){
						?>
						<p>
						<?php
						$q1="SELECT BID FROM issue WHERE MONTH(iTimeS)= MONTH(CURDATE()) GROUP BY BID ORDER BY COUNT(*) DESC LIMIT 5;";
						$res1=mysqli_query($mysqli,$q1);
						while ($rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC)){
							$q2="SELECT * FROM books WHERE BID=".$rw1[0]." LIMIT 1;";
							$res2=mysqli_query($mysqli,$q2);
							while ($rw2=mysqli_fetch_array($res2,MYSQLI_ASSOC)){
								?><span class="glyphicon glyphicon-book"></span> <strong><?php echo $rw2['bTitle']; ?></strong> by <em><?php echo $rw2['bAuthor']; ?></em><br/><?php
							}
						}
						?>
						</p>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> Preceding stats are based on current month.</em></p>
						<a href="lib-analytics-monthly" class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-stats"></span> More Analytics</a>
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
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-time"></span> Overdue book(s)</span></h4>
					<?php
					$ovb=$stats->countOverdueBooks();
					if($ovb>0){
						$owe=$stats->countOverdueBooksCharges();
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> You have <strong><?php echo $ovb; ?></strong> overdue book(s).</em></p>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> <strong>Ksh <?php echo $owe; ?></strong> to be collected from the students as charges for overdue books.</em></p>
						<a href="lib-books-view?cart=overdue" class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-book"></span> Overdue Books</a>
						<?php
					}
					else{
						?>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> There are no overdue books.</em></p>
						<?php
					}
					?>
				</div>
				
				<div class="col-sm-4">
					<h3><span class="glyphicon glyphicon-stats"></span> Students & Staff Stats</h3>
					<h4 class="text-danger"><span class='label label-danger'><span class="glyphicon glyphicon-dashboard"></span> Most Active Student(s)</span></h4>
					<?php
					$q="SELECT COUNT(UID) AS numrows FROM activity;";
					$res=mysqli_query($mysqli,$q) or die ("Query failed counting activities...");
					$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
					if($rw['numrows']>0){
						?>
						<p>
						<?php
						$q1="SELECT SID,UActivity FROM activity WHERE MONTH(uTimeS)= MONTH(CURDATE()) GROUP BY SID,UActivity ORDER BY COUNT(*) DESC LIMIT 7;";
						$res1=mysqli_query($mysqli,$q1) or die ("Query failed fetching user activities...");
						while ($rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC)){
							$q2="SELECT * FROM libcusts WHERE LID=".$rw1[0]." AND LType='student' LIMIT 1;";
							$res2=mysqli_query($mysqli,$q2);
							while ($rw2=mysqli_fetch_array($res2,MYSQLI_ASSOC)){
								?><span class="glyphicon glyphicon-user"></span> <?php echo ucwords(strtolower($rw2['LName'])); ?>, <em><?php echo $rw2['LNumb']; ?></em><br/><?php
							}
						}
						?>
						</p>
						<p class="text-info"><em><span class="glyphicon glyphicon-info-sign"></span> Preceding stats are based on current month.</em></p>
						<a href="lib-analytics-monthly" class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-stats"></span> More Analytics</a>
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
	//search students
	function searchStudents(){
		var stud=document.forms['srch']['q'].value;
		var elem=document.getElementById('search-res');
		stud=stud.trim();
		if (stud.length>=1){
			//elem.style.display="block";
			$('#search-res').fadeIn('fast');
			//new Ajax.Updater('search-res', 'lib-search?q=' + stud);
			$('#search-res').load('lib-search-custs?q=' + stud);//new Ajax.Updater('search-res', 'lib-search?q=' + stud);
		}
		else{
			$('#search-res').fadeOut('fast');
			document.forms['srch']['q'].value='';
		}
	}
	$("#q").focus();
</script>