<?php
session_start();  // Démarrer la session pour stocker le chemin du QR Code

ini_set('display_errors', 0); // Désactiver l'affichage des erreurs
error_reporting(E_ALL);
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et validation des données
    $firstname = isset($_POST['firstname']) ? htmlspecialchars(trim($_POST['firstname'])) : '';
    $lastname = isset($_POST['lastname']) ? htmlspecialchars(trim($_POST['lastname'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $role = isset($_POST['role']) ? htmlspecialchars(trim($_POST['role'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';

    if (empty($firstname) || empty($lastname) || empty($email) || empty($role) || empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'Tous les champs sont requis.']);
        exit;
    }

    // Vérifier l'existence des répertoires
    $directories = ['qrcodes', 'badges_png', 'badges_pdf'];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    // Vérifier l'image de fond
    $backgroundImage = 'images/Govathon 2024 (1).png';
    if (!file_exists($backgroundImage)) {
        echo json_encode(['success' => false, 'error' => "L'image de fond est introuvable."]);
        exit;
    }

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'root', '', 'reservation');
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

    $qrOutputFile = 'qrcodes/badge_' . $firstname . '_' . $lastname . '.png';
    QRcode::png($vcard, $qrOutputFile, QR_ECLEVEL_M, 4, 1);

    // Vérifier que l'image du QR Code a été créée
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
    $white = imagecolorallocate($background, 255, 255, 255);

    // Définir le chemin vers la police TTF
    $fontPath = __DIR__ . '/fonts/LiberationSans-Bold.ttf'; // Assurez-vous que le chemin est correct

    // Vérifier que le fichier de police existe
    if (!file_exists($fontPath)) {
        echo json_encode(['success' => false, 'error' => 'Le fichier de police est introuvable. Chemin : ' . $fontPath]);
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

    // Charger le QR code et l'ajouter
    $qrImage = imagecreatefrompng($qrOutputFile);
    if (!$qrImage) {
        echo json_encode(['success' => false, 'error' => 'Impossible de charger le QR code généré.']);
        exit;
    }

    // Coordonnées pour placer le QR code
    $qrX = 400; // Position X
    $qrY = 1200; // Position Y
    $qrWidth = 290; // Largeur du QR code
    $qrHeight = 290; // Hauteur du QR code

    imagecopyresampled($background, $qrImage, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight, imagesx($qrImage), imagesy($qrImage));

    $finalPngPath = 'badges_png/badge_final_' . $firstname . '_' . $lastname . '.png';
    imagepng($background, $finalPngPath);
    imagedestroy($background);
    imagedestroy($qrImage);

    if (!file_exists($finalPngPath)) {
        echo json_encode(['success' => false, 'error' => 'Échec de la génération du badge PNG.']);
        exit;
    }

    // Génération du PDF
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
    $pdf->Cell(40,10,"Prenom: $firstname");
    $pdf->Ln(10);
    $pdf->Cell(40,10,"Nom: $lastname");
    $pdf->Ln(10);

    $pdf->Cell(40,10,"Tel: $phone");
    $pdf->Ln(10);
    $pdf->Cell(40,10,"Fonction: $role");
    $pdf->Ln(20);

    // Ajouter le QR code dans le PDF
    // Ajustez les coordonnées selon vos besoins et le positionnement de l'image de fond
    $pdf->Image($qrOutputFile, 80, 150, 50, 50); // Exemple : position (80, 80) mm, taille 50x50 mm

    $finalPdfPath = 'badges_pdf/badge_final_' . $firstname . '_' . $lastname . '.pdf';
    $pdf->Output('F', $finalPdfPath);

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

    $stmt->bind_param("ssssssss", $firstname, $lastname, $email, $role, $phone, $qrOutputFile, $finalPngPath, $finalPdfPath);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'exécution de la requête : ' . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }

    // Fermer la requête et la connexion
    $stmt->close();
    $conn->close();

    // Réponse JSON finale
    echo json_encode([
        'success' => true,
        'qrCodePath' => $qrOutputFile,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'role' => $role,
        'phone' => $phone
    ]);
    exit;
} else {
    // Si la requête n'est pas en POST, on peut retourner une erreur
    echo json_encode(['success' => false, 'error' => 'Méthode de requête invalide.']);
    exit;
}
?>
