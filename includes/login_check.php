<?php
session_start(); 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (!empty($_POST["logout"])) { 
		session_destroy(); 
		session_unset();
		session_regenerate_id(true);
		header("location:index.php");
	}
} 

if (isset($_SESSION['u_id'])) {

		$name = $_SESSION['name'];
		$u_id = $_SESSION['u_id'];
 }
?>