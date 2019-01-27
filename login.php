<?php
	ob_start();
	function redirect($url) 
	{
		header('Location: '.$url);
		exit();
	}
?>
<html style="font-family: sans-serif;">
	<body>
		<h1>Login</h1>
		<form method="POST">
			<p>Enter Username: </p><input type="text" name="loguser"><br/><br/>
			<p>Enter Password: </p><input type="password" name="logpass"><br/><br/>
			<input type="submit" value="Log In"><br/>
			<a href="index.php"><h1>Register</h1></a>
		</form>
<?php
	
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') 
	{
			$db=sqlite_open("userinformation.db");
		$loguser = test_input($_POST['loguser']);
		$logpass = test_input($_POST['logpass']);
		
		session_start();
		session_regenerate_id();
		$_SESSION["loguser"] = $loguser;
					
		$query = "SELECT * FROM user WHERE user.username ='$loguser' AND user.password='$logpass'";
		$result = sqlite_query($db, $query);
		$count = sqlite_num_rows($result);
				
		if($count==1){
			redirect('loggedin.php');
		}
		else {
			echo "Username and Password not Recognised";
		}
		sqlite_close($db);
	}
?>
	</body>
</html>