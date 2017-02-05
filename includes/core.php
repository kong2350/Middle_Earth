<?php

////Clear SESSION vars with exceptions listed
if (isset($_POST['continue'])) {
$dontDelete = array('playerTeam', 'u_id', 'name', 'playerSess', 'fight', 'num_players', 'selectedChars', 'level','totalSlain','pl_magic_clicked','backs');
		foreach($_SESSION as $key => $value) {
	   		if (!in_array($key, $dontDelete)) {
	   			//echo $key;
			unset($_SESSION[$key]);
	   		}
	   }
}

?>