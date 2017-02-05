<?php 
// PDO connection
try { 

<<<<<<< Updated upstream
=======
	$conn = new PDO('mysql:host=localhost;dbname=bizsnap3_middle_earth','bizsnap3', 'v?n@Xm++');
	$GLOBALS['connect'] = new PDO('mysql:host=localhost;dbname=bizsnap3_middle_earth','bizsnap3', 'v?n@Xm++');

	
	
	//$conn = new PDO('mysql:host=localhost;dbname=bizsnap3_middle_earth','bizsnap3_chauc', 'cuahc2352Q');
	//$GLOBALS['connect'] = new PDO('mysql:host=localhost;dbname=bizsnap3_middle_earth','bizsnap3_chau', 'cuahc2352Q');
>>>>>>> Stashed changes
	
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$GLOBALS['connect']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
	echo "Could not connect to database";
	echo $e;
}
?>
