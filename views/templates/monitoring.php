<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Monitoring des articles</title>
    <link rel="stylesheet" href="css/monitoring.css">
    <link href="https://fonts.googleapis.com/css2?family=Qwitcher+Grypen:wght@400;700&display=swap" rel="stylesheet">
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
                        <tr class="comments-row" id="comments-row-<?= $article['id'] ?>">
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
            const row = document.getElementById(`comments-row-${articleId}`);
            
            if (!panel.classList.contains('active')) {
                panel.innerHTML = '<div class="loading">Chargement des commentaires...</div>';
                // Afficher la ligne du tableau
                row.classList.add('active');
                
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
                            // Cacher la ligne si erreur
                            row.classList.remove('active');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        panel.innerHTML = '<div class="error">Erreur lors du chargement des commentaires</div>';
                        // Cacher la ligne si erreur
                        row.classList.remove('active');
                    });
            } else {
                // Cacher la ligne quand on ferme le panneau
                row.classList.remove('active');
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