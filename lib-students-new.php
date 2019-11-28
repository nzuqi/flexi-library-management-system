<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=3.1;
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
		
		//book variables
		$ltype=$lno=$lname=$lform=$lstream=$lyear=$err="";
		
		if ($_SERVER["REQUEST_METHOD"]=="POST"){
			
			$lno=$validate->test_input($_POST["lno"]);
			$lname=$validate->test_input($_POST["lname"]);
			$lyear=$validate->test_input($_POST["lyear"]);
			
			if(!isset($_GET['staff'])){
				$lform=$validate->test_input($_POST["lform"]);
				$lstream=$validate->test_input($_POST["lstream"]);
			}
			
			$ltype='student';
			
			//run some tests
			if ($lno==""){
				$err="<b>Number</b> is required.";
				$notif->setInfo($err,'danger');
			}
			elseif ($validate->lnoExists($lno)){
				$err="<b>Number</b> already exists in the system.";
				$notif->setInfo($err,'danger');
			}
			elseif ($lname==""){
				$err="<b>Name</b> is required.";
				$notif->setInfo($err,'danger');
			}
			elseif (strlen($lname)<5){
				$err="<b>Name</b> is too short.";
				$notif->setInfo($err,'danger');
			}
			elseif ($lyear==""){
				$err="<b>Year</b> is required.";
				$notif->setInfo($err,'danger');
			}
			elseif (strlen($lyear)<4){
				$err="<b>Year</b> is too short. Use the format YYYY.";
				$notif->setInfo($err,'danger');
			}
			elseif (!preg_match('/[0-9]/',$lyear) || preg_match('/[a-zA-Z]/',$lyear)){
				$err="<b>Year</b> is invalid.";
				$notif->setInfo($err,'danger');
			}
			elseif(!isset($_GET['staff'])){
				if ($lform==""){
					$err="Student <b>form</b> is required.";
					$notif->setInfo($err,'danger');
				}
				elseif (!preg_match('/[0-9]/',$lform) || preg_match('/[a-zA-Z]/',$lform)){
					$err="Student <b>form</b> is invalid.";
					$notif->setInfo($err,'danger');
				}
				elseif ($lstream==""){
					$err="Student <b>stream</b> is required.";
					$notif->setInfo($err,'danger');
				}
			}
			
			if (isset($_GET["staff"]))
				$ltype='staff';
				
			if ($err==""){
				if(!isset($_GET['staff']))
					$sql="INSERT INTO libcusts(LAYear,LName,LNumb,LBan,LType,LForm,LStream) VALUES('$lyear','".ucwords(strtolower(mysqli_real_escape_string($mysqli,$lname)))."','$lno','0','$ltype',$lform,'".ucwords(strtolower(mysqli_real_escape_string($mysqli,$lstream)))."');";
				else
					$sql="INSERT INTO libcusts(LAYear,LName,LNumb,LBan,LType) VALUES('$lyear','".ucwords(strtolower(mysqli_real_escape_string($mysqli,$lname)))."','$lno','0','$ltype');";
				
				$result=mysqli_query($mysqli,$sql);
				if ($result){
					ulog($_SESSION["CURR_USER_ID"],"Successfully added 1 $ltype record to the system...");	//log this activity
					$notif->setInfo("The $ltype '".ucwords(strtolower($lname))."' was successfully saved.","success");
					if (isset($_GET["staff"]))
						header('location: ./lib-students-new?staff');
					else
						header('location: ./lib-students-new');
					exit();
				}
				else{
					$notif->setInfo("A critical error occured while saving the $ltype. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
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
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-plus"></span> New Students & Staff</h1>
			<ul class="nav nav-tabs">
				<li class="nav-item <?php if (!isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-new" class="nav-link"><strong>STUDENTS</strong></a></li>
				<li class="nav-item <?php if (isset($_GET["staff"])){ echo "active"; } ?>"><a href="lib-students-new?staff" class="nav-link"><strong>STAFF</strong></a></li>
			</ul>
			
			<!--*** IMPORT MODAL ***-->
			<div id="import<?php if (isset($_GET["staff"])){ echo "Staff"; } else{ echo "Students"; } ?>" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<!--modal content-->
					<div class="modal-content">
						<form action="lib-students-import<?php if (isset($_GET["staff"])){ echo "?staff"; } ?>" method="POST" enctype="multipart/form-data">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title text-success">Import <?php if (!isset($_GET["staff"])){ echo "students"; } else{ echo "staff"; } ?> From Excel Workbook</h4>
							</div>
							<div class="modal-body">
								<p class="text-danger"><strong><i class="glyphicon glyphicon-warning-sign"></i> Instructions</strong></p>
								<p class="text-danger">
									<em>Read and understand the following instructions before importing any file.</em><br/>
									1. Open MS Excel and create a new workbook<br/>
									<?php
									if (!isset($_GET["staff"])){
										?>2. Place these column headers in the first row: <strong>Admission No.</strong>, <strong>Student Name</strong>, <strong>Year of Admission</strong><br/><?php
									}
									else{
										?>2. Place these column headers in the first row: <strong>Staff No.</strong>, <strong>Staff Name</strong>, <strong>Year of Engagement</strong><br/><?php
									}
									?>
									3. Fill your records starting from <strong>Row 2</strong> in the same <strong>Sheet</strong>.<br/>
									4. DO NOT rearrange the columns.<br/>
									5. Save your workbook as Excel 97 - 2003 workbook (*.xls)<br/>
									6. Your data will now be ready for <strong>import</strong>.
								</p>
								<p class="text-danger">Contact <strong><?php echo D_NAME; ?></strong> for support if you encounter any issues.</p>
								<p>&nbsp;</p>
								<div class="form-group">
									<label for="file">File to import (*.xls <em>MS Excel 97 - 2003 workbook</em>)</label>
									<input name="file" id="file" class="form-control" type="file" />
								</div>
							</div>
							<div class="modal-footer">
								<button class="btn btn-success" ><i class="glyphicon glyphicon-import"></i> Import Records</button> &nbsp; <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!--********************-->
			
			<p>&nbsp;</p>
			<?php
			if (isset($_GET["staff"])){
				?><p class="text-info"><a class="btn btn-default btn-sm" href="lib-students?staff" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Staff</a> &nbsp; <a class="btn btn-danger btn-sm" href="lib-students-update?staff" role="button"><i class="glyphicon glyphicon-pencil"></i> Update Staff</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#importStaff"><i class="glyphicon glyphicon-import"></i> Import From Excel Workbook</a></p><?php
			}
			else{
				?><p class="text-info"><a class="btn btn-default btn-sm" href="lib-students" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Students</a> &nbsp; <a class="btn btn-danger btn-sm" href="lib-students-update" role="button"><i class="glyphicon glyphicon-pencil"></i> Update Students</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#importStudents"><i class="glyphicon glyphicon-import"></i> Import From Excel Workbook</a></p><?php
			}
			?>
			
			<p>&nbsp;</p>
			<p class="text-info">Fill in the details to add a new <?php if (isset($_GET["staff"])){ echo "staff"; } else{ echo "student"; } ?> record into the system.</p>
			<form role="form" method="POST" action="lib-students-new<?php if (isset($_GET["staff"])){ echo "?staff"; } ?>">
				<div class="form-group">
					<label for="lno"><?php if (isset($_GET["staff"])){ echo "Staff Number"; } else{ echo "Student Admission Number"; } ?> <span class="text-danger">*</span></label>
					<input type="text" name="lno" id="lno" class="form-control" value="<?php echo $lno; ?>" placeholder="<?php if (isset($_GET["staff"])){ echo "Staff ID number, or employment PIN number"; } else{ echo "Student's Admission mumber"; } ?>" />
				</div>
				<div class="form-group">
					<label for="lname"><?php if (isset($_GET["staff"])){ echo "Staff Name"; } else{ echo "Student Name"; } ?> <span class="text-danger">*</span></label>
					<input type="text" name="lname" id="lname" class="form-control" value="<?php echo $lname; ?>" placeholder="<?php if (isset($_GET["staff"])){ echo "Name of staff"; } else{ echo "Name of student"; } ?>" />
				</div>
				<?php
				if (!isset($_GET["staff"])){
					?>
					<div class="form-group">
						<label for="lform">Student Class (Form) <span class="text-danger">*</span></label>
						<input type="text" name="lform" id="lform" class="form-control" value="<?php echo $lform; ?>" placeholder="e.g. 1, 2, 3 or 4" />
					</div>
					<div class="form-group">
						<label for="lstream">Student Stream <span class="text-danger">*</span></label>
						<input type="text" name="lstream" id="lstream" class="form-control" value="<?php echo $lstream; ?>" placeholder="e.g Red, White, Green etc." />
					</div>
					<?php
				}
				?>
				<div class="form-group">
					<label for="lyear"><?php if (isset($_GET["staff"])){ echo "Employment Year"; } else{ echo "Admission Year"; } ?> (YYYY) <span class="text-danger">*</span></label>
					<input type="text" name="lyear" id="lyear" class="form-control" value="<?php echo $lyear; ?>" placeholder="<?php if (isset($_GET["staff"])){ echo "Year of employment"; } else{ echo "Year of admission"; } ?>" />
				</div>
				<button type="submit" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-floppy-disk"></i> Save Record</button> &nbsp; <button type="reset" class="btn btn-danger btn-lg"><i class="glyphicon glyphicon-remove"></i> Clear Details</button> &nbsp; <a class="btn btn-default btn-lg" href="lib-students<?php if (isset($_GET["staff"])){ echo "?staff"; } ?>" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All <?php if (isset($_GET["staff"])){ echo "Staff"; } else{ echo "Students"; } ?></a>
			</form>
			<p>&nbsp;</p>
			<hr>
			<?php $ui->printFooter(); ?>
		</div>
		<?php
	}
	
	$ui->printBottom();
?>