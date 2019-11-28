<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=3.4;
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
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-ban-circle"></span> Banned Students & Staff</h1>
			<?php
			if (!isset($_GET["staff"])){
				//check number of students entities available
				$quer="SELECT COUNT(LID) FROM libcusts WHERE lType='student' AND LBan=1;";
			}
			else{
				//check number of students entities available
				$quer="SELECT COUNT(LID) FROM libcusts WHERE lType='staff' AND LBan=1;";
			}
			$res=mysqli_query($mysqli,$quer);
			if ($res){					//if the query is successful
				while ($row=mysqli_fetch_array($res)){
					if ($row[0]==0){	//no students found
						?>
						<ul class="nav nav-tabs">
							<li class="nav-item <?php if (!isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-banned" class="nav-link"><strong>STUDENTS</strong></a></li>
							<li class="nav-item <?php if (isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-banned?staff" class="nav-link"><strong>STAFF</strong></a></li>
						</ul>
						<p>&nbsp;</p>
						<?php 
						if (!isset($_GET["staff"])){
							?>
							<p class="text-info"><a class="btn btn-success btn-sm" href="lib-students" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Students</a></p>
							<p class="text-info">There are no students banned from accessing library resources.</p>
							<?php
						}
						else{
							?>
							<p class="text-info"><a class="btn btn-success btn-sm" href="lib-students?staff" role="button"><i class="glyphicon glyphicon-plus"></i> View All Staff</a></p>
							<p class="text-info">There are no staff banned from accessing the library resources.</p>
							<?php
						}
					}
					else{				//found
						if (!isset($_GET["staff"]))
							$squer="SELECT * FROM libcusts WHERE lType='student' AND LBan=1;";
						else
							$squer="SELECT * FROM libcusts WHERE lType='staff' AND LBan=1;";
						$resl=mysqli_query($mysqli,$squer);
						?>
						
						<!----************* export modal**************---->
						<div id="exportModal" class="modal fade" role="dialog">
							<div class="modal-dialog">
								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<?php
										if (!isset($_GET["staff"])){
											?><h4 class="modal-title">Export Banned Students Records</h4><?php
										}
										else{
											?><h4 class="modal-title">Export Banned Staff Records</h4><?php
										}
										?>
									</div>
									<?php
									if (!isset($_GET["staff"])){
										?>
										<form action="lib-students-export-banned" method="POST">
											<div class="modal-body">
												<p>&nbsp;</p>
												<div class="radio"><label><input type="radio" name="dtype" id="dtype" value="student" checked /> Download All Banned Students Records (MS Excel File)</label></div>
												<p>&nbsp;</p>
											</div>
											<div class="modal-footer"> 
												<button type="submit" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-download-alt"></i> Download</button>
												<a class="btn btn-danger btn-sm" href="#" role="button" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</a>
											</div>
										</form>
										<?php
									}
									else{
										?>
										<form action="lib-students-export-banned?staff" method="POST">
											<div class="modal-body">
												<p>&nbsp;</p>
												<div class="radio"><label><input type="radio" name="dtype" id="dtype" value="staff" checked /> Download All Banned Staff Records (MS Excel File)</label></div>
												<p>&nbsp;</p>
											</div>
											<div class="modal-footer"> 
												<button type="submit" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-download-alt"></i> Download</button>
												<a class="btn btn-danger btn-sm" href="#" role="button" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</a>
											</div>
										</form>
										<?php
									}
									?>
								</div> 
							</div> 
						</div>
						<!----*********************************---->
						
						<ul class="nav nav-tabs">
							<li class="nav-item <?php if (!isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-banned" class="nav-link"><strong>STUDENTS</strong></a></li>
							<li class="nav-item <?php if (isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-banned?staff" class="nav-link"><strong>STAFF</strong></a></li>
						</ul>
						
						<p>&nbsp;</p>
						<?php
						if (!isset($_GET["staff"])){
							?>
							<p class="text-info"><a class="btn btn-default btn-sm" href="lib-students?staff" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View Staff</a> &nbsp; <a class="btn btn-success btn-sm" href="lib-students-new" role="button"><i class="glyphicon glyphicon-plus"></i> New Students</a> &nbsp; <a class="btn btn-danger btn-sm" href="lib-students-update" role="button"><i class="glyphicon glyphicon-pencil"></i> Update Students</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#exportModal"><i class="glyphicon glyphicon-export"></i> Export Banned Students Records</a></p>
							<p>&nbsp;</p>
							<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the students banned from accessing the library.</p>
							<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> Click on a record for more information, click on the columns to filter records, or search live for a specific student record by using the search widget.</p>
							<p>&nbsp;</p>
							
							<table class="table table-bordered table-striped table-responsive table-hover" id="render" cellspacing="0">
								<thead>
									<tr>
										<th>Adm. No.</th>
										<th>Name</th>
										<th>Class</th>
										<th>Adm. Year</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
								<?php
								while ($r=mysqli_fetch_array($resl)){
									?>
									
									<!--books modal-->
									<div id="modal<?php echo $r["LID"]; ?>" class="modal fade" role="dialog">
										<div class="modal-dialog">
											<!--modal content-->
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title text-success"><?php echo $r["LNumb"].", ".$r["LName"]; ?></h4>
												</div>
												<div class="modal-body">
													<p><strong>Student Name:</strong> <?php echo $r["LName"]; ?></p>
													<p><strong>Admission Number:</strong> <?php echo $r["LNumb"]; ?></p>
													<p><strong>Class:</strong> Form <?php echo $r["LForm"]; ?> <?php echo ucwords(strtolower($r["LStream"])); ?></p>
													<p><strong>Admission Year:</strong> <?php echo $r["LAYear"]; ?></p>
													<p><?php if($r["LBan"]==1){ echo '<strong>This student has been banned from accessing the Library materials</strong>'; } else{ echo '<strong>This student is authorized to access the Library materials</strong>'; } ?></p>
												</div>
												<div class="modal-footer">
													<?php
													if($r["LBan"]==1){
														?><a class="btn btn-success" href="./lib-students-action?action=allow&id=<?php echo $r["LID"]; ?>"><i class="glyphicon glyphicon-ok"></i> Allow Access</a> &nbsp; <?php
													}
													else{
														?><a class="btn btn-danger" href="./lib-students-action?action=deny&id=<?php echo $r["LID"]; ?>"><i class="glyphicon glyphicon-ban-circle"></i> Deny Access</a> &nbsp; <?php
													}
													?>
													<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
												</div>
											</div>
										</div>
									</div>
									<!--==============-->
									
									<tr style="cursor:pointer;" data-toggle="modal" data-target="#modal<?php echo $r["LID"]; ?>">
										<td><?php echo $r["LNumb"]; ?></td>
										<td><?php echo $r["LName"]; ?></td>
										<td>Form <?php echo $r["LForm"]; ?> <?php echo ucwords(strtolower($r["LStream"])); ?></td>
										<td><?php echo $r["LAYear"]; ?></td>
										<td><?php if($r["LBan"]==1){ echo "Denied Access"; } else { echo "Allowed Access"; } ?></td>
									</tr>
									<?php
								}
								?>
								</tbody>
							</table>
							
							<?php
						}
						else{
							?>
							<p class="text-info"><a class="btn btn-default btn-sm" href="lib-students" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View Students</a> &nbsp; <a class="btn btn-success btn-sm" href="lib-students-new?staff" role="button"><i class="glyphicon glyphicon-plus"></i> New Staff</a> &nbsp; <a class="btn btn-danger btn-sm" href="lib-students-update?staff" role="button"><i class="glyphicon glyphicon-pencil"></i> Update Staff</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#exportModal"><i class="glyphicon glyphicon-export"></i> Export Staff Records</a></p>
							<p>&nbsp;</p>
							<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the staff banned from accessing the library.</p>
							<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> Click on a record for more information, click on the columns to filter records, or search live for a specific staff record by using the search widget.</p>
							<p>&nbsp;</p>
							
							
							<table class="table table-bordered table-striped table-responsive table-hover" id="render" cellspacing="0">
								<thead>
									<tr>
										<th>Number</th>
										<th>Name</th>
										<!--<th>Adm. Year</th>-->
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
								<?php
								while ($r=mysqli_fetch_array($resl)){
									?>
									
									<!--books modal-->
									<div id="modal<?php echo $r["LID"]; ?>" class="modal fade" role="dialog">
										<div class="modal-dialog">
											<!--modal content-->
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title text-success"><?php echo $r["LNumb"].", ".$r["LName"]; ?></h4>
												</div>
												<div class="modal-body">
													<p><strong>Staff Name:</strong> <?php echo $r["LName"]; ?></p>
													<p><strong>Number:</strong> <?php echo $r["LNumb"]; ?></p>
													<!--<p><strong>Admission Year:</strong> <?php echo $r["LAYear"]; ?></p>-->
													<p><?php if($r["LBan"]==1){ echo '<strong>This staff has been banned from accessing the Library materials</strong>'; } else{ echo '<strong>This staff is authorized to access the Library materials</strong>'; } ?></p>
												</div>
												<div class="modal-footer">
													<?php
													if($r["LBan"]==1){
														?><a class="btn btn-success" href="./lib-students-action?action=allow&id=<?php echo $r["LID"]; ?>&staff"><i class="glyphicon glyphicon-ok"></i> Allow Access</a> &nbsp; <?php
													}
													else{
														?><a class="btn btn-danger" href="./lib-students-action?action=deny&id=<?php echo $r["LID"]; ?>&staff"><i class="glyphicon glyphicon-ban-circle"></i> Deny Access</a> &nbsp; <?php
													}
													?>
													<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
												</div>
											</div>
										</div>
									</div>
									<!--==============-->
									
									<tr style="cursor:pointer;" data-toggle="modal" data-target="#modal<?php echo $r["LID"]; ?>">
										<td><?php echo $r["LNumb"]; ?></td>
										<td><?php echo $r["LName"]; ?></td>
										<!--<td><?php echo $r["LAYear"]; ?></td>-->
										<td><?php if($r["LBan"]==1){ echo "Denied Access"; } else { echo "Allowed Access"; } ?></td>
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