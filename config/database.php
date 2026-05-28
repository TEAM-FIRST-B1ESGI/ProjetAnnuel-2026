<?php
$host = 'localhost';
$dbname = 'projet_app';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
// Ajouter demande retour d'erreur
?>