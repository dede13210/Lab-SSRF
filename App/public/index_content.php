<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-commerce Site</title>
  <style>
    /* Basic Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Arial', sans-serif;
  background-color: #e8f4f8; /* Background color changed to a light blue shade */
  color: #333;
  line-height: 1.6;
  padding: 20px;
}

header, footer {
  background-color: #007acc; /* Changed to a more vibrant blue */
  color: #ffffff;
  padding: 10px 20px;
  text-align: center;
}

h1 {
  margin-top: 20px;
}

/* Search bar styling */
form {
  margin-top: 20px;
  margin-bottom: 40px;
}

input[type="text"] {
  padding: 10px;
  width: 300px;
  margin-right: 10px;
  border: 2px solid #80c9ff; /* Light blue border */
  border-radius: 5px;
}

button {
  padding: 10px 20px;
  background-color: #007acc; /* Match header/footer color */
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
}

button:hover {
  background-color: #005f99; /* Darker blue for hover */
}

/* Product list styling */
ul {
  list-style-type: none;
}

li {
  background: #ffffff;
  margin: 20px 0;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

h3 {
  color: #007acc; /* Match header/footer color */
}

p {
  margin: 5px 0;
}

/* Responsive Design */
@media (max-width: 600px) {
  input[type="text"], button {
    width: 100%;
    margin-top: 10px;
  }

  input[type="text"] {
    width: calc(100% - 22px); /* accounting for padding and border */
  }
}
  </style>
</head>
<body>
  <h1>Welcome to our E-commerce Site</h1>
  
  <a href="/add-product">
  <button type="button">Add Product</button>
</a>

  <h2>Products</h2>
  <ul>
    <?php foreach ($products as $product): ?>
      <li>
        <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
        <img src="/images/<?= htmlspecialchars(str_replace(' ', '', $product['name'])) ?>.jpg" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" style="max-width: 100%; height: auto;">
        <p>Price: $<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?></p>
        <p>Description: <?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>
      </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>
