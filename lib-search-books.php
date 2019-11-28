<?php
	require("globals.php");
	
	if(isset($_GET['q']) && isset($_GET['cust'])){
		$q=mysqli_real_escape_string($mysqli,$_GET['q']);
		$cust=trim($_GET['cust']);
		$res=mysqli_query($mysqli,"SELECT COUNT(BID) FROM books WHERE bTitle LIKE '%".$q."%' OR bAuthor LIKE '%".$q."%' OR bAccNo LIKE '%".$q."%' OR bCartegory LIKE '%".$q."%' AND bReserve=0;");
		while($r=mysqli_fetch_array($res)){
			$cnt=$r[0];
		}
		if ($cnt>0){
			$sql_res=mysqli_query($mysqli,"SELECT * FROM books WHERE bTitle LIKE '%".$q."%' OR bAuthor LIKE '%".$q."%' OR bAccNo LIKE '%".$q."%' OR bCartegory LIKE '%".$q."%' AND bReserve=0 ORDER BY BID Asc LIMIT 10;");
			
			if (strlen($_GET['q'])>10)
				$fq=substr($_GET['q'],0,10)."...";
			else
				$fq=$_GET['q'];
			?>
			<p class="text-info" style="margin-top:10px;"><i class="glyphicon glyphicon-ok"></i> <strong><?php echo $cnt; ?></strong> result(s) found for '<strong><?php echo $fq; ?></strong>'</p>
			<div class="list-group">
			<?php
			
			while($row=mysqli_fetch_array($sql_res)){
				$title=$row['bTitle'];
				$author=$row['bAuthor'];
				$accnumb=$row['bAccNo'];
				$cart=$row['bSubject'];
				$b_title='<font color=\"#000\"><strong>'.$q.'</strong></font>';
				$b_author='<font color=\"#000\"><strong>'.$q.'</strong></font>';
				$b_accnumb='<font color=\"#000\"><strong>'.$q.'</strong></font>';
				$b_cart='<font color=\"#000\"><strong>'.$q.'</strong></font>';
				$final_title = str_ireplace($q, $b_title, $title);
				$final_author = str_ireplace($q, $b_author, $author);
				$final_accnumb = str_ireplace($q, $b_accnumb, $accnumb);
				$final_cart = str_ireplace($q, $b_cart, $cart);
				$bid=$row['BID'];
				?>
				<a class="list-group-item" href="./lib-home-check-borrow?id=<?php echo $cust; ?>&bid=<?php echo $bid; ?>&action=addCopy" style="box-shadow: 0 10px 6px -6px #ccc;">
					<span class="glyphicon glyphicon-book"></span> <?php echo $final_accnumb; ?> <?php echo $final_title; ?> by <?php echo $final_author; ?> (<?php echo $final_cart; ?>)</i>
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