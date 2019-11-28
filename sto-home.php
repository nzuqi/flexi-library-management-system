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
	$ui->printNavbar('stores');
	
	//check if the user is logged in
	if (!$user->verify()){
		//if the user is not logged in,
		//set current sub-system
		setSubSys('stores');
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
			<h1 class="text-danger" style="margin-top:0;"><span class="glyphicon glyphicon-home"></span> Stores Home</h1>
			
			<p>&nbsp;</p>
			<p class="text-info"><span class="glyphicon glyphicon-info-sign"></span> Apparently, there's no data available under this section.</p>
			<p>&nbsp;</p>
			
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
			$('#search-res').load('lib-search-custs?q=' + stud);//new Ajax.Updater('search-res', 'lib-search?q=' + stud);
		}
		else{
			$('#search-res').fadeOut('fast');
			document.forms['srch']['q'].value='';
		}
	}
	$("#q").focus();
</script>