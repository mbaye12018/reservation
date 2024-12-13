<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Configuration des erreurs
if (getenv('ENV') !== 'production') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Définir le type de contenu comme JSON
header('Content-Type: application/json; charset=UTF-8');

// Inclure l'autoload de Composer
require '/vendor/autoload.php'; // Assurez-vous que le chemin est correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Informations de connexion à la base de données
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "reservation";

// Créer une connexion
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Échec de la connexion à la base de données : ' . $conn->connect_error]);
    exit;
}

// Vérifier si la requête est en méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et validation des données
    $firstname     = isset($_POST['firstname']) ? htmlspecialchars(trim($_POST['firstname'])) : '';
    $lastname      = isset($_POST['lastname'])  ? htmlspecialchars(trim($_POST['lastname']))  : '';
    $email         = isset($_POST['email'])     ? htmlspecialchars(trim($_POST['email']))     : '';
    $role          = isset($_POST['role'])      ? htmlspecialchars(trim($_POST['role']))      : '';
    $country_code  = isset($_POST['country_code']) ? htmlspecialchars(trim($_POST['country_code'])) : '';
    $phone         = isset($_POST['phone'])     ? htmlspecialchars(trim($_POST['phone']))     : '';

    // Vérifier que tous les champs sont remplis
    if (empty($firstname) || empty($lastname) || empty($email) || empty($role) || empty($country_code) || empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'Tous les champs sont requis.']);
        exit;
    }

    // Combiner l'indicatif et le numéro
    $full_phone_number = $country_code . $phone;

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Adresse email invalide.']);
        exit;
    }

    // Validation du numéro de téléphone (format international simple)
    if (!preg_match('/^\+\d{7,15}$/', $full_phone_number)) {
        echo json_encode(['success' => false, 'error' => 'Numéro de téléphone invalide.']);
        exit;
    }

    // Préparer la requête pour vérifier l'existence de l'email
    $stmt = $conn->prepare("SELECT id FROM participant WHERE email = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Erreur de préparation de la requête SQL : ' . $conn->error]);
        exit;
    }

    // Liaison des paramètres
    $stmt->bind_param("s", $email);

    // Exécuter la requête
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'exécution de la requête : ' . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }

    // Stocker le résultat
    $stmt->store_result();

    // Vérifier si l'email existe déjà
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Cet email est déjà enregistré.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    // Fermer la requête préparée
    $stmt->close();

    // Générer un OTP aléatoire de 6 chiffres
    $otp = rand(100000, 999999);

    // Stocker l'OTP et les données utilisateur dans la session
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300; // OTP valide pendant 5 minutes
    $_SESSION['user_data'] = [
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'email'     => $email,
        'role'      => $role,
        'phone'     => $full_phone_number
    ];

    // Configuration de PHPMailer avec Gmail SMTP
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Serveur SMTP de Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'babacar12018@gmail.com'; // Votre adresse email Gmail
        $mail->Password   = 'VOTRE_MOT_DE_PASSE_APP'; // Votre mot de passe d'application Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Utilisez 'ssl' si nécessaire
        $mail->Port       = 587; // Port SMTP (465 pour SSL)

        // Destinataires
        $mail->setFrom('babacar12018@gmail.com', 'Gov\'athon 2024'); // Doit correspondre à l'adresse Gmail
        $mail->addAddress($email, "$firstname $lastname");

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Votre code de vérification Gov\'athon 2024';
        $mail->Body    = "
            <h3>Bonjour $firstname $lastname,</h3>
            <p>Merci de vous être inscrit à Gov'athon 2024.</p>
            <p>Votre code de vérification est : <strong>$otp</strong></p>
            <p>Ce code est valide pendant 5 minutes.</p>
            <p>Cordialement,<br>L'équipe Gov'athon</p>
        ";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'OTP envoyé avec succès par email.']);
    } catch (Exception $e) {
        // Supprimer les données de session si l'envoi échoue
        unset($_SESSION['otp']);
        unset($_SESSION['otp_expiry']);
        unset($_SESSION['user_data']);

        echo json_encode(['success' => false, 'error' => 'Échec de l\'envoi de l\'email : ' . $mail->ErrorInfo]);
    }

    // Fermer la connexion à la base de données
    $conn->close();
} else {
    // Si la requête n'est pas en POST, retourner une erreur
    echo json_encode(['success' => false, 'error' => 'Méthode de requête invalide.']);
    exit;
}
?>
