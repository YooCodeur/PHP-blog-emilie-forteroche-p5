<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=blog_forteroche', 'root', 'root'); // Mettez '' si pas de mot de passe
    echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
