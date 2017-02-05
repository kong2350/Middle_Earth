<?php

class MonsterTeam
 {
 	
	public function MonsterTeam($monster) {

		$_SESSION['monsterTeam'][] = $monster;
		//foreach ($monster as $data){
		//	echo $data. ", ";
		//}
	}
}

?>

