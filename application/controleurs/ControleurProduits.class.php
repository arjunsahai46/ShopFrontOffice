<?php
require_once Chemins::MODELES . 'gestion_panier.class.php';
require_once Chemins::MODELES . 'gestion_client.class.php';

class ControleurProduits {

    public function __construct() {
        // Constructeur de la classe (vide pour l'instant)
    }

    public function afficher() {
        $categorie = isset($_REQUEST['categorie']) ? $_REQUEST['categorie'] : 'all';
        VariablesGlobales::$libelleCategorie = $categorie;
        VariablesGlobales::$lesProduits = ($categorie === 'all') ? 
            GestionBoutique::getLesProduits() : 
            GestionBoutique::getLesProduitsByCategorie($categorie);
        require Chemins::VUES . 'v_produits.inc.php';
    }

    public function MettreAJourPanier() {
        if (isset($_POST['quantites']) && is_array($_POST['quantites'])) {
            foreach ($_POST['quantites'] as $idProduit => $quantite) {
                if ($quantite > 0) {
                    Panier::modifierQteProduit($idProduit, (int)$quantite);
                } else {
                    Panier::retirerProduit($idProduit);
                }
            }
        }
        header("Location: index.php?controleur=Produits&action=afficherPanier");
        exit();
    }

    public function afficherPanier(): void {
        Panier::initialiser();
        $produitsPanier = Panier::getProduits();

        if (Panier::isVide()) {
            $message = "Votre panier ne contient aucun produit.";
        } else {
            $message = null;
        }
        // Inclure la vue du panier
        require Chemins::VUES . 'v_panier.inc.php';
    }

    public static function AjouterPanier(): void {
        // On autorise l'ajout au panier même sans être connecté
        if (isset($_REQUEST['produitID']) && isset($_REQUEST['quantite'])) {
            $idProduit = $_REQUEST['produitID'];
            $qte = $_REQUEST['quantite'];

            Panier::initialiser();
            Panier::ajouterProduit($idProduit, $qte);

            header('Location: index.php?controleur=Produits&action=afficherPanier');
            exit();
        } else {
            echo "Erreur de paramètre dans l'ajout du panier !";
        }
    }

    public function retirerPanier(): void {
        if (isset($_REQUEST['idProduit'])) {
            $idProduit = $_REQUEST['idProduit'];

            if (Panier::contains($idProduit)) {
                Panier::retirerProduit($idProduit);
                header('Location: index.php?controleur=Produits&action=afficherPanier');
            } else {
                echo "Le produit avec l'identifiant $idProduit n'est pas dans le panier.";
            }
        } else {
            echo "Erreur : Aucun identifiant de produit fourni pour le retrait.";
        }
    }

    public function viderPanier(): void {
        if (isset($_SESSION['produits'])) {
            $_SESSION['produits'] = array(); // Réinitialisation du panier
        }
        header('Location: index.php?controleur=Produits&action=afficherPanier'); // Correction de l'URL
        exit();
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
            exit();
        }

        if (Panier::isVide()) {
            error_log("Panier vide - redirection vers la page du panier");
            header('Location: index.php?controleur=Produits&action=afficherPanier');
            exit();
        }

        error_log("Tentative de récupération du client avec l'ID: " . $_SESSION['client_id']);
        
        try {
            // Récupérer les informations du client directement depuis GestionClient
            require_once Chemins::MODELES . 'gestion_client.class.php';
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
                exit();
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération du client: " . $e->getMessage());
            $client = null;
        }
        
        // Afficher la vue de l'adresse
        require Chemins::VUES . 'v_checkout_adresse.inc.php';
    }

    public function commander(): void {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['client_id'])) {
            header('Location: index.php?controleur=Client&action=afficherConnexion&error=connexion_requise');
            exit();
        }

        if (Panier::isVide()) {
            header('Location: index.php?controleur=Produits&action=afficherPanier');
            exit();
        }

        // Récupérer les produits du panier
        $produitsPanier = Panier::getProduits();
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
        Panier::vider();

        // Rediriger vers une page de confirmation
        header('Location: index.php?controleur=Produits&action=confirmationCommande');
        exit();
    }

    public function confirmationCommande(): void {
        require Chemins::VUES . 'v_confirmation_commande.inc.php';
    }
}
?>