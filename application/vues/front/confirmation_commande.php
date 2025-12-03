<!-- -----------------------------------------------------------------------------
     Fichier : v_confirmation_commande.inc.php
     Rôle    : Vue affichant la confirmation de la commande et ses détails.
     ----------------------------------------------------------------------------- -->
<?php

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    header('Location: index.php?controleur=Client&action=afficherConnexion');
    return;
}

// Récupérer les informations du client
$client = GestionBoutique::getClientById($_SESSION['client_id']);

// Récupérer la dernière commande du client
$lesCommandes = GestionBoutique::getLesCommandes();
$derniereCommande = null;
foreach ($lesCommandes as $commande) {
    if ($commande->idClient == $_SESSION['client_id']) {
        $derniereCommande = $commande;
        break;
    }
}

// Inclure le CSS de confirmation
echo '<link rel="stylesheet" href="' . Chemins::CSS . 'pages/confirmation.css">';

if ($derniereCommande && $client) {
?>
    <div class="confirmation-container">
        <div class="confirmation-header">
            <h1>Confirmation de commande</h1>
            <div class="confirmation-message">
                <i class="fas fa-check-circle"></i>
                <p>Votre commande a été enregistrée avec succès !</p>
            </div>
        </div>

        <div class="confirmation-details">
            <div class="commande-info">
                <h2>Détails de la commande</h2>
                <p><strong>Numéro de commande :</strong> <?php echo str_pad($derniereCommande->id, 6, '0', STR_PAD_LEFT); ?></p>
                <p><strong>Date :</strong> <?php echo date('d/m/Y H:i', strtotime($derniereCommande->date)); ?></p>
            </div>

            <div class="client-info">
                <h2>Informations de livraison</h2>
                <p><strong>Nom :</strong> <?php echo $client->getNom() . ' ' . $client->getPrenom(); ?></p>
                <p><strong>Adresse :</strong> <?php echo $client->getRue(); ?></p>
                <p><strong>Code postal :</strong> <?php echo $client->getCodePostal(); ?></p>
                <p><strong>Ville :</strong> <?php echo $client->getVille(); ?></p>
            </div>

            <div class="produits-commandes">
                <h2>Produits commandés</h2>
                <table class="recap-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $lignesCommande = GestionBoutique::getToutesLesLignesCommandes();
                        foreach ($lignesCommande as $ligne) {
                            if ($ligne->idCommande == $derniereCommande->id) {
                                $produit = GestionBoutique::getProduitById($ligne->idProduit);
                                ?>
                                <tr>
                                    <td><?php echo $produit['nom']; ?></td>
                                    <td><?php echo number_format($ligne->prixUnitaire, 2, ',', ' '); ?> €</td>
                                    <td><?php echo $ligne->quantite; ?></td>
                                    <td><?php echo number_format($ligne->sousTotal, 2, ',', ' '); ?> €</td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Total</strong></td>
                            <td><strong><?php echo number_format($derniereCommande->sousTotal, 2, ',', ' '); ?> €</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="confirmation-actions">
            <a href="index.php?controleur=Produits&action=afficher" class="btn-continuer">Continuer mes achats</a>
            <a href="index.php?controleur=Client&action=afficherProfil" class="btn-profil">Voir mon profil</a>
        </div>
    </div>
<?php
}
?> 