<?php
session_start();

// Activer l'affichage des erreurs pour le débogage (désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Définir le type de contenu comme JSON
header('Content-Type: application/json; charset=UTF-8');

// Configuration directe des informations Infobip
$apiKeyWA = 'ac6b0a32a15d86d1c3b6e8db0157ac8f-43269c9d-bdce-470c-ba7b-5d11ba275a37'; // Remplacez par votre nouvelle clé API
$senderWA = '+447860099299'; // Remplacez par votre numéro WhatsApp autorisé (format E.164 sans le préfixe 'whatsapp:')

// Vérifier si la requête est en méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération de l'OTP saisi
    $enteredOtp = isset($_POST['otp']) ? trim($_POST['otp']) : '';

    // Vérifier que l'OTP est saisi
    if (empty($enteredOtp)) {
        echo json_encode(['success' => false, 'error' => 'Le code OTP est requis.']);
        exit;
    }

    // Vérifier que l'OTP et les données utilisateur existent dans la session
    if (!isset($_SESSION['otp']) || !isset($_SESSION['user_data'])) {
        echo json_encode(['success' => false, 'error' => 'Session expirée ou invalide. Veuillez soumettre à nouveau le formulaire.']);
        exit;
    }

    $storedOtp = $_SESSION['otp'];
    $userData = $_SESSION['user_data'];

    // Vérifier si l'OTP correspond
    if ($enteredOtp != $storedOtp) {
        echo json_encode(['success' => false, 'error' => 'Code OTP incorrect.']);
        exit;
    }

    // OTP correct, procéder à la génération du badge et à l'enregistrement des données

    // Extraire les données utilisateur
    $firstname = htmlspecialchars($userData['firstname']);
    $lastname  = htmlspecialchars($userData['lastname']);
    $email     = htmlspecialchars($userData['email']);
    $role      = htmlspecialchars($userData['role']);
    $phone     = htmlspecialchars($userData['phone']);

    // Valider le format du numéro de téléphone (E.164)
    if (!preg_match('/^\+\d{10,15}$/', $phone)) {
        echo json_encode(['success' => false, 'error' => 'Format de numéro de téléphone invalide.']);
        exit;
    }

    // Définir les répertoires nécessaires
    $directories = ['qrcodes', 'badges_png', 'badges_pdf'];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0775, true)) {
                echo json_encode(['success' => false, 'error' => "Impossible de créer le répertoire $dir."]);
                exit;
            }
        }
    }

    // Chemin vers l'image de fond
    $backgroundImage = 'images/Govathon 2024 (1).png'; // Renommé pour éviter les espaces et parenthèses
    if (!file_exists($backgroundImage)) {
        echo json_encode(['success' => false, 'error' => "L'image de fond est introuvable."]);
        exit;
    }

    // Connexion à la base de données
    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $dbnameDB = "reservation";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbnameDB);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Impossible de se connecter à la base de données.']);
        exit;
    }

    // Charger la librairie QRCode
    if (!file_exists('phpqrcode/qrlib.php')) {
        echo json_encode(['success' => false, 'error' => 'La librairie QR Code est introuvable.']);
        exit;
    }
    require 'phpqrcode/qrlib.php';

    // Génération du contenu vCard
    $vcard = "BEGIN:VCARD\n";
    $vcard .= "VERSION:3.0\n";
    $vcard .= "FN:$firstname $lastname\n";
    $vcard .= "N:$lastname;$firstname;;;\n";
    $vcard .= "EMAIL:$email\n";
    $vcard .= "TEL:$phone\n";
    $vcard .= "TITLE:$role\n";
    $vcard .= "END:VCARD";

    // Générer le QR code
    $qrOutputFile = 'qrcodes/badge_' . $firstname . '_' . $lastname . '.png';
    QRcode::png($vcard, $qrOutputFile, QR_ECLEVEL_M, 4, 1);

    // Vérifier que le QR code a été généré
    if (!file_exists($qrOutputFile)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du QR Code.']);
        exit;
    }

    // Génération du badge PNG complet
    $background = imagecreatefrompng($backgroundImage);
    if (!$background) {
        echo json_encode(['success' => false, 'error' => 'Impossible de charger l\'image de fond.']);
        exit;
    }

    // Définir les couleurs
    $black = imagecolorallocate($background, 0, 0, 0);

    // Chemin vers la police TTF
    $fontPath = __DIR__ . '/fonts/LiberationSans-Bold.ttf';
    if (!file_exists($fontPath)) {
        echo json_encode(['success' => false, 'error' => 'Le fichier de police est introuvable.']);
        exit;
    }

    // Définir les paramètres de texte
    $fontSize = 27; // Taille de la police en points
    $angle = 0; // Angle de rotation
    $textX = 70; // Position X (ajustez selon vos besoins)
    $prenameY = 900; // Position Y pour "Prénom" (ajustez selon vos besoins)
    $lastnameY = 950; // Position Y pour "Nom" (ajustez selon vos besoins)
    $phoneY = 1000; // Position Y pour "Tel" (ajustez selon vos besoins)
    $roleY = 1050; // Position Y pour "Fonction" (ajustez selon vos besoins)

    // Ajouter le texte avec imagettftext()
    imagettftext($background, $fontSize, $angle, $textX, $prenameY, $black, $fontPath, "Prénom: $firstname");
    imagettftext($background, $fontSize, $angle, $textX, $lastnameY, $black, $fontPath, "Nom: $lastname");
    imagettftext($background, $fontSize, $angle, $textX, $phoneY, $black, $fontPath, "Tel: $phone");
    imagettftext($background, $fontSize, $angle, $textX, $roleY, $black, $fontPath, "Fonction: $role");

    // Charger le QR code et l'ajouter sur l'image de fond
    $qrImage = imagecreatefrompng($qrOutputFile);
    if (!$qrImage) {
        echo json_encode(['success' => false, 'error' => 'Impossible de charger le QR code généré.']);
        exit;
    }

    // Coordonnées pour placer le QR code sur le badge PNG
    $qrX      = 400; // Position X (en pixels)
    $qrY      = 1200; // Position Y (en pixels)
    $qrWidth  = 290; // Largeur du QR code (en pixels)
    $qrHeight = 290; // Hauteur du QR code (en pixels)

    imagecopyresampled($background, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight, imagesx($qrImage), imagesy($qrImage));

    // Enregistrer le badge PNG final
    $finalPngPath = 'badges_png/badge_final_' . $firstname . '_' . $lastname . '.png';
    imagepng($background, $finalPngPath);
    imagedestroy($background);
    imagedestroy($qrImage);

    // Vérifier que le badge PNG a été généré
    if (!file_exists($finalPngPath)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du badge PNG.']);
        exit;
    }

    // Génération du PDF avec FPDF
    if (!file_exists('fpdf/fpdf.php')) {
        echo json_encode(['success' => false, 'error' => 'La librairie FPDF est introuvable.']);
        exit;
    }
    require('fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();

    // Ajouter l'image de fond dans le PDF
    // Assurez-vous que l'image de fond est en format JPEG ou PNG sans transparence
    // Ajustez les dimensions selon le format de la page (par défaut A4 : 210x297 mm)
    $pdf->Image($backgroundImage, 0, 0, 210, 297);

    // Ajouter le texte par-dessus l'image de fond
    $pdf->SetFont('Arial','B',26);

    $pdf->Ln(120);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(40,10,"Prénom: $firstname");
    $pdf->Ln(10);
    $pdf->Cell(40,10,"Nom: $lastname");
    $pdf->Ln(10);

    $pdf->Cell(40,10,"Tel: $phone");
    $pdf->Ln(10);
    $pdf->Cell(40,10,"Fonction: $role");
    $pdf->Ln(20);

    // Ajouter le QR code dans le PDF
    // Ajustez les coordonnées selon vos besoins et le positionnement de l'image de fond
    $pdf->Image($qrOutputFile, 80, 150, 50, 50); // Exemple : position (80, 150) mm, taille 50x50 mm

    $finalPdfPath = 'badges_pdf/badge_final_' . $firstname . '_' . $lastname . '.pdf';
    $pdf->Output('F', $finalPdfPath);

    // Vérifier que le PDF a été généré
    if (!file_exists($finalPdfPath)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du PDF.']);
        exit;
    }

    // Préparation de l'INSERT avec les chemins des fichiers
    $stmt = $conn->prepare("INSERT INTO participant (firstname, lastname, email, role, phone, qr_code_path, badge_png_path, badge_pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Échec de la préparation de la requête SQL : ' . $conn->error]);
        exit;
    }

    // Liaison des paramètres
    $stmt->bind_param("ssssssss", $firstname, $lastname, $email, $role, $phone, $qrOutputFile, $finalPngPath, $finalPdfPath);

    // Exécution de la requête
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'exécution de la requête : ' . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }

    // Fermer la requête et la connexion
    $stmt->close();
    $conn->close();

    // Nettoyer les données de la session
    unset($_SESSION['otp']);
    unset($_SESSION['user_data']);

    // Envoyer le message WhatsApp via Infobip
    // Préparer les données pour la requête WhatsApp
    $messageWhatsApp = "Cher $firstname $lastname, votre badge a été créé avec succès.";

    $dataWA = [
        'messages' => [
            [
                'from' => $senderWA, // Votre numéro WhatsApp autorisé (format E.164 sans 'whatsapp:')
                'to' => 'whatsapp:' . preg_replace('/\s+/', '', $phone), // Numéro du destinataire avec préfixe 'whatsapp:'
                'messageId' => uniqid(), // Générer un ID unique pour le message
                'content' => [
                    'templateName' => 'nom_du_template', // Remplacez par le nom de votre template
                    'templateData' => [
                        'body' => [
                            'placeholders' => [$firstname, $lastname] // Remplacez par les valeurs de vos placeholders
                        ]
                    ],
                    'language' => 'en_GB' // Remplacez par la langue appropriée
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

    // Initialiser cURL pour l'envoi WhatsApp
    $chWA = curl_init('https://n8n115.api.infobip.com/whatsapp/1/message/template'); // Assurez-vous que l'URL est correcte
    curl_setopt($chWA, CURLOPT_POST, 1);
    curl_setopt($chWA, CURLOPT_POSTFIELDS, json_encode($dataWA));
    curl_setopt($chWA, CURLOPT_HTTPHEADER, [
        'Authorization: App ' . $apiKeyWA, // Authentification avec la clé API
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($chWA, CURLOPT_RETURNTRANSFER, true);

    // Exécuter la requête WhatsApp
    $responseWA = curl_exec($chWA);
    $httpcodeWA = curl_getinfo($chWA, CURLINFO_HTTP_CODE);
    curl_close($chWA);

    // Vérifier la réponse WhatsApp
    if ($httpcodeWA >= 200 && $httpcodeWA < 300) {
        // WhatsApp envoyé avec succès
        $waMessage = 'Message WhatsApp envoyé avec succès.';
    } else {
        // WhatsApp n'a pas pu être envoyé
        // Vous pouvez analyser la réponse pour obtenir plus de détails
        $waError = json_decode($responseWA, true);
        $waMessage = 'Échec de l\'envoi du message WhatsApp.';
    }

    // Réponse JSON finale avec les chemins des fichiers et le statut du WhatsApp
    $response = [
        'success'      => true,
        'qrCodePath'   => $qrOutputFile,
        'badgePngPath' => $finalPngPath,
        'badgePdfPath' => $finalPdfPath,
        'firstname'    => $firstname,
        'lastname'     => $lastname,
        'email'        => $email,
        'role'         => $role,
        'phone'        => $phone,
        'whatsapp'     => $waMessage
    ];

    // Si l'envoi du WhatsApp a échoué, ajouter les détails de l'erreur
    if (isset($waError)) {
        $response['whatsapp_error'] = $waError;
    }

    echo json_encode($response);
    exit;
} else {
    // Si la requête n'est pas en POST, retourner une erreur
    echo json_encode(['success' => false, 'error' => 'Méthode de requête invalide.']);
    exit;
}
?>
