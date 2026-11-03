<?php 

namespace src\classes\message\Touitelist;

class TouiteList {
	
	protected array $touites;
	
	
	public function __construct() {
		$this->touites = [];
	}
	
	
	public function ajoutTouite (Touite $t) {
		$this->touites[] = $t;
	}
	
	public function getNbTouite(): int {
		return count($this->touites);
	}
	
	
}