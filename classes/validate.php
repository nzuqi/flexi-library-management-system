<?php
	
	class validate{
		
		//function to test the input
		public function test_input($data){
			$data=trim($data);
			$data=stripslashes($data);
			$data=htmlspecialchars($data);
			return $data;
		}
		
		//check admin acc
		public function adminAccExists(){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$que="SELECT COUNT(UID) FROM users WHERE uAuth='admin';";
			$res=mysqli_query($con,$que);
			while ($rw=mysqli_fetch_array($res)){
				if ($rw[0]>=1)
					return true;
				else
					return false;
			}
			return false;
		}
		
		//Check if book access number exists in the library already
		public function booknoExists($no){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT COUNT(BID) FROM books WHERE bAccNo='".$no."';";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if ($row2[0]>0)
						return true;
					else
						return false;
				}
			}
			return true;
		}
		
		//Check if username exists with another user while updating
		public function usernameExists($username){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT COUNT(UID) FROM users WHERE uUsername='".$username."';";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if ($row2[0]==1)
						return true;
					else
						return false;
				}
			}
			return false;
		}
		
		//Check if username exists
		public function usernameExistsWithOtherUser($username,$uid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT COUNT(UID) FROM users WHERE uUsername='".$username."' AND UID<>".$uid.";";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if ($row2[0]==1)
						return true;
					else
						return false;
				}
			}
			return false;
			
			dbconnect();
			$sql2="SELECT COUNT(UID) FROM users WHERE uUsername='".$username."' AND UID<>".$uid.";";
			$result2=mysql_query($sql2);
			if ($result2){
				while ($row2=mysql_fetch_array($result2)){
					if ($row2[0]>0)
						return true;
					else
						return false;
				}
			}
			return false;
		}
		
		//Check if 'idno' exists
		public function idnoExists($idno){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT COUNT(UID) FROM users WHERE uIDNumber='".$idno."';";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if ($row2[0]==1)
						return true;
					else
						return false;
				}
			}
			return false;
		}
		
		//Check if 'idno' exists with another user while updating details
		public function idnoExistsWithOtherUser($idno,$uid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT COUNT(UID) FROM users WHERE uIDNumber='".$idno."' AND UID<>".$uid.";";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if ($row2[0]>0)
						return true;
					else
						return false;
				}
			}
			return false;
		}
		
		//Check if book's access number exists
		public function baccnoExists($no){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT COUNT(BID) FROM books WHERE bAccNo='".$no."';";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if ($row2[0]==1)
						return true;
					else
						return false;
				}
			}
			return false;
		}
		
		//Check if 'accno' exists with another book while updating details
		public function accnoExistsWithOtherBook($accno,$bid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT COUNT(BID) FROM books WHERE bAccNo='".$accno."' AND BID<>".$bid.";";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if ($row2[0]>0)
						return true;
					else
						return false;
				}
			}
			return false;
		}
		
		//Check if library number exists
		public function lnoExists($no){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT COUNT(LID) FROM libcusts WHERE LNumb='".$no."';";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if ($row2[0]==1)
						return true;
					else
						return false;
				}
			}
			return false;
		}
		
		//Check if 'lno' exists with another user while updating details
		public function lnoExistsWithOtherUser($lno,$lid){
			$con=mysqli_connect(HOST, USER, PASSWORD, DATABASE) or die(DB_CON_ERR);
			$sql2="SELECT COUNT(LID) FROM libcusts WHERE lNumb='".$lno."' AND LID<>".$lid.";";
			$result2=mysqli_query($con,$sql2);
			if ($result2){
				while ($row2=mysqli_fetch_array($result2)){
					if ($row2[0]>0)
						return true;
					else
						return false;
				}
			}
			return false;
		}
		
		//system is activation
		public function keyExists($key){ $keys=array("cbd8b045d38a538b4f09e3c3d215f79f5bf99032","caeaf7bd0d4c2e7c42c78887a7a5beeeabccb293","e9f53124bf6d4168799112a62da48c465e6d0089","8f324516df80ce3e85085083076a91fa474dd602","8ce9ffeac285cbe1de768a9e56501e64ccac63a7","e606dccb8ab95e9bd7df158d27e9e5c4981f21fb","15b7c61716d9ff5cd95f39bfd963bc683988c8e6","0efb9f10c7d8ded3c36e8690b42ccf344b7a4a03","6ebb5938fd3601881056d7fa7e86aa56208522b5","8ce473707ec1644ee0b071282d9a4aa22f3b8e3c","a8c21c09f3b69265efcaf92c8402f44b6f8b1ca1","7b188f1624edbc3c5ad5eae22c43eb69c9df6f12","049f6b5bb1143c821bb697116fac62ac6032d43d","7bb2b0e8059689430e9f302cbdde76e6ce2742ff","6f0fc4a5309cc452782cc0d26edf4721e754c68f","19dd687b08b628cfb861ed60df63dc4f13aa7b15","6116bee0ac9a61cee4811ee62e817ddb1f35014e","2152f7d977cf1d49c7bd3be638e7b9b0f7e58522","070f93b642c55f235d8dcbce9b819fb096ee9abb","f728c7f2a7f756a64a4c2189e2a8700cb3d35761","b7977d7eaf874dae765586eb8a8943820ce3b169","66ec7fad56243d17bf97f79d645c730328800c16","0e8a62feef51ba127604cedf250cafd0a8600c29","c3ea35e824d58eff2e76c5ac5d4dab0039c91e17","63768933ed52bba6b9461e8521481abd338e8229","f28df0aeb8e0ce37aaf838063dc8f19d219530ff","39953153eb3361c61f1ae4a77d56692ec8f8ffcc","79897ad48af4820b7c31163eba6b43b57642b7a1","49b23bac4b9734aab9f456c86688b3842576cae2","ea71e9b0a1849ccc64e185b000f9ca30d187755c","b9c6f81afdc25bc551512e0457996b290860aa1e","b2d7e4a4fefea5d5989188feccb67197bcf3c28d","cbf78e08ef27ed8133046ea10666fef15669c07c","1b06bbe5679d16cfb793af93f9c1aa5c9159a81f","b5e43937c87ea29ed8296d71f680d0b919696f0b","53add29de7e04ae6b5c26cd20d24614fd78f5d6b","4d3c659e3ee7db023aedab3618330f6303439f1f","33f99552e8211ee66a7a31278be96c2f886d3152","bbe77b225de99476710cc61119d7f781c06d104f","7d58abb4ce71bdab367f81e7787208408b05154f","b0e5ac3c003540b6a47614020bcdea540aa55114","d21cd7ff17ac2e964b1b50a8caf18691da99293d","a7caba9b0397870345a96ed25c0e153d476f4f91","cbe3a39a791a6c818ba157fb43be9f7bec269f42","fa02a18912935dd171168625aafe4eab1e4e7a95","8e002332734178c111797308d1ab7e4fa70aa7d1","089eb9252c7f30ba824b64dd4b523192b5d16ec9","223ed93ccac011468a746612566040cbde0889ff","b4d3d384d9210bc971549e1ddf4aabd4a2d9c4ee","8597d6007530fcbebc2969dc50ce4fecd337a651","ca177f4261edc930113fdd0aa9c8e2f4add85825","d77e95ce21900dc33995f54a5252aa9d8ce8904e","07bdef470cfa86afbc54cb9a7eb26fee2d0e08cc","eff93974ff0bd71c53e7b0ca625cb77b6b89077a","89d68cbe7c58e974ec2f40937d9f6576328f2bce","127eacf6010a34d4be8c5c34a9bd51b9f2e4bd96","8ee79fdbb1fee16abd3067e35c854e189b8a0839","f85aa5a824c90d391a328aeb89c5ab94fb33e3d1","0f0c276d7d363216857f1783fec99d7b88b20e51","9e8d6b421d3ea81e32ff604712dbca97bdf5d9a3","d367c552c98d1e23e957de6dd710d44a68c8ffea","a9a9eacc62d83b3b11f126852380845b91deaecf","a76fe8bb3679ffead51d5bec7831b002ea1cef77","6b63913bbb6fe47e4c661bcf5e0df2ac076538ef","3c7d9f04fc80298d620a3fe3de644509625035c4","c0f4a284704bd16e1f6d1548fb80248d174b43e5","0d6a4ecb053d2bb0abc23151183780b0cae56ab8","34f8d002523ce467ada885234712593f0388a09c","2ca6aa76f3335070597f5cb0c5ad28d66081ec67","9e4a833b3c8cbbc57700779c363aa7b910616a5b","8b861bd98df6619742461654e201dae5c46c2b4e","008d99b37f7f066e5b6b690e46973b66e4b5dc67","802873eb6f8a7d14f92b4d64bb9acd89466a1601","a1791514029d418dc85da467ea0898e7cb20719a","20f0e308f99fa747a837a157af8a588dc036e901","0c6df4a4e9fef3a23e4cc64bf2c19d8369a7fe25","df329ab6c280b6745d15a30dc57e94cf25fc9ce0","7e18da32c33e32efd732e3c223bf096639d5dc1c","66d857dceba1468cdfa0249acb33fb32a00ac15b","c763a470811a7307b0c705e0a8f192080f434814","8f2b09fa84beafe54a7501357b1e4896495a0c4e","bdf30b7a4bd28f36acf7960bd72640f2b5cdabcc","9d4e04df8c11243367990303946401de03bd5eeb","fe3de237f1b83ae33c58fbad75b7e781a9c4ea49","2a6bf72bbb2d1f00cd27e02a6bed68e0f5dfe17c","47c4d6552fc512ff39ad04895bb5306a614a6927","51be1858324396b722c82ea5b482b5c271f6c7bd","b259e335932c46329ad219d69292982f89175116","fd5cb221f6655d890b68b86a788251e5263dcc73","1cda8d0e0a4413356c097e5b7dfdbc5d2cd10125","a007a4577d0c362e31340d3dc9d87dfabe1c0391","9189258df796ab6e0955e8c06664130fec9e7d5a","a5ceb062dcc1cd218ade7743acfce0926ff118e3","c34c7c141309c0e0ad68d43ffa7168b6344db572","4b50a7a008dfbc496f1efc91cbb4aff78f24f920","8ffd770fb11194958f326af92fbf6e8947ad67a1","240b674569264f2cace2fad73ea84d182cf3ab1b","b4f6c9b1731ef8c9a943c73c7ce20ad37fdb2ac6","a7ba97f15cc351184c4d48071e6726cda7e1ff82","d608c1d17f1fa78d8e09e89784feccad8c9f7a8b"); if (in_array($key,$keys)) { return true; } else { return false; } }
		public function isSysActivated(){
			if ($_SESSION["act_key"]!="" || $_SESSION["act_key"]!=null){
				if (!$this->keyExists($_SESSION["act_key"]))
					return false;
				else
					return true;
			}
			else
				return false;
		}
	}