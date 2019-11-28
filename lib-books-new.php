<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=2.1;
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
		$baccno=$btitle=$bauthor=$bdescription=$bedition=$bcartegory=$bsubject=$bpublisher=$bpopub=$byopub=$breserve=$err="";
		
		if ($_SERVER["REQUEST_METHOD"]=="POST"){
			
			$baccno=$validate->test_input($_POST["baccno"]);
			$btitle=$validate->test_input($_POST["btitle"]);
			$bauthor=$validate->test_input($_POST["bauthor"]);
			$bdescription=$validate->test_input($_POST["bdescription"]);
			$bedition=$validate->test_input($_POST["bedition"]);
			$bcartegory=$validate->test_input($_POST["bcartegory"]);
			$bsubject=$validate->test_input($_POST["bsubject"]);
			$bpublisher=$validate->test_input($_POST["bpublisher"]);
			$bpopub=$validate->test_input($_POST["bpopub"]);
			$byopub=$validate->test_input($_POST["byopub"]);
			$breserve=$validate->test_input($_POST["breserve"]);
			
			//run some tests
			if ($baccno==""){
				$err="Book <b>Access Number</b> is required.";
				$notif->setInfo($err,'danger');
			}
			elseif ($validate->baccnoExists($baccno)){
				$err="<b>Access Number</b> already exists in the system.";
				$notif->setInfo($err,'danger');
			}
			elseif ($btitle==""){
				$err="Book <b>Title</b> is required.";
				$notif->setInfo($err,'danger');
			}
			elseif ($bauthor==""){
				$err="Book <b>Author</b> is required.";
				$notif->setInfo($err,'danger');
			}
			elseif (strlen($bauthor)<5){
				$err="Book <b>Author's</b> name is too short.";
				$notif->setInfo($err,'danger');
			}
			elseif ($bcartegory==""){
				$err="Book <b>Cartegory</b> is required.";
				$notif->setInfo($err,'danger');
			}
			elseif (strlen($bcartegory)<3){
				$err="Book <b>Cartegory</b> name is too short.";
				$notif->setInfo($err,'danger');
			}
			elseif (strlen($byopub)>0){
				if (strlen($byopub)<>4){
					$err="<b>Year of publication</b> should be in YYYY formart.";
					$notif->setInfo($err,'danger');
				}
				elseif (!preg_match('/[0-9]/',$byopub) || preg_match('/[a-zA-Z]/',$byopub)){
					$err="<b>Year of publication</b> is invalid.";
					$notif->setInfo($err,'danger');
				}
			}
			
			if ($err==""){
				dbconnect();
				$sql="INSERT INTO books(bTitle,bAuthor,bPublisher,bBlurb,bEdition,bAccNo,bPoPub,bCartegory,bYoPub,bReserve)
						VALUES('".ucwords(strtolower(mysqli_real_escape_string($mysqli,$btitle)))."','".ucwords(strtolower(mysqli_real_escape_string($mysqli,$bauthor)))."','".ucwords(strtolower(mysqli_real_escape_string($mysqli,$bpublisher)))."','".mysqli_real_escape_string($mysqli,$bdescription)."','".mysqli_real_escape_string($mysqli,$bedition)."','".mysqli_real_escape_string($mysqli,$baccno)."','".ucwords(strtolower(mysql_real_escape_string($bpopub)))."','".ucwords(strtolower(mysqli_real_escape_string($mysqli,$bcartegory)))."','".$byopub."',".$breserve.");";
				$result=mysqli_query($mysqli,$sql);
				if ($result){
					ulog($_SESSION["CURR_USER_ID"],"Successfully added 1 book record to the system...");	//log this activity
					$notif->setInfo("The book '".ucwords(strtolower($btitle))."' by ".ucwords(strtolower($bauthor))." was successfully saved.","success");
					header('location: ./lib-books-new');
					exit();
				}
				else{
					$notif->setInfo("A critical error occured while saving the record. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
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
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-plus"></span> New Books</h1>
			
			<!--*** IMPORT MODAL ***-->
			<div id="importBooks" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<!--modal content-->
					<div class="modal-content">
						<form action="lib-books-import" method="POST" enctype="multipart/form-data">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title text-success">Import Books From Excel Workbook</h4>
							</div>
							<div class="modal-body">
								<p class="text-danger"><strong><i class="glyphicon glyphicon-warning-sign"></i> Instructions</strong></p>
								<p class="text-danger">
									<em>Read and understand the following instructions before importing any file.</em><br/>
									1. Open MS Excel and create a new workbook<br/>
									2. Place these column headers in the first row: <strong>Access No.</strong>, <strong>Author</strong>, <strong>Description</strong>, <strong>Edition</strong>, <strong>Cartegory</strong>, <strong>Subject</strong>, <strong>Publisher</strong>, <strong>Place of Publication</strong>, <strong>Year of Publication</strong> and <strong>Reserved</strong>.<br/>
									3. Fill your records starting from <strong>Row 2</strong> in the same <strong>Sheet</strong>.<br/>
									4. DO NOT rearrange the columns.<br/>
									5. Save your workbook as Excel 97 - 2003 workbook (*.xls)<br/>
									6. Your data will now be ready for <strong>import</strong>.
								</p>
								<a href="download?file=books-import-sample.xls" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-download-alt"></i> Download Sample</a>
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
			<p class="text-info"><a class="btn btn-default btn-sm" href="lib-books" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Books</a> &nbsp; <a class="btn btn-danger btn-sm" href="lib-books-update" role="button"><i class="glyphicon glyphicon-pencil"></i> Update Books</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#importBooks"><i class="glyphicon glyphicon-import"></i> Import From Excel Workbook</a></p>
			<p>&nbsp;</p>
			<p class="text-info">Fill in the details to add a new book record into the system.</p>
			<form role="form" method="POST">
				<div class="form-group">
					<label for="baccno">Book Access Number <span class="text-danger">*</span></label>
					<input type="text" name="baccno" id="baccno" class="form-control" value="<?php echo $baccno; ?>" placeholder="Library access number or any unique number" />
				</div>
				<div class="form-group">
					<label for="btitle">Book Title <span class="text-danger">*</span></label>
					<input type="text" name="btitle" id="btitle" class="form-control" value="<?php echo $btitle; ?>" placeholder="Title of the Book" />
				</div>
				<div class="form-group">
					<label for="bauthor">Book Author <span class="text-danger">*</span></label>
					<input type="text" name="bauthor" id="bauthor" class="form-control" value="<?php echo $bauthor; ?>" placeholder="Author of the Book" />
				</div>
				<div class="form-group">
					<label for="bauthor">Book Description</label>
					<textarea name="bdescription" class="form-control" rows="6" placeholder="Book description, you can type the blurb here" id="bdescription" ><?php echo $bdescription; ?></textarea>
				</div>
				<div class="form-group">
					<label for="bedition">Book Edition</label>
					<input type="text" name="bedition" id="bedition" class="form-control" value="<?php echo $bedition; ?>" placeholder="Book's edition" />
				</div>
				<div class="form-group">
					<label for="bcartegory">Book Cartegory <span class="text-danger">*</span></label>
					<input type="text" name="bcartegory" id="bcartegory" class="form-control" value="<?php echo $bcartegory; ?>" placeholder="Book's cartegory e.g. Physics, Mathematics etc." />
				</div>
				<div class="form-group">
					<label for="bsubject">Book Subject</label>
					<input type="text" name="bsubject" id="bsubject" class="form-control" value="<?php echo $bsubject; ?>" placeholder="Subject that this book belongs to e.g. Mathematics, Music, Chemistry..." />
				</div>
				<div class="form-group">
					<label for="bpublisher">Book Publisher</label>
					<input type="text" name="bpublisher" id="bpublisher" class="form-control" value="<?php echo $bpublisher; ?>" placeholder="Organization that published this book" />
				</div>
				<div class="form-group">
					<label for="bpopub">Place of Publication</label>
					<input type="text" name="bpopub" id="bpopub" class="form-control" value="<?php echo $bpopub; ?>" placeholder="Place where the book was published (Town, City or Country)" />
				</div>
				<div class="form-group">
					<label for="byopub">Year of Publication (YYYY)</label>
					<input type="text" name="byopub" id="byopub" class="form-control" value="<?php echo $byopub; ?>" placeholder="Year the book was published" />
				</div>
				<div class="form-group">
					<label for="breserve">Book Reservation <span class="text-danger">*</span></label>
					<select name="breserve" id="breserve" class="form-control">
						<option value="0" <?php if ($breserve==0){ echo "selected"; } ?> >No, anyone can borrow this book</option>
						<option value="1" <?php if ($breserve==1){ echo "selected"; } ?> >Yes, this book cannot leave the library</option>
					</select>
				</div>
				<button type="submit" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-floppy-disk"></i> Save Record</button> &nbsp; <button type="reset" class="btn btn-danger btn-lg"><i class="glyphicon glyphicon-remove"></i> Clear Details</button> &nbsp; <a class="btn btn-default btn-lg" href="lib-books" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Books</a>
			</form>
			<p>&nbsp;</p>
			<hr>
			<?php $ui->printFooter(); ?>
		</div>
		<?php
	}
	
	$ui->printBottom();
?>