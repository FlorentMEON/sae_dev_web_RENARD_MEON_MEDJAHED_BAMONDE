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
	
	public static function getInstance() {
		if(is_null(self::$instance)) {
			self::$instance = new Repository(self::$config);
		}
		return self::$instance;
	}
	

	public function getTouiteUtilisateur(string $nom) {
		$query = "SELECT t.id_touite, t.texte, t.date_publication, u.nom AS auteur 		
                  FROM TOUITE t
                  INNER JOIN UTILISATEUR u ON t.id_auteur = u.id_utilisateur
                  WHERE u.nom = :nom
                  ORDER BY t.date_publication DESC";			//Requete pour trouver les touite avec un nom d'utilisateur
		$stmt = $this->pdo->prepare($query);
		$secuNom = filter_var($nom, FILTER_SANITIZE_SPECIAL_CHARS);			
		$stmt->execute(['nom' => $secuNom]);
		$lignes = $stmt->fetchAll();			/// renvoie des resultats en tableau
		$liste = [];
		foreach ($lignes as $ligne) {     ///boucle pour ajouter les touites au tableau qui sera renvoyé
			$secuTexte = filter_var($ligne['texte'], FILTER_SANITIZE_SPECIAL_CHARS);
			$secuAuteur = filter_var($ligne['auteur'], FILTER_SANITIZE_SPECIAL_CHARS);				///Vérification du nom de l'utilisateur
			$touite = new Touite($secuTexte, $secuAuteur);
			$liste[] = $touite;
		}
		return $liste;
	}

	
	public function getTouiteTag(string $tag) {
		$query = "SELECT t.id_touite, t.texte, t.date_publication, u.nom AS auteur 		
                  FROM TOUITE t
                  INNER JOIN CONTENIR c ON t.id_touite = c.id_touite
                  INNER JOIN TAG tg ON c.id_tag = tg.id_tag
                  INNER JOIN UTILISATEUR u ON t.id_auteur = u.id_utilisateur
                  WHERE tg.libelle = :tag
                  ORDER BY t.date_publication DESC";///Requete pour trouver les touite avec un tag
		$stmt = $this->pdo->prepare($query);
		$secuTag = filter_var($tag, FILTER_SANITIZE_SPECIAL_CHARS);					///Vérification du tag
		$stmt->execute(['tag' => $secuTag]);
		$lignes = $stmt->fetchAll();					/// renvoie les resultats en tableau
		$liste = [];
		foreach ($lignes as $ligne) {			///boucle pour ajouter les touites au tableau qui sera renvoyé
			$secuTexte = filter_var($ligne['texte'], FILTER_SANITIZE_SPECIAL_CHARS);		///Vérification du texte
			$secuAuteur = filter_var($ligne['auteur'], FILTER_SANITIZE_SPECIAL_CHARS);       ///Vérification du nom de l'utilisateur
			$touite = new Touite($secuTexte, $secuAuteur);
			$touite->id = $ligne['id_touite'];
			$liste[] = $touite;
		}
		return $liste;
	}
	
	public function estUtilisateur(string $n) {
		$secuNom = filter_var($n, FILTER_SANITIZE_SPECIAL_CHARS);      ///Vérification du nom de l'utilisateur
		$query = "SELECT nom								
				  FROM utilisateur
				  WHERE nom = :nom";///Requete pour trouver l'ID de l'utilisateur mit en paramètre
		$stmt = $this->pdo->prepare($query);
		$stmt->execute(['nom' => $secuNom]);
		$ligne = $stmt->fetch();
		if ($ligne !== false) {
			return true;  			/// la méthode fetch renvoie false si elle ne trouve rien alors on teste son contenu
		}
		return false;			
	}
	
	public function ajoutTouite(string $n, string $texte) {
		$secuNom = filter_var($n, FILTER_SANITIZE_SPECIAL_CHARS);
		$secuTexte = filter_var($texte, FILTER_SANITIZE_SPECIAL_CHARS);
		$query1 = "SELECT id_utilisateur FROM UTILISATEUR WHERE nom = :nom";    ///Requete pour trouver l'ID de l'utilisateur mit en paramètre
		$stmt1 = $this->pdo->prepare($query1);
       	$stmt1->execute(['nom' => $secuNom]);			///Exécution de la commande
       	$Ligne1 = $stmt1->fetch();				
		$idAuteur = $Ligne1['id_utilisateur'];
		$query2 = "INSERT INTO TOUITE (texte, date_publication, id_auteur) 
                   VALUES (:texte, NOW(), :id_auteur)"; ///Requete pour ajouter un touite dans la BDD
		$stmt2 = $this->pdo->prepare($query2);
		$resultat = $stmt2->execute(['texte' => $secuTexte, 'id_auteur' => $idAuteur]);   ///Exécution de la commande
	}
	
	public function ajoutUtilisateur(string $n) {
		$secuNom = filter_var($n, FILTER_SANITIZE_SPECIAL_CHARS);       ///Vérification du nom de l'utilisateur
		$query = "INSERT INTO UTILISATEUR (nom, nb_touites) VALUES (:nom, 0)";    ///Requete pour ajouter un utilisateur dans la BDD
		$stmt = $this->pdo->prepare($query);      
		$stmt->execute(['nom' => $secuNom]);     ///Exécution de la commande
	}
}
