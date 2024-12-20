<?php 
    /** 
     * Affichage de la partie admin : liste des articles avec un bouton "modifier" pour chacun. 
     * Et un formulaire pour ajouter un article. 
     */
?>

<div class="admin-header">
    <h2>Edition des articles</h2>
    <div class="admin-actions">
        <a class="submit" href="index.php?action=showUpdateArticleForm">Ajouter un article</a>
        <a class="submit" href="index.php?action=monitoring">Tableau de bord</a>
    </div>
</div>

<div class="adminArticle">
    <?php foreach ($articles as $article) { ?>
        <div class="articleLine">
            <div class="title"><?= $article->getTitle() ?></div>
            <div class="content"><?= $article->getContent(200) ?></div>
            <div><a class="submit" href="index.php?action=showUpdateArticleForm&id=<?= $article->getId() ?>">Modifier</a></div>
            <div><a class="submit" href="index.php?action=deleteArticle&id=<?= $article->getId() ?>" <?= Utils::askConfirmation("Êtes-vous sûr de vouloir supprimer cet article ?") ?> >Supprimer</a></div>
        </div>
    <?php } ?>
</div>

<style>
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.admin-actions {
    display: flex;
    gap: 10px;
}

.admin-actions .submit {
    margin: 0;
}
</style>
