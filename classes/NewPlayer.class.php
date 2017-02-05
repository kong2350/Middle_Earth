<?php

class NewPlayer
{
	public $errors = array(); 
	public $conn;
	public $u_id;
	
	public function __construct () {
		
		$this->u_id = $_SESSION['u_id'];
		

	global $conn;
    $this->conn=$conn;
    
	if (empty($_POST['newName'])) {
		$errors[] = "yep";
	} else {
		 //echo $_POST['name'];
		$n = filter_var($_POST['newName'], FILTER_SANITIZE_STRING);
	}
	
	/////Analyze character input and make adjustments
		if ($_POST['type'] == "Warrior"){//Warrior
			$hit_points = 10 + rand(-1,3);
			$offense = rand(1, 6);
			$defense = rand(-2, 6);
			}
		if ($_POST['type'] == "Sorcerer"){//Sorcerer
			$hit_points = 4 + rand(-1,3);
			$offense = rand(-2, 6);
			$defense = rand(-2, 6);
			}
		if ($_POST['type'] == "Cleric"){//Cleric
			$hit_points = 8 + rand(-1,3);
			$offense = rand(-2, 6);
			$defense = rand(-2, 6);
			}
		
		$t = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
		$r = filter_var($_POST['race'], FILTER_SANITIZE_STRING);
		$hp = $hit_points;
		$o = $offense;
		$d = $defense;
		$w = filter_var($_POST['weapon'], FILTER_SANITIZE_STRING);
		if ($_POST['type'] == "Cleric"){
		$m = filter_var($_POST['cleric_magic'], FILTER_SANITIZE_STRING);
		 } else  {
		 $m = filter_var($_POST['sorc_magic'], FILTER_SANITIZE_STRING);
		 }
    	$g = 100;

	if (empty($errors)) { 
	try {
		$sql = $conn->prepare("INSERT INTO player (ch_name, type, race, hit_points, offense, defense, weapon, magic, gold, user) VALUES (:n, :t, :r, :hp, :o, :d, :w, :m, :g, :uid)");		
		$sql->bindParam(':n', $n);
		$sql->bindParam(':t', $t);
		$sql->bindParam(':r', $r);
		$sql->bindParam(':hp', $hp);
		$sql->bindParam(':o', $o);
		$sql->bindParam(':d', $d);
		$sql->bindParam(':w', $w);
		$sql->bindParam(':m', $m);
		$sql->bindParam(':g', $g);
		$sql->bindParam(':uid', $this->u_id);
		$sql->execute();
}
catch (PDOException $e) {
	echo $e->getMessage();
	echo "<br><div align=center><span class=body_wh_lg><b>Duplicate name<br>Please try again.</b></span></div>";
} 
}}}
?>