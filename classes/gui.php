<?php
	/*GUI class*/
	
	class gui{
		public $custom_page;
		public $page_title;
		public $active_menu;
		public $auth;
		
		public function __construct(){
			$this->custom_page=false;
			$this->active_menu=1;
			$this->page_title=WA_TITLE;
			$this->auth="";
		}
		
		//top section of a page
		public function printTop(){
			?>
			<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				
				<meta name="description" content="">
				<meta name="author" content="MAC Technologies">
				<link rel="icon" href="./images/favicon.png">

				<title><?php echo $this->page_title; ?></title>

				<!-- Bootstrap core CSS -->
				<link href="./plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

				<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
				<link href="./plugins/bootstrap/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

				<!-- Custom styles for this theme -->
				<link href="./styles/custom.css" rel="stylesheet">
				
				<!-- dataTables plugin -->
				<link href="./plugins/dataTables/dataTables.bootstrap.min.css" rel="stylesheet">
				
				<script src="./plugins/bootstrap/js/ie-emulation-modes-warning.js"></script>

				<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
				<!--[if lt IE 9]>
				<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
				<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
				<![endif]-->
			</head>
			<body>
			<?php
		}
		
		//bottom section of a page
		public function printBottom(){
			?>
				<script src="./plugins/chart.js-master/Chart.js"></script>
				<script src="./scripts/jquery.min.js"></script>
				<script src="./plugins/bootstrap/js/bootstrap.min.js"></script>
				<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
				<script src="./plugins/bootstrap/js/ie10-viewport-bug-workaround.js"></script>
			</body>
			</html>
			<?php
		}
		
		//page navbar
		public function printNavbar($sub=null){
			if ($this->custom_page==true){
				?>
				<nav class="navbar navbar-default navbar-fixed-top">
					<div class="container">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="./"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo WA_TITLE; ?></a>
						</div>
						<!--
						<div id="navbar" class="collapse navbar-collapse">
							<ul class="nav navbar-nav">
								<li><a href="./">GO HOME</a></li>
							</ul>
						</div>
						-->
					</div>
				</nav>
				<?php
			}
			else{
				?>
				<nav class="navbar navbar-default navbar-fixed-top">
					<div class="container">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="./"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo WA_TITLE; ?></a>
						</div>
						<div id="navbar" class="collapse navbar-collapse">
							<ul class="nav navbar-nav">
								<li<?php if($this->active_menu==1) echo ' class="active"'; ?>><a href="./lib-home"><i class="glyphicon glyphicon-home"></i> LIBRARY HOME</a></li>
								<li class="dropdown<?php if($this->active_menu>=2.1 && $this->active_menu<3) echo ' active'; ?>">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"></span><i class="glyphicon glyphicon-book"></i> BOOKS <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li<?php if($this->active_menu==2.1) echo ' class="active"'; ?>><a href="lib-books-new"><i class="glyphicon glyphicon-plus"></i> New Books</a></li>
										<li<?php if($this->active_menu==2.2) echo ' class="active"'; ?>><a href="lib-books-update"><i class="glyphicon glyphicon-pencil"></i> Update Books Records</a></li>
										<li role="separator" class="divider"></li>
										<li<?php if($this->active_menu==2.3) echo ' class="active"'; ?>><a href="lib-books"><i class="glyphicon glyphicon-book"></i> View All Books</a></li>
										<li role="separator" class="divider"></li>
										<li<?php if($this->active_menu==2.4) echo ' class="active"'; ?>><a href="lib-books-view?cart=issued"><i class="glyphicon glyphicon-share-alt"></i> View Issued Books</a></li>
										<li<?php if($this->active_menu==2.5) echo ' class="active"'; ?>><a href="lib-books-view?cart=overdue"><i class="glyphicon glyphicon-time"></i> View Overdue Books</a></li>
										<li<?php if($this->active_menu==2.6) echo ' class="active"'; ?>><a href="lib-books-view?cart=lost"><i class="glyphicon glyphicon-remove-circle"></i> View Lost Books</a></li>
										<li<?php if($this->active_menu==2.7) echo ' class="active"'; ?>><a href="lib-books-view?cart=compensate"><i class="glyphicon glyphicon-refresh"></i> View Compensated Books</a></li>
										<li role="separator" class="divider"></li>
										<li<?php if($this->active_menu==2.8) echo ' class="active"'; ?>><a href="lib-barcode-generator"><i class="glyphicon glyphicon-barcode"></i> Generate Barcodes</a></li>
									</ul>
								</li>
								<li class="dropdown<?php if($this->active_menu>=3.1 && $this->active_menu<4) echo ' active'; ?>">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"></span><i class="glyphicon glyphicon-user"></i> LIBRARY USERS <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li<?php if($this->active_menu==3.1) echo ' class="active"'; ?>><a href="lib-students-new"><i class="glyphicon glyphicon-plus"></i> New Students & Staff</a></li>
										<li<?php if($this->active_menu==3.2) echo ' class="active"'; ?>><a href="lib-students-update"><i class="glyphicon glyphicon-pencil"></i> Update Students & Staff Records</a></li>
										<li role="separator" class="divider"></li>
										<li<?php if($this->active_menu==3.3) echo ' class="active"'; ?>><a href="lib-students"><i class="glyphicon glyphicon-user"></i> View All Students & Staff</a></li>
										<li role="separator" class="divider"></li>
										<li<?php if($this->active_menu==3.4) echo ' class="active"'; ?>><a href="lib-students-banned"><i class="glyphicon glyphicon-ban-circle"></i> View Banned Students & Staff</a></li>
									</ul>
								</li>
								<li<?php if($this->active_menu==4) echo ' class="active"'; ?>><a href="lib-analytics"><i class="glyphicon glyphicon-stats"></i> ANALYTICS</a></li>
							</ul>
							<?php
							if (isset($_SESSION['CURR_USER_AUTH']) && isset($_SESSION['CURR_USER_ID']) && isset($_SESSION['CURR_USER_UN']) && isset($_SESSION['CURR_USER_NAME'])){
								$notif=new notification;
								?>
								<ul class="nav navbar-nav navbar-right">
									<li class="dropdown<?php if($this->active_menu>=9.1 && $this->active_menu<10) echo ' active'; ?>">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown"></span><?php echo strtoupper($_SESSION['CURR_USER_NAME']).$notif->displayNotifs(); ?> <span class="caret"></span></a>
										<ul class="dropdown-menu">
											<li<?php if($this->active_menu==9.1) echo ' class="active"'; ?>><a href="notifications"><i class="glyphicon glyphicon-bell"></i> Notifications<?php echo $notif->displayNotifs(); ?></a></li>
											<li<?php if($this->active_menu==9.2) echo ' class="active"'; ?>><a href="profile?user=FL<?php echo $_SESSION["CURR_USER_ID"]; ?>"><i class="glyphicon glyphicon-user"></i> My Profile</a></li>
											<li role="separator" class="divider"></li>
											<?php
											if ($_SESSION['CURR_USER_AUTH']=='admin'){
												?>
												<li<?php if($this->active_menu==9.3) echo ' class="active"'; ?>><a href="accounts"><i class="glyphicon glyphicon-list-alt"></i> Manage Accounts</a></li>
												<li<?php if($this->active_menu==9.4) echo ' class="active"'; ?>><a href="preferences"><i class="glyphicon glyphicon-cog"></i> Preferences</a></li>
												<li role="separator" class="divider"></li>
												<?php
											}
											?>
											<li><a href="logout"><font color="#ff0000"><i class="glyphicon glyphicon-log-out"></i> Log out</font></a></li>
										</ul>
									</li>
								</ul>
								<?php
							}
							?>
						</div>
					</div>
				</nav>
				<?php
			}
		}
		
		//page footer
		public function printFooter(){
			?>
			<footer class="footer">
				<?php
				if (trim($_SESSION["host_name"])!="" && trim($_SESSION["host_box"])!="" && trim($_SESSION["host_postalcode"])!="" && trim($_SESSION["host_ctown"])!=""){
					?><p class="small text-warning"><strong><?php echo WA_TITLE; ?></strong> is licensed to <strong><?php echo ucwords(strtolower($_SESSION["host_name"])); ?>, <?php echo strtoupper($_SESSION["host_box"]); ?> - <?php echo $_SESSION["host_postalcode"]; ?>, <?php echo ucwords(strtolower($_SESSION["host_ctown"])); ?>.</strong></p><?php
				}
				else{
					?><p class="text-danger">Licensee details unavailable.</p><?php
				}
				?>
				<p class="small text-info"><strong>Version <?php echo WA_VERSION; ?></strong>, revised on <strong><?php echo WA_REVISION; ?></strong></p>
				<p class="small text-warning">The source code and the GUI (Graphic User Interface) are the original works of <strong><?php echo D_NAME; ?></strong>.</p>
				<p class="small">Visit <a href="http://martin.co.ke/" target="_blank">www.martin.co.ke</a> for more web apps and snippets.</p>
				<p class="text-danger">&copy; <?php echo date('Y'); ?> <?php echo D_NAME; ?></p>
				<p>&nbsp;</p>
			</footer>
			<?php
		}
		
	}
	
?>