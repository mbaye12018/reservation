<?php
// index.php

// Activer l'affichage des erreurs pour le débogage (désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// Configuration Infobip - REMPLACEZ CES VALEURS PAR VOS PROPRES INFORMATIONS
$apiKeyWA = 'ac6b0a32a15d86d1c3b6e8db0157ac8f-43269c9d-bdce-470c-ba7b-5d11ba275a37'; // Remplacez par votre clé API Infobip
$senderWA = '+447307810250'; // Remplacez par votre numéro WhatsApp autorisé (format E.164 avec '+')
$templateName = 'authentication'; // Nom exact de votre template approuvé dans Infobip
$templateId = '1548546645941697'; // ID de modèle du template (si nécessaire)

// Fonction pour envoyer le message via Infobip
function sendWhatsAppMessage($phone, $apiKey, $sender, $templateName) {
    // Préparer les données pour l'API Infobip
    $dataWA = [
        'messages' => [
            [
                'from' => $sender, // Format E.164 avec le signe '+'
                'to' => 'whatsapp:' . preg_replace('/\s+/', '', $phone), // Numéro du destinataire avec préfixe 'whatsapp:'
                'messageId' => uniqid(),
                'content' => [
                    'templateName' => $templateName, // Nom exact du template
                    'templateData' => [
                        'body' => [
                            // Remplacez par les valeurs réelles attendues par votre template
                            // Par exemple, si le template "authentication" attend un code, remplacez ci-dessous
                            'placeholders' => ['John', 'Doe'] // Exemple de prénom et nom
                        ]
                    ],
                    'language' => 'en_GB' // Format correct avec un tiret
                ],
                'callbackData' => 'Données de rappel', // Optionnel : données de rappel
                'notifyUrl' => 'https://www.votresite.com/whatsapp_callback', // Optionnel : URL de notification
                'urlOptions' => [
                    'shortenUrl' => true,
                    'trackClicks' => true,
                    'trackingUrl' => 'https://www.votresite.com/click_report', // Remplacez par votre URL de suivi
                    'removeProtocol' => true,
                    'customDomain' => 'votredomaine.com' // Remplacez par votre domaine personnalisé
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
        'Authorization: App ' . $apiKey,
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
            return ['success' => true, 'message' => 'Message WhatsApp envoyé avec succès.'];
        } else {
            // Gestion des autres statuts (comme REJECTED)
            return [
                'success' => false,
                'error' => 'Échec de l\'envoi du message WhatsApp.',
                'details' => $messageStatus
            ];
        }
    } else {
        // Erreur lors de l'appel de l'API
        return [
            'success' => false,
            'error' => 'Erreur lors de l\'appel à l\'API Infobip.',
            'details' => $responseData
        ];
    }
}

// Gestion de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

    // Initialiser la réponse
    $response = [];

    // Valider le numéro de téléphone (format E.164)
    if (empty($phone)) {
        $response = ['success' => false, 'error' => 'Le numéro de téléphone est requis.'];
    } elseif (!preg_match('/^\+\d{10,15}$/', $phone)) {
        $response = ['success' => false, 'error' => 'Format de numéro de téléphone invalide. Utilisez le format E.164 (ex: +221773792737).'];
    } else {
        // Extraire le prénom et le nom (pour cet exemple, nous allons utiliser des valeurs statiques)
        // Vous pouvez adapter cela pour récupérer dynamiquement ces valeurs si nécessaire
        $firstname = 'John'; // Exemple de prénom
        $lastname = 'Doe';    // Exemple de nom

        // Envoyer le message via Infobip
        $sendResult = sendWhatsAppMessage($phone, $apiKeyWA, $senderWA, $templateName);

        if ($sendResult['success']) {
            $response = ['success' => true, 'message' => $sendResult['message']];
        } else {
            $response = [
                'success' => false,
                'error' => $sendResult['error'],
                'details' => isset($sendResult['details']) ? $sendResult['details'] : null
            ];
        }
    }

    // Retourner la réponse en JSON
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Envoi de Message WhatsApp via Infobip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0 16px 0;
            box-sizing: border-box;
            border: 2px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .response {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Envoyer un Message WhatsApp</h2>
    <form method="POST" id="whatsappForm">
        <label for="phone">Numéro de téléphone (format E.164, ex: +221773792737) :</label>
        <input type="text" id="phone" name="phone" placeholder="+221773792737" required>
        <button type="submit">Envoyer le Message</button>
    </form>
    <div id="response" class="response"></div>
</div>

<script>
    document.getElementById('whatsappForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const phone = document.getElementById('phone').value.trim();
        const responseDiv = document.getElementById('response');

        // Réinitialiser le message précédent
        responseDiv.style.display = 'none';
        responseDiv.textContent = '';
        responseDiv.className = 'response';

        // Valider le format E.164
        const e164Regex = /^\+\d{10,15}$/;
        if (!e164Regex.test(phone)) {
            showResponse('Format de numéro de téléphone invalide. Utilisez le format E.164 (ex: +221773792737).', 'error');
            return;
        }

        // Créer une requête POST avec AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.success) {
                            showResponse(res.message, 'success');
                        } else {
                            let errorMsg = res.error;
                            if (res.details) {
                                errorMsg += ' Détails: ' + JSON.stringify(res.details);
                            }
                            showResponse(errorMsg, 'error');
                        }
                    } catch (e) {
                        showResponse('Erreur de traitement de la réponse du serveur.', 'error');
                    }
                } else {
                    showResponse('Erreur lors de la communication avec le serveur.', 'error');
                }
            }
        };

        // Envoyer les données
        const params = 'phone=' + encodeURIComponent(phone);
        xhr.send(params);
    });

    function showResponse(message, type) {
        const responseDiv = document.getElementById('response');
        responseDiv.textContent = message;
        responseDiv.classList.add(type);
        responseDiv.style.display = 'block';
    }
</script>

</body>
</html>
