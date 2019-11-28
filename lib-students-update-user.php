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
		
		if (!isset($_GET["id"]) && !isset($_GET["type"])){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.",'danger');
			header('location: ./lib-students-update');
			exit();
		}
		
		$quer=mysqli_query($mysqli, "SELECT * FROM libcusts WHERE LID=".trim($_GET["id"]).";");
		$res=mysqli_num_rows($quer);
		if ($res==0){
			$notif->setInfo("The ".$_GET["type"]." record does not exist in the system.",'danger');
			header('location: ./lib-students-update');
			exit();
		}
		
		//book variables
		$ltype=$lno=$lname=$lform=$lstream=$lyear=$err="";
		
		if ($_SERVER["REQUEST_METHOD"]=="POST"){
			
			$lno=$validate->test_input($_POST["lno"]);
			$lname=$validate->test_input($_POST["lname"]);
			$lyear=$validate->test_input($_POST["lyear"]);
			
			if($_GET["type"]=='student'){
				$lform=$validate->test_input($_POST["lform"]);
				$lstream=$validate->test_input($_POST["lstream"]);
			}
			
			$ltype=$_GET["type"];
			
			//run some tests
			if ($lno==""){
				$err="<b>Number</b> is required.";
				$notif->setInfo($err,'danger');
			}
			elseif ($validate->lnoExistsWithOtherUser($lno,$_GET['id'])){
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
			elseif($_GET["type"]=='student'){
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
				
			if ($err==""){
				if($_GET["type"]=='student')
					$sql="UPDATE libcusts SET LAYear='$lyear',LName='".ucwords(strtolower(mysqli_real_escape_string($mysqli, $lname)))."',LNumb='$lno',LType='$ltype',LForm=$lform,LStream='".ucwords(strtolower(mysqli_real_escape_string($mysqli, $lstream)))."' WHERE LID=".trim($_GET["id"]).";";
				else
					$sql="UPDATE libcusts SET LAYear='$lyear',LName='".ucwords(strtolower(mysqli_real_escape_string($mysqli, $lname)))."',LNumb='$lno',LType='$ltype' WHERE LID=".trim($_GET["id"]).";";
				$result=mysqli_query($mysqli, $sql);
				if ($result){
					ulog($_SESSION["CURR_USER_ID"],"Successfully updated 1 $ltype record in the system...");	//log this activity
					$notif->setInfo("The $ltype '".ucwords(strtolower($lname))."' record was successfully updated.","success");
					if (isset($_GET["staff"]))
						header('location: ./lib-students-update?staff');
					else
						header('location: ./lib-students-update');
					exit();
				}
				else{
					$notif->setInfo("A critical error occured while updating the $ltype. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
				}
			}
			
		}
		else{
			$sql2="SELECT * FROM libcusts WHERE LID=".trim($_GET["id"])." LIMIT 1;";
			$result2=mysqli_query($mysqli, $sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					$ltype=$row2['LType'];$lno=$row2['LNumb'];$lname=$row2['LName'];$lyear=$row2['LAYear'];$lform=$row2['LForm'];$lstream=$row2['LStream'];
				}
			}
		}
		
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			
			if ($_GET["type"]=="student"){
				?><h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-pencil"></span> Update Student</h1><?php
			}
			else{
				?><h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-pencil"></span> Update Staff</h1><?php
			}
			?>
			
			<!--*** DELETE MODAL ***-->
			<div id="deleteModal" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<!--modal content-->
					<div class="modal-content">
						<form action="lib-students-update-user-delete" method="POST">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title text-success">Delete <?php echo ucwords($_GET["type"]); ?></h4>
							</div>
							<div class="modal-body">
								<p>&nbsp;</p>
								<input name="id" id="id" value="<?php echo $_GET["id"]; ?>" type="hidden" />
								<input name="type" id="type" value="<?php echo $_GET["type"]; ?>" type="hidden" />
								<p class="text-danger">Are you sure that you want to delete this <?php echo $_GET["type"]; ?> record?</p>
								<p class="text-info">This process is irreversable. Note that you will lose any records attached to this <?php echo $_GET["type"]; ?>.</p>
								<p>&nbsp;</p>
							</div>
							<div class="modal-footer">
								<button class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</button> &nbsp; <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!--********************-->
			
			<p>&nbsp;</p>
			<?php
			if (isset($_GET["staff"])){
				?><p class="text-info"><a class="btn btn-default btn-sm" href="lib-students?staff" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Staff</a> &nbsp; <a class="btn btn-danger btn-sm" href="#" role="button" data-toggle="modal" data-target="#deleteModal"><i class="glyphicon glyphicon-trash"></i> Delete Staff</a></p><?php
			}
			else{
				?><p class="text-info"><a class="btn btn-default btn-sm" href="lib-students" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Students</a> &nbsp; <a class="btn btn-danger btn-sm" href="#" role="button" data-toggle="modal" data-target="#deleteModal"><i class="glyphicon glyphicon-trash"></i> Delete Student</a></p><?php
			}
			?>
			<p>&nbsp;</p>
			<p class="text-info">You can change the details to update the selected record.</p>
			<form role="form" method="POST" action="lib-students-update-user?id=<?php echo $_GET["id"]; ?>&type=<?php echo $_GET["type"]; ?>">
				<div class="form-group">
					<label for="lno"><?php if ($_GET["type"]=="staff"){ echo "Staff Number"; } else{ echo "Student Admission Number"; } ?> <span class="text-danger">*</span></label>
					<input type="text" name="lno" id="lno" class="form-control" value="<?php echo $lno; ?>" placeholder="<?php if ($_GET["type"]=="staff"){ echo "Staff ID number, or employment PIN number"; } else{ echo "Student's Admission mumber"; } ?>" />
				</div>
				<div class="form-group">
					<label for="lname"><?php if ($_GET["type"]=="staff"){ echo "Staff Name"; } else{ echo "Student Name"; } ?> <span class="text-danger">*</span></label>
					<input type="text" name="lname" id="lname" class="form-control" value="<?php echo $lname; ?>" placeholder="<?php if ($_GET["type"]=="staff"){ echo "Name of staff"; } else{ echo "Name of student"; } ?>" />
				</div>
				<?php
				if ($_GET["type"]=='student'){
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
					<label for="lyear"><?php if ($_GET["type"]=="staff"){ echo "Employment Year"; } else{ echo "Admission Year"; } ?> (YYYY) <span class="text-danger">*</span></label>
					<input type="text" name="lyear" id="lyear" class="form-control" value="<?php echo $lyear; ?>" placeholder="Year the book was published" />
				</div>
				<button type="submit" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-floppy-disk"></i> Update Record</button> &nbsp; <a class="btn btn-danger btn-lg" href="lib-students-update-user?id=<?php echo trim($_GET["id"]); ?>&type=<?php echo trim($_GET["type"]); ?>" role="button"><i class="glyphicon glyphicon-repeat"></i> Undo Changes</a> &nbsp; <a class="btn btn-default btn-lg" href="lib-students-update<?php if ($_GET["type"]=="staff"){ echo "?staff"; } ?>" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All <?php if ($_GET["type"]=="staff"){ echo "Staff"; } else{ echo "Students"; } ?></a>
			</form>
			<p>&nbsp;</p>
			<hr>
			<?php $ui->printFooter(); ?>
		</div>
		<?php
	}
	
	$ui->printBottom();
?>