<?php
	session_start();
	session_regenerate_id();
	$currentUser = $_SESSION['loguser'];

	echo "<h1>Welcome!</h1>Currently logged in as: ".htmlspecialchars($currentUser, ENT_QUOTES, 'UTF-8');
	//error clean data
	function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	function adCheck(&$country)
	{
		global $adsense;
		if ($country < 2)
		{
			$country += 1;
			$adsense = false;
		}
		else
		{
			$adsense = true;
			$country = 0;
		}
	}
	
	$db=sqlite_open("userinformation.db");
	$result=sqlite_query($db,"SELECT * from user, messages WHERE user.ID = messages.ID");
	while($row=sqlite_fetch_array($result,SQLITE_ASSOC)) 
	{
		if($row['user.username'] === currentUser)
		{
			$userCountry = $row['user.country'];
		}
	}
	
	$adSense = array('UK' => 0, 'USA' => 0, 'Germany' => 0, 'China' => 0 );
?>
<html>
	<style>
		html {
			font-family: sans-serif;
		}
		table {
		border-collapse: collapse;
		}
		tr:nth-child(even) {
			background-color: #f2f2f2;
		}
		th, tr, td {
			padding: 7px;
			border: 1px solid black;
			min-width: 100px;
		}
	</style>
<h3>Current Registered Users:</h3>
<?php
	$result=sqlite_query($db,"SELECT * from user, messages WHERE user.ID = messages.ID");
	echo "<table border = 1><tr>";
	while($row=sqlite_fetch_array($result,SQLITE_ASSOC)) 
	{
		echo "<td>".$row['user.username']."(".$row['user.country'].")</td>";
	}
	echo "</tr></table></br>";	
?>

<h3>Send a message:</h3>
<form method="POST">
	To: 
<?php
	$result=sqlite_query($db,"SELECT * from user, messages WHERE user.ID = messages.ID");
	echo"<select name='users'>";
	while($row=sqlite_fetch_array($result,SQLITE_ASSOC))
	{
		echo "<option user = '".$row['user.username']."'>".$row['user.username']."</option>";
	}
	echo "</select>";
?>
	Message: <input type="text" name="message">
	<input type="submit" value="Send Message"><br/><br/>
</form>

<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST') 
	{
		$pass = true;
		
		$tousername = $_POST['users'];
		$fromusername = $currentUser;
		$ddate = date("Y/m/d");
		$ttime = date("h:i:sa");
		
		//Error check message input
		if ($_POST["message"] == "") {
			echo "Message is required<br/>";
			$pass = false;
		} 
		elseif(preg_match('/^[a-z0-9 \?\!\.\-]+$/i', $_POST['message'])) {
			$message = test_input($_POST['message']);
			$pass = true;
		}
		else {
			echo "Invalid character used in message<br/>";
			$pass = false;
		}
		
		//If pass error check
		if($pass === true)
		{
			$all=sqlite_query($db,"SELECT * from user, messages WHERE user.ID = messages.ID");
			$current_ID = count(sqlite_fetch_all($all, SQLITE_NUM))+1;
			
			sqlite_query($db, "INSERT INTO messages VALUES ($current_ID, '$tousername', '$fromusername', '$message', '$ddate', '$ttime')");
		}
	}
	
	echo "<h3>Display all messages:</h3>";
	echo "<table border = 1>
			<tr>
				<th><a href='loggedin.php?sort=tousername'>To</th>
				<th><a href='loggedin.php?sort=fromusername'>From</th>
				<th><a href='loggedin.php?sort=message'>Message</th>
				<th><a href='loggedin.php?sort=ddate'>Date</th>
				<th><a href='loggedin.php?sort=ttime'>Time</th>
			</tr>";

//Sort Table			
if (isset($_GET['sort']))
{
	$order = $_GET['sort'];
	$resultTo = sqlite_query($db, "SELECT * from messages, user WHERE messages.tousername = user.username ORDER BY messages.$order");
	$resultFrom=sqlite_query($db,"SELECT * from messages, user WHERE messages.fromusername = user.username ORDER BY messages.$order");
}
else
{
	$resultTo=sqlite_query($db,"SELECT * from messages, user WHERE messages.tousername = user.username");
	$resultFrom=sqlite_query($db,"SELECT * from messages, user WHERE messages.fromusername = user.username");
}
	while(($rowt=sqlite_fetch_array($resultTo,SQLITE_ASSOC)) && (($rowf=sqlite_fetch_array($resultFrom,SQLITE_ASSOC))))
	{
		//Ad Sense
		$adCountry = $rowt['user.country'];
		switch($adCountry){
			case 'UK':
				adCheck($adSense['UK']);
				break;
			case 'USA':
				adCheck($adSense['USA']);
				break;
			case 'Germany':
				adCheck($adSense['Germany']);
				break;
			case 'China':
				adCheck($adSense['China']);
				break;
			default:
				break;
		}
		
		//Display Messages
		if($rowt['messages.tousername'] === $currentUser || $rowf['messages.fromusername'] === $currentUser)
		{
		echo "<tr><td>".$rowt['messages.tousername']."(".$rowt['user.country'].")</td>";
		echo "<td>".$rowf['messages.fromusername']."(".$rowf['user.country'].")</td>";
		echo "<td>".$rowf['messages.message']."</td>";
		echo "<td>".$rowf['messages.ddate']."</td>";
		echo "<td>".$rowf['messages.ttime']."</td></tr>";
		}
	}
	echo "</table></br>";	
	if ($adsense === true)
	{
		echo "Only 5p per minute to call your friends in ".$adCountry."!!";
	}
	else
	{
		echo "Keep messaging to get offers!";
	}
		
		echo $adSense['UK'];
		echo $adSense['USA'];
		echo $adSense['Germany'];
		echo $adSense['China'];
		
	sqlite_close($db);
?>
<a href="login.php"><h1>Logout</h1></a>
</html>