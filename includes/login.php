<?php


$name_error=$pass_error=$name=$pass= null;

if (isset($_POST["login"])) { 
		$errors = array(); 
		
		if (empty($_POST['name'])) {
			$name_error = '<span class=error>* Please enter user name.</span>';
			$errors[] = "yep";
		} else {
			$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
		}
		
		if (empty($_POST['pass'])) {
			$pass_error = '<span class=error>* Please enter password.</span>';
			$errors[] = "yep";
		} else {
			$pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
		}
	
	if (empty($errors)) { 
		$sql = $conn->prepare("SELECT * FROM user WHERE name=:name && pass=SHA1(:pass)");
		$sql->bindParam(':name', $name);
		$sql->bindParam(':pass', $pass);
		$sql->execute();
		
		$row = $sql->fetch(PDO::FETCH_ASSOC);
		$name = $row['name'];
		$u_id = $row['u_id'];

		if ($row==""){
			echo "Incorrect password or user name. Try again<br>";		
		} else {
		   $_SESSION['name'] = $name;
		   $_SESSION['u_id'] = $u_id;
		   echo "<script>location='index.php'</script>";
		   //header("location:index.php");		  
		}	
	}
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<font class="body"><b>Login</b></span><br>
	
    <span class="body_sm">User Name:</span> <input type="text" size="15" name="name"  value="<?php echo $name;?>">
    <span class="error"> <?php echo $name_error;?></span><br>
    <span class="body_sm">Password:</span> <input type="text" size="15" name="pass" value="<?php echo $pass;?>">
    <span class="error"> <?php echo $pass_error;?></span>
    <input type="submit" name="submit_login" value="Login"> 
    <input type="hidden" name="login" value="login">   	
   	
</form>