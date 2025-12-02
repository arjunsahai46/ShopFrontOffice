<?php

//require_once '../../configs/mysql_config.class.php';
require_once 'ModelePDO.class.php';
require_once 'Client.class.php';
require_once __DIR__ . '/gestion_client.class.php';

class GestionBoutique extends ModelePDO {

// <editor-fold defaultstate="collapsed" desc="Champs statiques">

    public static function SupprimerCategorieById($id) {
        Return self::SupprimerTupleByChamp('categorie', 'id', $id);
    }

    public static function seDeconnecter() {
        self::$pdoCnxBase = null;
        //si on n'appelle pas la méthode, la déconnexion a lieu en fin de script        
    }

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Méthodes statiques">

    /**
     * Permet de se connecter à la base de données
     */
    public static function seConnecter() {
        if (!isset(self::$pdoCnxBase)) { //S'il n'y a pas encore eu de connexion
            try {
                self::$pdoCnxBase = new PDO('mysql:host=' . self::$serveur . ';dbname=' .
                        self::$base, self::$utilisateur, self::$passe);
                self::$pdoCnxBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdoCnxBase->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                self::$pdoCnxBase->query("SET CHARACTER SET utf8");
            } catch (Exception $e) {
                echo 'Erreur : ' . $e->getMessage() . '<br />'; // méthode de la classe Exception
                echo 'Code : ' . $e->getCode(); // méthode de la classe Exception
            }
        }
    }

    /**
     * Vérifie si l'utilisateur est un administrateur présent dans la base
     * @param type $login Login de l'utilisateur
     * @param type $passe Passe de l'utilisateur
     * @return type Booléen
     */
    public static function isAdminOK($login, $passe) {
        self::seConnecter();
        self::$requete = "SELECT * FROM Utilisateur where login=:login and passe=:passe";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue('login', $login);
        self::$pdoStResults->bindValue('passe', $passe);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch();
        self::$pdoStResults->closeCursor();
        if ((self::$resultat != null) and (self::$resultat->isAdmin))
            return true;
        else
            return false;
    }

    // Méthodes de gestion des catégories

    /**
     * Récupère toutes les catégories de la base de données.
     * 
     * @return array Tableau d'objets catégories.
     */
    public static function getLesCategories() {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM Categorie"; // Requête pour récupérer toutes les catégories
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    public static function getLesProduitsByCategorie($libelleCategorie) {
        self::seConnecter();
        // Convertir les underscores en espaces pour la comparaison avec la base de données
        $libelleCategorie = str_replace('_', ' ', $libelleCategorie);
        
        self::$requete = "SELECT P.id, P.nom, P.description, P.prix, P.image, P.QteStockProduit, C.libelle FROM produit P,categorie C where P.idCategorie = C.id AND libelle = :libCateg";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue('libCateg', $libelleCategorie);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll();
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }
    
    /**
     * Récupère un produit par son ID.
     * 
     * @param int $idProduit L'ID du produit.
     * @return array Détails du produit.
     */
    public static function getProduitById($idProduit) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT P.id, P.nom, P.prix, P.description, P.image, C.libelle FROM produit P, categorie C WHERE P.idCategorie = C.id AND P.id = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_ASSOC); // Récupère les détails du produit en tableau associatif
        self::$pdoStResults->closeCursor();
        
        return self::$resultat;
    }
    

    /**
     * Récupère une catégorie par son ID.
     * 
     * @param int $id L'ID de la catégorie.
     * @return object La catégorie correspondant à l'ID.
     */
    public static function getCategorieById($id) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM Categorie WHERE id = :id"; // Requête pour récupérer une catégorie par ID
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Retourne la liste des clients.
     * @return array Tableau d'objets client
     */
    public static function getLesClients() {
        self::seConnecter();
        $requete = "SELECT * FROM client";
        self::$pdoStResults = self::$pdoCnxBase->prepare($requete);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();

        // Création des objets Client à partir des données de la base
        $clients = [];
        foreach ($resultat as $data) {
            $clients[] = new Client($data->id, $data->nom, $data->prenom, $data->rue, $data->codePostal, $data->ville, $data->tel, $data->email);
        }
        return $clients;
    }

    /**
     * Retourne un client par son ID.
     * @param int $id L'ID du client à récupérer
     * @return Client Objet représentant un client
     */
    public static function getClientById($id) {
        $resultat = GestionClient::getClientById($id);
        
        if ($resultat) {
            return new Client(
                $resultat->id, 
                $resultat->nom, 
                $resultat->prenom, 
                $resultat->rue, 
                $resultat->codePostal, 
                $resultat->ville, 
                $resultat->tel, 
                $resultat->email,
                $resultat->mdp
            );
        } else {
            return null;
        }
    }

    /**
     * Retourne un client par son email.
     * @param string $email L'email du client à récupérer
     * @return Client|null Objet représentant un client ou null si non trouvé
     */
    public static function getClientByEmail($email) {
        self::seConnecter();
        $requete = "SELECT * FROM client WHERE email = :email";
        self::$pdoStResults = self::$pdoCnxBase->prepare($requete);
        self::$pdoStResults->bindValue(':email', $email);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();

        if ($resultat) {
            return new Client(
                $resultat->id, 
                $resultat->nom, 
                $resultat->prenom, 
                $resultat->rue, 
                $resultat->codePostal, 
                $resultat->ville, 
                $resultat->tel, 
                $resultat->email,
                $resultat->mdp
            );
        } else {
            return null;
        }
    }

    /**
     * Ajouter un client.
     * @param string $nom Nom du client
     * @param string $prenom Prénom du client
     * @param string $rue Rue du client
     * @param string $codePostal Code postal du client
     * @param string $ville Ville du client
     * @param string $tel Téléphone du client
     * @param string $email Email du client
     * @param string $mdp Mot de passe du client
     */
    public static function ajouterClient($nom, $prenom, $rue, $codePostal, $ville, $tel, $email, $mdp) {
        self::seConnecter();
        $requete = "INSERT INTO client (nom, prenom, rue, codePostal, ville, tel, email, mdp) VALUES (:nom, :prenom, :rue, :codePostal, :ville, :tel, :email, :mdp)";
        self::$pdoStResults = self::$pdoCnxBase->prepare($requete);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':prenom', $prenom);
        self::$pdoStResults->bindValue(':rue', $rue);
        self::$pdoStResults->bindValue(':codePostal', $codePostal);
        self::$pdoStResults->bindValue(':ville', $ville);
        self::$pdoStResults->bindValue(':tel', $tel);
        self::$pdoStResults->bindValue(':email', $email);
        self::$pdoStResults->bindValue(':mdp', $mdp);
        self::$pdoStResults->execute();
    }

    /**
     * Modifier un client.
     * @param int $id L'ID du client à modifier
     * @param string $nom Le nouveau nom du client
     * @param string $prenom Le nouveau prénom du client
     * @param string $rue La nouvelle rue du client
     * @param string $codePostal Le nouveau code postal du client
     * @param string $ville La nouvelle ville du client
     * @param string $tel Le nouveau téléphone du client
     * @param string $email Le nouveau email du client
     */
    public static function modifierClient($id, $nom, $prenom, $rue, $codePostal, $ville, $tel, $email) {
        self::seConnecter();
        $requete = "UPDATE client SET nom = :nom, prenom = :prenom, rue = :rue, codePostal = :codePostal, ville = :ville, tel = :tel, email = :email WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare($requete);
        self::$pdoStResults->bindValue(':id', $id);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':prenom', $prenom);
        self::$pdoStResults->bindValue(':rue', $rue);
        self::$pdoStResults->bindValue(':codePostal', $codePostal);
        self::$pdoStResults->bindValue(':ville', $ville);
        self::$pdoStResults->bindValue(':tel', $tel);
        self::$pdoStResults->bindValue(':email', $email);
        self::$pdoStResults->execute();
    }

    /**
     * Supprimer un client.
     * @param int $id L'ID du client à supprimer
     */
    public static function supprimerClient($id) {
        self::seConnecter();
        $requete = "DELETE FROM client WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare($requete);
        self::$pdoStResults->bindValue(':id', $id);
        self::$pdoStResults->execute();
    }

    // Méthodes de gestion des commandes

    /**
     * Récupère toutes les commandes de la base de données.
     * 
     * @return array Tableau d'objets commandes.
     */
    public static function getLesCommandes() {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM Commande"; // Requête pour récupérer toutes les commandes
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère une commande par son ID.
     * 
     * @param int $id L'ID de la commande.
     * @return object La commande correspondant à l'ID.
     */
    public static function getCommandeById($id) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM Commande WHERE id = :id"; // Requête pour récupérer une commande par ID
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Ajoute une nouvelle commande dans la base de données.
     * 
     * @param int $idClient L'id du client qui a passé la commande.
     * @param string $date La date de la commande.
     * @param float $sousTotal Le sous-total de la commande.
     * @return int L'ID de la commande créée
     */
    public static function ajouterCommande($idClient, $date, $sousTotal = 0) {
        self::seConnecter();
        self::$requete = "INSERT INTO commande (idClient, date, sousTotal) VALUES (:idClient, :date, :sousTotal)";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idClient', $idClient, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':date', $date);
        self::$pdoStResults->bindValue(':sousTotal', $sousTotal, PDO::PARAM_STR);
        self::$pdoStResults->execute();
        return self::$pdoCnxBase->lastInsertId();
    }

    /**
     * Modifie une commande dans la base de données.
     *
     * @param int $id L'ID de la commande à modifier.
     * @param string $date La nouvelle date de la commande.
     * @param int $idClient Le nouvel ID du client.
     * @param float $sousTotal Le nouveau sous-total de la commande.
     */
    public static function modifierCommande($id, $date, $idClient, $sousTotal) {
        self::seConnecter();
        self::$requete = "UPDATE commande SET date = :date, idClient = :idClient, sousTotal = :sousTotal WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':date', $date);
        self::$pdoStResults->bindValue(':idClient', $idClient, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':sousTotal', $sousTotal, PDO::PARAM_STR);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime une commande par son ID.
     * 
     * @param int $id L'ID de la commande à supprimer.
     */
    public static function supprimerCommande($id) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "DELETE FROM Commande WHERE id = :id"; // Requête de suppression
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    // Méthodes de gestion des fournisseurs

    /**
     * Récupère tous les fournisseurs de la base de données.
     * 
     * @return array Tableau d'objets fournisseurs.
     */
    public static function getLesFournisseurs() {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM Fournisseur"; // Requête pour récupérer tous les fournisseurs
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère un fournisseur par son ID.
     * 
     * @param int $id L'ID du fournisseur.
     * @return object Le fournisseur correspondant à l'ID.
     */
    public static function getFournisseurById($id) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM Fournisseur WHERE id = :id"; // Requête pour récupérer un fournisseur par ID
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Ajoute un nouveau fournisseur dans la base de données.
     * 
     * @param string $nom Le nom du fournisseur.
     * @param string $rue L'adresse du fournisseur.
     * @param string $codePostal Le code postal du fournisseur.
     * @param string $ville La ville du fournisseur.
     * @param string $tel Le téléphone du fournisseur.
     * @param string $email L'email du fournisseur.
     */
    public static function ajouterFournisseur($nom, $rue, $codePostal, $ville, $tel, $email) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "INSERT INTO Fournisseur (nom, rue, codePostal, ville, tel, email)
                          VALUES (:nom, :rue, :codePostal, :ville, :tel, :email)"; // Requête d'insertion
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':rue', $rue);
        self::$pdoStResults->bindValue(':codePostal', $codePostal);
        self::$pdoStResults->bindValue(':ville', $ville);
        self::$pdoStResults->bindValue(':tel', $tel);
        self::$pdoStResults->bindValue(':email', $email);
        self::$pdoStResults->execute();
    }

    /**
     * Modifie un fournisseur dans la base de données.
     *
     * @param int $id L'ID du fournisseur à modifier.
     * @param string $nom Le nom du fournisseur.
     * @param string $rue L'adresse du fournisseur.
     * @param string $codePostal Le code postal du fournisseur.
     * @param string $ville La ville du fournisseur.
     * @param string $tel Le numéro de téléphone du fournisseur.
     * @param string $email L'email du fournisseur.
     */
    public static function modifierFournisseur($id, $nom, $rue, $codePostal, $ville, $tel, $email) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "UPDATE Fournisseur SET nom = :nom, rue = :rue, codePostal = :codePostal, ville = :ville, tel = :tel, email = :email WHERE id = :id"; // Requête de mise à jour
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':rue', $rue);
        self::$pdoStResults->bindValue(':codePostal', $codePostal);
        self::$pdoStResults->bindValue(':ville', $ville);
        self::$pdoStResults->bindValue(':tel', $tel);
        self::$pdoStResults->bindValue(':email', $email);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime un fournisseur par son ID.
     * 
     * @param int $id L'ID du fournisseur à supprimer.
     */
    public static function supprimerFournisseur($id) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "DELETE FROM Fournisseur WHERE id = :id"; // Requête de suppression
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    // Méthodes de gestion des lignes de commande

    /**
     * Récupère toutes les lignes de commande de la base de données.
     *
     * @return array Tableau d'objets lignes de commande.
     */
    public static function getToutesLesLignesCommandes() {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM lignedecommande"; // Requête pour récupérer toutes les lignes de commande
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère une ligne de commande par son ID.
     *
     * @param int $idCommande L'ID de la commande.
     * @param int $idProduit L'ID du produit.
     * @return object La ligne de commande correspondant à l'ID de la commande et du produit.
     */
    public static function getLigneCommandeById($idCommande, $idProduit) {
        self::seConnecter();
        self::$requete = "SELECT * FROM lignedecommande WHERE idCommande = :idCommande AND idProduit = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Met à jour la quantité en stock d'un produit
     * 
     * @param int $idProduit L'ID du produit
     * @param int $quantite La quantité à soustraire du stock
     * @return bool True si la mise à jour a réussi, false sinon
     */
    private static function updateStock($idProduit, $quantite) {
        self::seConnecter();
        self::$requete = "UPDATE produit 
                         SET QteStockProduit = QteStockProduit - :quantite 
                         WHERE id = :idProduit AND QteStockProduit >= :quantite";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        $result = self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
        return $result && (self::$pdoStResults->rowCount() > 0);
    }

    /**
     * Vérifie si un produit est disponible en stock
     * 
     * @param int $idProduit L'ID du produit
     * @param int $quantite La quantité demandée
     * @return bool True si le produit est disponible en quantité suffisante
     */
    public static function verifierStock($idProduit, $quantite) {
        self::seConnecter();
        self::$requete = "SELECT QteStockProduit FROM produit WHERE id = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        
        return $resultat && $resultat->QteStockProduit >= $quantite;
    }

    /**
     * Ajoute une ligne de commande dans la base de données et met à jour le stock.
     * 
     * @param int $idCommande L'ID de la commande.
     * @param int $idProduit L'ID du produit.
     * @param int $quantite La quantité de produit commandée.
     * @param float $prixUnitaire Le prix unitaire du produit.
     * @param float $sousTotal Le sous-total de la ligne de commande.
     * @return bool True si l'ajout a réussi, false sinon
     */
    public static function ajouterLigneCommande($idCommande, $idProduit, $quantite, $prixUnitaire, $sousTotal) {
        // Vérifier d'abord si le stock est suffisant
        if (!self::verifierStock($idProduit, $quantite)) {
            return false;
        }

        self::seConnecter();
        try {
            // Démarrer une transaction
            self::$pdoCnxBase->beginTransaction();

            // Ajouter la ligne de commande
            self::$requete = "INSERT INTO lignedecommande (idCommande, idProduit, quantite, prixUnitaire, sousTotal) 
                            VALUES (:idCommande, :idProduit, :quantite, :prixUnitaire, :sousTotal)";
            self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
            self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
            self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
            self::$pdoStResults->bindValue(':quantite', $quantite, PDO::PARAM_INT);
            self::$pdoStResults->bindValue(':prixUnitaire', $prixUnitaire, PDO::PARAM_STR);
            self::$pdoStResults->bindValue(':sousTotal', $sousTotal, PDO::PARAM_STR);
            $resultatLigne = self::$pdoStResults->execute();
            self::$pdoStResults->closeCursor();

            // Mettre à jour le stock
            $resultatStock = self::updateStock($idProduit, $quantite);

            // Si tout s'est bien passé, on valide la transaction
            if ($resultatLigne && $resultatStock) {
                self::$pdoCnxBase->commit();
                return true;
            } else {
                // Sinon on annule
                self::$pdoCnxBase->rollBack();
                return false;
            }
        } catch (Exception $e) {
            // En cas d'erreur, on annule la transaction
            self::$pdoCnxBase->rollBack();
            return false;
        }
    }

    /**
     * Modifie une ligne de commande dans la base de données.
     *
     * @param int $idCommande L'ID de la commande.
     * @param int $idProduit L'ID du produit.
     * @param int $quantite La nouvelle quantité du produit dans la ligne de commande.
     */
    public static function modifierLigneCommande($idCommande, $idProduit, $quantite) {
        self::seConnecter();
        self::$requete = "UPDATE lignedecommande SET quantite = :quantite WHERE idCommande = :idCommande AND idProduit = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime une ligne de commande par son ID.
     * 
     * @param int $idCommande L'ID de la commande.
     * @param int $idProduit L'ID du produit de la ligne de commande.
     */
    public static function supprimerLigneCommande($idCommande, $idProduit) {
        self::seConnecter();
        self::$requete = "DELETE FROM lignedecommande WHERE idCommande = :idCommande AND idProduit = :idProduit";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Récupère tous les produits de la base de données.
     *
     * @return array Tableau d'objets produits.
     */
    public static function getLesProduits() {
        self::seConnecter();
        self::$requete = "SELECT P.id, P.nom, P.description, P.prix, P.image, P.QteStockProduit, C.libelle 
                         FROM produit P 
                         JOIN categorie C ON P.idCategorie = C.id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll();
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Ajoute un nouveau produit dans la base de données.
     *
     * @param string $nom Le nom du produit.
     * @param string $description La description du produit.
     * @param float $prix Le prix du produit.
     * @param string $image L'image du produit.
     * @param int $idCategorie L'ID de la catégorie du produit.
     * @param int $idFournisseur L'ID du fournisseur du produit.
     */
    public static function ajouterProduit($nom, $description, $prix, $image, $idCategorie, $idFournisseur) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "INSERT INTO Produit (nom, description, prix, image, idCategorie, idFournisseur)
                          VALUES (:nom, :description, :prix, :image, :idCategorie, :idFournisseur)"; // Requête d'insertion
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':description', $description);
        self::$pdoStResults->bindValue(':prix', $prix);
        self::$pdoStResults->bindValue(':image', $image);
        self::$pdoStResults->bindValue(':idCategorie', $idCategorie);
        self::$pdoStResults->bindValue(':idFournisseur', $idFournisseur);
        self::$pdoStResults->execute();
    }

    /**
     * Modifie un produit existant dans la base de données.
     *
     * @param int $id L'ID du produit à modifier.
     * @param string $nom Le nouveau nom du produit.
     * @param string $description La nouvelle description du produit.
     * @param float $prix Le nouveau prix du produit.
     * @param string $image La nouvelle image du produit.
     * @param int $idCategorie Le nouvel ID de la catégorie du produit.
     * @param int $idFournisseur Le nouvel ID du fournisseur du produit.
     */
    public static function modifierProduit($id, $nom, $description, $prix, $image, $idCategorie, $idFournisseur) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "UPDATE Produit 
                          SET nom = :nom, description = :description, prix = :prix, image = :image, 
                              idCategorie = :idCategorie, idFournisseur = :idFournisseur 
                          WHERE id = :id"; // Requête de mise à jour
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':nom', $nom);
        self::$pdoStResults->bindValue(':description', $description);
        self::$pdoStResults->bindValue(':prix', $prix);
        self::$pdoStResults->bindValue(':image', $image);
        self::$pdoStResults->bindValue(':idCategorie', $idCategorie);
        self::$pdoStResults->bindValue(':idFournisseur', $idFournisseur);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime un produit de la base de données.
     *
     * @param int $id L'ID du produit à supprimer.
     */
    public static function supprimerProduit($id) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "DELETE FROM Produit WHERE id = :id"; // Requête de suppression
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Récupère tous les utilisateurs de la base de données.
     *
     * @return array Tableau d'objets utilisateurs.
     */
    public static function getLesUtilisateurs() {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM Utilisateur"; // Requête pour récupérer tous les utilisateurs
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère un utilisateur par son ID.
     *
     * @param int $id L'ID de l'utilisateur.
     * @return object L'utilisateur correspondant à l'ID.
     */
    public static function getUtilisateurById($id) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM Utilisateur WHERE id = :id"; // Requête pour récupérer un utilisateur par ID
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère un utilisateur par son login.
     *
     * @param string $login Le login de l'utilisateur.
     * @return object L'utilisateur correspondant au login.
     */
    public static function getUtilisateurByLogin($login) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "SELECT * FROM Utilisateur WHERE login = :login"; // Requête pour récupérer un utilisateur par login
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':login', $login);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Ajoute un nouvel utilisateur dans la base de données.
     *
     * @param string $login Login de l'utilisateur.
     * @param string $passe Mot de passe de l'utilisateur.
     * @param string $email Adresse email de l'utilisateur.
     * @param int $isAdmin Indique si l'utilisateur est administrateur (1 pour oui, 0 pour non).
     */
    public static function ajouterUtilisateur($login, $passe, $email, $isAdmin = 0) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "INSERT INTO Utilisateur (login, passe, email, isAdmin)
                          VALUES (:login, :passe, :email, :isAdmin)"; // Requête d'insertion
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':login', $login);
        self::$pdoStResults->bindValue(':passe', password_hash($passe, PASSWORD_DEFAULT)); // Mot de passe crypté
        self::$pdoStResults->bindValue(':email', $email);
        self::$pdoStResults->bindValue(':isAdmin', $isAdmin, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Modifie un utilisateur existant dans la base de données.
     *
     * @param int $id L'ID de l'utilisateur à modifier.
     * @param string $login Le nouveau login de l'utilisateur.
     * @param string $passe Le nouveau mot de passe de l'utilisateur.
     * @param string $email Le nouveau email de l'utilisateur.
     * @param int $isAdmin Indique si l'utilisateur est administrateur (1 pour oui, 0 pour non).
     */
    public static function modifierUtilisateur($id, $login, $passe, $email, $isAdmin = 0) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "UPDATE Utilisateur 
                          SET login = :login, passe = :passe, email = :email, isAdmin = :isAdmin 
                          WHERE id = :id"; // Requête de mise à jour
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':login', $login);
        self::$pdoStResults->bindValue(':passe', password_hash($passe, PASSWORD_DEFAULT)); // Mot de passe crypté
        self::$pdoStResults->bindValue(':email', $email);
        self::$pdoStResults->bindValue(':isAdmin', $isAdmin, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Supprime un utilisateur de la base de données.
     *
     * @param int $id L'ID de l'utilisateur à supprimer.
     */
    public static function supprimerUtilisateur($id) {
        self::seConnecter(); // Se connecter à la base de données
        self::$requete = "DELETE FROM Utilisateur WHERE id = :id"; // Requête de suppression
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Récupère toutes les lignes d'une commande spécifique.
     *
     * @param int $idCommande L'ID de la commande
     * @return array Tableau d'objets lignes de commande
     */
    public static function getLignesCommandeById($idCommande) {
        self::seConnecter();
        self::$requete = "SELECT ldc.*, p.nom AS nom_produit, p.image AS image_produit
                          FROM lignedecommande ldc
                          JOIN produit p ON ldc.idProduit = p.id
                          WHERE ldc.idCommande = :idCommande";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idCommande', $idCommande, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Récupère un point relais par son ID.
     * @param int $id L'ID du point relais
     * @return object|null Les infos du point relais ou null
     */
    public static function getPointRelaisById($id) {
        self::seConnecter();
        self::$requete = "SELECT * FROM pointrelais WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Récupère une livraison par son ID.
     * @param int $id L'ID de la livraison
     * @return object|null La livraison ou null
     */
    public static function getLivraisonById($id) {
        self::seConnecter();
        self::$requete = "SELECT * FROM livraison WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Récupère toutes les commandes d'un client donné.
     * @param int $idClient L'ID du client
     * @return array Tableau d'objets commandes
     */
    public static function getCommandesByClientId($idClient) {
        self::seConnecter();
        self::$requete = "SELECT * FROM commande WHERE idClient = :idClient ORDER BY date DESC";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idClient', $idClient, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Met à jour le token de cookie pour un admin
     */
    public static function setAdminToken($login, $token) {
        self::seConnecter();
        self::$requete = "UPDATE Utilisateur SET token_cookie = :token WHERE login = :login AND isAdmin = 1";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':token', $token);
        self::$pdoStResults->bindValue(':login', $login);
        self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
    }

    /**
     * Retourne le login admin à partir d'un token de cookie
     */
    public static function getLoginByToken($token) {
        self::seConnecter();
        self::$requete = "SELECT login FROM Utilisateur WHERE token_cookie = :token AND isAdmin = 1";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':token', $token);
        self::$pdoStResults->execute();
        $result = self::$pdoStResults->fetch();
        self::$pdoStResults->closeCursor();
        return $result ? $result->login : null;
    }

    /**
     * Retourne les produits qui n'ont jamais été commandés.
     * @return array
     */
    public static function getProduitsJamaisCommandes() {
        self::seConnecter();
        self::$requete = "SELECT * FROM produit WHERE id NOT IN (SELECT DISTINCT idProduit FROM lignedecommande)";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Retourne le top des produits les plus commandés (quantité ou CA)
     * @param int $limite
     * @param bool $parCA true = par chiffre d'affaires, false = par quantité
     * @return array
     */
    public static function getTopProduitsCommandes($limite = 5, $parCA = false) {
        self::seConnecter();
        $order = $parCA ? 'totalCA' : 'totalQte';
        self::$requete = "SELECT p.*, SUM(ldc.quantite) AS totalQte, SUM(ldc.quantite * ldc.prixUnitaire) AS totalCA 
        FROM produit p JOIN lignedecommande ldc ON p.id = ldc.idProduit GROUP BY p.id ORDER BY $order DESC LIMIT :limite";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':limite', $limite, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        $resultat = self::$pdoStResults->fetchAll(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return $resultat;
    }

    /**
     * Appelle la procédure stockée de réapprovisionnement pour un produit donné.
     * @param int $idProduit
     * @param int $quantite
     * @return void
     */
    public static function reapprovisionnerProduit($idProduit, $quantite = 10) {
        self::seConnecter();
        self::$requete = "CALL ReapprovisionnerProduit(:idProduit, :quantite)";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':idProduit', $idProduit, PDO::PARAM_INT);
        self::$pdoStResults->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
    }

// </editor-fold>
}

?>