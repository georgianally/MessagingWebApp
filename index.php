<?php if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	include_once 'CreateDatabase.php';
}
?>
<html style="font-family: sans-serif;">
	<h1>Register</h1>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<p>Enter Username: </p><input type="text" name="username" value="<?php echo $_POST['username']; 
?>">
		<p>Enter Password: </p><input type="text" name="password" value="<?php echo $_POST['password']; 
?>">
		<p>Enter Country: 
			<select name="country">
				<option value="UK">UK</option>
				<option value="USA">USA</option>
				<option value="Germany">Germany</option>
				<option value="China">China</option>
			<select></p>
		<input type="submit" name="submit" value="Register New User"><br/><br/>
	</form>
	<a href="login.php"><h1>Login</h1></a>
<?php 
	$pass = true;

	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') 
	{
		//Error checking
		if ($_POST["username"] == "") {
			echo "Username is required<br/>";
			$pass = false;
		} 
		elseif(ctype_alnum($_POST['username'])) {
			$username = test_input($_POST['username']);
		}
		else {
			echo "Username only accepts Numbers and Letters<br/>";
			$pass = false;
		}
		
		if ($_POST["password"] == "") {
			echo "Password is required<br/>";
			$pass = false;
		} 
		elseif(ctype_alnum($_POST['password'])) {
			$password = test_input($_POST['password']);
		}
		else {
			echo "Password only accepts Numbers and Letters<br/>";
			$pass = false;
		}
		
		if ($_POST["country"] == "") {
			echo "Country is required<br/>";
			$pass = false;
		} 
		elseif (preg_match("/^[a-zA-Z]+$/",$_POST['country'])){
			$country = test_input($_POST['country']);
		} 
		else {
			echo "Country only accepts Letters<br/>";
			$pass = false;
		}		
		
		//If passes error check - store in db
		if($pass === true){
			$db=sqlite_open("userinformation.db");
								
			$all=sqlite_query($db,"SELECT * from user, messages WHERE user.ID = messages.ID");
			$current_ID = count(sqlite_fetch_all($all, SQLITE_NUM))+1;
								
			sqlite_query($db, "INSERT INTO user VALUES ($current_ID, '$username', '$password', '$country')");
			sqlite_query($db, "INSERT INTO messages VALUES ($current_ID, '$tousername', '$fromusername', '$message', '$ddate', '$ttime')");
			echo "<br/>".$_POST['username']." Successfully Registered.";
			sqlite_close($db);
		}
		else {
			echo "<br/>User not registered - Complete form to full";
		}
	}
?>
</html>