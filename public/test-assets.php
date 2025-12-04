<?php
/**
 * Script de test pour vérifier les chemins des assets
 * À supprimer après diagnostic
 */

// Simuler le calcul de BASE_ASSET_PATH comme dans bootstrap.php
$script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$script_dir = dirname($script_name);

$base_asset_path = rtrim($script_dir, '/');
if ($script_dir === '/' || $script_dir === '\\' || $script_dir === '.') {
    $base_asset_path = '';
}

function asset_path($path) {
    global $base_asset_path;
    $base = $base_asset_path;
    $asset = ltrim($path, '/');
    
    if (empty($base)) {
        return '/' . $asset;
    }
    
    $base = rtrim($base, '/');
    return $base . '/' . $asset;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Assets Paths</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Diagnostic des chemins d'assets</h1>
    
    <div class="info">
        <h2>Variables serveur :</h2>
        <p><strong>SCRIPT_NAME:</strong> <?php echo htmlspecialchars($script_name); ?></p>
        <p><strong>SCRIPT_DIR:</strong> <?php echo htmlspecialchars($script_dir); ?></p>
        <p><strong>BASE_ASSET_PATH:</strong> <?php echo htmlspecialchars($base_asset_path); ?></p>
        <p><strong>DOCUMENT_ROOT:</strong> <?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A'); ?></p>
        <p><strong>REQUEST_URI:</strong> <?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A'); ?></p>
    </div>
    
    <div class="info">
        <h2>Chemins générés :</h2>
        <p><strong>CSS Bootstrap:</strong> <?php echo asset_path('css/vendor/bootstrap.min.css'); ?></p>
        <p><strong>JS Bootstrap:</strong> <?php echo asset_path('js/vendor/bootstrap.bundle.min.js'); ?></p>
    </div>
    
    <div class="info">
        <h2>Vérification des fichiers :</h2>
        <?php
        $css_file = __DIR__ . '/css/vendor/bootstrap.min.css';
        $js_file = __DIR__ . '/js/vendor/bootstrap.bundle.min.js';
        
        echo '<p><strong>CSS existe:</strong> ';
        if (file_exists($css_file)) {
            echo '<span class="success">OUI</span> (' . filesize($css_file) . ' bytes)';
        } else {
            echo '<span class="error">NON</span> (chemin: ' . htmlspecialchars($css_file) . ')';
        }
        echo '</p>';
        
        echo '<p><strong>JS existe:</strong> ';
        if (file_exists($js_file)) {
            echo '<span class="success">OUI</span> (' . filesize($js_file) . ' bytes)';
        } else {
            echo '<span class="error">NON</span> (chemin: ' . htmlspecialchars($js_file) . ')';
        }
        echo '</p>';
        ?>
    </div>
    
    <div class="info">
        <h2>Test de chargement :</h2>
        <p><a href="<?php echo asset_path('css/vendor/bootstrap.min.css'); ?>" target="_blank">Tester CSS Bootstrap</a></p>
        <p><a href="<?php echo asset_path('js/vendor/bootstrap.bundle.min.js'); ?>" target="_blank">Tester JS Bootstrap</a></p>
    </div>
</body>
</html>

