<?php

require_once chemin(Chemins::MODELES.'GestionCategorie.class.php');
require_once chemin(Chemins::MODELES.'GestionBoutique.class.php');

class ControleurCategories {

    public function __construct() {
// si on séparait les modèles, le constructeur donnerait son chemin

    }

    public function afficher() {
        App::$lesCategories = GestionBoutique::getLesCategories();
        // Menu catégories intégré dans entete.php
    }

    public function ajouter() {
        if (isset ($_POST['ajouter'])) {
            $libelle = $_POST['libelleCategorie'];
            gestionCategorie::ajouterCategorie($libelle);

            header("Location:index.php?controleur=Admin&action=VoirCategorie&display=minimal");
            return;
        }

        $this->afficher();
    }

    public function supprimer() {
        if (isset($_POST['supprimer'])){
            $libelle = $_POST['libelleCategorie'];
            gestionCategorie::supprimerCategorie($libelle);

            header("Location:index.php?controleur=Admin&action=VoirCategorie&display=minimal");
            return;
        }
        $this->afficher();
    }

    public function modifier() {
        if (isset($_POST['modifier'])){
            $id = $_POST['idCategorie'];
            $libelle = $_POST['libelleCategorie'];
            gestionCategorie::modifierCategorie($id, $libelle);

            header("Location:index.php?controleur=Admin&action=VoirCategorie&display=minimal");
            return;
        }
        $this->afficher();
    }
}
?>