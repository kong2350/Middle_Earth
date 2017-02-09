<?php

require_once ('conn/pdo_connect.php');

spl_autoload_register(function ($classname) {
	require __DIR__ . "/classes/"
	. str_replace("\\", DIRECTORY_SEPARATOR, $classname)
	. ".class.php";
});
include ('includes/login_check.php');
include ('includes/core.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>MIDDLE EARTH</title>
<LINK REL=StyleSheet HREF="includes/styles.css" TYPE="text/css" MEDIA=screen>
<script type="text/javascript" src="jquery/jquery.js"></script>
<script type="text/javascript" src="includes/core.js"></script>
</head>
<body>
<!--<div align=center><span class=title>MEN & MONSTERS</span></div>-->
<?php
//////////   initialize session variables
if (!isset($_SESSION['fight'])){ $_SESSION['fight'] = "over";}
if (!isset($_SESSION['killed'])) { $_SESSION['killed'] = "";}
if (!isset($_SESSION['level'])) { $_SESSION['level'] = "1";}
if (!isset($_SESSION['backs'])) { $_SESSION['backs'] = "1";}
if (!isset($_SESSION['totalSlain'])) { $_SESSION['totalSlain'] = "0";}
if (!isset($_SESSION['totalChars'])) { $_SESSION['totalChars'] = 0;}
if (!isset($_SESSION['num_players'])){ $_SESSION['num_players']= 0;}
if (!isset($_SESSION['pl_magic_clicked'])) {$_SESSION['pl_magic_clicked'] = array();}

$magic_count  = 0;

?>
    <script>
   			background("<?php echo $_SESSION['backs']; ?>");
    </script>
<?php
	 
///// Engage in battle
if (isset($_POST['melee'])) {
	new Melee($_SESSION['playerTeam'], $_SESSION['monsterTeam']);
}

///// Create a new player DB entry
if (isset($_POST['newName'])) {
	new NewPlayer();
}

//////////  Choose player instance
if (isset($_POST['choose_player'])) {
	$sql = $conn->prepare("SELECT * FROM player where id = ". $_POST['player'] . " ");
	$sql->execute();
	
	$_SESSION['fight'] = "on";
	while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
		$id = $row['id'];
		$n = $row['ch_name'];
		$t = $row['type'];
		$r = $row['race'];
		$hp = $row['hit_points'];
		$o = $row['offense'];
		$d = $row['defense'];
		$w = $row['weapon'];
		$m = $row['magic'];
		$g = $row['gold'];
		$lvl = $row['lvl'];
		$exp_pts = $row['exp_pts'];
	}
	$_SESSION['playerSess'] = new Player($id, $n,$t,$r,$hp,$o,$d,$w,$m,$g, $lvl, $exp_pts);
	new PlayerTeam($_SESSION['playerSess']);	
}

////////// Create monsters and monster team
if (!isset($_SESSION['monstSess']) && ($_SESSION['fight'] == "on")){
	$_SESSION['num_monsters'] = rand(1,4);
	
	if ($_SESSION['level'] < 8) {
	$monster_lvl = $_SESSION['level'];
	} else {
		$monster_lvl = 8;
	}
	
	for ($i = 0; $i < $_SESSION['num_monsters']; $i++){
		$monst = rand(1, $monster_lvl );

		 $sql_m = $conn->prepare("SELECT * FROM monster WHERE frq=$monst");
		 $sql_m->execute();
	
		while($row = $sql_m->fetch(PDO::FETCH_ASSOC)) {
			$id = $row['m_id'];
			$r = $row['race'];
			$hp = $row['hit_points'] + rand(-2,2);
			$o = $row['offense'];
			$d = $row['defense'];
			$g = $row['gold'];
			$exp = $row['exp_pts'];
			$frq = $row['frq'];
			
			$_SESSION['monstSess'] = new Monster($id, $r,$hp,$o,$d,$g,$exp, $frq);
			new MonsterTeam($_SESSION['monstSess']);
		}
	}
}

////////// Remove player from active team / update DB
if (isset($_POST['retreat'])) {
	$addBack = $_SESSION['playerTeam'][$_POST['retreat']]->id;
	$hit_points = $_SESSION['playerTeam'][$_POST['retreat']]->hit_points;
	$sql = $conn->prepare("UPDATE player SET hit_points=".$hit_points." WHERE id='".$addBack."' ");	
	$sql->execute();
		
	// add player back to dropdown list
	$_SESSION['selectedChars'] = array_diff($_SESSION['selectedChars'], array($addBack));
	unset($_SESSION['playerTeam'][$_POST['retreat']]);
	$_SESSION['playerTeam'] = array_values($_SESSION['playerTeam']);
	$_SESSION['num_players'] -= 1;

}

////////// Heal player update DB
if (isset($_POST['heal'])) {
	$heal = $_SESSION['playerTeam'][$_POST['heal']]->id;
	$sql = $conn->prepare("SELECT player.type, ch_type.type, hitdice FROM player JOIN ch_type WHERE  player.type = player.type AND id='".$heal."' ");	
	$sql->execute();
	$row = $sql->fetch(PDO::FETCH_ASSOC);
	
	$hitdice =  $row['hitdice'];
	$hit_points = $_SESSION['playerTeam'][$_POST['heal']]->hit_points += $hitdice;
	
	$sql = $conn->prepare("UPDATE player SET hit_points=".$hit_points." WHERE id='".$heal."' ");	
	$sql->execute();
	
	$gold = ceil($_SESSION['playerTeam'][$_POST['heal']]->gold -= 250);
	$sql = $conn->prepare("UPDATE player SET gold=".$gold." WHERE id='".$heal."' ");	
	$sql->execute();
}

////////// Magic spell attack, monster pulse check
if (isset($_POST['pl_clicked'])) {

	$pl_clicked = $_POST['pl_clicked'];
	
	foreach( $pl_clicked as $key => $n ) {
  		//echo "The player index number is ".$n;
	}

	$magic = $_POST['magic'][$n];
	$monster_attacked = $_POST['monst_choice'][$n]-1;
	$player_attacking = $_POST['pl_magic_clicked'][$n];
	$monst_damage = $_POST['monst_damage'][$n];
	
	///remove magic info from player when spell is cast, checked in array for display
	$_SESSION['pl_magic_clicked'][] = $_POST['pl_magic_clicked'][$n];
	
	////Adjust SESSION hitpoints
	$_SESSION['monsterTeam'][$monster_attacked]->hit_points -= $monst_damage;
	
	/////Call melee class if monster is killed
	if ($_SESSION['monsterTeam'][$monster_attacked]->hit_points <=0) {
		 //new Melee($_SESSION['playerTeam'], $_SESSION['monsterTeam'], $monster_attacked );
		 new MagicAttack( $_SESSION['playerTeam'], $_SESSION['monsterTeam'], $monster_attacked );
	}
}

?>

<form id="main_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<?php
////////////////////////////////////////////////
//// Display game field ////////////////////////
////////////////////////////////////////////////
if($_SESSION['fight'] == "on") {
	echo "<table class=stats width=100%><tr><td><span class=body_lg><b>Monsters</b></span> <span class=body> - Total Slain ". $_SESSION['totalSlain']."</span></td></tr></table>";
 	echo "<center><table border=0 cellspacing=6 height=150><tr>";
		
	for ($i=0; $i < $_SESSION['num_monsters']; $i++) {

		//if (($_SESSION['monsterTeam'][$i] == null) || ($_SESSION['monsterTeam'][$i]->race == "undefined")) continue;
		echo "<td valign=bottom><span class=body_wh_sm>";
		$m_race = $_SESSION['monsterTeam'][$i]->race;
		$m_hp = $_SESSION['monsterTeam'][$i]->hit_points;
		
		echo "<img src=img/monst/".$m_race.".png height=100 class=glow><br>";
		echo $m_race . " <br><b>". $m_hp. "</b> hit points";
		echo "</span></td>";
	}
	echo "</tr></table></center>";
	echo "<table class=stats width=100%><tr><td><span class=body_lg><b>Players</b></span> <span class=body align=center> - Level ". $_SESSION['level']."</span></td></tr></table>";
	echo "<div align=center id=player_stats><table border=0 cellspacing=6 height=150><tr>";
	
	
	if (isset($_SESSION['playerTeam'])){
		for ($i=0; $i < $_SESSION['num_players']; $i++) {
			
			//print_r($_SESSION['playerTeam'][$i]);
			
			echo "<td valign=top><span class=body_wh_sm>";
			
			///display characters and place in array for dropdown list exclusion
			$pl_id =$_SESSION['playerTeam'][$i]->id;
			$pl_name =$_SESSION['playerTeam'][$i]->name;
			$pl_type = $_SESSION['playerTeam'][$i]->type;
			$pl_race = $_SESSION['playerTeam'][$i]->race;
			$pl_lvl = $_SESSION['playerTeam'][$i]->lvl;
			$pl_hp = $_SESSION['playerTeam'][$i]->hit_points;
			$gold = ceil($_SESSION['playerTeam'][$i]->gold);
			$weapon = $_SESSION['playerTeam'][$i]->weapon;
			$magic = $_SESSION['playerTeam'][$i]->magic;
			if ($pl_hp < 5) {
					$glow = "class=redglow";
			} else {
			 $glow = "class=glow";
		}
		
			echo "<img src=img/player/".$pl_race.".png height=120 ".$glow." id=ch_".$i."><br>";
			if ($pl_race == "human_f" ||  $pl_race == "human_m" ){$pl_race = "human";}
			
			echo "<b>". $pl_name ."</b> - ". $pl_race . " <button type=submit name=retreat value=$i class=btn_sm>X</button><br>Level ".$pl_lvl." ".$pl_type."<b><br>". $pl_hp. "</b> hit points<br>". $gold. "</b> gold<br></span>";

			//echo "<button type=submit name=retreat value=$i class=btn_sm>X</button><br> " ;
			if ($gold > 350) {
				echo " <button type=submit name=heal value=$i class=btn_sm>Heal</button><br>";
			}	
			
			//display magic spell if available
			if ( $pl_type == "Sorcerer" || $pl_type == "Cleric") {
				
				$sql = $conn->prepare("SELECT mag_id, name, damage FROM magic WHERE mag_id=$magic");
				$sql->execute();
				$row = $sql->fetch(PDO::FETCH_ASSOC);

				if (!in_array($pl_id, $_SESSION['pl_magic_clicked'] )) {
					echo "<input type=\"hidden\" name=\"pl_magic_clicked[]\" value=".$pl_id.">";
					echo "<input type=\"hidden\"  name=\"monst_damage[]\" value=".$row['damage'].">";
					echo " <input type=\"hidden\" name=\"magic[]\" value=".$row['name']." class=\"btn_sm\">";
					
					echo " <button name=\"pl_clicked[]\" value=".$magic_count." type=\"submit\" class=\"btn_sm\">".$row['name']."</button><br>";
					
					echo "<select name=monst_choice[] class=\"btn_sm\">";
					for ($t=1; $t < $_SESSION['num_monsters']+1; $t++) {
        				echo "<option value='".$t."' >".$t."-".$_SESSION['monsterTeam'][$t-1]->race . "</option>";
    				}
    				echo "</select>";
    			$magic_count++;
					
		}
				}	
				echo "</td>";
			}
		echo "</tr></table></div>";
	}
}

	///Show appropriate button
	if((isset($_SESSION['playerTeam'])) && (isset($_SESSION['monsterTeam']))){
		echo "<div align=center><input type='submit' name='melee' value='ENGAGE!' class='btn_red'></div>";
	} elseif((isset($_SESSION['playerTeam'])) && (!isset($_SESSION['monsterTeam']))) {
		echo "<div align=center><input type='submit' name='continue' value='Continue!'></div>";
	} elseif (isset($_SESSION['u_id'])) { 
	echo "<center><span class=message><bR><bR><img src=img/mi_logo.png><br><br><br><br>Create ".$_SESSION['name']."'s team to begin.</span></center>";
	} else  { 
	echo "<center><span class=message><bR><bR><img src=img/mi_logo.png><br><br><br><br><br>Login or Register to start</span></center>";
	}

	// Instructions
	echo "<div class=footer><table cellpadding=7 height=200><tr><td bgcolor=FFFFFF valign=top class=stats>
	<span class=body><b>How To Play</b></span><br><span class=body_sm>Log in or Register to the right and create your own team.  Create up to 10 characters, then select your team from the drop down list.<br><br>Start the melee by clicking 'ENGAGE'. Damage will be assessed for players and monsters each melee round. Characters that drop below 5 hit points will glow red. You can remove a character from the active fight by clicking 'X'. Hit points can be purchased with 350 gold by clicking the heal buuton. Sorcerers and clerics can launch targeted magic attacks once every 5 stages. Select the monster and click the spell.<br><br>A slain monsters's experience points and gold are divided equally among the current team. The tougher the monster, the more rewarded. When a certain amount of experience points are attained, a character rises a level, resulting in more hit points and attack strength. After every 5 stages, all surviving characters rejuvenate by gaining back the maximum number of hit points according to their current level.
	<br><br>Strategy involves choosing the right number of characters for the fight to maximize experience points and treasure. Kill THEM before YOUR team is eliminated.</span></td>";

	if (isset($_SESSION['u_id'])) {
	
		$sql_team = $conn->prepare("SELECT id, ch_name FROM player where hit_points > 0 AND user=$u_id ORDER BY ch_name");
		$sql_team->execute();
		echo "<td bgcolor=FFFFFF valign=top class=stats width=100>";
		
		$totalChars = $sql_team->rowcount();
		$_SESSION['totalChars'] = $sql_team->rowcount();

		if ($totalChars  > $_SESSION['num_players'])  {
			 echo "<span class=body><b>Add character to  " .$_SESSION['name'] ."'s team:<br><select name='player'>";
		    while($row = $sql_team->fetch(PDO::FETCH_ASSOC)) {
		    	$_SESSION['totalChars']++;
		    	if (in_array($row['id'], $_SESSION['selectedChars'] )) {echo "";}else{
		        echo "<option value='". $row['id'] ."'>".$row['ch_name']. "</option>";
		     }
		  }
		
		echo "</select></b><span><br>";
		echo "<input type='submit' name='choose_player' value='Select Player' class='btn'><br>";
	} else {
		echo "<b>--------------></b>";
	}
	
	 echo "<input type='submit' name='logout' value='LogOut' class='btn'>";
	 echo "</td>";

/////////////////////////////////////////////////////////////////////////////////////////
?>
</form><form id="char_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<?php

	if ($totalChars  < 10) {
		//// Create a character
		$sql_ch_type = $conn->prepare("SELECT type_id, type FROM ch_type ORDER BY type");
		$sql_ch_type->execute();
		$sql_race = $conn->prepare("SELECT img, race FROM race ORDER BY race");
		$sql_race->execute();
		$sql_weapon = $conn->prepare("SELECT w_id, type FROM weapons ORDER BY type");
		$sql_weapon->execute();
		$sql_magic_sorc = $conn->prepare("SELECT mag_id, ch, name FROM magic WHERE ch=1 ORDER BY name");
		$sql_magic_sorc->execute();
		$sql_magic_cleric = $conn->prepare("SELECT mag_id, ch, name FROM magic WHERE ch=2 ORDER BY name");
		$sql_magic_cleric->execute();
		
		echo "<td bgcolor=FFFFFF class=stats width=200 valign=top><span class=body>
	
		<b>Create a character:</b></span><br><span class=body_sm>Name <input type=text name=newName id=char_name value=\"\" size=15><br><span class=\"error_hide\" id=\"name_error\">* Please enter your character name.</span><br>";
	
		echo "Type <select name=type id=type_drop><option value=''>";
		while($row = $sql_ch_type->fetch(PDO::FETCH_ASSOC)) {
	        echo "<option value='". $row['type'] ."'>".$row['type']. "</option>";
	    }
	    echo "</select><br><span class=\"error_hide\" id=\"type_error\">* Please select your character type.</span>";
	
		echo "<br>Race <select name=race>";
		while($row = $sql_race->fetch(PDO::FETCH_ASSOC)) {
		        echo "<option value='". $row['img'] ."'>".$row['race']. "</option>";
	        }
	        echo "</select>";
		
		echo "<br>Weapon <select name=weapon>";
		while($row = $sql_weapon->fetch(PDO::FETCH_ASSOC)) {
		        echo "<option value='". $row['w_id'] ."'>".$row['type']. "</option>";
		}
		echo "</select>";
		
		echo "<div id=cleric_drop><br>Magic <select name=cleric_magic >";
		while($row = $sql_magic_cleric->fetch(PDO::FETCH_ASSOC)) {
		        echo "<option value='". $row['mag_id'] ."'>".$row['name']. "</option>";
		    }
		    echo "</select></div>";
		    
		echo "<div id=magic_drop><br>Magic <select name=sorc_magic >";
		while($row = $sql_magic_sorc->fetch(PDO::FETCH_ASSOC)) {
		        echo "<option value='". $row['mag_id'] ."'>".$row['name']. "</option>";
		}
		echo "</select></div>";
		    
		
		echo "</span><br>";
		echo "<div id=\"char_submit\"><button type=submit>Create</div>";
		echo "</form></td></tr></table>";
		} else {
			echo "<td bgcolor=FFFFFF class=stats width=200 valign=top><span class=body><b>You've reached the 10 character limit. If there's  a death on your team, create another.</b></span></td></tr></table>";
		}
	} else {
		echo "<td width=300 class=stats valign=top>";
		include ('includes/login.php');
		echo "<hr>";
		include ('includes/register.php');
		echo "</td></tr></table>";
	}
?>

</div>
</body>
</html>
