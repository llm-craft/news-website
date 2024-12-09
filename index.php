<?php
// API key for NewsAPI
define('API_KEY', '19077d1472b34f7e9af28a8a2fddabe6');

/**
 * Function to fetch news articles based on a keyword
 * @param string $keyword Search term
 * @return array Array of news articles
 */
function getNews($keyword) {
    $url = "https://newsapi.org/v2/everything?" . http_build_query([
        'q' => $keyword,
        'apiKey' => API_KEY,
        'language' => 'fr',
        'sortBy' => 'relevancy',
        'pageSize' => 10
    ]);

    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Process form submission
$articles = [];
$keyword = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['keyword'])) {
    $keyword = htmlspecialchars($_POST['keyword']);
    try {
        $result = getNews($keyword);
        if (isset($result['articles'])) {
            $articles = $result['articles'];
        } else {
            $error = "Aucun article trouvé";
        }
    } catch (Exception $e) {
        $error = "Une erreur s'est produite lors de la recherche";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .article-card {
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .article-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Recherche d'Articles</h1>
        
        <form method="POST" class="mb-4">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" placeholder="Entrez un mot-clé" value="<?php echo $keyword; ?>" required>
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($articles)): ?>
            <div class="row">
                <?php foreach ($articles as $article): ?>
                    <div class="col-md-6">
                        <div class="card article-card">
                            <?php if (!empty($article['urlToImage'])): ?>
                                <img src="<?php echo htmlspecialchars($article['urlToImage']); ?>" class="card-img-top" alt="Article image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($article['description']); ?></p>
                                <p class="card-text"><small class="text-muted">
                                    Par: <?php echo htmlspecialchars($article['author'] ?? 'Auteur inconnu'); ?><br>
                                    Date: <?php echo date('d/m/Y H:i', strtotime($article['publishedAt'])); ?>
                                </small></p>
                                <a href="<?php echo htmlspecialchars($article['url']); ?>" class="btn btn-primary" target="_blank">Lire l'article</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
