<?php
session_start();

// Activer l'affichage des erreurs pour le débogage (désactiver en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Définir le type de contenu comme JSON
header('Content-Type: application/json; charset=UTF-8');

// Informations de connexion à la base de données
$servername = "localhost";      // Remplacez par votre serveur
$username = "root";  // Remplacez par votre nom d'utilisateur
$password = "";  // Remplacez par votre mot de passe
$dbname = "reservation"; // Remplacez par votre nom de base de données

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Échec de la connexion à la base de données : ' . $conn->connect_error]);
    exit;
}

// Vérifier si la requête est en méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et validation des données
    $firstname = isset($_POST['firstname']) ? htmlspecialchars(trim($_POST['firstname'])) : '';
    $lastname  = isset($_POST['lastname'])  ? htmlspecialchars(trim($_POST['lastname']))  : '';
    $email     = isset($_POST['email'])     ? htmlspecialchars(trim($_POST['email']))     : '';
    $role      = isset($_POST['role'])      ? htmlspecialchars(trim($_POST['role']))      : '';
    $phone     = isset($_POST['phone'])     ? htmlspecialchars(trim($_POST['phone']))     : '';

    // Vérifier que tous les champs sont remplis
    if (empty($firstname) || empty($lastname) || empty($email) || empty($role) || empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'Tous les champs sont requis.']);
        exit;
    }

    // Préparer la requête pour vérifier l'existence du numéro de téléphone
    $stmt = $conn->prepare("SELECT id FROM participant WHERE phone = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Erreur de préparation de la requête SQL : ' . $conn->error]);
        exit;
    }

    // Liaison des paramètres
    $stmt->bind_param("s", $phone);

    // Exécuter la requête
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'exécution de la requête : ' . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }

    // Stocker le résultat
    $stmt->store_result();

    // Vérifier si le numéro de téléphone existe déjà
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Ce numéro de téléphone est déjà enregistré.']);
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
    $_SESSION['user_data'] = [
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'email'     => $email,
        'role'      => $role,
        'phone'     => $phone
    ];

    // Envoyer l'OTP via InfoBip
    // Remplacez ces valeurs par vos propres informations InfoBip
    $apiKey = 'ac6b0a32a15d86d1c3b6e8db0157ac8f-43269c9d-bdce-470c-ba7b-5d11ba275a37';
    $sender = 'GOVATHON'; // Nom ou numéro autorisé
    $recipient = $phone;
    $message = "Votre code de vérification est : $otp";

    // Préparer les données pour la requête
    $data = [
        'messages' => [
            [
                'from' => $sender,
                'destinations' => [
                    [
                        'to' => $recipient
                    ]
                ],
                'text' => $message
            ]
        ]
    ];

    // Initialiser cURL
    $ch = curl_init('https://api.infobip.com/sms/2/text/advanced');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: App ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Exécuter la requête
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Vérifier la réponse
    if ($httpcode >= 200 && $httpcode < 300) {
        echo json_encode(['success' => true, 'message' => 'OTP envoyé avec succès.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Échec de l\'envoi de l\'OTP.']);
    }

    // Fermer la connexion à la base de données
    $conn->close();
} else {
    // Si la requête n'est pas en POST, retourner une erreur
    echo json_encode(['success' => false, 'error' => 'Méthode de requête invalide.']);
    exit;
}
?>
