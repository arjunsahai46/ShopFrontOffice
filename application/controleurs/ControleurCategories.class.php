<?php

require_once Chemins::MODELES.'gestion_categorie.class.php';
require_once Chemins::MODELES.'gestion_boutique.class.php';

class ControleurCategories {

    public function __construct() {
// si on séparait les modèles, le constructeur donnerait son chemin

    }

    public function afficher() {
        VariablesGlobales::$lesCategories = GestionBoutique::getLesCategories();
        require Chemins::VUES_PERMANENTES . 'v_menu_categories.inc.php';
    }

    public function ajouter() {
        if (isset ($_POST['ajouter'])) {
            $libelle = $_POST['libelleCategorie'];
            gestionCategorie::ajouterCategorie($libelle);

            header("Location:index.php?controleur=Admin&action=VoirCategorie&display=minimal");
            exit();
        }

        $this->afficher();
    }

    public function supprimer() {
        if (isset($_POST['supprimer'])){
            $libelle = $_POST['libelleCategorie'];
            gestionCategorie::supprimerCategorie($libelle);

            header("Location:index.php?controleur=Admin&action=VoirCategorie&display=minimal");
            exit();
        }
        $this->afficher();
    }

    public function modifier() {
        if (isset($_POST['modifier'])){
            $id = $_POST['idCategorie'];
            $libelle = $_POST['libelleCategorie'];
            gestionCategorie::modifierCategorie($id, $libelle);

            header("Location:index.php?controleur=Admin&action=VoirCategorie&display=minimal");
            exit();
        }
        $this->afficher();
    }
}
?>