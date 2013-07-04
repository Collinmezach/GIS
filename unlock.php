<?php
session_start();
if(!$_SESSION['check']) {
	header("location:/login.php");
}
else {
	include("functions.php");
		$suc = unlock($_GET['id']);
		if(!$suc) {
			echo("error");
		}
		else {
			header("location:/index.php");
		}
			
}