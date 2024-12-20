<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Monitoring des articles</title>
    <style>
        .monitoring-container {
            padding: 20px;
            max-width: 1366px;
            margin: 0 auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            position: relative;
        }
        
        th a {
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
            padding: 5px 0;
        }
        
        th a::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
            padding: 6px 12px;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 4px;
            font-size: 12px;
            font-weight: normal;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 1000;
        }
        
        th a::after {
            content: '';
            position: absolute;
            bottom: calc(100% + 4px);
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: rgba(0, 0, 0, 0.8) transparent transparent transparent;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 1000;
        }
        
        th:hover a::before,
        th:hover a::after {
            opacity: 1;
            visibility: visible;
        }
      
        .sort-arrow {
            margin-left: 5px;
            display: inline-block;
            width: 0;
            height: 0;
            vertical-align: middle;
        }
        
        .sort-arrow.asc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 4px solid #333;
        }
        
        .sort-arrow.desc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #333;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #666;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        /* Styles pour la gestion des commentaires */
        .comments-panel {
            display: none;
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .comments-panel.active {
            display: block;
        }
        
        .comment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .comment-content {
            flex-grow: 1;
            margin-right: 15px;
        }
        
        .comment-meta {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        
        .comment-text {
            color: #333;
        }
        
        .delete-comment {
            padding: 5px 10px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .delete-comment:hover {
            background-color: #c82333;
        }
        
        .show-comments {
            padding: 5px 10px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-right: 10px;
        }
        
        .show-comments:hover {
            background-color: #5a6268;
        }
        
        .no-comments {
            color: #666;
            font-style: italic;
            padding: 10px;
        }
        
        /* Animation pour le panneau de commentaires */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .comments-panel.active {
            animation: slideDown 0.3s ease-out;
        }
        
        .comments-list {
            margin-top: 10px;
        }
        .comment {
            background: #f5f5f5;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .comment-header {
            color: #666;
            margin-bottom: 5px;
        }
        .comment-content {
            margin: 10px 0;
        }
        .comment-actions {
            text-align: right;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background: #c82333;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .error {
            color: #dc3545;
            padding: 10px;
            text-align: center;
        }
        .no-comments {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        
        /* Pour s'assurer que les boutons s'alignent correctement */
        .show-comments, .submit {
            display: inline-block;
            vertical-align: middle;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="monitoring-container">
        <h1>Tableau de bord</h1>
        
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
                            <th>Actions</th>
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
                                <button class="show-comments" onclick="toggleComments(<?= $article['id'] ?>)">
                                    Voir les commentaires
                                </button>
                                <a href="index.php?action=showUpdateArticleForm&id=<?= $article['id'] ?>" class="submit">
                                    Modifier
                                </a>
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
