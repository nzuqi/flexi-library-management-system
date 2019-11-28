<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=2.3;
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
		
		if (!isset($_GET["id"])){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.",'danger');
			header('location: ./lib-books-update');
			exit();
		}
		
		$quer=mysqli_query($mysqli,"SELECT * FROM books WHERE BID=".trim($_GET["id"]).";");
		$res=mysqli_num_rows($quer);
		if ($res==0){
			$notif->setInfo("The book record does not exist in the system.",'danger');
			header('location: ./lib-books-update');
			exit();
		}
		
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
			elseif ($validate->accnoExistsWithOtherBook($baccno,$_GET["id"])){
				$err="<b>Access Number</b> already exists in the system with another book.";
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
				$sql="UPDATE books SET bTitle='".ucwords(strtolower(mysqli_real_escape_string($mysqli,$btitle)))."',bAuthor='".ucwords(strtolower(mysqli_real_escape_string($mysqli,$bauthor)))."',bPublisher='".ucwords(strtolower(mysqli_real_escape_string($mysqli,$bpublisher)))."',bBlurb='".mysqli_real_escape_string($mysqli,$bdescription)."',bEdition='".mysqli_real_escape_string($mysqli,$bedition)."',bAccNo='".mysqli_real_escape_string($mysqli,$baccno)."',bPoPub='".ucwords(strtolower(mysqli_real_escape_string($mysqli,$bpopub)))."',bCartegory='".ucwords(strtolower(mysqli_real_escape_string($mysqli,$bcartegory)))."',bYoPub='".$byopub."',bReserve=".$breserve." WHERE BID=".$_GET['id']." LIMIT 1;";
				$result=mysqli_query($mysqli,$sql);
				if ($result){
					ulog($_SESSION["CURR_USER_ID"],"Successfully updated 1 book record in the system...");	//log this activity
					$notif->setInfo("The book '".ucwords(strtolower($btitle))."' by ".ucwords(strtolower($bauthor))." was successfully updated.","success");
					header('location: ./lib-books-update');
					exit();
				}
				else{
					$notif->setInfo("A critical error occured while updating the record. Please try again, if it insists, consider reporting this to ".D_NAME.".",'danger');
				}
			}
		}
		else{
			$sql2="SELECT * FROM books WHERE BID=".trim($_GET["id"])." LIMIT 1;";
			$result2=mysqli_query($mysqli,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					$baccno=$row2['bAccNo'];$btitle=$row2['bTitle'];$bauthor=$row2['bAuthor'];
					$bdescription=$row2['bBlurb'];$bedition=$row2['bEdition'];$bcartegory=$row2['bCartegory'];$bsubject=$row2['bSubject'];
					$bpublisher=$row2['bPublisher'];$bpopub=$row2['bPoPub'];$byopub=$row2['bYoPub'];$breserve=$row2['bReserve'];
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
			
			<!--*** DELETE MODAL ***-->
			<div id="deleteBook" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<!--modal content-->
					<div class="modal-content">
						<form action="lib-books-update-book-delete" method="POST">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title text-success">Delete Book</h4>
							</div>
							<div class="modal-body">
								<p>&nbsp;</p>
								<input name="id" id="id" value="<?php echo $_GET["id"]; ?>" type="hidden" />
								<p class="text-danger">Are you sure that you want to delete this book record?</p>
								<p class="text-info">This process is irreversable. Note that you will lose all books records and any other records attached to this book.</p>
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
			
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-pencil"></span> <?php echo $btitle." <em>by</em> ".$bauthor; ?></h1>
			<p>&nbsp;</p>
			<p class="text-info"><a class="btn btn-default btn-sm" href="lib-books" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Books</a> &nbsp; <a class="btn btn-danger btn-sm" href="#" role="button" data-toggle="modal" data-target="#deleteBook"><i class="glyphicon glyphicon-trash"></i> Delete Book</a></p>
			<p>&nbsp;</p>
			<p class="text-info">You can change the details to update the selected record.</p>
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
				<button type="submit" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-floppy-disk"></i> Update Record</button> &nbsp; <a class="btn btn-danger btn-lg" href="lib-books-update-book?id=<?php echo trim($_GET["id"]); ?>" role="button"><i class="glyphicon glyphicon-repeat"></i> Undo Changes</a> &nbsp; <a class="btn btn-default btn-lg" href="lib-books" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Books</a>
			</form>
			<p>&nbsp;</p>
			<hr>
			<?php $ui->printFooter(); ?>
		</div>
		<?php
	}
	
	$ui->printBottom();
?>
