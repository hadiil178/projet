<?php
$host = 'localhost'; // ou 127.0.0.1
$dbname = 'projet'; // Remplacé par 'elegance' pour correspondre au nom de la base de données du script de connexion
$username = 'root'; // Par défaut sur XAMPP
$password = ''; // Par défaut sur XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>