<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Product</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #e8f4f8;
      color: #333;
      padding: 20px;
    }

    form {
      max-width: 400px;
      margin: 0 auto;
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }

    input[type="text"], input[type="number"], textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 2px solid #80c9ff;
      border-radius: 5px;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #007acc;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background-color: #005f99;
    }
  </style>
</head>
<body>
  <h1>Add New Product</h1>

  <form action="/add-product" method="POST">
    <label for="name">Product Name</label>
    <input type="text" id="name" name="name" required>

    <label for="price">Price</label>
    <input type="number" step="0.01" id="price" name="price" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4" required></textarea>

    <label for="image_url">Image URL</label>
    <input type="text" id="image_url" name="image_url" required>

    <button type="submit">Add Product</button>
  </form>
</body>
</html>
