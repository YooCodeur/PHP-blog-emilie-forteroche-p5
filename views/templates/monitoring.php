<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Monitoring des articles</title>
    <style>
        body {
            background: #efe1ba;
            font-family: Times New Roman, serif;
        }

        .monitoring-container {
            width: 95%;
            margin: 20px auto;
            border: 2px solid #255e33;
        }

        h1, h2, h3 {
            color: #255e33;
            text-align: center;
            font-family: Times New Roman, serif;
            margin: 10px 0;
            padding: 10px;
            background: #efe1ba;
            border-bottom: 2px solid #255e33;
        }
        h1 {
            color: #f13c1f;
            text-align: center;
            font-family: 'Qwitcher Grypen', cursive;
            margin: 10px 0;
            padding: 10px;
            background: #efe1ba;
            border-bottom: 2px solid #255e33;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .stats-card table thead th,
        .stats-card table thead th a {
            background-color: #255e33 !important;
            color: #fff !important;
            font-weight: bold;
            padding: 15px;
            text-align: center;
            border: none;
        }

        .stats-card table thead th:hover,
        .stats-card table thead th a:hover {
            background-color: #1a4024 !important;
        }

        /* Suppression des autres styles qui peuvent entrer en conflit */
      

        td {
            padding: 8px;
            border-bottom: 1px solid #255e33;
        }

        tr:nth-child(even) {
            background: #efe1ba;
        }

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding: 15px;
            background: #efe1ba;
            border-bottom: 2px solid #255e33;
        }

        .stat-card {
            border: 1px solid #255e33;
            padding: 15px;
            text-align: center;
            background: #fff;
        }

        .stat-value {
            font-size: 24px;
            color: #255e33;
            font-weight: bold;
        }

        .actions-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 200px;
        }

        .show-comments, .submit {
            background: #255e33;
            color: #fff;
            border: 1px solid #1a4024;
            padding: 8px 10px;
            cursor: pointer;
            font-family: Times New Roman, serif;
            text-decoration: none;
            display: block;
            margin: 2px 0;
            width: 100%;
            text-align: center;
            box-sizing: border-box;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .show-comments:hover, .submit:hover {
            background: #1a4024;
            color: #fff;
            transform: scale(1.02);
        }

        .comments-panel {
            display: none;
            padding: 10px;
            margin: 10px;
            background: #efe1ba;
            border: 1px solid #255e33;
        }

        .comments-panel.active {
            display: block;
        }

        .comment-item {
            background: #fff;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #255e33;
        }

        .comment-meta {
            color: #255e33;
            font-style: italic;
            margin-bottom: 5px;
        }

        .comment-text {
            color: #333;
            margin: 5px 0;
        }

        .delete-comment {
            background: #255e33;
            color: #fff;
            border: 1px solid #1a4024;
            padding: 3px 8px;
            cursor: pointer;
            font-family: Times New Roman, serif;
            font-weight: bold;
        }

        .delete-comment:hover {
            background: #1a4024;
        }

        .loading, .error, .no-comments {
            color: #255e33;
            text-align: center;
            padding: 10px;
            font-style: italic;
        }

        a {
            color: #255e33;
            text-decoration: none;
        }

        a:hover {
            color: #1a4024;
        }

        th a {
            color: #fff;
            text-decoration: none;
            display: block;
            font-weight: bold;
        }

        th a:hover {
            color: #efe1ba;
            background: #1a4024;
            transition: all 0.3s ease;
        }

        .sort-arrow {
            display: inline-block;
            margin-left: 5px;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="monitoring-container">
        <h2>Tableau de bord</h2>
        
        <div class="stats-summary">
            <div class="stat-card">
                <h3>Total des articles</h3>
                <div class="stat-value"><?= count($articles) ?></div>
            </div>
            <div class="stat-card">
                <h3>Total des vues</h3>
                <div class="stat-value">
                    <?= array_sum(array_column($articles, 'views')) ?>
                </div>
            </div>
            <div class="stat-card">
                <h3>Total des commentaires</h3>
                <div class="stat-value">
                    <?= array_sum(array_column($articles, 'comment_count')) ?>
                </div>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stats-card">
                <h2>Liste des articles</h2>
                <table>
                    <thead>
                        <tr>
                            <?php
                            $columns = [
                                'title' => ['label' => 'Titre', 'tooltip' => 'Cliquez pour trier par titre'],
                                'views' => ['label' => 'Vues', 'tooltip' => 'Cliquez pour trier par nombre de vues'],
                                'comment_count' => ['label' => 'Commentaires', 'tooltip' => 'Cliquez pour trier par nombre de commentaires'],
                                'created_at' => ['label' => 'Date de création', 'tooltip' => 'Cliquez pour trier par date']
                            ];
                            
                            foreach ($columns as $column => $info) {
                                $currentOrder = ($sort_column === $column) ? $sort_order : '';
                                $nextOrder = ($currentOrder === 'asc') ? 'desc' : 'asc';
                                $tooltipText = $info['tooltip'];
                                
                                if ($sort_column === $column) {
                                    $tooltipText .= $sort_order === 'asc' ? ' (trié croissant)' : ' (trié décroissant)';
                                }
                                ?>
                                <th>
                                    <a href="index.php?action=monitoring&sort=<?= $column ?>&order=<?= $nextOrder ?>" 
                                       data-tooltip="<?= $tooltipText ?>">
                                        <?= $info['label'] ?>
                                        <?php if ($sort_column === $column): ?>
                                            <span class="sort-arrow <?= $sort_order ?>"></span>
                                        <?php endif; ?>
                                    </a>
                                </th>
                            <?php } ?>
                         
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?= htmlspecialchars($article['title']) ?></td>
                            <td><?= $article['views'] ?></td>
                            <td><?= $article['comment_count'] ?></td>
                            <td><?= (new DateTime($article['created_at']))->format('d/m/Y') ?></td>
                            <td>
                                <div class="actions-container">
                                    <button class="show-comments" onclick="toggleComments(<?= $article['id'] ?>)">
                                        Voir les commentaires
                                    </button>
                                    <a href="index.php?action=showUpdateArticleForm&id=<?= $article['id'] ?>" class="submit">
                                        Modifier
                                    </a>
                                    <a class="submit" href="index.php?action=deleteArticle&id=<?= $article['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                        Supprimer
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <div id="comments-<?= $article['id'] ?>" class="comments-panel">
                                    <?php if (isset($article['comments']) && !empty($article['comments'])): ?>
                                        <?php foreach ($article['comments'] as $comment): ?>
                                            <div class="comment-item">
                                                <div class="comment-content">
                                                    <div class="comment-meta">
                                                        Par <?= htmlspecialchars($comment['pseudo']) ?> 
                                                        le <?= (new DateTime($comment['date_creation']))->format('d/m/Y H:i') ?>
                                                    </div>
                                                    <div class="comment-text">
                                                        <?= htmlspecialchars($comment['content']) ?>
                                                    </div>
                                                </div>
                                                <button class="delete-comment" 
                                                        onclick="deleteComment(<?= $comment['id'] ?>, <?= $article['id'] ?>)"
                                                        data-confirm="Êtes-vous sûr de vouloir supprimer ce commentaire ?">
                                                    Supprimer
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="no-comments">Aucun commentaire pour cet article</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        function toggleComments(articleId) {
            const panel = document.getElementById(`comments-${articleId}`);
            if (!panel.classList.contains('active')) {
                panel.innerHTML = '<div class="loading">Chargement des commentaires...</div>';
                // Charger les commentaires via AJAX si pas encore chargés
                fetch(`index.php?action=getArticleComments&id=${articleId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            updateCommentsPanel(articleId, data.comments);
                        } else {
                            panel.innerHTML = `<div class="error">${data.message || 'Erreur lors du chargement des commentaires'}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        panel.innerHTML = '<div class="error">Erreur lors du chargement des commentaires</div>';
                    });
            }
            panel.classList.toggle('active');
        }
        
        function updateCommentsPanel(articleId, comments) {
            const panel = document.getElementById(`comments-${articleId}`);
            if (!comments || comments.length === 0) {
                panel.innerHTML = '<div class="no-comments">Aucun commentaire pour cet article</div>';
                return;
            }
            
            let html = '<div class="comments-list">';
            comments.forEach(comment => {
                const date = new Date(comment.dateCreation);
                const formattedDate = date.toLocaleString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                html += `
                    <div class="comment">
                        <div class="comment-header">
                            <strong>${comment.pseudo}</strong> - ${formattedDate}
                        </div>
                        <div class="comment-content">
                            ${comment.content}
                        </div>
                        <div class="comment-actions">
                            <button onclick="deleteComment(${comment.id}, ${articleId})" class="delete-btn">
                                Supprimer
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            panel.innerHTML = html;
        }

        function deleteComment(commentId, articleId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
                return;
            }

            const panel = document.getElementById(`comments-${articleId}`);
            panel.style.opacity = '0.5';

            fetch(`index.php?action=deleteComment&id=${commentId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Recharger les commentaires
                        panel.innerHTML = '<div class="loading">Actualisation...</div>';
                        panel.style.opacity = '1';
                        return fetch(`index.php?action=getArticleComments&id=${articleId}`);
                    } else {
                        throw new Error(data.message || 'Erreur lors de la suppression');
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateCommentsPanel(articleId, data.comments);
                        // Mettre à jour le compteur de commentaires dans le tableau
                        const countCell = document.querySelector(`tr[data-article-id="${articleId}"] td:nth-child(3)`);
                        if (countCell) {
                            const currentCount = parseInt(countCell.textContent);
                            countCell.textContent = currentCount - 1;
                        }
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    panel.style.opacity = '1';
                    alert(error.message || 'Erreur lors de la suppression du commentaire');
                });
        }
    </script>
</body>
</html>