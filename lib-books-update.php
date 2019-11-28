<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=2.2;
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
				$sql2="DELETE FROM books;";
				$result2=mysqli_query($mysqli,$sql2);
				if ($result2){
					ulog($_SESSION["CURR_USER_ID"],"Successfully deleted all books records and any details attached to them in the system...");	//log this activity
					$notif->setInfo("The books records were successfully deleted.","success");
					header('location: ./lib-books');
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
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-pencil"></span> Update Books</h1>
			<?php
			//check number of book entities available
			$quer="SELECT COUNT(BID) FROM books;";
			$res=mysqli_query($mysqli,$quer);
			if ($res){					//if the query is successful
				while ($row=mysqli_fetch_array($res)){
					if ($row[0]==0){	//no books found
						?>
						<p>&nbsp;</p>
						<p class="text-info"><a class="btn btn-success btn-sm" href="lib-books-new" role="button"><i class="glyphicon glyphicon-plus"></i> New Books</a></p>
						<p class="text-info">Books data unavailable, you can add books records by clicking the button above.</p>
						<p class="text-info">A list of all the available books will be available here once the database has been updated, then you'll be able to update the records.</p>
						<?php
					}
					else{				//found
						$squer="SELECT * FROM books;";
						$resl=mysqli_query($mysqli,$squer);
						?>
						
						<!--*** DELETE MODAL ***-->
						<div id="deleteBooks" class="modal fade" role="dialog">
							<div class="modal-dialog">
								<!--modal content-->
								<div class="modal-content">
									<form action="lib-books-update" method="POST" enctype="multipart/form-data">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title text-success">Delete Books</h4>
										</div>
										<div class="modal-body">
											<p>&nbsp;</p>
											<input name="delete" id="delete" value="all" type="hidden" />
											<p class="text-danger">Are you sure that you want to delete all the books records?</p>
											<p class="text-info">This process is irreversable. Note that you will lose all books records and any other records attached to them.</p>
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
						
						<p>&nbsp;</p>
						<p class="text-info"><a class="btn btn-default btn-sm" href="lib-books" role="button"><i class="glyphicon glyphicon-chevron-left"></i> View All Books</a> &nbsp; <a class="btn btn-success btn-sm" href="lib-books-new" role="button"><i class="glyphicon glyphicon-plus"></i> New Books</a> &nbsp; <a class="btn btn-danger btn-sm" href="#" role="button" data-toggle="modal" data-target="#deleteBooks"><i class="glyphicon glyphicon-trash"></i> Delete All Books</a></p>
						<p>&nbsp;</p>
						<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the available books in the system.</p>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> Click on a record to update, click on the columns to filter records, or search live for a specific book record by using the search widget.</p>
						<p>&nbsp;</p>
						<table class="table table-bordered table-striped table-responsive table-hover" id="render" cellspacing="0">
							<thead>
								<tr>
									<th>Access No.</th>
									<th>Title</th>
									<th>Author</th>
									<th>Cartegory</th>
									<th>Subject</th>
									<th>Reservation</th>
								</tr>
							</thead>
							<tbody>
							<?php
							while ($r=mysqli_fetch_array($resl)){
								?>
								<tr style="cursor:pointer;" onClick="JavaScript:window.location='lib-books-update-book?id=<?php echo $r["BID"]; ?>';">
									<td><?php echo $r["bAccNo"]; ?></td>
									<td><?php echo $r["bTitle"]; ?></td>
									<td><?php echo $r["bAuthor"]; ?></td>
									<td><?php echo $r["bCartegory"]; ?></td>
									<td><?php echo $r["bSubject"]; ?></td>
									<td><?php if($r["bReserve"]==0){ echo "Not Reserved"; } else { echo "Reserved"; } ?></td>
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