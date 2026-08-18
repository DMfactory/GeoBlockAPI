<?php
/**
 * Plugin Name: DM-GeoBlock
 * Description: Blocca l'accesso al frontend WordPress per utenti fuori dall'Italia usando un server remoto come sorgente GeoIP.
 * Version: 1.2
 * Author: DMfactory.cloud
 */

// Blocca accesso diretto al file
if (!defined('ABSPATH')) exit;

add_action('template_redirect', function() {

    // Non applicare il blocco nel backend (wp-admin)
    if (is_admin()) return;

    // 👉 URL API REMOTA (modifica con il tuo server)
    $remote_api = "https://apps.dmfactory.cloud/geoip/api.php"; //qui inserisci l'url del server api
    $domain_access= "Ciao.it"; //inserisci il dominio del sito 

    // IP del visitatore
    $ip = $_SERVER['REMOTE_ADDR'];

    // Costruisci la chiamata
    $url = $remote_api . "?ip=" . urlencode($ip)."&domain=".urlencode($domain_access);

    // Recupero risposta
    $response = @file_get_contents($url);

    // Se l'API non risponde -> bloccare o permettere? Qui blocchiamo per sicurezza.
    if ($response === false) {
        wp_die(
            "Impossibile verificare la tua nazione di origine: il server non risponde",
            "Accesso Negato",
            ["response" => 403]
        );
        exit;
    }

    // Decodifica JSON
    $data = json_decode($response, true);

    // Verifica formato valido
    if (!isset($data["country"])) {
        wp_die(
            "Errore nella verifica della nazione: country not set",
            "Accesso Negato",
            ["response" => 403]
        );
        exit;
    }

    // 🚫 Se NON è Italia → blocco
    if ($data["country"] !== "IT") {
        wp_die(
            "Accesso Negato per questa nazione. <br> Nazione rilevata: ".$data["country"]." <br> IP: ".$ip,
            "Accesso Negato",
            ["response" => 403]
        );
        exit;
    }

    // 🇮🇹 Se è Italia → tutto ok
});
