<?php
function validateUrl($url) {
    $parsedUrl = parse_url($url);
    if ($parsedUrl === false || !isset($parsedUrl['host'])) {
        return false;
    }

    // Vérification du schéma de l'URL
    if (!in_array($parsedUrl['scheme'], ['http', 'https'])) {
        return false;
    }

    // Permettre les requêtes locales pour le test SSRF
    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imageUrl = $_POST['url'];
    $imagePath = 'downloaded_image.jpg';

    if (!validateUrl($imageUrl)) {
        $error = "Invalid URL or disallowed URL.";
    } else {
        $imageData = @file_get_contents($imageUrl);
        
        if ($imageData === FALSE) {
            $error = "Unable to download image.";
        } else {
            file_put_contents($imagePath, $imageData);
            $success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Download Image</title>
</head>
<body>
    <h1>Download Image</h1>
    <form method="POST" action="">
        <label for="url">Image URL:</label>
        <input type="text" id="url" name="url" required>
        <button type="submit">Download</button>
    </form>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif (isset($success)): ?>
        <h1>Image Downloaded</h1>
        <img src="downloaded_image.jpg" alt="Downloaded Image">
    <?php endif; ?>
</body>
</html>
