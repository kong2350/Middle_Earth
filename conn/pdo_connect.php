<?php 
// PDO connection
try { 

	//$conn = new PDO('mysql:host=localhost;dbname=middle_earth','root', '22jPxhy1');
	//$GLOBALS['connect'] = new PDO('mysql:host=localhost;dbname=middle_earth','root', '22jPxhy1');
        $conn = new PDO('mysql:host=localhost;dbname=middle_earth','root', 'root');
	$GLOBALS['connect'] = new PDO('mysql:host=localhost;dbname=middle_earth','root', 'root');
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$GLOBALS['connect']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
	echo "Could not connect to database";
}
?>