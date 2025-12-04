<?php
/**
 * Point d'entrée MVC unique
 * 
 * Charge le bootstrap qui gère le routing
 * Supporte deux formats d'URL :
 * - Moderne : /produits/afficher (via .htaccess → ?url=produits/afficher)
 * - Ancien : ?controleur=Produits&action=afficher (compatibilité)
 */

// PROTECTION: Si c'est une requête pour un fichier statique, ne pas exécuter
// (ne devrait jamais arriver ici si .htaccess fonctionne correctement)
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot|map|pdf|zip|txt|json|xml)$/i', $request_uri)) {
    http_response_code(404);
    header('Content-Type: text/plain');
    exit('Static file should not be processed by index.php. Check .htaccess configuration.');
}

require_once __DIR__ . '/../application/bootstrap.php';
?>
