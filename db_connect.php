<?php
// Fichier: db_connect.php

// Paramètres de connexion
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservation";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion à la base de données : " . $conn->connect_error);
}
?>
