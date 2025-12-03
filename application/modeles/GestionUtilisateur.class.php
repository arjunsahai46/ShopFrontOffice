<?php

require_once 'ModelePDO.class.php';

class GestionUtilisateur extends ModelePDO {

    /**
     * Récupère tous les utilisateurs de la base de données.
     *
     * @return array Tableau d'objets utilisateurs.
     */
    public static function getLesUtilisateurs() {
        self::seConnecter();
        self::$requete = "SELECT * FROM utilisateur";
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
        self::seConnecter();
        self::$requete = "SELECT * FROM utilisateur WHERE id = :id";
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
        self::seConnecter();
        self::$requete = "SELECT * FROM utilisateur WHERE login = :login";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':login', $login);
        self::$pdoStResults->execute();
        self::$resultat = self::$pdoStResults->fetch(PDO::FETCH_OBJ);
        self::$pdoStResults->closeCursor();
        return self::$resultat;
    }

    /**
     * Vérifie si l'utilisateur est un administrateur présent dans la base
     * @param string $login Login de l'utilisateur
     * @param string $passe Passe de l'utilisateur
     * @return bool Booléen
     */
    public static function isAdminOK($login, $passe) {
        self::seConnecter();
        self::$requete = "SELECT * FROM utilisateur where login=:login and passe=:passe";
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

    /**
     * Ajoute un nouvel utilisateur dans la base de données.
     *
     * @param string $login Login de l'utilisateur.
     * @param string $passe Mot de passe de l'utilisateur.
     * @param string $email Adresse email de l'utilisateur.
     * @param int $isAdmin Indique si l'utilisateur est administrateur (1 pour oui, 0 pour non).
     */
    public static function ajouterUtilisateur($login, $passe, $email, $isAdmin = 0) {
        self::seConnecter();
        self::$requete = "INSERT INTO utilisateur (login, passe, email, isAdmin)
                          VALUES (:login, :passe, :email, :isAdmin)";
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
        self::seConnecter();
        self::$requete = "UPDATE utilisateur 
                          SET login = :login, passe = :passe, email = :email, isAdmin = :isAdmin 
                          WHERE id = :id";
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
        self::seConnecter();
        self::$requete = "DELETE FROM utilisateur WHERE id = :id";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':id', $id, PDO::PARAM_INT);
        self::$pdoStResults->execute();
    }

    /**
     * Met à jour le token de cookie pour un admin
     * @param string $login Le login de l'admin
     * @param string|null $token Le token (null pour supprimer)
     */
    public static function setAdminToken($login, $token) {
        self::seConnecter();
        self::$requete = "UPDATE utilisateur SET token_cookie = :token WHERE login = :login AND isAdmin = 1";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':token', $token);
        self::$pdoStResults->bindValue(':login', $login);
        self::$pdoStResults->execute();
        self::$pdoStResults->closeCursor();
    }

    /**
     * Retourne le login admin à partir d'un token de cookie
     * @param string $token Le token de cookie
     * @return string|null Le login ou null si non trouvé
     */
    public static function getLoginByToken($token) {
        self::seConnecter();
        self::$requete = "SELECT login FROM utilisateur WHERE token_cookie = :token AND isAdmin = 1";
        self::$pdoStResults = self::$pdoCnxBase->prepare(self::$requete);
        self::$pdoStResults->bindValue(':token', $token);
        self::$pdoStResults->execute();
        $result = self::$pdoStResults->fetch();
        self::$pdoStResults->closeCursor();
        return $result ? $result->login : null;
    }
}
