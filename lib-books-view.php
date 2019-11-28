<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=2.9;
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
		
		if (!isset($_GET["cart"])){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.",'danger');
			header('location: ./lib-books');
			exit();
		}
		
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			
			////////////////////////////////////////////////////////////////////////////////////
			///////ISSUED CARTEGORY
			if($_GET["cart"]=='issued'){
				?>
				<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-share-alt"></span> Issued Books</h1>
				<?php
				//check number of issue entities available
				$quer="SELECT COUNT(IID) FROM issue WHERE iState=0;";
				$res=mysqli_query($mysqli,$quer);
				if ($res){					//if the query is successful
					while ($row=mysqli_fetch_array($res)){
						if ($row[0]==0){	//no issues found
							?>
							<p>&nbsp;</p>
							<p><a class="btn btn-default btn-sm" href="lib-home"><i class="glyphicon glyphicon-home"></i> Library Home</a> &nbsp; <a class="btn btn-success btn-sm" href="lib-books"><i class="glyphicon glyphicon-book"></i> View All Books</a></p>
							<p>&nbsp;</p>
							<p class="text-info">You have not yet issued any books to students or staff.</p>
							<p>&nbsp;</p>
							<?php
						}
						else{				//found
							$squer="SELECT * FROM issue WHERE iState=0;";
							$resl=mysqli_query($mysqli,$squer);
							?>
							
							<!----*************modal**************---->
							<div id="exportModal" class="modal fade" role="dialog">
								<div class="modal-dialog">
									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Export Issued Books Records</h4>
										</div>
										<form action="lib-books-export" method="POST">
											<div class="modal-body">
												<p>&nbsp;</p>
												<div class="radio"><label><input type="radio" name="dtype" id="issue" value="issue" checked /> Download All Issued Books Records (MS Excel File)</label></div>
												<p>&nbsp;</p>
											</div>
											<div class="modal-footer"> 
												<button type="submit" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-download-alt"></i> Download</button>
												<a class="btn btn-danger btn-sm" href="#" role="button" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</a>
											</div>
										</form>
									</div> 
								</div> 
							</div>
							<!----*********************************---->
							
							<p class="text-info"><a class="btn btn-success btn-sm" href="lib-books" role="button"><i class="glyphicon glyphicon-book"></i> View All Books</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#exportModal"><i class="glyphicon glyphicon-export"></i> Export Records</a></p>
							<p>&nbsp;</p>
							<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the issued books.</p>
							<p>&nbsp;</p>
							<table class="table table-bordered table-striped table-responsive table-hover" id="render" cellspacing="0">
								<thead>
									<tr>
										<th>Book No.</th>
										<th>Title</th>
										<th>Author</th>
										<!--<th>Cartegory</th>
										<th>Subject</th>-->
										<th>Issued To</th>
										<th>Number</th>
										<th>Class</th>
										<th>Issue Date</th>
									</tr>
								</thead>
								<tbody>
								<?php
								while ($r=mysqli_fetch_array($resl)){
									$q2="SELECT * FROM books WHERE BID=".$r['BID']." LIMIT 1;";
									$res2=mysqli_query($mysqli,$q2);
									while ($rw2=mysqli_fetch_array($res2)){
										?>
										<tr>
											<td><?php echo $rw2["bAccNo"]; ?></td>
											<td><?php echo $rw2["bTitle"]; ?></td>
											<td><?php echo $rw2["bAuthor"]; ?></td>
											<!--<td><?php echo $rw2["bCartegory"]; ?></td>
											<td><?php echo $rw2["bSubject"]; ?></td>-->
											<?php
											$q3="SELECT * FROM libcusts WHERE LID=".$r['SID']." LIMIT 1;";
											$res3=mysqli_query($mysqli,$q3);
											while ($rw3=mysqli_fetch_array($res3)){
												?>
												<td><?php echo ucwords(strtolower($rw3["LName"])); ?></td>
												<td><?php echo $rw3["LNumb"]; ?></td>
												<td>Form <?php echo $rw3["LForm"]; ?> <?php echo ucwords(strtolower($rw3["LStream"])); ?></td>
												<?php
											}
											?>
											<td><?php echo date('D, j M y',strtotime($r["iTimeS"])); ?></td>
										</tr>
										<?php
									}
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
			}
			////////////////////////////////////////////////////////////////////////////////////
			///////OVERDUE CARTEGORY
			elseif($_GET["cart"]=='overdue'){
				?>
				<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-time"></span> Overdue Books</h1>
				<?php
				//check number of overdue entities available
				$quer="SELECT COUNT(IID) AS numrows FROM issue WHERE (iTimeS+INTERVAL iDuration DAY)<CURDATE();";
				$res=mysqli_query($mysqli,$quer);
				if ($res){					//if the query is successful
					while ($row=mysqli_fetch_array($res)){
						if ($row[0]==0){	//no overdues found
							?>
							<p>&nbsp;</p>
							<p><a class="btn btn-default btn-sm" href="lib-home"><i class="glyphicon glyphicon-home"></i> Library Home</a> &nbsp; <a class="btn btn-success btn-sm" href="lib-books"><i class="glyphicon glyphicon-book"></i> View All Books</a></p>
							<p>&nbsp;</p>
							<p class="text-info">You have not yet issued any books to students or staff.</p>
							<p>&nbsp;</p>
							<?php
						}
						else{				//found
							$squer="SELECT * FROM issue WHERE (iTimeS+INTERVAL iDuration DAY)<CURDATE();";
							$resl=mysqli_query($mysqli,$squer);
							?>
							
							<!----*************modal**************---->
							<div id="exportModal" class="modal fade" role="dialog">
								<div class="modal-dialog">
									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Export overdue Books Records</h4>
										</div>
										<form action="lib-books-export" method="POST">
											<div class="modal-body">
												<p>&nbsp;</p>
												<div class="radio"><label><input type="radio" name="dtype" id="overdue" value="overdue" checked /> Download All Overdue Books Records (MS Excel File)</label></div>
												<p>&nbsp;</p>
											</div>
											<div class="modal-footer"> 
												<button type="submit" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-download-alt"></i> Download</button>
												<a class="btn btn-danger btn-sm" href="#" role="button" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</a>
											</div>
										</form>
									</div> 
								</div> 
							</div>
							<!----*********************************---->
							
							<p class="text-info"><a class="btn btn-success btn-sm" href="lib-books" role="button"><i class="glyphicon glyphicon-book"></i> View All Books</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#exportModal"><i class="glyphicon glyphicon-export"></i> Export Records</a></p>
							<p>&nbsp;</p>
							<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the overdue books.</p>
							<p>&nbsp;</p>
							<table class="table table-bordered table-striped table-responsive table-hover" id="render" cellspacing="0">
								<thead>
									<tr>
										<th>Book No.</th>
										<th>Title</th>
										<th>Author</th>
										<!--<th>Cartegory</th>
										<th>Subject</th>-->
										<th>Issued To</th>
										<th>Number</th>
										<th>Class</th>
										<th>Issue Date</th>
										<th>Date Overdue</th>
									</tr>
								</thead>
								<tbody>
								<?php
								while ($r=mysqli_fetch_array($resl)){
									$q2="SELECT * FROM books WHERE BID=".$r['BID']." LIMIT 1;";
									$res2=mysqli_query($mysqli,$q2);
									while ($rw2=mysqli_fetch_array($res2)){
										?>
										<tr>
											<td><?php echo $rw2["bAccNo"]; ?></td>
											<td><?php echo $rw2["bTitle"]; ?></td>
											<td><?php echo $rw2["bAuthor"]; ?></td>
											<!--<td><?php echo $rw2["bCartegory"]; ?></td>
											<td><?php echo $rw2["bSubject"]; ?></td>-->
											<?php
											$q3="SELECT * FROM libcusts WHERE LID=".$r['SID']." LIMIT 1;";
											$res3=mysqli_query($mysqli,$q3);
											while ($rw3=mysqli_fetch_array($res3)){
												?>
												<td><?php echo ucwords(strtolower($rw3["LName"])); ?></td>
												<td><?php echo $rw3["LNumb"]; ?></td>
												<td>Form <?php echo $rw3["LForm"]; ?> <?php echo ucwords(strtolower($rw3["LStream"])); ?></td>
												<?php
											}
											?>
											<td><?php echo date('D, j M y',strtotime($r["iTimeS"])); ?></td>
											<td><?php echo date('D, j M y',strtotime($r["iTimeS"])+(86400*$r["iDuration"])); ?></td>
										</tr>
										<?php
									}
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
			}
			////////////////////////////////////////////////////////////////////////////////////
			///////LOST CARTEGORY
			elseif($_GET["cart"]=='lost'){
				?>
				<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-remove-circle"></span> Lost Books</h1>
				<?php
				//check number of lost entities available
				$quer="SELECT COUNT(IID) FROM issue WHERE iState=2;";
				$res=mysqli_query($mysqli,$quer);
				if ($res){					//if the query is successful
					while ($row=mysqli_fetch_array($res)){
						if ($row[0]==0){	//no lost books found
							?>
							<p>&nbsp;</p>
							<p><a class="btn btn-default btn-sm" href="lib-home"><i class="glyphicon glyphicon-home"></i> Library Home</a> &nbsp; <a class="btn btn-success btn-sm" href="lib-books"><i class="glyphicon glyphicon-book"></i> View All Books</a></p>
							<p>&nbsp;</p>
							<p class="text-info">There are no lost books found.</p>
							<p>&nbsp;</p>
							<?php
						}
						else{				//found
							$squer="SELECT * FROM issue WHERE iState=2;";
							$resl=mysqli_query($mysqli,$squer);
							?>
							
							<!----*************modal**************---->
							<div id="exportModal" class="modal fade" role="dialog">
								<div class="modal-dialog">
									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Export Lost Books Records</h4>
										</div>
										<form action="lib-books-export" method="POST">
											<div class="modal-body">
												<p>&nbsp;</p>
												<div class="radio"><label><input type="radio" name="dtype" id="lost" value="lost" checked /> Download All Lost Books Records (MS Excel File)</label></div>
												<p>&nbsp;</p>
											</div>
											<div class="modal-footer"> 
												<button type="submit" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-download-alt"></i> Download</button>
												<a class="btn btn-danger btn-sm" href="#" role="button" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</a>
											</div>
										</form>
									</div> 
								</div> 
							</div>
							<!----*********************************---->
							
							<p>&nbsp;</p>
							<p class="text-info"><a class="btn btn-success btn-sm" href="lib-books" role="button"><i class="glyphicon glyphicon-book"></i> View All Books</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#exportModal"><i class="glyphicon glyphicon-export"></i> Export Records</a></p>
							<p>&nbsp;</p>
							<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the books marked as lost.</p>
							<p>&nbsp;</p>
							<table class="table table-bordered table-striped table-responsive table-hover" id="render" cellspacing="0">
								<thead>
									<tr>
										<th>Book No.</th>
										<th>Title</th>
										<th>Author</th>
										<!--<th>Cartegory</th>
										<th>Subject</th>-->
										<th>Issued To</th>
										<th>Number</th>
										<th>Class</th>
										<th>Marked Lost On</th>
									</tr>
								</thead>
								<tbody>
								<?php
								while ($r=mysqli_fetch_array($resl)){
									$q2="SELECT * FROM books WHERE BID=".$r['BID']." LIMIT 1;";
									$res2=mysqli_query($mysqli,$q2);
									while ($rw2=mysqli_fetch_array($res2)){
										?>
										<tr>
											<td><?php echo $rw2["bAccNo"]; ?></td>
											<td><?php echo $rw2["bTitle"]; ?></td>
											<td><?php echo $rw2["bAuthor"]; ?></td>
											<!--<td><?php echo $rw2["bCartegory"]; ?></td>
											<td><?php echo $rw2["bSubject"]; ?></td>-->
											<?php
											$q3="SELECT * FROM libcusts WHERE LID=".$r['SID']." LIMIT 1;";
											$res3=mysqli_query($mysqli,$q3);
											while ($rw3=mysqli_fetch_array($res3)){
												?>
												<td><?php echo ucwords(strtolower($rw3["LName"])); ?></td>
												<td><?php echo $rw3["LNumb"]; ?></td>
												<td>Form <?php echo $rw3["LForm"]; ?> <?php echo ucwords(strtolower($rw3["LStream"])); ?></td>
												<?php
											}
											?>
											<td><?php echo date('D, j M y',strtotime($r["iTimeS"])); ?></td>
										</tr>
										<?php
									}
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
			}
			////////////////////////////////////////////////////////////////////////////////////
			///////COMPENSATED CARTEGORY
			elseif($_GET["cart"]=='compensate'){
				?>
				<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-refresh"></span> Compensated Books</h1>
				<?php
				//check number of compensated books entities available
				$quer="SELECT COUNT(IID) FROM issue WHERE iState=3;";
				$res=mysqli_query($mysqli,$quer);
				if ($res){					//if the query is successful
					while ($row=mysqli_fetch_array($res)){
						if ($row[0]==0){	//no records found
							?>
							<p>&nbsp;</p>
							<p><a class="btn btn-default btn-sm" href="lib-home"><i class="glyphicon glyphicon-home"></i> Library Home</a> &nbsp; <a class="btn btn-success btn-sm" href="lib-books"><i class="glyphicon glyphicon-book"></i> View All Books</a></p>
							<p>&nbsp;</p>
							<p class="text-info">No records found for compensated books.</p>
							<p>&nbsp;</p>
							<?php
						}
						else{				//found
							$squer="SELECT * FROM issue WHERE iState=3;";
							$resl=mysqli_query($mysqli,$squer);
							?>
							
							<!----*************modal**************---->
							<div id="exportModal" class="modal fade" role="dialog">
								<div class="modal-dialog">
									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Export Compensated Books Records</h4>
										</div>
										<form action="lib-books-export" method="POST">
											<div class="modal-body">
												<p>&nbsp;</p>
												<div class="radio"><label><input type="radio" name="dtype" id="compensated" value="compensated" checked /> Download All Compensated Books Records (MS Excel File)</label></div>
												<p>&nbsp;</p>
											</div>
											<div class="modal-footer"> 
												<button type="submit" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-download-alt"></i> Download</button>
												<a class="btn btn-danger btn-sm" href="#" role="button" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</a>
											</div>
										</form>
									</div> 
								</div> 
							</div>
							<!----*********************************---->
							
							<p class="text-info"><a class="btn btn-success btn-sm" href="lib-books" role="button"><i class="glyphicon glyphicon-book"></i> View All Books</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#exportModal"><i class="glyphicon glyphicon-export"></i> Export Records</a></p>
							<p>&nbsp;</p>
							<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the issued books.</p>
							<p>&nbsp;</p>
							<table class="table table-bordered table-striped table-responsive table-hover" id="render" cellspacing="0">
								<thead>
									<tr>
										<th>Book No.</th>
										<th>Title</th>
										<th>Author</th>
										<th>Issued To</th>
										<th>Number</th>
										<th>Class</th>
										<th>Marked As Compensated On</th>
									</tr>
								</thead>
								<tbody>
								<?php
								while ($r=mysqli_fetch_array($resl)){
									$q2="SELECT * FROM books WHERE BID=".$r['BID']." LIMIT 1;";
									$res2=mysqli_query($mysqli,$q2);
									while ($rw2=mysqli_fetch_array($res2)){
										?>
										<tr>
											<td><?php echo $rw2["bAccNo"]; ?></td>
											<td><?php echo $rw2["bTitle"]; ?></td>
											<td><?php echo $rw2["bAuthor"]; ?></td>
											<?php
											$q3="SELECT * FROM libcusts WHERE LID=".$r['SID']." LIMIT 1;";
											$res3=mysqli_query($mysqli,$q3);
											while ($rw3=mysqli_fetch_array($res3)){
												?>
												<td><?php echo ucwords(strtolower($rw3["LName"])); ?></td>
												<td><?php echo $rw3["LNumb"]; ?></td>
												<td>Form <?php echo $rw3["LForm"]; ?> <?php echo ucwords(strtolower($rw3["LStream"])); ?></td>
												<?php
											}
											?>
											<td><?php echo date('D, j M Y',strtotime($r["iTimeS"])); ?></td>
										</tr>
										<?php
									}
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
			}
			//////////////////////////////////////////////////////////////////////////////
			///////////WRONG CARTEGORY
			else{
				$notif->setInfo("<strong>".$_GET['cart']."</strong> cartegory DOES NOT exist. Avoid manipulating the URL.",'danger');
				header('location: ./lib-books');
				exit();
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