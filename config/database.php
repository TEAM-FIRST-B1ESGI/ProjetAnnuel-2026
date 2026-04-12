<?php
$host = 'localhost';
$dbname = 'app_budget';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
// Ajouter demande retour d'erreur
?>