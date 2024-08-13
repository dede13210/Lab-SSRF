<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use MongoDB\Client as MongoClient;
use Workerman\Worker;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Uri;
use MongoDB\BSON\Regex as Regex;
use Psr\Http\Message\ServerRequestInterface;
use Workerman\Protocols\Http\Request as WorkermanRequest;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = AppFactory::create();

// Add middleware for routing
$app->addRoutingMiddleware();

// Custom middleware to serve static files
$app->add(function ($request, $handler) {
    $uri = $request->getUri()->getPath();
    $file = __DIR__ . $uri;

    if (file_exists($file) && is_file($file)) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(file_get_contents($file));
        return $response->withHeader('Content-Type', mime_content_type($file));
    }

    return $handler->handle($request);
});

// Configuration de MongoDB
$username = 'admin';
$password = 'password';
$host = 'mongodb';
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

// Route pour la page d'accueil
$app->get('/', function ($request, $response, $args) use ($productsCollection) {
    // Récupérer les produits visibles
    $products = $productsCollection->find(['visible' => true])->toArray();

    // Inclure le contenu dynamique de la page d'accueil
    ob_start();
    include 'index_content.php';
    $output = ob_get_clean();

    // Retourner la réponse
    $response->getBody()->write($output);
    return $response;
});

// Route pour afficher le formulaire d'ajout de produit
$app->get('/add-product', function ($request, $response, $args) {
    ob_start();
    include 'add_product.php';
    $output = ob_get_clean();
    $response->getBody()->write($output);
    return $response;
});

// Route pour gérer la soumission du formulaire d'ajout de produit
$app->post('/add-product', function ($request, $response, $args) use ($productsCollection) {
    $parsedBody = $request->getParsedBody();

    $imageUrl = $parsedBody['image_url'];
    $name = str_replace(' ', '', $parsedBody['name']);

    $downloadResult = downloadImage($imageUrl, __DIR__ . '/images', $name . '.jpg');
    if ($downloadResult !== null) {
        $response->getBody()->write('<div>' . htmlspecialchars($downloadResult) . '</div>');
        return $response;
    }

    $savedImageUrl = '/images/' . $name . '.jpg';

    $newProduct = [
        'name' => $parsedBody['name'],
        'price' => (float)$parsedBody['price'],
        'description' => $parsedBody['description'],
        'image_url' => $savedImageUrl,
        'visible' => true
    ];

    $productsCollection->insertOne($newProduct);

    // Construire un message de confirmation
    $message = "Produit ajouté avec succès. <a href='/'>Retour à l'accueil</a> ou <a href='/add-product'>Ajouter un autre produit</a>";

    // Afficher le message directement sur la page
    $response->getBody()->write($message);
    return $response;
});

$app->get('/delete/{productname}', function (Request $request, Response $response, array $args) use ($productsCollection) {
    $productname = $args['productname'];

    error_log("Delete route triggered for product: " . $productname);

    // Vérifier que la requête provient d'un espace local
    $remoteAddr = $request->getAttribute('REMOTE_ADDR');
    error_log("Request from IP: " . $remoteAddr);
    
    if (!in_array($remoteAddr, ['127.0.0.1', '::1'])) {
        $response->getBody()->write('Access forbidden');
        error_log("Access forbidden for IP: " . $remoteAddr);
        return $response->withStatus(403);
    }

    // Trouver le produit dans la base de données
    $product = $productsCollection->findOne(['name' => $productname]);
    if ($product) {
        error_log("Product found: " . json_encode($product));
        // Supprimer l'image associée
        $imagePath = '/images/' . str_replace(' ', '', $product['name']);
        if (file_exists($imagePath)) {
            unlink($imagePath);
            error_log("Image deleted: " . $imagePath);
        } else {
            error_log("Image not found: " . $imagePath);
        }

        // Supprimer le produit de la base de données
        $productsCollection->deleteOne(['name' => $productname]);
        error_log("Product deleted successfully");
        $response->getBody()->write('Product deleted successfully');
        return $response;
    } else {
        error_log("Product not found");
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


// Démarrer le serveur Workerman
$worker = new Worker('http://0.0.0.0:3000');
$worker->count = 4; // Définir le nombre de processus workers

// Fonction pour télécharger une image depuis une URL et l'enregistrer sur le serveur
function downloadImage($url, $saveDir, $filename) {
    $savePath = $saveDir . DIRECTORY_SEPARATOR . $filename;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 20);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "cURL error: $error";
    }

    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if (strpos($contentType, 'image') === false) {
        return $response;
    }

    file_put_contents($savePath, $response);
    return null;
}

function convertToPsrRequest(WorkermanRequest $workermanRequest): ServerRequestInterface {
    // Convert WorkermanRequest to PSR-7 ServerRequest
    $uri = $workermanRequest->uri();
    $method = $workermanRequest->method();
    $headers = $workermanRequest->header();
    $body = $workermanRequest->rawBody();
    $protocol = $workermanRequest->protocolVersion();

    // Create a Guzzle PSR-7 request and manually set the body
    $psrRequest = new ServerRequest(
        $method,
        $uri,
        $headers,
        $body,
        $protocol
    );

    // If it's a POST request, make sure to handle the body correctly
    if ($method === 'POST' && !empty($body)) {
        parse_str($body, $parsedBody);
        $psrRequest = $psrRequest->withParsedBody($parsedBody);
    }

    return $psrRequest;
}

$worker->onMessage = function ($connection, WorkermanRequest $workermanRequest) use ($app) {
    // Get the client's IP address
    $remoteAddr =  $connection->getRemoteIp();


    error_log("Request from IP: " . $remoteAddr);

    $psrRequest = convertToPsrRequest($workermanRequest);
    $psrRequest = $psrRequest->withAttribute('REMOTE_ADDR', $remoteAddr);
    $uri = new Uri(
        $psrRequest->getUri()->getScheme(),
        $psrRequest->getUri()->getHost(),
        $psrRequest->getUri()->getPort(),
        $psrRequest->getUri()->getPath(),
        $psrRequest->getUri()->getQuery()
    );

    $slimRequest = ServerRequestFactory::createFromGlobals()
        ->withUri($uri)
        ->withMethod($psrRequest->getMethod())
        ->withParsedBody($psrRequest->getParsedBody())
        ->withAttribute('REMOTE_ADDR', $remoteAddr);

    $response = $app->handle($slimRequest);

    $connection->send((string)$response->getBody());
};

Worker::runAll();