<?php

class ControleurAdmin {

    public function __construct() {
// si on séparait les modèles, le constructeur donnerait son chemin
// require_once Chemins::MODELES.'gestion_categories.class.php';
        // Reconnexion auto admin via cookie sécurisé
        if (!isset($_SESSION['login_admin']) && isset($_COOKIE['admin_token'])) {
            $login = GestionBoutique::getLoginByToken($_COOKIE['admin_token']);
            if ($login) {
                $_SESSION['login_admin'] = $login;
                // Régénérer un nouveau token pour plus de sécurité
                $newToken = bin2hex(random_bytes(32));
                GestionBoutique::setAdminToken($login, $newToken);
                setcookie('admin_token', $newToken, time() + 7 * 24 * 3600, '/', '', false, true);
            } else {
                setcookie('admin_token', '', time() - 3600, '/', '', false, true);
            }
        }
    }

    public function afficherIndex() {
        if (isset($_SESSION['login_admin']))
            require Chemins::VUES_ADMIN . 'v_index_admin.inc.php';
        else
            require Chemins::VUES_ADMIN . 'v_connexion_admin.inc.php';
    }

    public function verifierConnexion() {
        if (GestionBoutique::isAdminOK($_POST['login'], $_POST['passe'])) {
            $_SESSION['login_admin'] = $_POST['login'];
            if (isset($_POST['connexion_auto'])) {
                $token = bin2hex(random_bytes(32));
                GestionBoutique::setAdminToken($_POST['login'], $token);
                setcookie('admin_token', $token, time() + 7 * 24 * 3600, '/', '', false, true);
            }
            header("Location:index.php?controleur=Admin&action=afficherIndex&display=minimal");
        } else
            require Chemins::VUES_ADMIN . 'v_acces_interdit.inc.php';
    }

    public function seDeconnecter() {
        // Suppression des variables de session et de la session
        if (isset($_SESSION['login_admin'])) {
            GestionBoutique::setAdminToken($_SESSION['login_admin'], null);
        }
        $_SESSION = array();
        session_destroy();
        header("Location:index.php");
        setcookie('admin_token', '', time() - 3600, '/', '', false, true);
    }

    public function VoirCategorie() {
        require Chemins::VUES_ADMIN . 'v_adminCategorie.inc.php';
    }

    public function voirStatsProduits() {
        $produitsJamaisCommandes = GestionBoutique::getProduitsJamaisCommandes();
        $topProduitsQte = GestionBoutique::getTopProduitsCommandes(5, false);
        $topProduitsCA = GestionBoutique::getTopProduitsCommandes(5, true);
        require Chemins::VUES_ADMIN . 'v_stats_produits.inc.php';
    }

    public function reapprovisionnerProduit() {
        if (isset($_POST['idProduit'])) {
            $idProduit = (int)$_POST['idProduit'];
            $quantite = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 10;
            GestionBoutique::reapprovisionnerProduit($idProduit, $quantite);
            $_SESSION['msg_reappro'] = 'Produit réapprovisionné avec succès !';
        }
        header('Location: index.php?controleur=Admin&action=voirStatsProduits');
        exit();
    }
}