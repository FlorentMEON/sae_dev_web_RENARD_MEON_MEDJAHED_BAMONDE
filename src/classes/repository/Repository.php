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
	public function getTouiteUtilisateur(string $nom) {
		$query = "SELECT t.id_touite, t.texte, t.date_publication, u.nom AS auteur 
                  FROM TOUITE t
                  INNER JOIN UTILISATEUR u ON t.id_auteur = u.id_utilisateur
                  WHERE u.nom = :nom
                  ORDER BY t.date_publication DESC";
		$stmt = $this->pdo->prepare($query);
		$secuNom = filter_var($nom, FILTER_SANITIZE_SPECIAL_CHARS);
		$stmt->execute(['nom' => $secuNom]);
		$lignes = $stmt->fetchAll();
		$liste = [];
		foreach ($lignes as $ligne) {
			$secuTexte = filter_var($ligne['texte'], FILTER_SANITIZE_SPECIAL_CHARS);
			$secuAuteur = filter_var($ligne['auteur'], FILTER_SANITIZE_SPECIAL_CHARS);
			$touite = new Touite($secuTexte, $secuAuteur);
			$liste[] = $touite;
		}
		return $liste
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
	
	public function ajoutTouite(string $n, string $texte) {
		$secuNom = filter_var($n, FILTER_SANITIZE_SPECIAL_CHARS);
		$secuTexte = filter_var($texte, FILTER_SANITIZE_SPECIAL_CHARS);
		$query1 = "SELECT id_utilisateur FROM UTILISATEUR WHERE nom = :nom";
        $stmt1 = $this->pdo->prepare($queryUser);
       	$stmt1->execute(['nom' => $secuNom]);
       	$Ligne1 = $stmt1->fetch();
		$idAuteur = $Ligne1['id_utilisateur'];
		$query2 = "INSERT INTO TOUITE (texte, date_publication, id_auteur) 
                   VALUES (:texte, NOW(), :id_auteur)";
       	$stmt2 = $this->pdo->prepare($queryTouite);
		$resultat = $stmtTouite->execute(['texte' => $secuTexte, 'id_auteur' => $idAuteur]);
	}
	
	public function ajoutUtilisateur(string $n) {
		$secuNom = filter_var($n, FILTER_SANITIZE_SPECIAL_CHARS);
		$query = "INSERT INTO UTILISATEUR (nom, nb_touites) VALUES (:nom, 0)";
		$stmt = $this->pdo->prepare($query);
		$stmt->execute(['nom' => $secuNom]);
	}
	
	
	
	
}
>