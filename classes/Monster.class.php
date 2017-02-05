<?php
//// Class constructs random monsters for array
class Monster extends Player
{
    public function __construct($id, $race, $hit_points, $offense, $defense, $gold, $exp_pts, $frq) {
        $this->id = $id;
        $this->race = $race;
        $this->hit_points = $hit_points;
        $this->offense = $offense;
        $this->defense = $defense;
        $this->gold = $gold;
        $this->exp_pts = $exp_pts;
        $this->frq = $frq;
    }	
}
?>