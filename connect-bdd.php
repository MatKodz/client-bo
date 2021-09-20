<?php
// connexion bdd
$servername = "localhost";
$username = "root";
$password = "root";
$mydb = "Customer_management";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$mydb", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print '<p class="alert alert-warning">Erreur connexion!: ' . $e->getMessage() . '</p>';
    exit('<p class="alert alert-danger">Connexion impossible</p>');
      }
?>
