<?php 

namespace src\classes\Message\Touitelist;

class TouiteList {
	
	protected array $touites;
	
	
	public function __construct(array $t = []) {
		if(!(empty($t))) {
			$this->touites = $t;
		}
	}
	
	
	public function ajoutTouite (Touite $t) {
		$this->touites[] = $t;
		$this->nbTouite += 1;
	}
	
	public function getNbTouite(): int {
		$i = 0;
		foreach($t as $to) {
		$i += 1;
		}
		return $i;
	}
	
	
}