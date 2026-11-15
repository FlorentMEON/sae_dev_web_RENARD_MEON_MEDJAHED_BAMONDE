# Touiteur

•RENARD

•MEON

•MEDJAHED

•BAMONDE

## Présentation du projet

Touiteur est une application web de microblogging permettant aux utilisateurs de publier de courts messages appelés *touites*, de suivre d'autres utilisateurs et de s'abonner à des tags.

Un touite peut contenir jusqu'à 235 caractères et être associé à différents tags.

## Fonctionnalités

- Consultation des touites publiés.
- Publication de nouveaux touites.
- Création et gestion des utilisateurs.
- Consultation des touites par utilisateur.
- Consultation des touites par tag.
- Suivi d'autres utilisateurs.
- Abonnement à des tags.
- Affichage des influenceurs selon leur nombre d'abonnés.
- Association de tags aux touites.

## Technologies utilisées

- PHP
- PDO pour l'accès aux bases de données
- SQL
- HTML / CSS
- SQLite pour la version hébergée sur Webetu

## Bases de données et environnements

**Attention : la base de données utilisée dans l'archive Git et celle utilisée sur le serveur Webetu sont différentes.**

### Archive Git

L'archive Git conserve la version initiale du projet, qui utilise **MySQL**.

Le schéma et les fichiers SQL présents dans cette version correspondent donc à la structure MySQL d'origine.

### Hébergement Webetu

Pour le déploiement sur Webetu, le projet a été adapté afin d'utiliser **SQLite** à la place de MySQL.

La base de données hébergée est `touiteur.sqlite`.

Les adaptations réalisées comprennent notamment :

- La modification de la connexion PDO pour utiliser SQLite.
- L'adaptation des requêtes SQL incompatibles avec SQLite.
- La migration des données initialement présentes dans MySQL vers SQLite.
- La recréation des triggers MySQL sous forme de triggers SQLite.

La version hébergée utilise les règles suivantes :

- Incrémentation automatique du compteur de touites après une publication.
- Limitation à 50 touites par utilisateur et par jour.
- Interdiction de modifier l'auteur d'un touite.
- Interdiction de se suivre soi-même.

## Structure de la base SQLite

La base de données contient sept tables :

| Table | Rôle |
|---|---|
| `UTILISATEUR` | Informations sur les utilisateurs |
| `TOUITE` | Publications des utilisateurs |
| `TAG` | Tags disponibles |
| `IMAGE` | Images associées aux touites |
| `SUIVRE` | Relations entre utilisateurs |
| `CONTENIR` | Associations entre touites et tags |
| `ABONNEMENT_TAG` | Abonnements des utilisateurs aux tags |

Les contraintes d'intégrité et les triggers assurent le respect des principales règles de gestion.

## Configuration de la version Webetu

La configuration de la base SQLite est stockée dans un fichier `config.ini`, placé en dehors du répertoire public du site.

Exemple de configuration :

```ini
database = "/chemin/vers/touiteur.sqlite"
```

Le chemin doit correspondre à l'emplacement réel du fichier SQLite sur le serveur.

La connexion à la base de données est effectuée avec PDO et le pilote SQLite.

## Déploiement

La version hébergée sur Webetu utilise SQLite. Le fichier `touiteur.sqlite` doit être présent sur le serveur et accessible en écriture par PHP.

Le fichier de configuration doit rester en dehors du répertoire public. L'accès HTTP direct au fichier de base de données doit également être interdit.

Les scripts temporaires de création et d'importation de la base de données ne doivent pas rester accessibles sur le serveur après leur utilisation.

## Remarque sur les versions

La version présente dans l'archive Git et la version hébergée sur Webetu ne reposent pas sur le même système de gestion de base de données :

- **Git : MySQL**
- **Webetu : SQLite**

Cette différence est volontaire et correspond à l'adaptation réalisée pour permettre l'hébergement du projet sur Webetu.