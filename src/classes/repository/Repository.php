<?php

namespace touiteur\repository;

use touiteur\message\Touite;

class Repository {
	
	private \PDO $pdo;
	private static ?Repository $instance = null;
	private static array $config = [];
	
	private function __construct(array $conf) {
		$this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'],
			[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXECPTION,
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_STRINGIFY_FETCHES => false]);
		$this->pdo->prepare('SET NAMES \'UTF8\'')->execute();
	}
	
	public static function setConfig(string $file): void {
		$conf = parse_ini_file($file);
		if ($conf === false) {
			throw new Exception ("Erreur fichier de configuration invalide");
		}
		self::$config = [
			'dsn' => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}",
            'user' => $conf['username'],
            'pass' => $conf['password']];
	}
	
	public static function getInstance(string $file) {
		if(is_null(self::instance)) {
			self::instance = new Repository(self::$config);
		}
		return self::$instance;
	}
	
	
	/// VA ETRE MODIFIER POUR AVOIR EN FONCTION DES UTILISATEUR OU DES TAGS
	public function getTouite(int $idTouite) { 
		$query = "SELECT t.id_touite, t.texte, t.date_publication, u.nom AS auteur 
				  FROM TOUITE t
				  INNER JOIN UTILISATEUR u ON t.id_auteur = u.id_utilisateur
				  WHERE t.id_touite = :id";
		$stmt = $this->pdo->prepare($query);
		$secuId = filter_var($idTouite, FILTER_SANITIZE_NUMBER_INT);
		$stmt->execute(['id' => $secuId]);
		$ligne = $stmt->fetch();
		if ($ligne === false) {
			return null;
		}
		$secuTexte = filter_var($ligne['texte'], FILTER_SANITIZE_SPECIAL_CHARS);
		$secuAuteur = filter_var($ligne['auteur'], FILTER_SANITIZE_SPECIAL_CHARS);
		$touite = new Touite($secuTexte, $secuAuteur);
		return $touite;
	}
	
	public function estUtilisateur(string $n) {
		$secuNom = filter_var($n, FILTER_SANITIZE_SPECIAL_CHARS);
		$query = "SELECT nom		
				  FROM utilisateur
				  WHERE nom = :nom";
		$stmt = $this->pdo->prepare($query);
		$stmt->execute(['nom' => $secuNom]);
		$ligne = $stmt->fetch();
	}
	
	public function ajoutUtilisateur(string $n) {
	}
	
	public function ajoutTouite(string $n, string $texte) {
	}
	
	
}
>