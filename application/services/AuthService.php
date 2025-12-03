<?php

/**
 * Service d'authentification
 * 
 * Gère la logique métier d'authentification
 */
class AuthService
{
    /**
     * Authentifie un utilisateur
     * 
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe
     * @return object|false Utilisateur authentifié ou false
     */
    public static function authentifier($email, $password)
    {
        require_once chemin(Paths::MODELES . 'GestionUtilisateur.class.php');
        $utilisateur = GestionUtilisateur::getUtilisateurByEmail($email);
        
        if ($utilisateur && password_verify($password, $utilisateur->motDePasse)) {
            return $utilisateur;
        }
        
        return false;
    }

    /**
     * Authentifie un administrateur
     * 
     * @param string $login Login de l'admin
     * @param string $password Mot de passe
     * @return object|false Admin authentifié ou false
     */
    public static function authentifierAdmin($login, $password)
    {
        require_once chemin(Paths::MODELES . 'GestionAdmin.class.php');
        return GestionAdmin::authentifier($login, $password);
    }

    /**
     * Vérifie si un utilisateur est connecté
     * 
     * @return bool True si connecté, false sinon
     */
    public static function estConnecte()
    {
        return isset($_SESSION['client']) || isset($_SESSION['admin']);
    }

    /**
     * Vérifie si l'utilisateur est un administrateur
     * 
     * @return bool True si admin, false sinon
     */
    public static function estAdmin()
    {
        return isset($_SESSION['admin']);
    }

    /**
     * Déconnecte l'utilisateur
     */
    public static function deconnecter()
    {
        unset($_SESSION['client']);
        unset($_SESSION['admin']);
        session_destroy();
    }
}

