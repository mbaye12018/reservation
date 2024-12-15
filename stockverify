<?php
// send_otp.php

// Désactiver l'affichage des erreurs en production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

// Inclure l'autoloader de Composer
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    echo json_encode(['success' => false, 'error' => 'Autoloader Composer introuvable.']);
    error_log("send_otp.php: Autoloader Composer introuvable.");
    exit;
}

require $autoloadPath;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Démarrer la session pour stocker l'OTP et les données utilisateur
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour générer un OTP à 6 chiffres
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= mt_rand(0, 9);
    }
    return $otp;
}

// Vérifier si la requête est une méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Méthode de requête invalide.']);
    error_log("send_otp.php: Méthode de requête invalide.");
    exit;
}

// Récupérer et valider les données du formulaire
$firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
$lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$country_code = filter_input(INPUT_POST, 'country_code', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

// Vérifier les champs obligatoires
if (!$firstname || !$lastname || !$phone || !$country_code || !$email || !$role) {
    echo json_encode(['success' => false, 'error' => 'Veuillez remplir tous les champs obligatoires.']);
    error_log("send_otp.php: Champs obligatoires manquants.");
    exit;
}

// Générer l'OTP
$otp = generateOTP();

// Stocker l'OTP et les données utilisateur dans la session avec l'heure de génération
$_SESSION['otp'] = $otp;
$_SESSION['user_data'] = [
    'firstname' => $firstname,
    'lastname'  => $lastname,
    'phone'     => $phone,
    'country_code' => $country_code,
    'email'     => $email,
    'role'      => $role
];
$_SESSION['otp_generated_time'] = time();
$_SESSION['otp_attempts'] = 0;

// Configuration SMTP pour Gmail
$smtpHost = 'smtp.gmail.com';                    // Serveur SMTP de Gmail
$smtpUsername = 'babacar12018@gmail.com';        // Votre adresse email Gmail
$smtpPassword = 'cnjxwwqntexkquru';              // Mot de passe d'application Gmail
$smtpPort = 587;                                 // Port SMTP de Gmail (587 pour TLS)
$smtpEncryption = 'tls';                         // Type de chiffrement (tls ou ssl)

// Informations de l'expéditeur
$fromEmail = 'babacar12018@gmail.com';           // Même que $smtpUsername
$fromName = "Govathon 2024";                      // Nom de l'expéditeur

// Préparer l'email avec PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP
    $mail->isSMTP();                                            // Utiliser SMTP
    $mail->Host       = $smtpHost;                              // Définir le serveur SMTP
    $mail->SMTPAuth   = true;                                   // Activer l'authentification SMTP
    $mail->Username   = $smtpUsername;                          // Nom d'utilisateur SMTP
    $mail->Password   = $smtpPassword;                          // Mot de passe SMTP
    $mail->SMTPSecure = $smtpEncryption;                        // Activer le chiffrement TLS
    $mail->Port       = $smtpPort;                              // Port TCP à utiliser

    // Expéditeur et destinataire
    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($email, "$firstname $lastname");          // Ajouter un destinataire

    // Contenu de l'email
    $mail->isHTML(true);                                        // Définir le format de l'email en HTML
    $mail->Subject = 'Votre Code de Vérification OTP';
    $mail->Body    = "<p>Bonjour $firstname $lastname,</p>
                      <p>Votre code de vérification est : <strong>$otp</strong></p>
                      <p>Ce code est valable pendant 10 minutes.</p>
                      <p>Merci de votre inscription à Gov'athon 2024.</p>";
    $mail->AltBody = "Bonjour $firstname $lastname,\n\nVotre code de vérification est : $otp\n\nCe code est valable pendant 10 minutes.\n\nMerci de votre inscription à Gov'athon 2024.";

    // Envoyer l'email
    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // En cas d'exception, enregistrer l'erreur dans le journal des erreurs du serveur
    error_log("send_otp.php: Erreur d'envoi de l'OTP : " . $mail->ErrorInfo);

    // Retourner une erreur JSON avec une description générique
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'envoi de l\'OTP. Veuillez réessayer plus tard.']);
}
?>
