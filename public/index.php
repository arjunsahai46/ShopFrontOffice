<?php
/**
 * Point d'entrée MVC unique
 * 
 * Charge le bootstrap qui gère le routing
 * Supporte deux formats d'URL :
 * - Moderne : /produits/afficher (via .htaccess → ?url=produits/afficher)
 * - Ancien : ?controleur=Produits&action=afficher (compatibilité)
 */

require_once __DIR__ . '/../application/bootstrap.php';
