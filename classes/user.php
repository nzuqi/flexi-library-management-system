<?php
	class user{
		public $username;
		
		//set user session variables
		public function setUserSessVars($uid,$uname,$username,$password,$auth){
			if(!isset($_SESSION["CURR_USER_NAME"]))
				$_SESSION["CURR_USER_NAME"]=$uname;
			if(!isset($_SESSION["CURR_USER_ID"]))
				$_SESSION["CURR_USER_ID"]=$uid;
			if(!isset($_SESSION["CURR_USER_UN"]))
				$_SESSION["CURR_USER_UN"]=$username;
			if(!isset($_SESSION["CURR_USER_PASS"]))
				$_SESSION["CURR_USER_PASS"]=$password;
			if(!isset($_SESSION["CURR_USER_AUTH"]))
				$_SESSION["CURR_USER_AUTH"]=$auth;
		}
		
		//unset user session variables
		public function unsetUserSessVars(){
			if(isset($_SESSION["CURR_USER_NAME"]))
				unset($_SESSION["CURR_USER_NAME"]);
			if(isset($_SESSION["CURR_USER_ID"]))
				unset($_SESSION["CURR_USER_ID"]);
			if(isset($_SESSION["CURR_USER_UN"]))
				unset($_SESSION["CURR_USER_UN"]);
			if(isset($_SESSION["CURR_USER_PASS"]))
				unset($_SESSION["CURR_USER_PASS"]);
			if(isset($_SESSION["CURR_USER_AUTH"]))
				unset($_SESSION["CURR_USER_AUTH"]);
		}

		//check logged in status
		public function login_check($mysqli) {
			// Check if all session variables are set 
			if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
				$user_id = $_SESSION['user_id'];
				$login_string = $_SESSION['login_string'];
				$username = $_SESSION['username'];
				
				// Get the user-agent string of the user.
				$user_browser = $_SERVER['HTTP_USER_AGENT'];
				//$user_browser = "CHROME_47";
				
				if ($stmt = $mysqli->prepare("SELECT uPassword FROM users WHERE UID = ? LIMIT 1")) {
					// Bind "$user_id" to parameter. 
					$stmt->bind_param('i', $user_id);
					$stmt->execute();   // Execute the prepared query.
					$stmt->store_result();
		 
					if ($stmt->num_rows == 1) {
						// If the user exists get variables from result.
						$stmt->bind_result($password);
						$stmt->fetch();
						$login_check = $password . sha1(md5($user_browser));
		 
						if ($login_check==$login_string){
							// Logged In!!!! 
							return true;
						} else {
							// Not logged in 
							return false;
						}
					} else {
						// Not logged in 
						return false;
					}
				} else {
					// Not logged in 
					return false;
				}
			} else {
				// Not logged in 
				return false;
			}
		}
		
		//insert activity to db
		function updateActivity($sid,$actv){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="INSERT INTO activity(SID,UActivity) VALUES($sid,'$actv');";
			$result2=mysqli_query($con,$sql2);
			if ($result2)
				return true;
			else
				return false;
		}
		
		//get ID Number of user
		public function getUIdNumb($uid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$id="";
			$sql2="SELECT uIDNumber FROM users WHERE UID=$uid LIMIT 1;";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					$id=$row2[0];
				}
			}
			return $id;
		}
		
		//get ID of user
		public function getUID($idno){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$id="";
			$sql2="SELECT UID FROM users WHERE uIDNumber='".$idno."' LIMIT 1;";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					$id=$row2[0];
				}
			}
			return $id;
		}
		
		//get name of user
		public function getUName($uid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sname="";
			$sql2="SELECT uName FROM users WHERE UID=$uid LIMIT 1;";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					$sname=$row2[0];
				}
			}
			return $sname;
		}
		
	}