<?php
header('Content-Type: application/json');

// Désactiver l'affichage des erreurs en production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/chemin/vers/votre/log/php-error.log');

try {
    session_start();

    // Récupérer l'OTP envoyé par le formulaire
    $receivedOtp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_NUMBER_INT);
    if (!$receivedOtp) {
        throw new Exception('OTP invalide.');
    }

    // Vérifier l'OTP
    if (!isset($_SESSION['otp']) || $receivedOtp != $_SESSION['otp']) {
        throw new Exception('OTP incorrect.');
    }

    // OTP vérifié avec succès
    // (Ajoutez ici la logique supplémentaire, comme la confirmation de la réservation)

    // Répondre avec succès
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Répondre avec une erreur
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
