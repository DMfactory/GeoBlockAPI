<?php
require 'geoip2.phar';
use GeoIp2\Database\Reader;

// Controlla che APCu sia attivo
if (!function_exists('apcu_store')) {
    die(json_encode(['error' => 'APCu non attivo']));
}

$reader = new Reader(__DIR__ . '/GeoLite2-Country.mmdb');

$ip = $_GET['ip'] ?? $_SERVER['REMOTE_ADDR'];
$domain = $_GET['domain'] ?? 'ND';

try {
    $record = $reader->country($ip);
    $country = $record->country->isoCode;

    // --- Funzione per salvare in APCu le ultime richieste ---
    $key = 'geo_last_requests';
    $log = apcu_fetch($key) ?: [];

    $log[] = [
        'time'    => date('H:i:s'),
        'ip'      => $ip,
        'country' => $country,
        'domain' => $domain

    ];

    // Mantieni solo le ultime 50 richieste
    if (count($log) > 150) {
        array_shift($log);
    }

    apcu_store($key, $log);

    // Risposta JSON per chi chiama l'API
    echo json_encode([
        'ip' => $ip,
        'country' => $country
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
