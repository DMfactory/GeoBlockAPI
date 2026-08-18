<?php
/**
 * Aggiorna GeoLite2-Country.mmdb da GitHub
 * Mantiene backup del vecchio file con "_old"
 */

$mmdbUrl = "https://github.com/P3TERX/GeoLite.mmdb/raw/download/GeoLite2-Country.mmdb";
$savePath = __DIR__ . "/GeoLite2-Country.mmdb";
$oldPath = __DIR__ . "/GeoLite2-Country_old.mmdb";

// Elimina eventuale vecchio backup
if (file_exists($oldPath)) {
    unlink($oldPath);
}

// Rinomina il file attuale come backup
if (file_exists($savePath)) {
    rename($savePath, $oldPath);
}

// Scarica il nuovo file
$ch = curl_init($mmdbUrl);
$fp = fopen($savePath, 'w+');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_FAILONERROR, true);

if (!curl_exec($ch)) {
    echo "Errore download: " . curl_error($ch) . PHP_EOL;
    fclose($fp);
    // Ripristina il backup se il download fallisce
    if (file_exists($oldPath)) {
        rename($oldPath, $savePath);
    } else {
        unlink($savePath);
    }
    curl_close($ch);
    exit(1);
}

curl_close($ch);
fclose($fp);

echo "GeoLite2-Country.mmdb aggiornato con successo!" . PHP_EOL;
