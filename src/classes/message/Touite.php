<?php

namespace touiteur\message;

use DateTime;

class Touite {
	
	protected string $texte;
	protected string $utilisateur;

	protected DateTime $date;
	
	
	public function __construct(string $t, string $u) {
		$this->texte = $t;
		$this->utilisateur = $u;
		$this->date = date_create("now",null);
	}
	
	public function getTags() {
		$tags = [];
		$pattern = '/#([a-zA-Z0-9_]+)/';
		if (preg_match_all($pattern, $this->texte, $matches)) {
		$tags = $matches[0];
		}
		return $tags;
	}
	
	public function getDate(){
		return $this->date->format('d,m,y');
	}
	
	public function __get(string $att): mixed {
		if (property_exists($this, $att)) {
			return $this->$att;
		}

		return null;
	}
	
	
	
	
	
}