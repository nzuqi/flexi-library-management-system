<?php
	require("globals.php");
	
	//===create objects===
	$ui=new gui;
	$user=new user;
	$validate=new validate;
	$notif=new notification;
	$stats=new stats;
	
	$ui->active_menu=1;
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
		
		//check if there's the required parameter passed
		if (!isset($_GET['id'])){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.","warning");
			header('location: ./lib-home');
			exit();
		}
		//check if there's the required parameter passed
		if (substr($_GET['id'],0,2)!="FL"){
			$notif->setInfo("Missing parameter detected. Avoid manipulating the URL.","warning");
			header('location: ./lib-home');
			exit();
		}
		
		//$uid=$validate->decrypt($_GET['user'],WA_SALT);
		$uid=substr($_GET['id'],2,strlen($_GET['id']));
		
		//check if the UID exists in 'users'
		$q="SELECT COUNT(LID) AS numrows FROM libcusts WHERE LID=$uid;";
		$res=mysqli_query($mysqli,$q) or die ("Query failed checking 'lid' on 'libcusts'");
		$rw=mysqli_fetch_array($res,MYSQLI_ASSOC);
		if ($rw==0){
			$notif->setInfo("The requested profile could not be found on the database.","warning");
			header('location: ./err/?code=404');
			exit();
		}
		
		//set maximum books to issue at ago
		$max_issue=$_SESSION["LIB_MAX_BOOKS"];
		
		//count already available books in list
		function bcList($max_issue){
			$cntr=0;
			for ($a=1;$a<=$max_issue;$a++){
				if(isset($_SESSION["BORROW_".$_GET['id']."_B$a"]))
					$cntr=$cntr+1;
			}
			return $cntr;
		}
		
		//check if it's in list
		function bookInList($max_issue,$bid){
			$cntr=0;
			for ($a=1;$a<=$max_issue;$a++){
				if(isset($_SESSION["BORROW_".$_GET['id']."_B$a"])){
					if ($bid==$_SESSION["BORROW_".$_GET['id']."_B$a"])
						$cntr=$cntr+1;
				}
			}
			if ($cntr>0)
				return true;
			else
				return false;
		}
		
		//book actions
		if (isset($_GET['bid']) && isset($_GET['action'])){
			$cnt=bcList($max_issue);
			$bws=$stats->userBooksWith($uid);
			//add copy
			if ($_GET["action"]=="addCopy"){
				if(($bws+$cnt)>=$max_issue)
					$notif->setInfo("Sorry, you cannot issue more than $max_issue books to this user, the user already has $bws book(s) in possession and $cnt currently selected.","warning");
				elseif(bookInList($max_issue,$_GET['bid']))
					$notif->setInfo("Sorry, the book is already in the current list.","warning");
				elseif($stats->isBookReserved($_GET['bid']))
					$notif->setInfo("Sorry, the selected book is marked as reserved.","warning");
				else
					$_SESSION["BORROW_".$_GET['id']."_B".($cnt+1)]=$_GET['bid'];
			}
			//remove all
			if ($_GET["action"]=="removeAll"){
				for ($x=1;$x<=$max_issue;$x++){
					if(isset($_SESSION["BORROW_".$_GET['id']."_B$x"]))
						unset($_SESSION["BORROW_".$_GET['id']."_B$x"]);
				}
			}
			header("location: ./lib-home-check-borrow?id=".$_GET['id']);
			exit();
		}
		
		?>
		<div class="container">
			<!--alerts-->
			<?php
			echo $notif->printImportant();
			echo $notif->alertInfo();
			?>
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-check"></span> Borrow Books</h1>
			<div class="row">
				<?php
				$sqlf="SELECT * FROM libcusts WHERE LID=$uid LIMIT 1;";
				$resultf=mysqli_query($mysqli,$sqlf);
				while($r=mysqli_fetch_array($resultf)){
					?>
					<div class="col-sm-12">
						<h3 class="text-success" style="margin-top:0;"><span class="glyphicon glyphicon-user"></span> <?php echo ucwords(strtolower(trim($r['LName']))); ?>, <?php echo $r['LNumb']; ?> (<?php echo ucwords($r['LType']); ?>)</h3>
						<p>This <?php echo $r['LType']; ?>, <?php echo ucwords(strtolower(trim($r['LName']))); ?>, has visited the library to borrow books.</p>
						<p class="text-info" style="margin-top:10px;">Search for <strong>books</strong>, click on the search results to add book to list.</p>
						<form role="form" method="GET" autocomplete="off" action="#" name="srch" onSubmit="return false;" >
							<div class="form-group">
								<input type="text" name="username" id="q" class="form-control input-lg" value="" placeholder="Type here..." style="border-radius:0 !important;" onkeyup="JavaScript:searchStudents()" />
							</div>
						</form>
						<div class="search-res col-sm-12" id="search-res">
							<p>&nbsp;</p><p><img src="./images/loading.gif"> Searching, please wait...</p>
						</div>
						<p>&nbsp;</p>
					</div>
					<?php
				}
				?>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h3 class="text-success" style="margin-top:0;"><span class="glyphicon glyphicon-list"></span> Selected Books</h3>
					<?php
					//count set books
					$cntr=bcList($max_issue);
					if ($cntr>0){
						//list books here
						?>
						<p class="text-info">These are the books selected to be issued.</p>
						<div class="list-group">
							<?php
							for ($i=1;$i<=$cntr;$i++){
								$sql_res=mysqli_query($mysqli,"SELECT * FROM books WHERE BID=".$_SESSION["BORROW_".$_GET['id']."_B$i"].";");
								while($row=mysqli_fetch_array($sql_res)){
									?>
									<li class="list-group-item">
										<span class="glyphicon glyphicon-book"></span> <?php echo $row['bAccNo']; ?> <?php echo $row['bTitle']; ?> by <?php echo $row['bAuthor']; ?> (<?php echo $row['bCartegory']; ?>)</i>
									</li>
									<?php
								}
							}
							?>
						</div>
						<form action="./lib-home-check-action" method="POST">
							<input type="hidden" name="uid" value="<?php echo $uid; ?>"/>
							<input type="hidden" name="max_issue" value="<?php echo $max_issue; ?>"/>
							<div class="form-group">
								<label for="ddays">Due Date</label>
								<p class="text-info">Book(s) to be returned before the set amount of <strong>days</strong> are over, otherwise it will be marked as <strong>overdue</strong>.</p>
								<div class="input-group">
									<input type="text" id="ddays" name="ddays" value="7" placeholder="e.g. 7" class="form-control" />
									<div class="input-group-addon">Days</div>
								</div>
							</div>
							<p>&nbsp;</p>
							<a href="./lib-home-check-borrow?id=<?php echo $_GET['id']; ?>&bid&action=removeAll" class="btn btn-danger btn-lg"><i class="glyphicon glyphicon-trash"></i> Clear All</a> &nbsp; <button type="submit" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-ok"></i> Issue Books</button> &nbsp; <a href="lib-home" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-remove"></i> Cancel</a>
						</form>
						<?php
					}
					else{
						?>
						<p>&nbsp;</p>
						<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> There are no selected books to issue.</p>
						<p>&nbsp;</p>
						<?php
					}
					?>
					<p>&nbsp;</p>
				</div>
			</div>
			
			<hr>
			
			<?php $ui->printFooter(); ?>
		</div>
		<?php
	}
	
	$ui->printBottom();
?>
<script>
	//search students
	function searchStudents(){
		var stud=document.forms['srch']['q'].value;
		var elem=document.getElementById('search-res');
		stud=stud.trim();
		if (stud.length>=1){
			//elem.style.display="block";
			$('#search-res').fadeIn('fast');
			//new Ajax.Updater('search-res', 'lib-search?q=' + stud);
			$('#search-res').load('lib-search-books?q=' + stud + '&cust=<?php echo $_GET["id"]; ?>');
		}
		else{
			$('#search-res').fadeOut('fast');
			document.forms['srch']['q'].value='';
		}
	}
	$("#q").focus();
</script>