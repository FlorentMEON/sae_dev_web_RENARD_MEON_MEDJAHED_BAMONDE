<?php 

namespace touiteur\message;

class TouiteList implements \Iterator{
	
	protected array $touites;
	protected int $position = 0;
	
	
	public function __construct() {
		$this->touites = [];
	}
	
	public function ajoutTouite (Touite $t) {
		$this->touites[] = $t;
	}
	
	public function getNbTouite(): int {
		return count($this->touites);
	}

	public function rewind(): void {
        $this->position = 0;
    }

	public function current(): Touite{
        return $this->touites[$this->position];
    }

	public function key(): int {
        return $this->position;
    }

    public function next(): void {
        ++$this->position;
    }

    public function valid(): bool {
        return isset($this->touites[$this->position]);
    }
	
}