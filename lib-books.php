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
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-book"></span> Books</h1>
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
						<p class="text-info">A list of all the available books will be available here once the database has been updated.</p>
						<?php
					}
					else{				//found
						$squer="SELECT * FROM books;";
						$resl=mysqli_query($mysqli,$squer);
						?>
						
						<!----*************modal**************---->
						<div id="exportModal" class="modal fade" role="dialog">
							<div class="modal-dialog">
								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Export Books Records</h4>
									</div>
									<form action="lib-books-export" method="POST">
										<div class="modal-body">
											<p>&nbsp;</p>
											<div class="radio"><label><input type="radio" name="dtype" id="all" value="all" checked /> Download All Books Records (MS Excel File)</label></div>
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
						<p class="text-info"><a class="btn btn-success btn-sm" href="lib-books-new" role="button"><i class="glyphicon glyphicon-plus"></i> New Books</a> &nbsp; <a class="btn btn-danger btn-sm" href="lib-books-update" role="button"><i class="glyphicon glyphicon-pencil"></i> Update Books</a> &nbsp; <a class="btn btn-warning btn-sm" href="#" role="button" data-toggle="modal" data-target="#exportModal"><i class="glyphicon glyphicon-export"></i> Export Records</a></p>
						<p>&nbsp;</p>
						<p class="text-success">Hello <strong><?php echo $_SESSION["CURR_USER_NAME"] ?></strong>, here's a list of all the available books in the system.</p>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> Click on a record for more information, click on the columns to filter records, or search live for a specific book record by using the search widget.</p>
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
								
								<!--books modal-->
								<div id="modal<?php echo $r["BID"]; ?>" class="modal fade" role="dialog">
									<div class="modal-dialog">
										<!--modal content-->
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title text-success"><?php echo $r["bAccNo"].", ".$r["bTitle"]." <em>by</em> ".$r["bAuthor"]; ?></h4>
											</div>
											<div class="modal-body">
												<p><strong>Title:</strong> <?php echo $r["bTitle"]; ?></p>
												<p><strong>Author:</strong> <?php echo $r["bAuthor"]; ?></p>
												<p><strong>Cartegory:</strong> <?php echo $r["bCartegory"]; ?></p>
												<p><strong>Subject:</strong> <?php if (strlen(trim($r["bSubject"]))==0){ echo "<em>Unavailable</em>"; } else{ echo $r["bSubject"]; } ?></p>
												<p><strong>Publisher:</strong> <?php if (strlen(trim($r["bPublisher"]))==0){ echo "<em>Unavailable</em>"; } else{ echo $r["bPublisher"]; } ?></p>
												<p><strong>Edition:</strong> <?php if (strlen(trim($r["bEdition"]))==0){ echo "<em>Unavailable</em>"; } else{ echo $r["bEdition"]; } ?></p>
												<p><strong>Place of Publication:</strong> <?php if (strlen(trim($r["bPoPub"]))==0){ echo "<em>Unavailable</em>"; } else{ echo $r["bPoPub"]; } ?></p>
												<p><strong>Year of Publication:</strong> <?php if (strlen(trim($r["bYoPub"]))==0){ echo "<em>Unavailable</em>"; } else{ echo $r["bYoPub"]; } ?></p>
												<p><strong>Description</strong></p>
												<p><?php echo $r["bBlurb"]; ?></p>
												<p><?php if($r["bReserve"]==0){ echo '<strong>This book is not reserved</strong>'; } else{ echo '<strong>This book is reserved</strong>'; } ?></p>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
											</div>
										</div>
									</div>
								</div>
								<!--==============-->
								
								<tr style="cursor:pointer;" data-toggle="modal" data-target="#modal<?php echo $r["BID"]; ?>">
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
				?><p class="text-danger"><strong>MySQL error:</strong> <?php echo mysql_error(); ?></p><?php
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