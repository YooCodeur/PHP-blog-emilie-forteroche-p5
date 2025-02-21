<?php
    /**
     * Affichage de Liste des articles. 
     */
?>

<div class="articleList">
    <?php foreach($articles as $article) { ?>
        <article class="article">
            <a href="index.php?action=showArticle&id=<?= $article->getId() ?>">
                <h2><?= $article->getTitle() ?></h2>
                <span class="quotation">«</span>
                <p><?= $article->getContent(400) ?></p>
                
                <div class="footer">
                    <span class="info"><?= ucfirst(Utils::convertDateToFrenchFormat($article->getDateCreation())) ?></span>
                    <span class="info">Lire +</span>
                </div>
            </a>
        </article>
    <?php } ?>
</div>