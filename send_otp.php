<?php
// send_otp.php

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
$structure = filter_input(INPUT_POST, 'structure', FILTER_SANITIZE_STRING); // Nouveau champ

// Vérifier les champs obligatoires
if (!$firstname || !$lastname || !$phone || !$country_code || !$email || !$role || !$structure) {
    echo json_encode(['success' => false, 'error' => 'Veuillez remplir tous les champs obligatoires.']);
    error_log("send_otp.php: Champs obligatoires manquants. Données reçues - Prénom: $firstname, Nom: $lastname, Téléphone: $phone, Indicatif: $country_code, Email: $email, Fonction: $role, Structure: $structure");
    exit;
}

// Optionnel : Validation supplémentaire pour le champ "Structure"
// Par exemple, vérifier la longueur ou les caractères autorisés
if (strlen($structure) < 2 || strlen($structure) > 100) {
    echo json_encode(['success' => false, 'error' => 'La structure doit comporter entre 2 et 100 caractères.']);
    error_log("send_otp.php: Validation échouée pour la structure: $structure");
    exit;
}

// Connexion à la base de données pour vérifier l'unicité de l'email
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbnameDB = "reservation";

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbnameDB);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Impossible de se connecter à la base de données.']);
    error_log("send_otp.php: Connexion à la base de données échouée: " . $conn->connect_error);
    exit;
}
error_log("send_otp.php: Connexion à la base de données réussie.");

// Vérifier si l'email existe déjà
$email_check_stmt = $conn->prepare("SELECT id FROM participant WHERE email = ?");
if (!$email_check_stmt) {
    echo json_encode(['success' => false, 'error' => 'Erreur interne. Veuillez réessayer plus tard.']);
    error_log("send_otp.php: Erreur de préparation de la requête SQL pour vérifier l'email: " . $conn->error);
    $conn->close();
    exit;
}
$email_check_stmt->bind_param("s", $email);
$email_check_stmt->execute();
$email_check_stmt->store_result();

if ($email_check_stmt->num_rows > 0) {
    // L'email est déjà utilisé
    echo json_encode(['success' => false, 'error' => 'Cette adresse email est déjà utilisée.']);
    error_log("send_otp.php: Tentative d'inscription avec un email existant: $email");
    $email_check_stmt->close();
    $conn->close();
    exit;
}

$email_check_stmt->close();

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
    'role'      => $role,
    'structure' => $structure // Stocker le nouveau champ
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
    $mail->Subject = 'Votre Code OTP';
    $mail->Body    = "<p>Bonjour $firstname $lastname,</p>
                    <p>Votre code de vérification est : <strong>$otp</strong></p>
                    <p>Ce code est valable pendant 10 minutes.</p>
                    <p>Merci de votre inscription à Gov'athon 2024.</p>
                    <p><strong>Structure :</strong> $structure</p>"; // Optionnel : Inclure la structure dans l'email
    $mail->AltBody = "Bonjour $firstname $lastname,\n\nVotre code de vérification est : $otp\n\nCe code est valable pendant 10 minutes.\n\nMerci de votre inscription à Gov'athon 2024.\n\nStructure : $structure";

    // Envoyer l'email
    $mail->send();
    echo json_encode(['success' => true]);
    error_log("send_otp.php: OTP envoyé à $email. Structure: $structure");
} catch (Exception $e) {
    // En cas d'exception, enregistrer l'erreur dans le journal des erreurs du serveur
    error_log("send_otp.php: Erreur d'envoi de l'OTP à $email : " . $mail->ErrorInfo);

    // Retourner une erreur JSON avec une description générique
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'envoi de l\'OTP. Veuillez réessayer plus tard.']);
    exit;
}

$conn->close();
?>
