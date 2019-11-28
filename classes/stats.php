<?php
	
	class stats{
		
		//check if book is reserved
		public function isBookReserved($bid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT bReserve FROM books WHERE BID=".$bid.";";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if($row2[0]==1)
						return true;
					else
						return false;
				}
			}
			else
				return true;
		}
		
		//check if book is lost
		public function isBookLost($iid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT iState FROM issue WHERE IID=".$iid.";";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if($row2[0]==2)
						return true;
					else
						return false;
				}
			}
			else
				return true;
		}
		
		//count overdue charges for all books
		public function countOverdueBooksCharges(){
			$ksh=$d=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT iDuration,iTimeS FROM issue WHERE (iTimeS+INTERVAL iDuration DAY)<CURDATE() AND iState=0;";
			$result2=mysqli_query($con,$sql2) or die(mysqli_error($con));
			while ($row2=mysqli_fetch_array($result2)){
				$d=ceil((time()/86400)-((strtotime($row2[1])/86400)+$row2[0]));
				$ksh=$ksh+($_SESSION['LIB_OVERDUE_CHARGES']*$d);
			}
			return $ksh;
		}
		
		//count overdue days for book on user
		public function countOverdueBookUserCharges($iid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$ksh=$d=0;
			$sql2="SELECT iDuration,iTimeS FROM issue WHERE IID=$iid AND (iTimeS+INTERVAL iDuration DAY)<CURDATE() AND iState=0;";
			$result2=mysqli_query($con,$sql2) or die(mysqli_error($con));
			while ($row2=mysqli_fetch_array($result2)){
				$d=ceil((time()/86400)-((strtotime($row2[1])/86400)+$row2[0]));
				$ksh=$ksh+($_SESSION['LIB_OVERDUE_CHARGES']*$d);
			}
			return $ksh;
		}
		
		//count overdue days for books
		public function countOverdueBooksUserCharges($uid){
			$ksh=$d=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT iDuration,iTimeS FROM issue WHERE SID=".$uid." AND (iTimeS+INTERVAL iDuration DAY)<CURDATE() AND iState=0;";
			$result2=mysqli_query($con,$sql2) or die(mysqli_error($con));
			while ($row2=mysqli_fetch_array($result2)){
				$d=ceil((time()/86400)-((strtotime($row2[1])/86400)+$row2[0]));
				$ksh=$ksh+($_SESSION['LIB_OVERDUE_CHARGES']*$d);
			}
			return $ksh;
		}
		
		//count overdue books
		public function countLostBooks(){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(IID) AS numrows FROM issue WHERE iState=2;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//count overdue books
		public function countOverdueBooks(){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(IID) AS numrows FROM issue WHERE (iTimeS+INTERVAL iDuration DAY)<CURDATE() AND iState=0;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//count overdue books [user]
		public function countOverdueBooksUser($uid){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(IID) AS numrows FROM issue WHERE (iTimeS+INTERVAL iDuration DAY)<CURDATE() AND SID=$uid AND iState=0;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//check if book is overdue
		public function isBookOverdue($iid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(*) AS numrows FROM issue WHERE IID=$iid AND (iTimeS+INTERVAL iDuration DAY)<CURDATE() AND iState=0;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			if($rw1['numrows']==1)
				return true;
			else
				return false;
		}
		
		//count user borrowed books
		public function userBooksBorrowed($uid){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(IID) AS numrows FROM issue WHERE iState=0 AND SID=$uid;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//count user returned books
		public function userBooksReturned($uid){
			$c=0;
			$q1="SELECT COUNT(IID) AS numrows FROM issue WHERE iState=1 AND SID=$uid;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//count user lost books
		public function userBooksLost($uid){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(IID) AS numrows FROM issue WHERE iState=2 AND SID=$uid;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//count user paid books
		public function userBooksPaid($uid){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(IID) AS numrows FROM issue WHERE iState=3 AND SID=$uid;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//count books with student
		public function userBooksWith($uid){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(IID) AS numrows FROM issue WHERE (iState=0 OR iState=2) AND SID=$uid;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//count banned custs
		public function countBannedCusts(){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(LID) AS numrows FROM libcusts WHERE LBan=0;";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//count banned staff
		public function countBannedStaff(){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(LID) AS numrows FROM libcusts WHERE LBan=1 AND LType='staff';";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
		//count banned students
		public function countBannedStuds(){
			$c=0;
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$q1="SELECT COUNT(LID) AS numrows FROM libcusts WHERE LBan=1 AND LType='student';";
			$res1=mysqli_query($con,$q1);
			$rw1=mysqli_fetch_array($res1,MYSQLI_ASSOC);
			$c=$rw1['numrows'];
			return $c;
		}
		
	}