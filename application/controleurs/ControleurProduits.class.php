<?php
require_once chemin(Chemins::MODELES . 'GestionPanier.class.php');
require_once chemin(Chemins::MODELES . 'GestionClient.class.php');

class ControleurProduits {

    public function __construct() {
        // Constructeur de la classe (vide pour l'instant)
    }

    public function afficher() {
        $categorie = isset($_REQUEST['categorie']) ? $_REQUEST['categorie'] : 'all';
        App::$libelleCategorie = $categorie;
        App::$lesProduits = ($categorie === 'all') ? 
            GestionBoutique::getLesProduits() : 
            GestionBoutique::getLesProduitsByCategorie($categorie);
        require chemin(Chemins::VUES . 'front/produits/liste.php');
    }

    public function MettreAJourPanier() {
        if (isset($_POST['quantites']) && is_array($_POST['quantites'])) {
            foreach ($_POST['quantites'] as $idProduit => $quantite) {
                if ($quantite > 0) {
                    GestionPanier::modifierQteProduit($idProduit, (int)$quantite);
                } else {
                    GestionPanier::retirerProduit($idProduit);
                }
            }
        }
        header("Location: index.php?controleur=Produits&action=afficherPanier");
        return;
    }

    public function afficherPanier(): void {
        GestionPanier::initialiser();
        $produitsPanier = GestionPanier::getProduits();

        if (GestionPanier::isVide()) {
            $message = "Votre panier ne contient aucun produit.";
        } else {
            $message = null;
        }
        // Inclure la vue du panier
        require chemin(Chemins::VUES . 'front/panier.php');
    }

    public static function AjouterPanier(): void {
        // On autorise l'ajout au panier même sans être connecté
        if (isset($_REQUEST['produitID']) && isset($_REQUEST['quantite'])) {
            $idProduit = $_REQUEST['produitID'];
            $qte = $_REQUEST['quantite'];

            GestionPanier::initialiser();
            GestionPanier::ajouterProduit($idProduit, $qte);

            header('Location: index.php?controleur=Produits&action=afficherPanier');
        } else {
            // Erreur de paramètre - rediriger vers le panier
            header('Location: index.php?controleur=Produits&action=afficherPanier&error=parametre');
        }
    }

    public function retirerPanier(): void {
        if (isset($_REQUEST['idProduit'])) {
            $idProduit = $_REQUEST['idProduit'];

            if (GestionPanier::contains($idProduit)) {
                GestionPanier::retirerProduit($idProduit);
                header('Location: index.php?controleur=Produits&action=afficherPanier');
            } else {
                // Produit non trouvé - rediriger vers le panier
                header('Location: index.php?controleur=Produits&action=afficherPanier&error=produit_inexistant');
            }
        } else {
            // Aucun identifiant fourni - rediriger vers le panier
            header('Location: index.php?controleur=Produits&action=afficherPanier&error=parametre_manquant');
        }
    }

    public function viderPanier(): void {
        if (isset($_SESSION['produits'])) {
            $_SESSION['produits'] = array(); // Réinitialisation du panier
        }
        header('Location: index.php?controleur=Produits&action=afficherPanier'); // Correction de l'URL
        return;
    }

    public function afficherCheckoutAdresse(): void {
        // S'assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Debug des données de session
        error_log("Session complète : " . print_r($_SESSION, true));
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['client_id'])) {
            error_log("Utilisateur non connecté - redirection vers la page de connexion");
            header('Location: index.php?controleur=Client&action=afficherConnexion&error=connexion_requise');
            return;
        }

        if (GestionPanier::isVide()) {
            error_log("Panier vide - redirection vers la page du panier");
            header('Location: index.php?controleur=Produits&action=afficherPanier');
            return;
        }

        error_log("Tentative de récupération du client avec l'ID: " . $_SESSION['client_id']);
        
        try {
            // Récupérer les informations du client directement depuis GestionClient
            require_once chemin(Chemins::MODELES . 'GestionClient.class.php');
            $client = GestionClient::getClientById($_SESSION['client_id']);
            
            if ($client) {
                error_log("Client récupéré avec succès de la BDD: " . print_r($client, true));
                
                // Créer un tableau avec toutes les données du client
                $clientData = [
                    'id' => $client->id,
                    'nom' => $client->nom,
                    'prenom' => $client->prenom,
                    'email' => $client->email,
                    'tel' => $client->tel,
                    'rue' => $client->rue,
                    'codePostal' => $client->codePostal,
                    'ville' => $client->ville
                ];
                
                // Mettre à jour les données de session
                $_SESSION['client_data'] = $clientData;
                
                error_log("Données client mises en session : " . print_r($_SESSION['client_data'], true));
            } else {
                error_log("Client non trouvé dans la base de données avec l'ID: " . $_SESSION['client_id']);
                session_destroy();
                header('Location: index.php?controleur=Client&action=afficherConnexion&error=client_inexistant');
                return;
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération du client: " . $e->getMessage());
            $client = null;
        }
        
        // Afficher la vue de l'adresse
        require chemin(Chemins::VUES . 'front/checkout/adresse.php');
    }

    public function commander(): void {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['client_id'])) {
            header('Location: index.php?controleur=Client&action=afficherConnexion&error=connexion_requise');
            return;
        }

        if (GestionPanier::isVide()) {
            header('Location: index.php?controleur=Produits&action=afficherPanier');
            return;
        }

        // Récupérer les produits du panier
        $produitsPanier = GestionPanier::getProduits();
        $totalCommande = 0;
        $date = date('Y-m-d H:i:s');
        $idClient = $_SESSION['client_id'];

        // Créer la commande
        $idCommande = GestionBoutique::ajouterCommande($idClient, $date);

        // Ajouter les lignes de commande
        foreach ($produitsPanier as $idProduit => $quantite) {
            $produit = GestionBoutique::getProduitById($idProduit);
            $prixUnitaire = $produit['prix'];
            $sousTotal = $prixUnitaire * $quantite;
            $totalCommande += $sousTotal;

            GestionBoutique::ajouterLigneCommande($idCommande, $idProduit, $quantite, $prixUnitaire, $sousTotal);
        }

        // Mettre à jour le sous-total de la commande
        GestionBoutique::modifierCommande($idCommande, $date, $idClient, $totalCommande);

        // Vider le panier
        GestionPanier::vider();

        // Rediriger vers une page de confirmation
        header('Location: index.php?controleur=Produits&action=confirmationCommande');
        return;
    }

    public function confirmationCommande(): void {
        require chemin(Chemins::VUES . 'front/confirmation_commande.php');
    }
}
?>