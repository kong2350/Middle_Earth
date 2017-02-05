<?php

class Player
 {
 	public $id;
	public $name;
	public $type;
	public $race;
	public $hit_points;
	public $offense;
	public $defense;
	public $weapon;
	public $magic;
	public $gold;
	public $lvl;
	public $exp_pts;
	
	public function __construct($id, $name, $type, $race, $hit_points, $offense, $defense, $weapon, $magic, $gold, $lvl, $exp_pts) {
	
		$this->id= $id;
		$this->name = $name;
		$this->type = $type;
		$this->race = $race;
		$this->hit_points = $hit_points;
		$this->offense = $offense;
		$this->defense = $defense;
		$this->weapon = $weapon;
		$this->magic = $magic;
		$this->gold = $gold;
		$this->lvl = $lvl;
		$this->exp_pts = $exp_pts;
	}
}
?>