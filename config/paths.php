<?php

class Paths {

    //Chemins à l'intérieur du dossier APPLICATION
    const MODELES = "application/modeles/";
    const VUES = "application/vues/";
    const VUES_LAYOUT = "application/vues/layout/";
    const VUES_PERMANENTES = "application/vues/layout/"; // Alias pour compatibilité
    const CONTROLEURS = "application/controleurs/";
    //Chemins à l'intérieur du dossier PUBLIC (absolus depuis la racine du site)
    const IMAGES = "/images/";
    const IMAGES_PRODUITS = "/images/produits/generic/"; // Images produits génériques (renommé de produit/ à generic/)
    const JS = "/js/";
    const CSS = '/css/'; // Dossier CSS principal
    const STYLES = '/css/'; // Alias pour compatibilité (redirige vers css/)
    //Autres chemins du site
    const CONFIG = "config/";
    const LIBS = "libs/";
    const VUES_ADMIN = "application/vues/admin/";
    const SERVICES = "application/services/";
    const VALIDATION = "application/validation/";
    const EXCEPTIONS = "application/exceptions/";
}

// Fonction helper pour obtenir un chemin complet avec ROOT_PATH si défini
function chemin($cheminRelatif) {
    if (defined('ROOT_PATH')) {
        return ROOT_PATH . $cheminRelatif;
    }
    return $cheminRelatif;
}

// Alias pour compatibilité
class_alias('Paths', 'Chemins');

?>

