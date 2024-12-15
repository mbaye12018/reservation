<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Activer l'affichage des erreurs pour le débogage (désactiver en production)
error_log("qrOutputFile = $qrOutputFile");
error_log("finalPngPath = $finalPngPath");
error_log("finalPdfPath = $finalPdfPath");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Définir le type de contenu comme JSON
header('Content-Type: application/json; charset=UTF-8');

// Les variables $apiKeyWA et $senderWA restent, mais ne sont plus utilisées
$apiKeyWA = 'ac6b0a32a15d86d1c3b6e8db0157ac8f-43269c9d-bdce-470c-ba7b-5d11ba275a37'; 
$senderWA = '+447307810250';

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

    // Suppression de la vérification du format de numéro de téléphone
    // if (!preg_match('/^\+\d{10,15}$/', $phone)) {
    //     echo json_encode(['success' => false, 'error' => 'Format de numéro de téléphone invalide.']);
    //     exit;
    // }

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
    $backgroundImage = 'images/Govathon2024.png'; 
    if (!file_exists($backgroundImage)) {
        echo json_encode(['success' => false, 'error' => "L'image de fond est introuvable."]);
        exit;
    }

    // Connexion à la base de données en local
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

    // Préparer la requête pour insérer les données utilisateur
    $stmt = $conn->prepare("INSERT INTO participant (firstname, lastname, email, role, phone, qr_code_path, badge_png_path, badge_pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Échec de la préparation de la requête SQL : ' . $conn->error]);
        exit;
    }

    $qrOutputFile = '';
    $finalPngPath = '';
    $finalPdfPath = '';

    $temp_qr_path = '';
    $temp_png_path = '';
    $temp_pdf_path = '';
    $stmt->bind_param("ssssssss", $firstname, $lastname, $email, $role, $phone, $temp_qr_path, $temp_png_path, $temp_pdf_path);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'exécution de la requête : ' . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }

    $participant_id = $stmt->insert_id;
    $stmt->close();

    $qrData = [
        'id' => $participant_id,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'role' => $role,
        'phone' => $phone
    ];

    $qrContent = json_encode($qrData);
    $qrOutputFile = 'qrcodes/badge_' . $participant_id . '.png';
    QRcode::png($qrContent, $qrOutputFile, QR_ECLEVEL_M, 4, 1);

    if (!file_exists($qrOutputFile)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du QR Code.']);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    $background = imagecreatefrompng($backgroundImage);
    if (!$background) {
        echo json_encode(['success' => false, 'error' => 'Impossible de charger l\'image de fond.']);
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    $black = imagecolorallocate($background, 0, 0, 0);

    $fontPath = __DIR__ . '/fonts/LiberationSans-Bold.ttf';
    if (!file_exists($fontPath)) {
        echo json_encode(['success' => false, 'error' => 'Le fichier de police est introuvable.']);
        imagedestroy($background);
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    $phoneWithoutCode = preg_replace('/^\+221/', '', $phone);

    $fontSize = 27;
    $angle = 0;
    $textX = 70;
    $prenameY = 900;
    $lastnameY = 950;
    $phoneY = 1000;
    $roleY = 1050;

    imagettftext($background, $fontSize, $angle, $textX, $prenameY, $black, $fontPath, "Prénom: $firstname");
    imagettftext($background, $fontSize, $angle, $textX, $lastnameY, $black, $fontPath, "Nom: $lastname");
    imagettftext($background, $fontSize, $angle, $textX, $phoneY, $black, $fontPath, "Tel: $phoneWithoutCode");
    imagettftext($background, $fontSize, $angle, $textX, $roleY, $black, $fontPath, "Fonction: $role");

    $qrImage = imagecreatefrompng($qrOutputFile);
    if (!$qrImage) {
        echo json_encode(['success' => false, 'error' => 'Impossible de charger le QR code généré.']);
        imagedestroy($background);
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    $qrX      = 400;
    $qrY      = 1200;
    $qrWidth  = 290;
    $qrHeight = 290;

    imagecopyresampled($background, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight, imagesx($qrImage), imagesy($qrImage));

    $finalPngPath = 'badges_png/badge_final_' . $participant_id . '.png';
    imagepng($background, $finalPngPath);
    imagedestroy($background);
    imagedestroy($qrImage);

    if (!file_exists($finalPngPath)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du badge PNG.']);
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    if (!file_exists('fpdf/fpdf.php')) {
        echo json_encode(['success' => false, 'error' => 'La librairie FPDF est introuvable.']);
        unlink($qrOutputFile);
        unlink($finalPngPath);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    require('fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();

    $pdf->Image($backgroundImage, 0, 0, 210, 297);
    $pdf->SetFont('Arial','B',26);
    $pdf->Ln(120);

    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(40,10,"Prenom: $firstname", 0, 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(40,10,"Nom: $lastname", 0, 1, 'L');
    $pdf->Ln(5);

    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(40, 10, "Tel: $phoneWithoutCode", 0, 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(40, 10, "Fonction: $role", 0, 1, 'L');
    $pdf->Ln(20);

    $pdf->Image($qrOutputFile, 80, 190, 50, 50);

    $finalPdfPath = 'badges_pdf/badge_final_' . $participant_id . '.pdf';
    $pdf->Output('F', $finalPdfPath);

    if (!file_exists($finalPdfPath)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du PDF.']);
        unlink($qrOutputFile);
        unlink($finalPngPath);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    $update_stmt = $conn->prepare("UPDATE participant SET qr_code_path = ?, badge_png_path = ?, badge_pdf_path = ? WHERE id = ?");
    if (!$update_stmt) {
        echo json_encode(['success' => false, 'error' => 'Échec de la préparation de la mise à jour SQL : ' . $conn->error]);
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

    $_SESSION['download_badge_id'] = $participant_id;

    $protocol = "http://";
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';
    $url = $protocol . $host . $basePath;

    $badgePngUrl = $protocol . $host . '/' . $finalPngPath;
    $badgePdfUrl = $protocol . $host . '/' . $finalPdfPath;
    $qrCodeUrl = $protocol . $host . '/' . $qrOutputFile;

    // Notification par email à la place de WhatsApp
 

    $smtpHost = 'smtp.gmail.com';
    $smtpUsername = 'babacar12018@gmail.com';
    $smtpPassword = 'cnjxwwqntexkquru';
    $smtpPort = 587;
    $smtpEncryption = 'tls';

    $fromEmail = 'babacar12018@gmail.com';
    $fromName = "Govathon 2024";

    $mail = new PHPMailer(true);

    $messageWhatsApp = "Cher $firstname $lastname, votre badge a été créé avec succès.";

    try {
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUsername;
        $mail->Password   = $smtpPassword;
        $mail->SMTPSecure = $smtpEncryption;
        $mail->Port       = $smtpPort;

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email, "$firstname $lastname");

        $mail->isHTML(true);
        $mail->Subject = 'Badge créé avec succès';
        $mail->Body    = "<p>Cher $firstname $lastname,</p><p>Votre badge a été créé avec succès. Vous pouvez le télécharger dès maintenant.</p>";
        $mail->AltBody = "Cher $firstname $lastname,\n\nVotre badge a été créé avec succès. Vous pouvez le télécharger dès maintenant.";

        $mail->send();
        $waMessage = 'Email de notification envoyé avec succès.';
    } catch (Exception $e) {
        $waMessage = 'Échec de l\'envoi de l\'email de notification.';
        $waError = $mail->ErrorInfo;
    }

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

    if (isset($waError)) {
        $response['whatsapp_error'] = $waError;
    }

    echo json_encode($response);
    exit;
} else {
    // Si la requête n'est pas en POST
    echo json_encode(['success' => false, 'error' => 'Méthode de requête invalide.']);
    exit;
}
?>
