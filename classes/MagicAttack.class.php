<?php

class MagicAttack
{
  public $update;
  public $conn;

  public function __construct ($playerTeam, $monsterTeam, $magic_attack) {

  global $conn;
  $this->conn=$conn;

  //// Only called on death of monster with magic
	if ($magic_attack !== null) {
		$monster_attacked = $magic_attack;

		//// update DB for hitpoints and gold distrubution
		$_SESSION['totalSlain']++;
		$_SESSION['killed'] = "<b>You smote the " . $monsterTeam[$monster_attacked]->race. "!</b><br>";
		echo "<div id=skull align=center><img src=img/skull.png height=350><br><table><tr><td bgcolor=#333><span class=body_wh_lg><b>";
		echo $_SESSION['killed'];
		echo "</b></span></td></tr></table></div>";
		echo "<script>showSkull();</script>";

		for ($i = 0; $i < $_SESSION['num_players']; $i++){

			$hit_points = $playerTeam[$i]->hit_points;
			$gold = $playerTeam[$i]->gold;
			$id = $playerTeam[$i]->id;

			$playerTeam[$i]->exp_pts +=  ($monsterTeam[$monster_attacked]->exp_pts / $_SESSION['num_players']);
			$playerTeam[$i]->gold +=  ($monsterTeam[$monster_attacked]->gold / $_SESSION['num_players']);
			$exp_pts = $playerTeam[$i]->exp_pts;
			$gold = $playerTeam[$i]->gold;

			$sql = $this->conn->prepare("UPDATE player SET hit_points=". $hit_points.", exp_pts=". $exp_pts ." , gold=".$gold." WHERE id='".$id."' ");
			$sql->execute();

			//// Stored procedure call to adjust player level
			/*$sql_stored = 'CALL adjustLevel(:id)';
    		$stmt = $conn->prepare($sql_stored);

    		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
   			$stmt->execute();
    		$stmt->closeCursor();*/

      $sql = $this->conn->prepare("SELECT exp_pts, lvl, hit_points FROM player WHERE id = $id");
      $sql->execute();

      $row = $sql->fetch(PDO::FETCH_ASSOC);
    	$expPts = $row["exp_pts"];
    	$currentLvl = $row["lvl"];
    	$hitPts = $row["hit_points"];

    IF ($expPts < 1000) {
     $newLevel = 1;
     } ELSEIF ($expPts  >= 1000 && $expPts  <= 2999) {
        $newLevel = 2;
     } ELSEIF ($expPts  >= 3000 && $expPts  <= 5999)  {
        $newLevel = 3;
     } ELSEIF ($expPts  >= 6000 && $expPts  <= 9999)  {
        $newLevel = 4;
     } ELSEIF ($expPts  >= 10000 && $expPts  <= 14999)  {
        $newLevel = 5;
     } ELSEIF ($expPts  >= 15000 && $expPts  <= 20999)  {
        $newLevel = 6;
     } ELSEIF ($expPts  >= 21000 && $expPts  <= 27999)  {
        $newLevel = 7;
     } ELSEIF ($expPts  >= 28000 && $expPts  <= 35999)  {
        $newLevel = 8;
     } ELSEIF ($expPts  >= 36000 && $expPts  <= 44999)  {
        $newLevel = 9;
     }

 $sql = $this->conn->prepare("UPDATE player SET lvl=$newLevel WHERE id = $id");
 $sql->execute();

  IF ($currentLvl < $newLevel) {
  $hitPts =  $hitPts + 12;
    $sql = $this->conn->prepare("UPDATE player SET hit_points=$hitPts  WHERE id = $id");
     $sql->execute();
 }

			//// Update on screen for level and hit points
			$sql_update = $this->conn->prepare("SELECT hit_points, lvl FROM player WHERE id=$id");
			$sql_update->execute();
			while($row = $sql_update->fetch(PDO::FETCH_ASSOC)) {
				$playerTeam[$i]->hit_points = $row['hit_points'];
        $playerTeam[$i]->lvl = $row['lvl'];
    		 }
		}

		//// delete monster, reset array index, deduct team count
		unset($_SESSION['monsterTeam'][$monster_attacked]);
		$_SESSION['monsterTeam'] = array_values($_SESSION['monsterTeam']);
		$_SESSION['num_monsters'] = $_SESSION['num_monsters']-1;
	}

  ///////////////////////////////////////////////////////////////////////////////////
	if ($_SESSION['num_monsters'] == 0) {
		unset($_SESSION['monsterTeam']);

		//// Next  dungeon level increase
		$_SESSION['level']++;
		if ($_SESSION['backs'] < 5) {
			$_SESSION['backs']++;
		} else {
			$_SESSION['backs'] = 1;
		}

		if ($_SESSION['level'] % 5 == 0) {
			$_SESSION['pl_magic_clicked'] = array();
			for ($i = 0; $i < $_SESSION['totalChars']; $i++) {

				$sql = $this->conn->prepare("SELECT id, player.type, ch_type.type, hitdice FROM player JOIN ch_type WHERE player.type = ch_type.type AND id='".$_SESSION['playerTeam'][$i]->id."'");
				$sql->execute();
				while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
					$hit_dice = $row['hitdice'];
					echo  $hit_dice;
 				}
				$hitpoints = ($_SESSION['playerTeam'][$i]->lvl * $hit_dice);
				//// Save hitpoints in DB
				$sql = $this->conn->prepare("UPDATE player SET hit_points = $hitpoints WHERE id='".$_SESSION['playerTeam'][$i]->id."'");
				$sql->execute();

			}
		}
	}

}
}
?>
