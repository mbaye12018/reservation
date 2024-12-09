<?php
// send_message.php

// Activer l'affichage des erreurs pour le débogage (désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Définir le type de contenu comme JSON
header('Content-Type: application/json; charset=UTF-8');

// Inclure la configuration
require 'config.php';

// Récupérer les données JSON envoyées par le client
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Vérifier que le numéro de téléphone est fourni
if (!isset($data['phone'])) {
    echo json_encode(['success' => false, 'error' => 'Numéro de téléphone manquant.']);
    exit;
}

$phone = trim($data['phone']);

// Valider le format E.164
if (!preg_match('/^\+\d{10,15}$/', $phone)) {
    echo json_encode(['success' => false, 'error' => 'Format de numéro de téléphone invalide.']);
    exit;
}

// Préparer les données pour l'API Infobip
$messageId = uniqid();
$dataWA = [
    'messages' => [
        [
            'from' => '+' . INFOBIP_SENDER_NUMBER, // Format E.164 avec le signe '+'
            'to' => 'whatsapp:' . preg_replace('/\s+/', '', $phone),
            'messageId' => $messageId,
            'content' => [
                'templateName' => INFIBIP_TEMPLATE_NAME, // Nom exact du template
                'templateData' => [
                    'body' => [
                        // Remplacez par les valeurs réelles attendues par votre template
                        // Par exemple, si le template "authentication" attend un code, remplacez ci-dessous
                        'placeholders' => ['Votre Prénom', 'Votre Nom']
                    ]
                ],
                'language' => 'en-GB' // Format correct avec un tiret
            ],
            'callbackData' => 'Données de rappel',
            'notifyUrl' => INFIBIP_NOTIFY_URL,
            'urlOptions' => [
                'shortenUrl' => true,
                'trackClicks' => true,
                'trackingUrl' => INFIBIP_TRACKING_URL,
                'removeProtocol' => true,
                'customDomain' => INFIBIP_CUSTOM_DOMAIN
            ]
        ]
    ]
];

// Initialiser cURL
$ch = curl_init('https://n8n115.api.infobip.com/whatsapp/1/message/template');

// Configurer les options cURL
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataWA));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: App ' . INFOBIP_API_KEY,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Exécuter la requête cURL
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Analyser la réponse
$responseData = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300) {
    // Vérifier le statut de chaque message
    $messageStatus = $responseData['messages'][0]['status'];
    if ($messageStatus['groupName'] === 'DELIVERED') {
        echo json_encode(['success' => true, 'message' => 'Message WhatsApp envoyé avec succès.']);
    } else {
        // Gestion des autres statuts (comme REJECTED)
        echo json_encode([
            'success' => false,
            'error' => 'Échec de l\'envoi du message WhatsApp.',
            'details' => $messageStatus
        ]);
    }
} else {
    // Erreur lors de l'appel de l'API
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de l\'appel à l\'API Infobip.',
        'details' => $responseData
    ]);
}
?>
