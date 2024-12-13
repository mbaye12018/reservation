<?php
session_start();

// Activer l'affichage des erreurs pour le débogage (désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Définir le type de contenu comme JSON
header('Content-Type: application/json; charset=UTF-8');

// Configuration des informations Infobip
$apiKeyWA = 'ac6b0a32a15d86d1c3b6e8db0157ac8f-43269c9d-bdce-470c-ba7b-5d11ba275a37'; // Remplacez par votre clé API Infobip
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
    $backgroundImage = 'images/Govathon2024.png'; // Assurez-vous que ce fichier existe
    if (!file_exists($backgroundImage)) {
        echo json_encode(['success' => false, 'error' => "L'image de fond est introuvable."]);
        exit;
    }

    // Connexion à la base de données
 

    $servername = "91.234.195.179";
    $usernameDB = "c2275612c_gov_athon";
    $passwordDB = "Passer@2024";
    $dbnameDB = "c2275612c_govathon2024";

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

    // Préparer la requête pour insérer les données utilisateur
    $stmt = $conn->prepare("INSERT INTO participant (firstname, lastname, email, role, phone, qr_code_path, badge_png_path, badge_pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Échec de la préparation de la requête SQL : ' . $conn->error]);
        exit;
    }

    // Initialiser les chemins de fichiers à insérer (temporaires pour l'instant)
    $qrOutputFile = '';
    $finalPngPath = '';
    $finalPdfPath = '';

    // Lier les paramètres (les chemins seront mis à jour après génération)
    $temp_qr_path = '';
    $temp_png_path = '';
    $temp_pdf_path = '';
    $stmt->bind_param("ssssssss", $firstname, $lastname, $email, $role, $phone, $temp_qr_path, $temp_png_path, $temp_pdf_path);

    // Exécuter l'INSERT
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'exécution de la requête : ' . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }

    // Récupérer l'ID généré automatiquement
    $participant_id = $stmt->insert_id;

    // Fermer la requête (maintenant que l'ID est récupéré)
    $stmt->close();

    // Créer un tableau associatif avec les informations à encoder (utiliser participant_id)
    $qrData = [
        'id' => $participant_id,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'role' => $role,
        'phone' => $phone
    ];

    // Convertir le tableau en JSON
    $qrContent = json_encode($qrData);

    // Générer le QR code avec participant_id
    $qrOutputFile = 'qrcodes/badge_' . $participant_id . '.png';
    QRcode::png($qrContent, $qrOutputFile, QR_ECLEVEL_M, 4, 1);

    // Vérifier que le QR code a été généré
    if (!file_exists($qrOutputFile)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du QR Code.']);
        // Optionnel : Supprimer l'entrée utilisateur si QR Code échoue
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    // Génération du badge PNG complet
    $background = imagecreatefrompng($backgroundImage);
    if (!$background) {
        echo json_encode(['success' => false, 'error' => 'Impossible de charger l\'image de fond.']);
        // Optionnel : Supprimer l'entrée utilisateur et le QR Code si le background échoue
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    // Définir les couleurs
    $black = imagecolorallocate($background, 0, 0, 0);

    // Chemin vers la police TTF
    $fontPath = __DIR__ . '/fonts/LiberationSans-Bold.ttf';
    if (!file_exists($fontPath)) {
        echo json_encode(['success' => false, 'error' => 'Le fichier de police est introuvable.']);
        // Optionnel : Supprimer l'entrée utilisateur, le QR Code et le background si la police échoue
        imagedestroy($background);
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    $phoneWithoutCode = preg_replace('/^\+221/', '', $phone);


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
    imagettftext($background, $fontSize, $angle, $textX, $phoneY, $black, $fontPath, "Tel: $phoneWithoutCode");
    imagettftext($background, $fontSize, $angle, $textX, $roleY, $black, $fontPath, "Fonction: $role");

    // Charger le QR code et l'ajouter sur l'image de fond
    $qrImage = imagecreatefrompng($qrOutputFile);
    if (!$qrImage) {
        echo json_encode(['success' => false, 'error' => 'Impossible de charger le QR code généré.']);
        // Optionnel : Supprimer l'entrée utilisateur, le QR Code et le background si le QR Code échoue
        imagedestroy($background);
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    // Coordonnées pour placer le QR code sur le badge PNG
    $qrX      = 400; // Position X (en pixels)
    $qrY      = 1200; // Position Y (en pixels)
    $qrWidth  = 290; // Largeur du QR code (en pixels)
    $qrHeight = 290; // Hauteur du QR code (en pixels)

    imagecopyresampled($background, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight, imagesx($qrImage), imagesy($qrImage));

    // Enregistrer le badge PNG final avec participant_id
    $finalPngPath = 'badges_png/badge_final_' . $participant_id . '.png';
    imagepng($background, $finalPngPath);
    imagedestroy($background);
    imagedestroy($qrImage);

    // Vérifier que le badge PNG a été généré
    if (!file_exists($finalPngPath)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du badge PNG.']);
        // Optionnel : Supprimer l'entrée utilisateur, le QR Code et le background si le badge PNG échoue
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    // Génération du PDF avec FPDF
    if (!file_exists('fpdf/fpdf.php')) {
        echo json_encode(['success' => false, 'error' => 'La librairie FPDF est introuvable.']);
        // Optionnel : Supprimer l'entrée utilisateur, le QR Code, le badge PNG et le background si FPDF échoue
        unlink($qrOutputFile);
        unlink($finalPngPath);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    require('fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();

    // Ajouter l'image de fond dans le PDF
    // Assurez-vous que l'image de fond est en format JPEG ou PNG sans transparence
    // Ajustez les dimensions selon le format de la page (par défaut A4 : 210x297 mm)
    $pdf->Image($backgroundImage, 0, 0, 210, 297);
$pdf->SetFont('Arial','B',26); // Police Arial, Gras, Taille 26
$pdf->Ln(120); // Saut de ligne de 120 mm

// Définir la police pour "Prenom" et "Nom" en Gras avec une taille augmentée
$pdf->SetFont('Arial','B',16); // Police Arial, Gras, Taille 16

// Ajouter "Prenom" en Gras
$pdf->Cell(40,10,"Prenom: $firstname", 0, 1, 'L'); // Cellule largeur 40 mm, hauteur 10 mm, alignement à gauche
$pdf->Ln(5); // Saut de ligne de 10 mm

// Ajouter "Nom" en Gras
$pdf->Cell(40,10,"Nom: $lastname", 0, 1, 'L');
$pdf->Ln(5);

// Définir la police pour les autres champs (Police normale, Taille augmentée)
$pdf->SetFont('Arial','',14); // Police Arial, Normal, Taille 14

// Supprimer le préfixe "+221" du téléphone
$phoneWithoutCode = preg_replace('/^\+221/', '', $phone);

// Ajouter "Tel" en gras
$pdf->SetFont('Arial', 'B', 16); // Police Arial, Gras, Taille 16
$pdf->Cell(40, 10, "Tel: $phoneWithoutCode", 0, 1, 'L');
$pdf->Ln(5);

// Ajouter "Fonction" en gras
$pdf->Cell(40, 10, "Fonction: $role", 0, 1, 'L');
$pdf->Ln(20); // Saut de ligne de 20 mm


// Ajouter le QR code dans le PDF
// Ajuster les coordonnées Y pour déplacer le QR code plus bas (par exemple de 150 à 160)
$pdf->Image($qrOutputFile, 80, 190, 50, 50); // Position X=80 mm, Y=160 mm, Taille 50x50 mm


    // Enregistrer le PDF final avec participant_id
    $finalPdfPath = 'badges_pdf/badge_final_' . $participant_id . '.pdf';
    $pdf->Output('F', $finalPdfPath);

    // Vérifier que le PDF a été généré
    if (!file_exists($finalPdfPath)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du PDF.']);
        // Optionnel : Supprimer l'entrée utilisateur, le QR Code, le badge PNG, le PDF et le background si le PDF échoue
        unlink($qrOutputFile);
        unlink($finalPngPath);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    // Mettre à jour les chemins des fichiers dans la base de données
    $update_stmt = $conn->prepare("UPDATE participant SET qr_code_path = ?, badge_png_path = ?, badge_pdf_path = ? WHERE id = ?");
    if (!$update_stmt) {
        echo json_encode(['success' => false, 'error' => 'Échec de la préparation de la mise à jour SQL : ' . $conn->error]);
        // Optionnel : Supprimer les fichiers et l'entrée utilisateur si la mise à jour échoue
        unlink($qrOutputFile);
        unlink($finalPngPath);
        unlink($finalPdfPath);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    $update_stmt->bind_param("sssi", $qrOutputFile, $finalPngPath, $finalPdfPath, $participant_id);

    if (!$update_stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour de la base de données : ' . $update_stmt->error]);
        // Optionnel : Supprimer les fichiers et l'entrée utilisateur si la mise à jour échoue
        $update_stmt->close();
        unlink($qrOutputFile);
        unlink($finalPngPath);
        unlink($finalPdfPath);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    $update_stmt->close();
    $conn->close();

    // Nettoyer les données de la session
    unset($_SESSION['otp']);
    unset($_SESSION['user_data']);

    // **Définir l'identifiant unique pour le téléchargement avec participant_id**
    $_SESSION['download_badge_id'] = $participant_id;

    // Générer les URLs absolues pour les badges
$protocol = "https://";
$host = $_SERVER['HTTP_HOST'];
$basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';
$url = $protocol . $host . $basePath;


    // Construction des URLs en tenant compte du chemin relatif
    $badgePngUrl = $protocol . $host . '/' . $finalPngPath;
    $badgePdfUrl = $protocol . $host . '/' . $finalPdfPath;
    $qrCodeUrl = $protocol . $host . '/' . $qrOutputFile;

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
                    'language' => 'fr_FR' // Remplacez par la langue appropriée
                ],
                'callbackData' => 'Données de rappel', // Optionnel : données de rappel
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

    // Réponse JSON finale avec les URLs des fichiers et le statut du WhatsApp
    $response = [
        'success'      => true,
        'qrCodeUrl'    => $qrCodeUrl,
        'badgePngUrl'  => $badgePngUrl,
        'badgePdfUrl'  => $badgePdfUrl,
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

