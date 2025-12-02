<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//echo sha1("passeGrandChef");
session_start(); // Pour éviter erreurs SESSIONS

ob_start(); // Pour éviter erreur COOKIES

require_once 'configs/chemins.class.php';
require_once Chemins::CONFIGS . 'mysql_config.class.php';
require_once Chemins::CONFIGS . 'variables_globales.class.php';
require_once Chemins::MODELES . 'gestion_boutique.class.php';
require_once Chemins::MODELES . 'gestion_client.class.php';
require_once Chemins::MODELES . 'gestion_panier.class.php';
require_once Chemins::MODELES . 'gestion_commande.class.php';
require_once Chemins::MODELES . 'gestion_admin.class.php';

require_once Chemins::CONTROLEURS . 'ControleurProduits.class.php';
require_once Chemins::CONTROLEURS . 'ControleurClient.class.php';
require_once Chemins::CONTROLEURS . 'ControleurAdmin.class.php';
require_once Chemins::CONTROLEURS . 'ControleurPanier.class.php';

$displayHeaderFooter = !isset($_REQUEST['display']) || $_REQUEST['display'] !== 'minimal';

if ($displayHeaderFooter) {
    require Chemins::VUES_PERMANENTES . 'v_entete.inc.php';
}

// Récupération du contrôleur et de l'action depuis l'URL
$controleur = isset($_GET['controleur']) ? $_GET['controleur'] : 'Produits';
$action = isset($_GET['action']) ? $_GET['action'] : 'afficher';

// error_log debug désactivé en production
// error_log('>>> Requête : ' . $_SERVER['REQUEST_METHOD'] . ' - controleur=' . (isset($_GET['controleur']) ? $_GET['controleur'] : '') . ' - action=' . (isset($_GET['action']) ? $_GET['action'] : ''));

// Création des instances de contrôleurs
$controleurProduits = new ControleurProduits();
$controleurClient = new ControleurClient();
$controleurAdmin = new ControleurAdmin();
$controleurPanier = new ControleurPanier();

// Routage des requêtes
switch ($controleur) {
    case 'Panier':
        switch ($action) {
            case 'afficherAdresse':
                $controleurPanier->afficherAdresse();
                break;
            case 'validerAdresse':
                $controleurPanier->validerAdresse();
                break;
            case 'afficherLivraison':
                $controleurPanier->afficherLivraison();
                break;
            case 'validerLivraison':
                $controleurPanier->validerLivraison();
                break;
            case 'afficherPaiement':
                $controleurPanier->afficherPaiement();
                break;
            case 'validerPaiement':
                $controleurPanier->validerPaiement();
                break;
            case 'processPayPalPayment':
                $controleurPanier->processPayPalPayment();
                break;
            case 'confirmation':
                $controleurPanier->confirmation();
                break;
            case 'testPayPal':
                $controleurPanier->testPayPal();
                break;
            default:
                $controleurPanier->afficherAdresse();
        }
        break;

    case 'Produits':
        switch ($action) {
            case 'afficher':
                $controleurProduits->afficher();
                break;
            case 'afficherPanier':
                $controleurProduits->afficherPanier();
                break;
            case 'AjouterPanier':
                $controleurProduits->AjouterPanier();
                break;
            case 'retirerPanier':
                $controleurProduits->retirerPanier();
                break;
            case 'viderPanier':
                $controleurProduits->viderPanier();
                break;
            case 'MettreAJourPanier':
                $controleurProduits->MettreAJourPanier();
                break;
            case 'commander':
                $controleurProduits->commander();
                break;
            case 'confirmationCommande':
                $controleurProduits->confirmationCommande();
                break;
            case 'afficherCheckoutAdresse':
                $controleurProduits->afficherCheckoutAdresse();
                break;
            default:
                // Action par défaut
                $controleurProduits->afficher();
        }
        break;

    case 'Client':
        switch ($action) {
            case 'afficherConnexion':
                $controleurClient->afficherConnexion();
                break;
            case 'afficherInscription':
                $controleurClient->afficherInscription();
                break;
            case 'traiterConnexion':
                $controleurClient->traiterConnexion();
                break;
            case 'traiterInscription':
                $controleurClient->traiterInscription();
                break;
            case 'deconnexion':
                $controleurClient->deconnexion();
                break;
            case 'afficherProfil':
                $controleurClient->afficherProfil();
                break;
            case 'afficherModificationProfil':
                $controleurClient->afficherModificationProfil();
                break;
            case 'modifierProfil':
                $controleurClient->modifierProfil();
                break;
            case 'afficherCommandes':
                $controleurClient->afficherCommandes();
                break;
            default:
                // Action par défaut
                $controleurClient->afficherConnexion();
        }
        break;

    case 'Admin':
        switch ($action) {
            case 'afficherIndex':
                $controleurAdmin->afficherIndex();
                break;
            case 'afficherIndexAdmin':
                $controleurAdmin->afficherIndexAdmin();
                break;
            case 'verifierConnexion':
                $controleurAdmin->verifierConnexion();
                break;
            case 'seDeconnecter':
                $controleurAdmin->seDeconnecter();
                break;
            case 'VoirCategorie':
                $controleurAdmin->VoirCategorie();
                break;
            case 'voirStatsProduits':
                $controleurAdmin->voirStatsProduits();
                break;
            case 'reapprovisionnerProduit':
                $controleurAdmin->reapprovisionnerProduit();
                break;
            default:
                // Action par défaut
                $controleurAdmin->afficherIndex();
        }
        break;

    case 'Categories':
        require_once Chemins::CONTROLEURS . 'ControleurCategories.class.php';
        $controleurCategories = new ControleurCategories();
        
        switch ($action) {
            case 'ajouter':
                $controleurCategories->ajouter();
                break;
            case 'supprimer':
                $controleurCategories->supprimer();
                break;
            case 'modifier':
                $controleurCategories->modifier();
                break;
            default:
                // Action par défaut
                $controleurCategories->afficher();
        }
        break;

    default:
        // Contrôleur par défaut
        $controleurProduits->afficher();
}

if (isset($_POST['test_submit'])) { error_log('>>> TEST : formulaire soumis !'); }

// Résumé du panier et pied de page

if ($displayHeaderFooter) {
    // require Chemins::VUES_PERMANENTES . 'v_resume_panier.inc.php';
    require Chemins::VUES_PERMANENTES . 'v_pied.inc.php';
}
?>