<?php
// verify_otp.php
session_start();
require __DIR__ . '/vendor/autoload.php';

// Désactiver l'affichage des erreurs en production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Initialisation des variables pour éviter les notices
$qrOutputFile = '';
$finalPngPath = '';
$finalPdfPath = '';

// Définir le type de contenu comme JSON
header('Content-Type: application/json; charset=UTF-8');

// Fonction pour générer un OTP à 6 chiffres (si nécessaire)
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= mt_rand(0, 9);
    }
    return $otp;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredOtp = isset($_POST['otp']) ? trim($_POST['otp']) : '';

    if (empty($enteredOtp)) {
        echo json_encode(['success' => false, 'error' => 'Le code OTP est requis.']);
        error_log("verify_otp.php: Le code OTP est requis mais n'a pas été fourni.");
        exit;
    }

    if (!isset($_SESSION['otp']) || !isset($_SESSION['user_data'])) {
        echo json_encode(['success' => false, 'error' => 'Session expirée ou invalide. Veuillez soumettre à nouveau le formulaire.']);
        error_log("verify_otp.php: Session expirée ou données utilisateur manquantes.");
        exit;
    }

    $storedOtp = $_SESSION['otp'];
    $userData = $_SESSION['user_data'];

    if ($enteredOtp != $storedOtp) {
        // Incrémenter le compteur de tentatives
        if (!isset($_SESSION['otp_attempts'])) {
            $_SESSION['otp_attempts'] = 1;
        } else {
            $_SESSION['otp_attempts'] += 1;
        }

        // Limiter le nombre de tentatives à 5
        if ($_SESSION['otp_attempts'] >= 5) {
            // Supprimer les données de la session
            unset($_SESSION['otp']);
            unset($_SESSION['user_data']);
            echo json_encode(['success' => false, 'error' => 'Nombre maximum de tentatives atteint. Veuillez réinscrire.']);
            error_log("verify_otp.php: Nombre maximum de tentatives de vérification de l'OTP atteint.");
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Code OTP incorrect.']);
        error_log("verify_otp.php: Code OTP incorrect. Entré: $enteredOtp, Stocké: $storedOtp");
        exit;
    }

    // Extraire et nettoyer les données utilisateur
    $firstname = htmlspecialchars($userData['firstname']);
    $lastname  = htmlspecialchars($userData['lastname']);
    $email     = htmlspecialchars($userData['email']);
    $role      = htmlspecialchars($userData['role']);
    $phone     = htmlspecialchars($userData['phone']);
    $structure = htmlspecialchars($userData['structure']); // Nouveau champ

    // Vérifier et créer les répertoires nécessaires
    $directories = ['qrcodes', 'badges_png', 'badges_pdf'];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0775, true)) {
                echo json_encode(['success' => false, 'error' => "Impossible de créer le répertoire $dir."]);
                error_log("verify_otp.php: Impossible de créer le répertoire $dir.");
                exit;
            }
            error_log("verify_otp.php: Répertoire créé: $dir");
        }
    }

    // Vérifier l'existence de l'image de fond
    $backgroundImage = 'images/Govathon2024.png';
    if (!file_exists($backgroundImage)) {
        echo json_encode(['success' => false, 'error' => "L'image de fond est introuvable."]);
        error_log("verify_otp.php: L'image de fond $backgroundImage est introuvable.");
        exit;
    }
    error_log("verify_otp.php: Image de fond trouvée: $backgroundImage");

    // Connexion à la base de données
    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $dbnameDB = "reservation";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbnameDB);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Impossible de se connecter à la base de données.']);
        error_log("verify_otp.php: Connexion à la base de données échouée: " . $conn->connect_error);
        exit;
    }
    error_log("verify_otp.php: Connexion à la base de données réussie.");

    // Vérifier l'existence de la librairie QR Code
    if (!file_exists('phpqrcode/qrlib.php')) {
        echo json_encode(['success' => false, 'error' => 'La librairie QR Code est introuvable.']);
        error_log("verify_otp.php: La librairie QR Code est introuvable.");
        $conn->close();
        exit;
    }
    require 'phpqrcode/qrlib.php';
    error_log("verify_otp.php: Librairie QR Code chargée.");

    // Vérifier si l'email existe déjà (au cas où 'send_otp.php' n'avait pas vérifié)
    $email_check_stmt = $conn->prepare("SELECT id FROM participant WHERE email = ?");
    if (!$email_check_stmt) {
        echo json_encode(['success' => false, 'error' => 'Erreur interne. Veuillez réessayer plus tard.']);
        error_log("verify_otp.php: Erreur de préparation de la requête SQL pour vérifier l'email: " . $conn->error);
        $conn->close();
        exit;
    }
    $email_check_stmt->bind_param("s", $email);
    $email_check_stmt->execute();
    $email_check_stmt->store_result();

    if ($email_check_stmt->num_rows > 0) {
        // L'email est déjà utilisé
        echo json_encode(['success' => false, 'error' => 'Cette adresse email est déjà utilisée.']);
        error_log("verify_otp.php: Tentative d'inscription avec un email existant: $email");
        $email_check_stmt->close();
        $conn->close();
        exit;
    }

    $email_check_stmt->close();

    // Préparer l'INSERT dans la table participant
    $stmt = $conn->prepare("INSERT INTO participant (firstname, lastname, email, role, phone, structure, qr_code_path, badge_png_path, badge_pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Échec de la préparation de la requête SQL : ' . $conn->error]);
        error_log("verify_otp.php: Échec de la préparation de la requête SQL: " . $conn->error);
        $conn->close();
        exit;
    }
    error_log("verify_otp.php: Requête SQL préparée.");

    // Initialiser les chemins temporaires
    $temp_qr_path = '';
    $temp_png_path = '';
    $temp_pdf_path = '';
    $stmt->bind_param("sssssssss", $firstname, $lastname, $email, $role, $phone, $structure, $temp_qr_path, $temp_png_path, $temp_pdf_path);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'exécution de la requête : ' . $stmt->error]);
        error_log("verify_otp.php: Erreur lors de l'exécution de la requête SQL: " . $stmt->error);
        $stmt->close();
        $conn->close();
        exit;
    }
    error_log("verify_otp.php: Requête SQL exécutée. Participant ID: " . $stmt->insert_id);

    $participant_id = $stmt->insert_id;
    $stmt->close();

    // Générer le QR Code
    $qrData = [
        'id' => $participant_id,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'role' => $role,
        'phone' => $phone,
        'structure' => $structure // Inclure la structure dans le QR Code
    ];

    $qrContent = json_encode($qrData);
    $qrOutputFile = 'qrcodes/badge_' . $participant_id . '.png';
    QRcode::png($qrContent, $qrOutputFile, QR_ECLEVEL_M, 4, 1);
    error_log("verify_otp.php: QR Code généré: $qrOutputFile");

    if (!file_exists($qrOutputFile)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du QR Code.']);
        error_log("verify_otp.php: Échec de la génération du QR Code: $qrOutputFile");
        // Supprimer l'entrée créée dans la base de données
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    // Générer le badge PNG
    $background = imagecreatefrompng($backgroundImage);
    if (!$background) {
        echo json_encode(['success' => false, 'error' => 'Impossible de charger l\'image de fond.']);
        error_log("verify_otp.php: Impossible de charger l'image de fond: $backgroundImage");
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    error_log("verify_otp.php: Image de fond chargée.");

    $black = imagecolorallocate($background, 0, 0, 0);

    $fontPath = __DIR__ . '/fonts/LiberationSans-Bold.ttf';
    if (!file_exists($fontPath)) {
        echo json_encode(['success' => false, 'error' => 'Le fichier de police est introuvable.']);
        error_log("verify_otp.php: Fichier de police introuvable: $fontPath");
        imagedestroy($background);
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    error_log("verify_otp.php: Fichier de police trouvé: $fontPath");

    // Définir les positions pour le texte
    $fontSize = 27;
    $angle = 0;
    $textX = 70;
    $prenameY = 900;
    $lastnameY = 950;
    // $phoneY = 1000; // Retiré
    $roleY = 1000; // Modifié de 1050 à 1000 pour réduire l'espace
    $structureY = 1050; // Modifié de 1100 à 1050 pour uniformiser

    // Ajouter le texte sur le badge PNG
    imagettftext($background, $fontSize, $angle, $textX, $prenameY, $black, $fontPath, "prenom: $firstname"); // Remplacé "Prénom" par "prenom"
    imagettftext($background, $fontSize, $angle, $textX, $lastnameY, $black, $fontPath, "Nom: $lastname");
    // imagettftext($background, $fontSize, $angle, $textX, $phoneY, $black, $fontPath, "Tel: $phoneWithoutCode"); // Retiré
    imagettftext($background, $fontSize, $angle, $textX, $roleY, $black, $fontPath, "Fonction: $role");
    imagettftext($background, $fontSize, $angle, $textX, $structureY, $black, $fontPath, "Structure: $structure"); // Ajouter "Structure"
    error_log("verify_otp.php: Texte ajouté au badge PNG.");

    // Charger le QR Code
    $qrImage = imagecreatefrompng($qrOutputFile);
    if (!$qrImage) {
        echo json_encode(['success' => false, 'error' => 'Impossible de charger le QR code généré.']);
        error_log("verify_otp.php: Impossible de charger le QR code généré: $qrOutputFile");
        imagedestroy($background);
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    error_log("verify_otp.php: QR Code chargé.");

    // Définir la position et la taille du QR Code sur le badge
    $qrX = 400;
    $qrY = 1200;
    $qrWidth = 290;
    $qrHeight = 290;

    // Copier le QR Code sur le badge PNG
    imagecopyresampled($background, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight, imagesx($qrImage), imagesy($qrImage));
    error_log("verify_otp.php: QR Code copié sur le badge PNG.");

    // Définir le chemin final du badge PNG
    $finalPngPath = 'badges_png/badge_final_' . $participant_id . '.png';
    imagepng($background, $finalPngPath);
    imagedestroy($background);
    imagedestroy($qrImage);
    error_log("verify_otp.php: Badge PNG généré: $finalPngPath");

    if (!file_exists($finalPngPath)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du badge PNG.']);
        error_log("verify_otp.php: Échec de la génération du badge PNG: $finalPngPath");
        unlink($qrOutputFile);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }

    // Générer le badge PDF
    if (!file_exists('fpdf/fpdf.php')) {
        echo json_encode(['success' => false, 'error' => 'La librairie FPDF est introuvable.']);
        error_log("verify_otp.php: La librairie FPDF est introuvable.");
        unlink($qrOutputFile);
        unlink($finalPngPath);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    require('fpdf/fpdf.php');
    error_log("verify_otp.php: Librairie FPDF chargée.");

    $pdf = new FPDF();
    $pdf->AddPage();

    // Ajouter l'image de fond
    $pdf->Image($backgroundImage, 0, 0, 210, 297);
    $pdf->SetFont('Arial','B',26);
    $pdf->Ln(120);

    // Ajouter les informations utilisateur
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(40,10,"prenom: $firstname", 0, 1, 'L'); // Remplacé "Prénom" par "prenom"
    $pdf->Ln(3); // Réduit de 5 à 3 pour diminuer l'espacement

    $pdf->Cell(40,10,"Nom: $lastname", 0, 1, 'L');
    $pdf->Ln(3); // Réduit de 5 à 3 pour diminuer l'espacement

    // $pdf->Cell(40, 10, "Tel: $phoneWithoutCode", 0, 1, 'L'); // Retiré
    // $pdf->Ln(5); // Retiré

    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(40, 10, "Fonction: $role", 0, 1, 'L');
    $pdf->Ln(3); // Réduit de 5 à 3 pour diminuer l'espacement
    $pdf->Cell(40, 10, "Structure: $structure", 0, 1, 'L'); // Ajouter "Structure"
    $pdf->Ln(3); // Réduit de 5 à 3 pour diminuer l'espacement
    $pdf->Ln(20);

    // Ajouter le QR Code au PDF
    $pdf->Image($qrOutputFile, 80, 190, 50, 50);
    error_log("verify_otp.php: QR Code ajouté au badge PDF.");

    // Définir le chemin final du badge PDF
    $finalPdfPath = 'badges_pdf/badge_final_' . $participant_id . '.pdf';
    $pdf->Output('F', $finalPdfPath);
    error_log("verify_otp.php: Badge PDF généré: $finalPdfPath");

    if (!file_exists($finalPdfPath)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du PDF.']);
        error_log("verify_otp.php: Échec de la génération du PDF: $finalPdfPath");
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
        error_log("verify_otp.php: Échec de la préparation de la mise à jour SQL: " . $conn->error);
        unlink($qrOutputFile);
        unlink($finalPngPath);
        unlink($finalPdfPath);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    error_log("verify_otp.php: Requête UPDATE préparée.");

    $update_stmt->bind_param("sssi", $qrOutputFile, $finalPngPath, $finalPdfPath, $participant_id);

    if (!$update_stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour de la base de données : ' . $update_stmt->error]);
        error_log("verify_otp.php: Erreur lors de l'exécution de l'UPDATE: " . $update_stmt->error);
        $update_stmt->close();
        unlink($qrOutputFile);
        unlink($finalPngPath);
        unlink($finalPdfPath);
        $conn->query("DELETE FROM participant WHERE id = $participant_id");
        $conn->close();
        exit;
    }
    error_log("verify_otp.php: UPDATE exécutée avec succès. Affected rows: " . $update_stmt->affected_rows);

    $update_stmt->close();
    $conn->close();

    // Nettoyer les données de la session
    unset($_SESSION['otp']);
    unset($_SESSION['user_data']);

    // Définir l'identifiant unique pour le téléchargement avec participant_id
    $_SESSION['download_badge_id'] = $participant_id;
    error_log("verify_otp.php: Session 'download_badge_id' définie à $participant_id.");

    // Générer les URLs absolues pour les badges
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
                $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://"; // Détecter le protocole
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';

    $badgePngUrl = $protocol . $host . '/' . $finalPngPath;
    $badgePdfUrl = $protocol . $host . '/' . $finalPdfPath;
    $qrCodeUrl = $protocol . $host . '/' . $qrOutputFile;

    error_log("verify_otp.php: URLs générées: PNG=$badgePngUrl, PDF=$badgePdfUrl, QR=$qrCodeUrl");

    // **Suppression de la Notification par Email**
    /*
    // Notification par email
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
        error_log("verify_otp.php: Email de notification envoyé à $email.");
    } catch (Exception $e) {
        $waMessage = 'Échec de l\'envoi de l\'email de notification.';
        $waError = $mail->ErrorInfo;
        error_log("verify_otp.php: Échec de l'envoi de l'email: " . $mail->ErrorInfo);
    }
    */

    // Construire la réponse JSON
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
        'structure'    => $structure, // Inclure la structure dans la réponse JSON
        // 'whatsapp'     => $waMessage // Retiré car l'email n'est plus envoyé
    ];

    /*
    if (isset($waError)) {
        $response['whatsapp_error'] = $waError;
    }
    */

    echo json_encode($response);
    error_log("verify_otp.php: Réponse JSON envoyée.");
    exit;
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode de requête invalide.']);
    error_log("verify_otp.php: Méthode de requête invalide: " . $_SERVER["REQUEST_METHOD"]);
    exit;
}
?>
