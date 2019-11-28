<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=3.2;
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
		if ($_SERVER["REQUEST_METHOD"]=="POST"){
			
			if($_POST["delete"]=="all"){
				if (isset($_GET["staff"]))
					$sql2="DELETE FROM libcusts WHERE lType='staff';";
				else
					$sql2="DELETE FROM libcusts WHERE lType='student';";
				$result2=mysqli_query($mysqli,$sql2);
				if ($result2){
					if (isset($_GET["staff"])){
						ulog($_SESSION["CURR_USER_ID"],"Successfully deleted all staff records and any details attached to them in the system...");	//log this activity
						$notif->setInfo("The staff records were successfully deleted.","success");
						header('location: ./lib-students?staff');
					}
					else{
						ulog($_SESSION["CURR_USER_ID"],"Successfully deleted all students records and any details attached to them in the system...");	//log this activity
						$notif->setInfo("The students records were successfully deleted.","success");
						header('location: ./lib-students');
					}
					exit();
				}
				else{
					$notif->setInfo("A critical error occured while deleting the records. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
				}
			}
		}
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-pencil"></span> Update Students & Staff</h1>
			<?php
			if (!isset($_GET["staff"])){
				//check number of students entities available
				$quer="SELECT COUNT(LID) FROM libcusts WHERE lType='student';";
			}
			else{
				//check number of students entities available
				$quer="SELECT COUNT(LID) FROM libcusts WHERE lType='staff';";
			}
			$res=mysqli_query($mysqli,$quer);
			if ($res){					//if the query is successful
				while ($row=mysqli_fetch_array($res)){
					if ($row[0]==0){	//no students found
						?>
						<ul class="nav nav-tabs">
							<li class="nav-item <?php if (!isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-update" class="nav-link"><strong>UPDATE STUDENTS</strong></a></li>
							<li class="nav-item <?php if (isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-update?staff" class="nav-link"><strong>UPDATE STAFF</strong></a></li>
						</ul>
						<p>&nbsp;</p>
						<?php 
						if (!isset($_GET["staff"])){
							?>
							<p class="text-info"><a class="btn btn-success btn-sm" href="lib-students-new" role="button"><i class="glyphicon glyphicon-plus"></i> New Students</a></p>
							<p class="text-info">Students data unavailable, you can add students records by clicking the button above.</p>
							<p class="text-info">A list of all the available students will be available here once the database has been updated.</p>
							<?php
						}
						else{
							?>
							<p class="text-info"><a class="btn btn-success btn-sm" href="lib-students-new?staff" role="button"><i class="glyphicon glyphicon-plus"></i> New Staff</a></p>
							<p class="text-info">Staff data unavailable, you can add staff records by clicking the button above.</p>
							<p class="text-info">A list of all the available staff will be available here once the database has been updated.</p>
							<?php
						}
					}
					else{				//found
						if (!isset($_GET["staff"]))
							$squer="SELECT * FROM libcusts WHERE lType='student';";
						else
							$squer="SELECT * FROM libcusts WHERE lType='staff';";
						$resl=mysqli_query($mysqli,$squer);
						?>
						
						<!--*** DELETE MODAL ***-->
						<div id="deleteModal" class="modal fade" role="dialog">
							<div class="modal-dialog">
								<!--modal content-->
								<div class="modal-content">
									<form action="lib-students-update<?php if (isset($_GET["staff"])){ echo "?staff"; } ?>" method="POST">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title text-success">Delete <?php if (isset($_GET["staff"])){ echo "Staff"; } else{ echo "Students"; } ?></h4>
										</div>
										<div class="modal-body">
											<p>&nbsp;</p>
											<input name="delete" id="delete" value="all" type="hidden" />
											<p class="text-danger">Are you sure that you want to delete all the <?php if (isset($_GET["staff"])){ echo "staff"; } else{ echo "students"; } ?> records?</p>
											<p class="text-info">This process is irreversable. Note that you will lose all <?php if (isset($_GET["staff"])){ echo "staff"; } else{ echo "students"; } ?> records and any other records attached to them.</p>
											<p>&nbsp;</p>
										</div>
										<div class="modal-footer">
											<button class="btn btn-danger" ><i class="glyphicon glyphicon-trash"></i> Delete</button> &nbsp; <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</button>
										</div>
									</form>
								</div>
							</div>
						</div>
						<!--********************-->
						
						<!--*** PROMOTE MODAL ***-->
						<div id="promoteModal" class="modal fade" role="dialog">
							<div class="modal-dialog">
								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Promote Students</h4>
									</div>
									<form action="lib-students-update-promote" method="POST">
										<div class="modal-body">
											<p class='text-info'><i class='glyphicon glyphicon-info-sign'></i> This feature updates library records by promoting students to one class ahead at the beginning of an academic year.</p>
											<p>&nbsp;</p>
											<div class="radio"><label><input type="radio" name="ptype" value="promote" checked /> Promote <strong>all</strong> students to the next class.</label></div>
											<p>&nbsp;</p>
											<p class='text-info'><i class='glyphicon glyphicon-info-sign'></i> It is recommended that you <strong>export</strong> all library records before promoting students.</p>
											<p class='text-warning'><em><i class='glyphicon glyphicon-warning-sign'></i> For <strong>form 4</strong> students, delete each record individually while clearing them.</em></p>
										</div>
										<div class="modal-footer"> 
											<button type="submit" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-upload"></i> Promote Students</button>
											<a class="btn btn-danger btn-sm" href="#" role="button" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</a>
										</div>
									</form>
								</div> 
							</div>
						</div>
						<!--********************-->
						<ul class="nav nav-tabs">
							<li class="nav-item <?php if (!isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-update" class="nav-link"><strong>UPDATE STUDENTS</strong></a></li>
							<li class="nav-item <?php if (isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-update?staff" class="nav-link"><strong>UPDATE STAFF</strong></a></li>
						</ul>
						
						<p>&nbsp;</p>
						<?php
						if (!isset($_GET["staff"])){
							?>
							<p class="text-info"><a class="btn btn-default btn-sm" href="lib-students" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View Students</a> &nbsp; <a class="btn btn-success btn-sm" href="lib-students-new" role="button"><i class="glyphicon glyphicon-plus"></i> New Students</a> &nbsp; <a class="btn btn-danger btn-sm" href="#" role="button" data-toggle="modal" data-target="#deleteModal"><i class="glyphicon glyphicon-trash"></i> Delete Students Records</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#promoteModal"><i class="glyphicon glyphicon-upload"></i> Promote Students</a></p>
							<p>&nbsp;</p>
							<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the available students in the system.</p>
							<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> Click on a record to update, click on the columns to filter records, or search live for a specific student record by using the search widget.</p>
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
									<tr style="cursor:pointer;" onClick="JavaScript:window.location='lib-students-update-user?id=<?php echo $r["LID"]; ?>&type=student';">
										<td><?php echo $r["LNumb"]; ?></td>
										<td><?php echo ucwords(strtolower($r["LName"])); ?></td>
										<td>Form <?php echo $r["LForm"]; ?> <?php echo ucwords(strtolower($r["LStream"])); ?></td>
										<td><?php echo $r["LAYear"]; ?></td>
										<td><?php if($r["LBan"]==1){ echo "Banned Access"; } else { echo "Authorized Access"; } ?></td>
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
							<p class="text-info"><a class="btn btn-default btn-sm" href="lib-students?staff" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View Staff</a> &nbsp; <a class="btn btn-success btn-sm" href="lib-students-new?staff" role="button"><i class="glyphicon glyphicon-plus"></i> New Staff</a> &nbsp; <a class="btn btn-danger btn-sm" href="#" role="button" data-toggle="modal" data-target="#deleteModal"><i class="glyphicon glyphicon-trash"></i> Delete Staff Records</a></p>
							<p>&nbsp;</p>
							<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the available staff in the system.</p>
							<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> Click on a record to update, click on the columns to filter records, or search live for a specific staff record by using the search widget.</p>
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
									<tr style="cursor:pointer;" onClick="JavaScript:window.location='lib-students-update-user?id=<?php echo $r["LID"]; ?>&type=staff';">
										<td><?php echo $r["LNumb"]; ?></td>
										<td><?php echo $r["LName"]; ?></td>
										<!--<td><?php echo $r["LAYear"]; ?></td>-->
										<td><?php if($r["LBan"]==1){ echo "Banned Access"; } else { echo "Authorized Access"; } ?></td>
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