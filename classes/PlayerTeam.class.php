<?php

class PlayerTeam
 {

	public function PlayerTeam($player) {

		$_SESSION['playerTeam'][] = $player;		
		$_SESSION['num_players'] = $_SESSION['num_players'] +1;	
		$_SESSION['selectedChars'][] = $player->id;
		
	}
}

?>