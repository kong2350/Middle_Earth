<?php

$name_error = $password_error = "";

if (isset($_POST['register'])) {
	$errors = array(); 
	
	// Sanitize and check for correct input
	if (empty($_POST['name'])) {
		$name_error = '<span class=error>* Please enter a user name.</span>';
		$errors[] = "yep";
	} else {
		$n = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	}
	
	// Check for a password match:
	if (!empty($_POST['pass1'])) {
		if ($_POST['pass1'] != $_POST['pass2']) {
			$password_error= '<span class=error>* Your passwords did not match.</span>';
			$errors[] = "yep";
		} else {
			$p = htmlentities(trim($_POST['pass1']));
		}
	} else {
		$password_error = '<span class=error> * Please enter password(s).</span>';
		$errors[] = "yep";
	}
	
	if (empty($errors)) { 
	try {
		$sql = $conn->prepare("INSERT INTO user (name, pass) VALUES (:n, SHA1(:p))");		
				
		$sql->bindParam(':n', $n);
		$sql->bindParam(':p', $p);
		$sql->execute();
		$success = true;
		
}
	catch (PDOException $e) {
		//echo $e->getMessage();
		$success = false;
	echo "<br><span class=error><b>User already exists. Please try again.</b></span></div>";
	
}

if ($success) {
	
			echo '<span class="body_lg"><b>Thank you!</b></span><br /><span class="body">You are now registered,  log in above.</span>';	

		   $u_id = $conn->lastInsertId();
		   $_SESSION['name'] = $n;
		   $_SESSION['u_id'] = $u_id;
		   echo "<script>location='index.php'</script>";
		
		} else {
			//echo $e;
			echo '<br><span class="error">You are not registered.</span>'; 
			
		} 
	}
} 
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

	<span class="body"><b>Register</b></span><br />
	<span class="body_sm">User Name:</span> <input type="text"  name="name" size="15" maxlength="20" value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>" /> <?php echo $name_error; ?><br>
	<span class="body_sm">Password:</span> <input type="password" name="pass1" size="15" maxlength="20" /><?php echo $password_error; ?><br>
	<span class="body_sm">Retype Password:</span> <input type="password" name="pass2" size="15" maxlength="20" /><?php echo $password_error; ?>
	<input type="submit" name="register" value="Register" />
	<input type="hidden" name="register" value="TRUE" />
	<!--<br><font class="error">* All fields required</font>-->

</form>