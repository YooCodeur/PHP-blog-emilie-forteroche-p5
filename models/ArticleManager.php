<?php

/**
 * Classe qui gère les articles.
 */
class ArticleManager extends AbstractEntityManager 
{
    /**
     * Récupère tous les articles.
     * @return array : un tableau d'objets Article.
     */
    public function getAllArticles() : array
    {
        $sql = "SELECT * FROM article";
        $result = $this->db->query($sql);
        $articles = [];

        while ($article = $result->fetch()) {
            $articles[] = new Article($article);
        }
        return $articles;
    }
    
    /**
     * Récupère un article par son id.
     * @param int $id : l'id de l'article.
     * @return Article|null : un objet Article ou null si l'article n'existe pas.
     */
    public function getArticleById(int $id) : ?Article
    {
        $sql = "SELECT * FROM article WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        $article = $result->fetch();
        if ($article) {
            return new Article($article);
        }
        return null;
    }
    /** sert à récupérer les données dans la base de données */
    public function getAllArticlesWithStats(string $sort_column = 'title', string $sort_order = 'asc') : array
    {
        // Récupération des données sans tri
        $query = "
            SELECT 
                a.id,
                a.title,
                a.views,
                a.date_creation as created_at,
                (SELECT COUNT(*) FROM comment WHERE id_article = a.id) AS comment_count
            FROM article AS a";
        
        $stmt = $this->db->query($query);
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Tri en PHP
        usort($articles, function($a, $b) use ($sort_column, $sort_order) {
            $valueA = $a[$sort_column];
            $valueB = $b[$sort_column];
            
            // Conversion des dates pour comparaison
            if ($sort_column === 'created_at') {
                $valueA = strtotime($valueA);
                $valueB = strtotime($valueB);
            }
            
            // Comparaison en fonction du type de données
            if (is_numeric($valueA) && is_numeric($valueB)) {
                $comparison = $valueA - $valueB;
            } else {
                $comparison = strcasecmp($valueA, $valueB);
            }
            
            // Application de l'ordre de tri
            return $sort_order === 'asc' ? $comparison : -$comparison;
        });
        
        return $articles;
    }
    
    /**
     * Ajoute ou modifie un article.
     * On sait si l'article est un nouvel article car son id sera -1.
     * @param Article $article : l'article à ajouter ou modifier.
     * @return void
     */
    public function addOrUpdateArticle(Article $article) : void 
    {
        if ($article->getId() == -1) {
            $this->addArticle($article);
        } else {
            $this->updateArticle($article);
        }
    }

    /**
     * Ajoute un article.
     * @param Article $article : l'article à ajouter.
     * @return void
     */
    public function addArticle(Article $article) : void
    {
        $sql = "INSERT INTO article (id_user, title, content, date_creation, date_update) VALUES (:id_user, :title, :content, NOW(), NULL)";
        $this->db->query($sql, [
            'id_user' => $article->getIdUser(),
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ]);
    }

    /**
     * Modifie un article.
     * @param Article $article : l'article à modifier.
     * @return void
     */
    public function updateArticle(Article $article) : void
    {
        $sql = "UPDATE article SET title = :title, content = :content, date_update = NOW() WHERE id = :id";
        $this->db->query($sql, [
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'id' => $article->getId()
        ]);
    }

    /**
     * Supprime un article.
     * @param int $id : l'id de l'article à supprimer.
     * @return void
     */
    public function deleteArticle(int $id) : void
    {
        $sql = "DELETE FROM article WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

    /**
     * Incrémente le nombre de vues d'un article
     * @param int $id : l'id de l'article
     * @return void
     */
    public function incrementViews(int $id): void {
        $sql = "UPDATE article SET views = views + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }
}