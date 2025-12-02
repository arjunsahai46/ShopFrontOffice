<?php
class ControleurPanier {
    
    public function __construct() {
        error_log("ControleurPanier::__construct() appelé");
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['client_data'])) {
            error_log("Session client_data non trouvée - Redirection vers la connexion");
            // header('Location: index.php?controleur=Client&action=connexion');
            // exit();
        } else {
            error_log("Session client_data trouvée : " . print_r($_SESSION['client_data'], true));
        }
    }

    public function afficherAdresse() {
        error_log("ControleurPanier::afficherAdresse() appelé (depuis une redirection ou accès direct)");
        require_once 'application/vues/v_checkout_adresse.inc.php';
    }

    public function validerAdresse() {
        error_log("ControleurPanier::validerAdresse() appelé");
        error_log("POST data reçues : " . print_r($_POST, true));
        
        // Récupérer et valider les données du formulaire
        $adresse = [
            'prenom' => htmlspecialchars(trim($_POST['prenom'] ?? '')),
            'nom' => htmlspecialchars(trim($_POST['nom'] ?? '')),
            'telephone' => htmlspecialchars(trim($_POST['telephone'] ?? '')),
            'code_postal' => htmlspecialchars(trim($_POST['code_postal'] ?? '')),
            'ville' => htmlspecialchars(trim($_POST['ville'] ?? '')),
            'adresse' => htmlspecialchars(trim($_POST['adresse'] ?? '')),
            'complement' => htmlspecialchars(trim($_POST['complement'] ?? ''))
        ];

        // Vérifier que les champs requis sont remplis
        $champs_requis = ['prenom', 'nom', 'telephone', 'code_postal', 'ville', 'adresse'];
        $erreurs = [];
        
        foreach ($champs_requis as $champ) {
            if (empty($adresse[$champ])) {
                $erreurs[] = "Le champ $champ est requis";
            }
        }

        if (!empty($erreurs)) {
            error_log("Erreurs de validation d'adresse : " . print_r($erreurs, true));
            // Rediriger vers le formulaire avec un message d'erreur
            $_SESSION['erreurs'] = $erreurs;
            header('Location: index.php?controleur=Panier&action=afficherAdresse');
            exit();
        }

        // Sauvegarder les données dans la session
        $_SESSION['adresse_livraison'] = $adresse;
        error_log("Adresse sauvegardée en session : " . print_r($adresse, true));

        // Rediriger vers la page de livraison
        header('Location: index.php?controleur=Panier&action=afficherLivraison');
        exit();
    }

    public function afficherLivraison() {
        error_log("ControleurPanier::afficherLivraison() appelé");
        
        // Initialiser le panier s'il n'existe pas
        if (!isset($_SESSION['panier'])) {
            require_once 'application/modeles/gestion_panier.class.php';
            Panier::initialiser();
            $produits = Panier::getProduits();
            $_SESSION['panier'] = [];
            
            foreach ($produits as $id => $quantite) {
                $produit = GestionBoutique::getProduitById($id);
                if ($produit) {
                    $produit['quantite'] = $quantite;
                    $_SESSION['panier'][] = $produit;
                }
            }
        }
        
        // Vérifier si le panier est vide
        if (empty($_SESSION['panier'])) {
            header('Location: index.php?controleur=Produits&action=afficherPanier');
            exit();
        }

        require_once 'application/vues/v_checkout_livraison.inc.php';
    }

    public function validerLivraison() {
        error_log("ControleurPanier::validerLivraison() appelé");
        error_log("POST data reçues pour validerLivraison: " . print_r($_POST, true));
        require_once 'application/modeles/gestion_boutique.class.php';
        $pdo = GestionBoutique::getPDO();
        $type_livraison = htmlspecialchars(trim($_POST['delivery_type'] ?? ''));
        if (empty($type_livraison)) {
            error_log('Erreur : type de livraison manquant');
            $_SESSION['erreurs'] = ["Le type de livraison est requis"];
            header('Location: index.php?controleur=Panier&action=afficherLivraison');
            exit();
        }
        $idLivraison = null;
        if ($type_livraison === 'home') {
            error_log('Traitement livraison à domicile');
            // Insérer l'adresse dans la table domicile
            $domRue = $_SESSION['adresse_livraison']['adresse'];
            $domCodePostal = $_SESSION['adresse_livraison']['code_postal'];
            $domVille = $_SESSION['adresse_livraison']['ville'];
            try {
                $stmt = $pdo->prepare("INSERT INTO domicile (domRue, domCodePostal, domVille) VALUES (?, ?, ?)");
                $stmt->execute([$domRue, $domCodePostal, $domVille]);
                $idDomicile = $pdo->lastInsertId();
            } catch (PDOException $e) {
                error_log('Erreur SQL domicile : ' . $e->getMessage());
            }
            // Insérer la livraison avec l'id du domicile
            $stmt = $pdo->prepare("INSERT INTO livraison (idDomicile, idRelais) VALUES (?, NULL)");
            $stmt->execute([$idDomicile]);
            $idLivraison = $pdo->lastInsertId();
        } elseif ($type_livraison === 'pickup' && isset($_POST['pickup_point'], $_POST['prNom'], $_POST['prRue'], $_POST['prCodePostal'], $_POST['prVille'])) {
            error_log('Traitement point relais');
            // Récupérer les infos du point relais depuis le formulaire
            $prNom = $_POST['prNom'] ?? '';
            $prRue = $_POST['prRue'] ?? '';
            $prCodePostal = $_POST['prCodePostal'] ?? '';
            $prVille = $_POST['prVille'] ?? '';
            // Insérer le point relais à chaque fois, même si doublon
            try {
                $stmt = $pdo->prepare("INSERT INTO pointrelais (prNom, prRue, prCodePostal, prVille) VALUES (?, ?, ?, ?)");
                $stmt->execute([$prNom, $prRue, $prCodePostal, $prVille]);
                $idPointRelais = $pdo->lastInsertId();
                error_log('Nouveau point relais inséré, id=' . $idPointRelais);
            } catch (PDOException $e) {
                error_log('Erreur SQL pointrelais : ' . $e->getMessage());
            }
            // Insérer la livraison avec l'id du point relais
            $stmt = $pdo->prepare("INSERT INTO livraison (idDomicile, idRelais) VALUES (NULL, ?)");
            $stmt->execute([$idPointRelais]);
            $idLivraison = $pdo->lastInsertId();
            // Stocker les infos du point relais en session
            $_SESSION['pickup_point_infos'] = [
                'prNom' => $prNom,
                'prRue' => $prRue,
                'prCodePostal' => $prCodePostal,
                'prVille' => $prVille
            ];
        } else if ($type_livraison === 'pickup') {
            error_log('Erreur : champs point relais manquants');
            $_SESSION['erreurs'] = ["Veuillez sélectionner un point relais"];
            header('Location: index.php?controleur=Panier&action=afficherLivraison');
            exit();
        }
        $_SESSION['idLivraison'] = $idLivraison;
        $_SESSION['livraison'] = [
            'type' => $type_livraison,
            'date_estimee' => date('Y-m-d', strtotime('+2 days'))
        ];
        error_log("Livraison enregistrée en session: " . print_r($_SESSION['livraison'], true));
        header('Location: index.php?controleur=Panier&action=afficherPaiement');
        exit();
    }

    // Méthode spécifique pour la livraison à domicile (solution temporaire)
    public function validerLivraisonDomicile() {
        error_log("ControleurPanier::validerLivraisonDomicile() appelé");
        
        // Vérifier que l'adresse a été renseignée
        if (!isset($_SESSION['adresse_livraison'])) {
            error_log("Erreur: adresse de livraison non trouvée en session");
            header('Location: index.php?controleur=Panier&action=afficherAdresse');
            exit();
        }
        
        // Sauvegarder les informations de livraison dans la session
        $_SESSION['livraison'] = [
            'type' => 'home',
            'date_estimee' => date('Y-m-d', strtotime('+2 days'))
        ];
        
        error_log("Livraison à domicile enregistrée en session: " . print_r($_SESSION['livraison'], true));
        
        // Rediriger vers la page de paiement avec un paramètre pour indiquer que c'est une redirection directe
        header('Location: index.php?controleur=Panier&action=afficherPaiement&direct=1');
        exit();
    }

    public function afficherPaiement() {
        error_log("ControleurPanier::afficherPaiement() appelé avec GET: " . print_r($_GET, true));
        
        // Vérifier si les étapes précédentes ont été complétées
        if (!isset($_SESSION['adresse_livraison'])) {
            error_log("Adresse de livraison non trouvée en session - redirection vers adresse");
            header('Location: index.php?controleur=Panier&action=afficherAdresse');
            exit();
        }
        
        // Vérifier si les informations de livraison existent
        if (!isset($_SESSION['livraison'])) {
            error_log("Informations de livraison non trouvées en session - redirection vers livraison");
            header('Location: index.php?controleur=Panier&action=afficherLivraison');
            exit();
        }
        
        // Initialiser le panier s'il n'existe pas
        if (!isset($_SESSION['panier'])) {
            require_once 'application/modeles/gestion_panier.class.php';
            Panier::initialiser();
            $produits = Panier::getProduits();
            $_SESSION['panier'] = [];
            
            foreach ($produits as $id => $quantite) {
                $produit = GestionBoutique::getProduitById($id);
                if ($produit) {
                    $produit['quantite'] = $quantite;
                    $_SESSION['panier'][] = $produit;
                }
            }
        }
        
        // Vérifier si le panier est vide
        if (empty($_SESSION['panier'])) {
            header('Location: index.php?controleur=Produits&action=afficherPanier');
            exit();
        }

        require_once 'application/vues/v_checkout_paiement.inc.php';
    }
    
    public function validerPaiement() {
        error_log('>>> validerPaiement appelée');
        error_log('>>> POST reçu dans validerPaiement : ' . print_r($_POST, true));
        if (empty($_POST['payment_method'])) {
            error_log('Redirection : payment_method manquant');
            header('Location: index.php?controleur=Panier&action=afficherAdresse');
            exit();
        }
        if (!isset($_POST['terms_consent'])) {
            error_log('Redirection : terms_consent non coché');
            header('Location: index.php?controleur=Panier&action=afficherAdresse');
            exit();
        }
        $paymentMethod = $_POST['payment_method'];
        $cardNumber = $_POST['card_number'] ?? '';
        $cardName = $_POST['card_name'] ?? '';
        $cardExpiry = $_POST['card_expiry'] ?? '';
        $cardCVV = $_POST['card_cvc'] ?? '';
        if(empty($cardNumber) || empty($cardName) || empty($cardExpiry) || empty($cardCVV)) {
            error_log('Redirection : informations carte manquantes');
            header('Location: index.php?controleur=Panier&action=paiement&erreur=card_details');
            exit();
        }
        require_once("application/modeles/ModelePDO.class.php");
        $pdo = ModelePDO::getPDO();
        try {
            $pdo->beginTransaction();
            $dateExpiration = null;
            if (preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $cardExpiry, $matches)) {
                $month = $matches[1];
                $year = '20' . $matches[2];
                $dateExpiration = $year . '-' . $month . '-01';
            }
            error_log('Date expiration transformée : ' . $dateExpiration);
            if (empty($dateExpiration)) {
                error_log('Redirection : date d\'expiration non valide');
                header('Location: index.php?controleur=Panier&action=afficherPaiement&erreur=date');
                exit();
            }
            $stmt = $pdo->prepare("INSERT INTO paiement (numeroCarte, nomCarte, dateExpiration, codeConfidentiel) VALUES (?, ?, ?, ?)");
            $stmt->execute([$cardNumber, $cardName, $dateExpiration, $cardCVV]);
            $idPaiement = $pdo->lastInsertId();
            $idClient = $_SESSION['client_data']['id'];
            $dateCommande = date('Y-m-d H:i:s');
            $sousTotal = 0;
            foreach ($_SESSION['panier'] as $item) {
                $sousTotal += $item['prix'] * $item['quantite'];
            }
            $moyPaiement = $_POST['payment_method'] ?? 'carte';
            switch ($moyPaiement) {
                case 'card':
                    $moyPaiement = 'CB';
                    break;
                case 'paypal':
                    $moyPaiement = 'PayPal';
                    break;
                case 'floa3':
                case 'floa6':
                    $moyPaiement = 'FLOA';
                    break;
            }
            $idLivraison = $_SESSION['idLivraison'] ?? null;
            if (!$idLivraison) {
                error_log('Erreur : idLivraison manquant');
                throw new Exception('Erreur lors de la sélection de la livraison.');
            }
            $stmt = $pdo->prepare("INSERT INTO commande (date, idClient, sousTotal, moyPaiement, idLivraison) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$dateCommande, $idClient, $sousTotal, $moyPaiement, $idLivraison]);
            $idCommande = $pdo->lastInsertId();
            $_SESSION['commande']['id'] = $idCommande;
            $stmt = $pdo->prepare("INSERT INTO lignedecommande (idCommande, idProduit, quantite, prixUnitaire, sousTotal) VALUES (?, ?, ?, ?, ?)");
            foreach ($_SESSION['panier'] as $item) {
                $sousTotalLigne = $item['prix'] * $item['quantite'];
                $stmt->execute([
                    $idCommande,
                    $item['id'],
                    $item['quantite'],
                    $item['prix'],
                    $sousTotalLigne
                ]);
            }
            // Appel de la procédure stockée pour vider le panier du client en base
            $stmt = $pdo->prepare("CALL ViderPanierClient(:idClient)");
            $stmt->bindValue(':idClient', $idClient, PDO::PARAM_INT);
            $stmt->execute();
            $pdo->commit();
            unset($_SESSION['panier']);
            unset($_SESSION['produits']);
            error_log('Redirection : confirmation de commande');
            header('Location: index.php?controleur=Panier&action=confirmation');
            exit();
        } catch (Exception $e) {
            error_log('Erreur SQL : ' . $e->getMessage());
            $pdo->rollBack();
            header('Location: index.php?controleur=Panier&action=paiement&erreur=sql');
            exit();
        }
    }
    
    public function confirmation() {
        error_log("ControleurPanier::confirmation() appelé");
        error_log("GET: " . print_r($_GET, true));
        error_log("POST: " . print_r($_POST, true));
        error_log("SESSION: " . print_r($_SESSION, true));

        require_once 'application/modeles/gestion_boutique.class.php';

        // Récupérer l'ID de la commande depuis la session
        $idCommande = $_SESSION['commande']['id'] ?? null;
        $commande = null;
        $lignesCommande = [];
        $livraison = null;
        $pointRelais = null;
        if ($idCommande) {
            $commande = GestionBoutique::getCommandeById($idCommande);
            $lignesCommande = GestionBoutique::getLignesCommandeById($idCommande);
            if ($commande && $commande->idLivraison) {
                $livraison = GestionBoutique::getLivraisonById($commande->idLivraison);
                if ($livraison && $livraison->idRelais) {
                    $pointRelais = GestionBoutique::getPointRelaisById($livraison->idRelais);
                }
            }
        }

        // Récupérer l'adresse de livraison depuis la session
        $adresseLivraison = $_SESSION['adresse_livraison'] ?? null;

        require_once 'application/vues/v_checkout_confirmation.inc.php';
    }

    public function processPayPalPayment() {
        error_log("ControleurPanier::processPayPalPayment() appelé avec les données POST: " . print_r($_POST, true));
        
        // Vérifier que la méthode de paiement et le montant sont définis
        if (!isset($_POST['payment_type']) || !isset($_POST['amount'])) {
            error_log("Paramètres manquants pour processPayPalPayment");
            header('Location: index.php?controleur=Panier&action=afficherPaiement&erreur=parametres');
            exit();
        }
        
        // Récupérer les données
        $paymentType = $_POST['payment_type'];
        $amount = floatval($_POST['amount']);
        
        // Vérifier que le montant est valide
        if ($amount <= 0) {
            error_log("Montant invalide pour processPayPalPayment: $amount");
            header('Location: index.php?controleur=Panier&action=afficherPaiement&erreur=montant');
            exit();
        }
        
        error_log("Création d'une commande PayPal avec les données: type=$paymentType, montant=$amount");
        
        // Simuler la création d'une commande
        $_SESSION['commande'] = [
            'id' => 'CMD-' . substr(uniqid(), -8),
            'date' => date('Y-m-d H:i:s'),
            'montant' => $amount,
            'methode_paiement' => 'paypal',
            'type_paiement' => $paymentType,
            'statut' => 'en_cours'
        ];
        
        // Enregistrer l'information dans la session
        $_SESSION['payment_info'] = [
            'method' => 'paypal',
            'type' => $paymentType,
            'amount' => $amount,
            'date' => date('Y-m-d H:i:s')
        ];
        
        error_log("État de la session après création commande: " . print_r($_SESSION, true));
        
        // Utiliser les paramètres corrects pour PayPal
        $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        
        // L'URL complète de retour à votre site (avec le protocole et le domaine)
        $server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $base_url = $protocol . $server_name;
        
        if ($server_name === 'localhost' || strpos($server_name, '127.0.0.1') !== false) {
            // En local, ajoutez le chemin complet
            $dir_name = dirname($_SERVER['SCRIPT_NAME']);
            if ($dir_name == '/' || $dir_name == '\\') {
                $dir_name = '';
            }
            $base_url .= $dir_name;
        }
        
        // Paramètres pour PayPal
        $paypal_params = [
            'cmd' => '_xclick',                     // Transaction de base
            'business' => 'sb-43aiqa28175353@business.example.com', // Votre email PayPal sandbox
            'item_name' => 'Commande ' . $_SESSION['commande']['id'],
            'item_number' => $_SESSION['commande']['id'],
            'amount' => $amount,
            'currency_code' => 'EUR',
            'return' => $base_url . '/checkout_test.php',  // Page de retour après paiement réussi
            'cancel_return' => $base_url . '/index.php?controleur=Panier&action=afficherPaiement',
            'notify_url' => $base_url . '/index.php?controleur=Panier&action=ipnHandler',
            'custom' => $paymentType . '|' . $_SESSION['commande']['id'],
            'charset' => 'utf-8',
            'no_shipping' => '1',        // Ne pas demander d'adresse de livraison (déjà collectée)
            'no_note' => '1',            // Ne pas permettre de notes
            'rm' => '2',                 // Retour avec méthode POST
            'lc' => 'FR',                // Locale française
            'bn' => 'PP-BuyNowBF'        // Identifiant de bouton
        ];
        
        // Si c'est un paiement en 4x
        if ($paymentType === '4x') {
            $paypal_params['item_name'] = 'Commande ' . $_SESSION['commande']['id'] . ' (Paiement en 4x)';
        }
        
        // Construire l'URL PayPal
        $paypal_redirect = $paypal_url . '?' . http_build_query($paypal_params);
        
        error_log("Redirection vers PayPal : " . $paypal_redirect);
        
        // Rediriger vers PayPal
        header("Location: " . $paypal_redirect);
        exit();
    }
    
    public function ipnHandler() {
        // Cette méthode traiterait normalement les notifications instantanées de paiement de PayPal
        // Pour simplifier, on ne l'implémente pas complètement
        error_log("ControleurPanier::ipnHandler() appelé - IPN PayPal reçu");
        
        // En production, il faudrait vérifier l'authenticité de l'IPN avec PayPal
        // et mettre à jour le statut de la commande en conséquence
        
        // Pas de redirection ici car cette méthode est appelée par PayPal, pas par l'utilisateur
    }

    public function testPayPal() {
        // Cette méthode est uniquement pour tester le processus PayPal
        echo "<h1>Test de redirection PayPal</h1>";
        echo "<p>Cette page simule le processus de paiement PayPal sans aucune redirection externe.</p>";
        
        // Simuler la création d'une commande
        $_SESSION['commande'] = [
            'id' => 'CMD-TEST-' . substr(uniqid(), -8),
            'date' => date('Y-m-d H:i:s'),
            'montant' => 100,
            'methode_paiement' => 'paypal',
            'type_paiement' => 'standard',
            'statut' => 'payé'
        ];
        
        echo "<p>Commande créée dans la session avec l'ID: <strong>" . $_SESSION['commande']['id'] . "</strong></p>";
        echo "<pre>SESSION: " . print_r($_SESSION, true) . "</pre>";
        
        echo "<p><a href='index.php?controleur=Panier&action=confirmation'>Aller à la page de confirmation</a></p>";
    }
}
?> 