<?php
// --- Controllo GeoIP Italia ---
require_once __DIR__ . '/geoip2.phar';  // PHAR nella stessa cartella
use GeoIp2\Database\Reader;

$ip = $_SERVER['REMOTE_ADDR'];

try {
    $reader = new Reader(__DIR__ . '/GeoLite2-Country.mmdb');
    $record = $reader->country($ip);
    $country = $record->country->isoCode;

    if ($country !== "IT") {
        header("HTTP/1.1 403 Forbidden");
        die("Accesso consentito solo dall'Italia.<br> Nazione Identificata: ".$country. "<br> IP: ".$ip );
    }
} catch (Exception $e) {
    header("HTTP/1.1 403 Forbidden");
    die("Impossibile determinare la nazione <br> IP: ".$ip);
}

echo $country;

