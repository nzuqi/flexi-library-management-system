<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	$file=new file;
	
	//$ui->active_menu=9.4;
	$ui->custom_page=true;
	//====================
	$ui->printTop();
	$ui->printNavbar();
	
	//check authorization to edit admin details
	function isUserAdmin(){
		if (isset($_SESSION["CURR_USER_AUTH"])){
			if($_SESSION["CURR_USER_AUTH"]=="admin")
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	$ierr="";
	$iname=$_SESSION["host_name"];$ipost=$_SESSION["host_box"];$icode=$_SESSION["host_postalcode"];$itown=$_SESSION["host_ctown"];$akey=$_SESSION["act_key"];$max_books=$_SESSION["LIB_MAX_BOOKS"];$ocharge=$_SESSION["LIB_OVERDUE_CHARGES"];
	
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		if (isset($_GET['activate'])){
			$akey=$_POST["akey"];
			$akey=sha1($validate->test_input($akey));
			if (empty($akey))
				$ierr="<b>Activation Key</b> required.";
			if (!$validate->keyExists($akey))
				$ierr="Incorrect <b>Activation Key</b>. Confirm the key and try again.";
			if ($ierr==""){
				//overwrite INI
				$file->overwriteINI($akey,$iname,$ipost,$icode,$itown,$max_books,$ocharge);
				$notif->setInfo(WA_TITLE." is now activated. Thank you for using this product.","success");
				//reload
				header("Location: ./lib-home");
				exit();
			}
			else
				$notif->setInfo($ierr,"danger");
		}
		elseif (isset($_GET['license'])){
			$iname=$validate->test_input($_POST["iname"]);
			$ipost=$validate->test_input($_POST["ipost"]);
			$icode=$validate->test_input($_POST["icode"]);
			$itown=$validate->test_input($_POST["itown"]);
			
			if (empty($iname))
				$ierr="<b>Institution Name</b> is required.";
			elseif (strlen($iname)<3)
				$ierr="<b>Institution Name</b> is too short.";
			elseif (empty($ipost))
				$ierr="<b>Postal Address</b> required.";
			//elseif (preg_match("/^[a-zA-Z]*$/",$ipost))
			//	$ierr="Only <em>numbers</em> are allowed in Postal Address.";
			elseif (strlen($ipost)<5)
				$ierr="<b>Postal Address</b> is too short.";
			elseif (empty($icode))
				$ierr="<b>Postal Code</b> required.";
			//elseif (preg_match("/^[a-zA-Z]*$/",$icode))
			//	$ierr="Only <em>numbers</em> are allowed in Postal Code.";
			elseif (strlen($icode)<2)
				$ierr="<b>Postal Code</b> is too short.";
			elseif (empty($itown))
				$ierr="<b>Institution Town</b> is required.";
			elseif (strlen($itown)<3)
				$ierr="<b>Institution Town</b> is too short.";
			
			if ($ierr==""){
				//overwrite INI
				$file->overwriteINI($akey,$iname,$ipost,$icode,$itown,$max_books,$ocharge);
				$notif->setInfo("Preferences successfully saved.","success");
				//reload
				header("Location: ./lib-home");
				exit();
			}
			else
				$notif->setInfo($ierr,"danger");
		}
		elseif (isset($_GET['library'])){
			$max_books=$validate->test_input($_POST["mbooks"]);
			$ocharge=$validate->test_input($_POST["ocharge"]);
			
			if (empty($max_books))
				$ierr="Maximum number of books to be issued to users is required.";
			elseif (!preg_match('/[0-9]/',$max_books) || preg_match('/[a-zA-Z]/',$max_books))
				$ierr="Maximum number of books to be issued to users is invalid.";
			elseif (empty($ocharge))
				$ierr="Charge rate to overdue books is required.";
			elseif (!preg_match('/[0-9]/',$ocharge) || preg_match('/[a-zA-Z]/',$ocharge))
				$ierr="Charge rate to overdue books is invalid.";
			
			if ($ierr==""){
				//overwrite INI
				$file->overwriteINI($akey,$iname,$ipost,$icode,$itown,$max_books,$ocharge);
				$notif->setInfo("Preferences successfully saved.","success");
				//reload
				header("Location: preferences");
				exit();
			}
			else
				$notif->setInfo($ierr,"danger");
		}
	}
	?>
	<div class="container">
		<!--alerts-->
		<?php
		echo $notif->alertInfo();
		?>
		<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-cog"></span> Preferences</h1>
		
		<!--===ACTIVATION===-->
		<h3 class="text-warning" style="margin-top:0;"><span class="glyphicon glyphicon-bullhorn"></span> Activation</h3>
		<?php
		if (!$validate->isSysActivated()){
			?>
			<p class="text-danger"><i class="glyphicon glyphicon-remove"></i> <?php echo WA_TITLE; ?> is not <strong>activated</strong>.</p>
			<form role="form" action="preferences?activate" method="POST" autocomplete="off">
				<div class="form-group">
					<label for="akey">Activation Key <span class="text-danger">*</span></label>
					<input type="text" name="akey" id="akey" class="form-control" value="" placeholder="Activation Key" />
				</div>
				<button type="submit" class="btn btn-danger btn-lg"><i class="glyphicon glyphicon-ok"></i> Activate System</button>
			</form>
			<?php
		}
		else{
			?>
			<p class="text-info"><i class="glyphicon glyphicon-ok"></i> <?php echo WA_TITLE; ?> is <strong>activated</strong>.</p>
			<?php
		}
		?>
		<hr/>
		
		<!--===LIBRARY===-->
		<h3 class="text-warning" style="margin-top:0;"><span class="glyphicon glyphicon-book"></span> Library Preferences</h3>
		<?php
		if ($user->login_check($mysqli)){
			if($_SESSION["CURR_SUB_SYSTEM"]=="library"){
				?>
				<p class="text-danger"><i class="glyphicon glyphicon-wrench"></i> Update <?php echo WA_TITLE; ?>'s library settings.</p>
				<form role="form" action="preferences?library" method="POST">
					<div class="form-group">
						<label for="mbooks">Maximum Books Issued <span class="text-danger">*</span></label>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> This is the maximum number of books to be issued to a single user. If the target is hit, the user has to return the books in possession first before any issuing.</p>
						<input type="text" name="mbooks" id="mbooks" class="form-control" value="<?php echo $max_books; ?>" placeholder="Maximum Books Issued e.g. 5" />
					</div>
					<div class="form-group">
						<label for="ocharge">Overdue Books Charges Rate (Ksh) <span class="text-danger">*</span></label>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> This is the amount charged on overdue books each day.</p>
						<input type="text" name="ocharge" id="ocharge" class="form-control" value="<?php echo $ocharge; ?>" placeholder="Overdue Charges e.g. 5" />
					</div>
					<button type="submit" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-floppy-disk"></i> Save Preferences</button>
				</form>
				<?php
			}
			else{
				?>
				<p class="text-danger"><i class="glyphicon glyphicon-wrench"></i> Log in to the library sub-system to update <?php echo WA_TITLE; ?>'s library settings.</p>
				<?php
			}
		}
		else{
			?>
			<p class="text-danger"><i class="glyphicon glyphicon-wrench"></i> Log in to update <?php echo WA_TITLE; ?>'s library settings.</p>
			<?php
		}
		?>
		<hr/>
		
		<!--===LICENSING===-->
		<form role="form" action="preferences?license" method="POST" autocomplete="off">
			<h3 class="text-warning" style="margin-top:0;"><span class="glyphicon glyphicon-info-sign"></span> License Information</h3>
			<p class="text-info"><?php echo WA_TITLE; ?> is licensed to:</p>
			<?php
			if(!isUserAdmin()){
				?><p class="text-info"><i>Log in as an <strong>administrator</strong> to activate <strong><?php echo WA_TITLE; ?></strong> now.</i></p><?php
			}
			?>
			<div class="form-group">
				<label for="iname">Institution Name <span class="text-danger">*</span></label>
				<input type="text" name="iname" id="iname" class="form-control" value="<?php echo $iname; ?>" placeholder="Name of Institution" <?php if(!isUserAdmin()) echo "disabled"; ?> />
			</div>
			<div class="form-group">
				<label for="ipost">Postal Address <span class="text-danger">*</span></label>
				<input type="text" name="ipost" id="idno" class="form-control" value="<?php echo $ipost; ?>" placeholder="Postal Address e.g. P.O. Box 12345" <?php if(!isUserAdmin()) echo "disabled"; ?> />
			</div>
			<div class="form-group">
				<label for="ipost">Postal Code <span class="text-danger">*</span></label>
				<input type="text" name="icode" id="icode" class="form-control" value="<?php echo $icode; ?>" placeholder="Postal Code" <?php if(!isUserAdmin()) echo "disabled"; ?> />
			</div>
			<div class="form-group">
				<label for="ipost">Town <span class="text-danger">*</span></label>
				<input type="text" name="itown" id="itown" class="form-control" value="<?php echo $itown; ?>" placeholder="Town" <?php if(!isUserAdmin()) echo "disabled"; ?> />
			</div>
			<button type="submit" class="btn btn-success btn-lg" <?php if(!isUserAdmin()) echo "disabled='disabled'"; ?>><i class="glyphicon glyphicon-floppy-disk"></i> Save Preferences</button>
		</form>
		<hr>
		<?php $ui->printFooter(); ?>
	</div>
	<?php
	$ui->printBottom();
?>