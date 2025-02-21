<?php 
/**
 * Ce fichier est le template principal qui "contient" ce qui aura été généré par les autres vues.  
 * 
 * Les variables qui doivent impérativement être définie sont : 
 *      $title string : le titre de la page.
 *      $content string : le contenu de la page. 
 */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emilie Forteroche</title>
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Qwitcher+Grypen:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <nav>
            <?php
                // Récupération de l'action courante
                $currentAction = $_GET['action'] ?? 'default';
                
                // Définition des classes pour chaque lien
                $homeClass = ($currentAction === 'default') ? 'active' : '';
                $aboutClass = ($currentAction === 'apropos') ? 'active' : '';
                $adminClass = ($currentAction === 'monitoring') ? 'active' : '';
            ?>
            <a href="index.php" class="<?= $homeClass ?>">Articles</a>
            <a href="index.php?action=apropos" class="<?= $aboutClass ?>">À propos</a>
            <?php 
                if (isset($_SESSION['user'])) {
                    echo '<a href="index.php?action=monitoring" class="' . $adminClass . '">Admin</a>';
                    echo '<a href="index.php?action=disconnectUser">Déconnexion</a>';
                } else {
                    echo '<a href="index.php?action=connectionForm">Connexion</a>';
                }
            ?>
        </nav>
        <h1>Emilie Forteroche</h1>
    </header>

    <main>    
        <?= $content /* Ici est affiché le contenu réel de la page. */ ?>
    </main>
    
    <footer>
        <p>Copyright © Emilie Forteroche 2025 - Openclassrooms</p>
    </footer>

</body>
</html>