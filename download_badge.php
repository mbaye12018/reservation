<?php
// download_badge.php

session_start();

// Vérifier si l'utilisateur a une session valide avec un identifiant unique
if (!isset($_SESSION['download_badge_id'])) {
    http_response_code(403);
    echo "Accès refusé.";
    exit;
}

// Récupérer l'identifiant unique du badge de la session
$badge_id = $_SESSION['download_badge_id'];

// Vérifier si le paramètre 'type' est présent
if (!isset($_GET['type'])) {
    http_response_code(400);
    echo "Requête invalide.";
    exit;
}

$type = strtolower(trim($_GET['type']));
if ($type !== 'png' && $type !== 'pdf') {
    http_response_code(400);
    echo "Type de fichier invalide.";
    exit;
}

// Définir le répertoire et le chemin du fichier basé sur le type
$directory = ($type === 'png') ? 'badges_png' : 'badges_pdf';
$filePath = "$directory/badge_final_$badge_id.$type";

// Vérifier que le fichier existe
if (!file_exists($filePath)) {
    http_response_code(404);
    echo "Fichier non trouvé.";
    exit;
}

// Connexion à la base de données pour récupérer les informations utilisateur
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbnameDB = "reservation";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbnameDB);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Erreur de connexion à la base de données.";
    exit;
}

// Préparer la requête pour récupérer le prénom et le nom basé sur badge_png_path ou badge_pdf_path
$column = ($type === 'png') ? 'badge_png_path' : 'badge_pdf_path';

$stmt = $conn->prepare("SELECT firstname, lastname FROM participant WHERE $column = ?");
if (!$stmt) {
    http_response_code(500);
    echo "Erreur de préparation de la requête.";
    exit;
}

$stmt->bind_param("s", $filePath);
$stmt->execute();
$stmt->bind_result($firstname, $lastname);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo "Participant non trouvé.";
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();
$conn->close();

// Définir les en-têtes appropriées
switch ($type) {
    case 'png':
        header('Content-Type: image/png');
        break;
    case 'pdf':
        header('Content-Type: application/pdf');
        break;
    default:
        header('Content-Type: application/octet-stream');
}

// Définir le nom du fichier lors du téléchargement en utilisant prénom et nom
$customFilename = 'badge_' . strtolower($firstname) . '_' . strtolower($lastname) . '.' . $type;
header('Content-Disposition: attachment; filename="' . $customFilename . '"');
header('Content-Length: ' . filesize($filePath));

// Lire le fichier et le servir au client
readfile($filePath);

// Nettoyer l'identifiant unique de la session après le téléchargement
unset($_SESSION['download_badge_id']);

exit();
?>
