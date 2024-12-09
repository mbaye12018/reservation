<?php
// Connexion à la base de données
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Récupération des données du QR code
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM participant WHERE id = :id");
$stmt->execute([':id' => $id]);
$participant = $stmt->fetch();

if ($participant) {
    echo "Nom : " . $participant['nom'] . "<br>";
    echo "Prénom : " . $participant['prenom'] . "<br>";
    echo "Fonction : " . $participant['fonction'] . "<br>";
    echo "Téléphone : " . $participant['telephone'] . "<br>";
    echo "Numéro de chaise : " . $participant['numero_chaise'];
} else {
    echo "Participant non trouvé.";
}
?>
