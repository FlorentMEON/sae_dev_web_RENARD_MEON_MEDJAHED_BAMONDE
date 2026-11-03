<?php

namespace touiteur\message\Touite;

class Touite {
	
	protected string $texte;
	protected string $utilisateur;
	
	
	public function __construct(string $t, string $u) {
		$this->texte = $t;
		$this->utilisateur = $u;
	}
	
	public function getTags() {
		$tags = [];
		$pattern = '/#([a-zA-Z0-9_]+)/';
		if (preg_match_all($pattern, $this->texte, $matches)) {
		$tags = $matches[0];
		}
		return $tags;
	}
	
	public function __get(string $att): mixed {
		if (property_exists($this, $att)) {
			return $this->$att;
		}

		return null;
	}
	
	
	
	
	
}