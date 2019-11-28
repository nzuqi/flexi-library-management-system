<?php
	require("globals.php");
	
	if(isset($_GET['q'])){
		$q=strtoupper(mysqli_real_escape_string($mysqli,$_GET['q']));
		$res=mysqli_query($mysqli,"SELECT COUNT(LID) FROM libcusts WHERE LName LIKE '%".$q."%' OR LNumb LIKE '%".$q."%';");
		while($r=mysqli_fetch_array($res)){
			$cnt=$r[0];
		}
		if ($cnt>0){
			$sql_res=mysqli_query($mysqli,"SELECT * FROM libcusts WHERE LName LIKE '%".$q."%' OR LNumb LIKE '%".$q."%' ORDER BY LID Asc LIMIT 10;");
			
			if (strlen($_GET['q'])>10)
				$fq=substr($_GET['q'],0,10)."...";
			else
				$fq=$_GET['q'];
			?>
			<p class="text-info" style="margin-top:10px;"><i class="glyphicon glyphicon-ok"></i> <strong><?php echo $cnt; ?></strong> result(s) found for '<strong><?php echo $fq; ?></strong>'</p>
			<div class="list-group">
			<?php
			
			while($row=mysqli_fetch_array($sql_res)){
				$name=$row['LName'];
				$numb=$row['LNumb'];
				$b_name='<font color=\"#000\"><strong>'.$q.'</strong></font>';
				$b_numb='<font color=\"#000\"><strong>'.$q.'</strong></font>';
				$final_name = str_ireplace($q, $b_name, $name);
				$final_numb = str_ireplace($q, $b_numb, $numb);
				$sid=$row['LID'];
				?>
				<a class="list-group-item" href="./lib-home-check?id=FL<?php echo $sid; ?>" style="box-shadow: 0 10px 6px -6px #ccc;">
					<span class="glyphicon glyphicon-user"></span> <?php echo $final_name; ?>, <?php echo $final_numb; ?> (<?php echo ucwords($row['LType']); ?>)</i>
				</a>
				<?php
			}
			?>
			</div>
			<?php
		}
		else{
			if (strlen($_GET['q'])>10)
				$fq=substr($_GET['q'],0,10)."...";
			else
				$fq=$_GET['q'];
			?><p class="text-danger" style="margin-top:10px;"><i class="glyphicon glyphicon-remove"></i> No results found for '<strong><?php echo $fq; ?></strong>'</p><?php
		}
	}
?>