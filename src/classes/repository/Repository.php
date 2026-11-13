<?php
namespace touiteur\repository;
use touiteur\message\Touite;

class Repository {
	
	private \PDO $pdo;
	private static ?Repository $instance = null;
	private static array $config = [];
	
	private function __construct(array $conf) {
		$this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'],
			[\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_PERSISTENT => true,
			\PDO::ATTR_EMULATE_PREPARES => false,
			\PDO::ATTR_STRINGIFY_FETCHES => false]);
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

	public function getTouites() {
		$query = "SELECT t.id_touite, t.texte, t.date_publication, u.nom AS auteur 		
                  FROM TOUITE t
                  INNER JOIN UTILISATEUR u ON t.id_auteur = u.id_utilisateur
                  ORDER BY t.date_publication DESC";			//Requete pour trouver les touite
		$stmt = $this->pdo->prepare($query);	
		$stmt->execute();
		$lignes = $stmt->fetchAll();			/// renvoie des resultats en tableau
		$liste = [];
		foreach ($lignes as $ligne) {     ///boucle pour ajouter les touites au tableau qui sera renvoyé
			$touite = new Touite($ligne['texte'], $ligne['auteur'], new DateTime($ligne['date_publication']));
			$touite->id = $ligne['id_touite'];
			$liste[] = $touite;
		}
		return $liste;
	}
	

	public function getTouiteUtilisateur(string $nom) {
		$query = "SELECT t.id_touite, t.texte, t.date_publication, u.nom AS auteur 		
                  FROM TOUITE t
                  INNER JOIN UTILISATEUR u ON t.id_auteur = u.id_utilisateur
                  WHERE u.nom = :nom
                  ORDER BY t.date_publication DESC";			//Requete pour trouver les touite avec un nom d'utilisateur
		$stmt = $this->pdo->prepare($query);	
		$stmt->execute(['nom' => $nom]);
		$lignes = $stmt->fetchAll();			/// renvoie des resultats en tableau
		$liste = [];
		foreach ($lignes as $ligne) {     ///boucle pour ajouter les touites au tableau qui sera renvoyé
			$touite = new Touite($ligne['texte'], $ligne['auteur'], new DateTime($ligne['date_publication']));
			$touite->id = $ligne['id_touite'];
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
		$stmt->execute(['tag' => $tag]);
		$lignes = $stmt->fetchAll();					/// renvoie les resultats en tableau
		$liste = [];
		foreach ($lignes as $ligne) {			///boucle pour ajouter les touites au tableau qui sera renvoyé
			$touite = new Touite($ligne['texte'], $ligne['auteur'], new DateTime($ligne['date_publication']));
			$touite->id = $ligne['id_touite'];
			$liste[] = $touite;
		}
		return $liste;
	}

	public function getTouitesUtiTag(array $utils, array $tags): array {
	    $touites = [];

	    // Création des paramètres pour les utilisateurs
	    $paramsUtils = [];
	    foreach ($utils as $i => $utilisateur) {
	        $paramsUtils[] = ":nom$i";
	    }

	    // Création des paramètres pour les tags
	    $paramsTags = [];
	    foreach ($tags as $i => $tag) {
	        $paramsTags[] = ":tag$i";
	    }

	    $conditions = [];
	    $params = [];

	    if (!empty($utils)) {
	        $conditions[] = "u.nom IN (" . implode(",", $paramsUtils) . ")";

	        foreach ($utils as $i => $utilisateur) {
	            $params["nom$i"] = $utilisateur;
	        }
	    }

	    if (!empty($tags)) {
	        $conditions[] = "tg.libelle IN (" . implode(",", $paramsTags) . ")";

	        foreach ($tags as $i => $tag) {
	            $params["tag$i"] = $tag;
	        }
	    }

	    // Évite une requête sans condition
	    if (empty($conditions)) {
	        return [];
	    }

	    $query = "SELECT DISTINCT t.id_touite, t.texte,
	                     t.date_publication, u.nom AS auteur
	              FROM touite t
	              INNER JOIN CONTENIR c ON t.id_touite = c.id_touite
	              INNER JOIN TAG tg ON c.id_tag = tg.id_tag
	              INNER JOIN UTILISATEUR u ON t.id_auteur = u.id_utilisateur
	              WHERE " . implode(" OR ", $conditions) . "
	              ORDER BY t.date_publication DESC";

	    $stmt = $this->pdo->prepare($query);
	    $stmt->execute($params);

	    $lignes = $stmt->fetchAll();

	    foreach ($lignes as $ligne) {
	        $touite = new Touite($ligne['texte'], $ligne['auteur'], new DateTime($ligne['date_publication']));
			$touite->id = $ligne['id_touite'];
	        $touites[] = $touite;
	    }

	    return $touites;
	}

	public function getTouiteById($id){
		$query = "SELECT t.id_touite, t.texte, t.date_publication, u.nom AS auteur 		
                  FROM TOUITE t
                  INNER JOIN UTILISATEUR u ON t.id_auteur = u.id_utilisateur
                  WHERE t.id_touite = :id;";		//Requete pour trouver les touite avec son id

		$stmt = $this->pdo->prepare($query);	
		$stmt->execute(['id' => $id]);
		$ligne = $stmt->fetch();			/// renvoie des resultats en tableau

		$touite = new Touite($ligne['texte'], $ligne['auteur'], new DateTime($ligne['date_publication']));
		$touite->id = $ligne['id_touite'];

		return $touite;
	}
	
	public function getIdUtilisateur(string $n) {
		$query = "SELECT nom								
				  FROM utilisateur
				  WHERE nom = :nom";///Requete pour trouver l'ID de l'utilisateur mit en paramètre
		$stmt = $this->pdo->prepare($query);
		$stmt->execute(['nom' => $n]);
		return $stmt->fetch(); /// renvoie l'ID de l'utilisateur ou false si il n'existe pas			
	}

	public function getIdTag(string $libelle) {
		$query = "SELECT id_tag								
				  FROM Tag
				  WHERE libelle = :libelle"; ///Requete pour trouver l'ID du tag mit en paramètre
		$stmt = $this->pdo->prepare($query);
		$stmt->execute(['libelle' => $libelle]);
		return $stmt->fetch(); /// renvoie l'ID du tag ou false si il n'existe pas
	}

	public function getInfluenceurs(){
		$query = "SELECT u.id_utilisateur, u.nom, u.prenom, COUNT(s.id_suivi) AS nb_abonnes
				  FROM UTILISATEUR u
				  LEFT JOIN SUIVRE s ON u.id_utilisateur = s.id_suivi
				  GROUP BY u.id_utilisateur, u.nom, u.prenom
				  ORDER BY nb_abonnes DESC;";

		$stmt = $this->pdo->prepare($query);
	    $stmt->execute();

		$influenceurs = [];
	    $lignes = $stmt->fetchAll();

	    foreach ($lignes as $ligne) {
	        $influenceurs[$ligne['nom']] = $ligne['nb_abonnes'];
	    }

	    return $influenceurs;
	}
	
	public function ajoutTouite(string $n, string $texte) {
		$query1 = "SELECT id_utilisateur FROM UTILISATEUR WHERE nom = :nom";    ///Requete pour trouver l'ID de l'utilisateur mit en paramètre
		$stmt1 = $this->pdo->prepare($query1);
       	$stmt1->execute(['nom' => $n]);			///Exécution de la commande
       	$Ligne1 = $stmt1->fetch();				
		$idAuteur = $Ligne1['id_utilisateur'];

		$query2 = "INSERT INTO TOUITE (texte, date_publication, id_auteur) 
                   VALUES (:texte, NOW(), :id_auteur)"; ///Requete pour ajouter un touite dans la BDD
		$stmt2 = $this->pdo->prepare($query2);

		$stmt2->execute(['texte' => $texte, 'id_auteur' => $idAuteur]);   ///Exécution de la commande
		return (int) $this->pdo->lastInsertId();
	}
	

	public function ajoutUtilisateur(string $n) {
		$emailFactice = uniqid() . "@touiteur.app"; 			///Generation de l'email, on n'utilise pas d'email mais il en faut une unique
		$query = "INSERT INTO UTILISATEUR (nom, email, nb_touites) VALUES (:nom, :email, 0)"; 
		$stmt = $this->pdo->prepare($query);      
		$stmt->execute(['nom' => $n,'email' => $emailFactice]);    
		return (int) $this->pdo->lastInsertId(); 
	}

		public function ajoutTag($libelle){
		$query = "INSERT INTO TAG (libelle) VALUES (:libelle)";

		$stmt = $this->pdo->prepare($query);
		$stmt->execute(['libelle' => $libelle]);
		return (int) $this->pdo->lastInsertId();
	}

	public function ajouterTagTouite($idTouite, $idTag): bool {
	    $query = "INSERT INTO CONTENIR (id_touite, id_tag) VALUES (:id_touite, :id_tag)";

	    $stmt = $this->pdo->prepare($query);

	    return $stmt->execute(['id_touite' => $idTouite,'id_tag' => $idTag]);
	}

	public function getUtilisateurSuivi($nom){
		$utils = [];

		$query = "SELECT u2.nom as suivi
				  FROM UTILISATEUR u1
				  INNER JOIN SUIVRE s ON u1.id_utilisateur = s.id_suiveur
				  INNER JOIN UTILISATEUR u2 ON s.id_suivi = u2.id_utilisateur
				  WHERE u1.nom = :nom;";

		$stmt = $this->pdo->prepare($query);
		$stmt->execute(['nom' => $nom]);
		$lignes = $stmt->fetchAll();			/// renvoie des resultats en tableau
		
		foreach ($lignes as $ligne) {		/// boucle pour ajouter les utilisateurs suivis au tableau qui sera renvoyé
			$utils[] = $ligne['suivi'];
		}

		return $utils;
	}

	public function getTagsSuivi($nom){
		$tags = [];

		$query = "SELECT t.libelle as libelle
				  FROM UTILISATEUR u
				  INNER JOIN ABONNEMENT_TAG a ON u.id_utilisateur = a.id_abonne
				  INNER JOIN TAG t ON a.id_tag = t.id_tag
				  WHERE u.nom = :nom;";

		$stmt = $this->pdo->prepare($query);
		$stmt->execute(['nom' => $nom]);
		$lignes = $stmt->fetchAll();			/// renvoie des resultats en tableau
		
		foreach ($lignes as $ligne) {		/// boucle pour ajouter les tags suivis au tableau qui sera renvoyé
			$tags[] = $ligne['libelle'];
		}

		return $tags;
	}
}


