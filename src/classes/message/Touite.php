<?php

namespace touiteur\message;

use DateTime;

class Touite {
	
	protected string $texte;
	protected string $utilisateur;

	protected DateTime $date;
	
	
	public function __construct(string $t, string $u, DateTime $d = new DateTime()) {
		$this->texte = $t;
		$this->utilisateur = $u;
		$this->date = $d;
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
		return $this->date->format('d/m/Y à H:i');
	}
	
	public function __get(string $att): mixed {
		if (property_exists($this, $att)) {
			return $this->$att;
		}

		return null;
	}
	
	
	
	
	
}