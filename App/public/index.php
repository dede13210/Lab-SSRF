<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use MongoDB\Client as MongoClient;

// Configuration de l'application
$app = AppFactory::create();
$serverPort = 3000;

// Configuration de MongoDB
$username = 'admin';
$password = 'password';
$host = '127.0.0.1';
$portDB = '27017';
$database = 'ecommerceDB';

// Construction de l'URI de connexion MongoDB
$uri = "mongodb://$username:$password@$host:$portDB/$database?authSource=admin";

// Connexion à MongoDB avec gestion des erreurs
try {
    $client = new MongoClient($uri);
    $db = $client->$database;
    $productsCollection = $db->products;
    error_log("MongoDB connected successfully");
} catch (Exception $e) {
    error_log("MongoDB connection error: " . $e->getMessage());
    exit;
}

// Route pour la page d'accueil avec filtre des produits visibles
$app->get('/', function ($request, $response, $args) use ($productsCollection) {
    $products = $productsCollection->find(['visible' => true])->toArray();

    // Pass products to the HTML file
    ob_start();
    include 'index_content.php'; // Now including the PHP file that handles dynamic content
    $output = ob_get_clean();
    $response->getBody()->write($output);
    return $response;
});

// Route pour la recherche
$app->get('/search', function ($request, $response, $args) use ($productsCollection) {
    $searchQuery = $request->getQueryParams()['query'] ?? '';

    $parsedQuery = ['name' => new MongoDB\BSON\Regex($searchQuery, 'i')];

    $finalQuery = [
        '$and' => [
            ['visible' => true],
            $parsedQuery
        ]
    ];

    $products = $productsCollection->find($finalQuery)->toArray();
    
    // Pass products to the HTML file
    ob_start();
    include 'index_content.php'; // Now including the PHP file that handles dynamic content
    $output = ob_get_clean();
    $response->getBody()->write($output);
    return $response;
});

// Route pour afficher le formulaire d'ajout de produit
$app->get('/add-product', function ($request, $response, $args) {
    ob_start();
    include 'add_product.php'; // Ce fichier doit contenir le formulaire HTML
    $output = ob_get_clean();
    $response->getBody()->write($output);
    return $response;
});

// Route pour gérer la soumission du formulaire d'ajout de produit
$app->post('/add-product', function ($request, $response, $args) use ($productsCollection) {
    $parsedBody = $request->getParsedBody();

    // Récupérer l'URL de l'image
    $imageUrl = $parsedBody['image_url'];

    // Télécharger et enregistrer l'image
    $filename = downloadImage($imageUrl, __DIR__ . '/images',$parsedBody['name'].'.jpg');
    $savedImageUrl = '/images/' . $filename;

    $newProduct = [
        'name' => $parsedBody['name'],
        'price' => (float) $parsedBody['price'],
        'description' => $parsedBody['description'],
        'image_url' => $savedImageUrl,
        'visible' => true
    ];

    // Insérer le nouveau produit dans la collection MongoDB
    $productsCollection->insertOne($newProduct);

    // Rediriger vers la page d'accueil après l'ajout du produit
    return $response->withHeader('Location', '/')->withStatus(302);
});

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->get('/delete/{productname}', function (Request $request, Response $response, array $args) use ($productsCollection) {
    $productname = $args['productname'];

    // Vérifier que la requête provient d'un espace local
    if (!in_array($request->getServerParams()['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
        $response->getBody()->write('Access forbidden');
        return $response->withStatus(403);
    }

    // Trouver le produit dans la base de données
    $product = $productsCollection->findOne(['name' => $productname]);

    if ($product) {
        // Supprimer l'image associée
        $imagePath = 'path/to/images/' . $product['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Supprimer le produit de la base de données
        $productsCollection->deleteOne(['name' => $productname]);

        $response->getBody()->write('Product deleted successfully');
        return $response;
    } else {
        $response->getBody()->write('Product not found');
        return $response->withStatus(404);
    }
});


// Route pour afficher la page de confirmation de suppression
$app->get('/delete-confirmation', function ($request, $response, $args) {
    ob_start();
    include 'delete_confirmation.php';
    $output = ob_get_clean();
    $response->getBody()->write($output);
    return $response;
});


// Fonction pour télécharger une image depuis une URL et l'enregistrer sur le serveur
function downloadImage($url, $saveDir, $filename) {
    $ch = curl_init($url);
    $basename = basename(parse_url($url, PHP_URL_PATH));


    $savePath = $saveDir . DIRECTORY_SEPARATOR . $filename;

    $fp = fopen($savePath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    return $filename;
}




// Démarrer le serveur
$app->run();
