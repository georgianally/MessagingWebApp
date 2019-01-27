<?php
	$db=sqlite_open("userinformation.db");

	@sqlite_query($db, "DROP TABLE user");
	@sqlite_query($db, "DROP TABLE messages");

	sqlite_query($db, "CREATE TABLE user (ID integer, username varchar(20) , password varchar(20) , country varchar(20))", $error_msg);
	sqlite_query($db, "CREATE TABLE messages (ID integer, tousername varchar(20) , fromusername varchar(20) , message varchar(20) , ddate date , ttime time)", $error_msg);
?>