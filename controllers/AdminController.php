<?php 
/**
 * Contrôleur de la partie admin.
 */
 
class AdminController {

    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin() : void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On affiche la page d'administration.
        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }

    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected() : void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm() : void 
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser() : void 
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page de monitoring.
        Utils::redirect("monitoring");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser() : void 
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page de monitoring.
        Utils::redirect("monitoring");
    }

    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle() : void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);
       
        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Affiche la page de monitoring
     * @return void
     */
    public function showMonitoring() : void
    {
        $this->checkIfUserIsConnected(); 
        
        // Récupération des paramètres de tri
        $sort_column = Utils::request('sort', 'title');
        $sort_order = Utils::request('order', 'asc');
        
        // Validation des paramètres de tri
        $allowed_columns = ['title', 'views', 'comment_count', 'created_at'];
        if (!in_array($sort_column, $allowed_columns)) {
            $sort_column = 'title';
        }
        
        $sort_order = strtolower($sort_order) === 'desc' ? 'desc' : 'asc';
        
        // Récupère les articles avec leurs statistiques
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticlesWithStats($sort_column, $sort_order);
        
        // Appelle la vue de monitoring
        $view = new View("Monitoring des articles");
        $view->render("monitoring", [
            'articles' => $articles,
            'sort_column' => $sort_column,
            'sort_order' => $sort_order
        ]);
    }

    /**
     * Récupère les commentaires d'un article en AJAX
     * @return void
     */
    public function getArticleComments() : void
    {
       
        // Log pour le débogage
        error_log("Début getArticleComments");
        
        header('Content-Type: application/json');
        
        // Vérification de la connexion pour AJAX
        if (!isset($_SESSION['user'])) {
            error_log("Utilisateur non connecté");
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
            return;
        }
        
        $articleId = Utils::request('id', -1);
        error_log("Article ID reçu : " . $articleId);
        
        if ($articleId === -1) {
            error_log("ID article manquant");
            echo json_encode(['success' => false, 'message' => 'ID article manquant']);
            return;
        }
        
        try {
            error_log("Tentative de récupération des commentaires");
            $commentManager = new CommentManager();
            $comments = $commentManager->getAllCommentsByArticleId($articleId);
            error_log("Nombre de commentaires trouvés : " . count($comments));
            
            // Convertir les objets Comment en tableaux pour JSON
            $commentsArray = array_map(function($comment) {
                return [
                    'id' => $comment->getId(),
                    'pseudo' => $comment->getPseudo(),
                    'content' => $comment->getContent(),
                    'dateCreation' => $comment->getDateCreation()->format('Y-m-d H:i:s')
                ];
            }, $comments);
            
            $response = json_encode(['success' => true, 'comments' => $commentsArray]);
            error_log("Réponse JSON générée : " . $response);
            echo $response;
            
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des commentaires : " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        return;
    }
    
    /**
     * Supprime un commentaire
     * @return void
     */
    public function deleteComment() : void
    {
        header('Content-Type: application/json');
        
        // Vérification de la connexion pour AJAX
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
            return;
        }
        
        $commentId = Utils::request('id', -1);
        
        if ($commentId === -1) {
            echo json_encode(['success' => false, 'message' => 'ID commentaire manquant']);
            return;
        }
        
        try {
            $commentManager = new CommentManager();
            
            // Récupérer le commentaire pour vérifier s'il existe
            $comment = $commentManager->getCommentById($commentId);
            
            if (!$comment) {
                echo json_encode(['success' => false, 'message' => 'Commentaire introuvable']);
                return;
            }
            
            // Supprimer le commentaire
            if ($commentManager->deleteComment($comment)) {
                echo json_encode(['success' => true, 'message' => 'Commentaire supprimé avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du commentaire']);
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression du commentaire : " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        return;
    }
}