<?php include("connection.php"); ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #e6ffed, #f3fff3);
      margin: 0;
      padding: 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h2 {
      color: #2d6a4f;
      text-align: center;
      margin-bottom: 20px;
      animation: fadeInDown 0.8s ease;
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    form {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      width: 400px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeIn 1s ease;
    }

    form:hover {
      transform: scale(1.02);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    label {
      font-weight: bold;
      color: #1b4332;
    }

    input[type="text"],
    input[type="number"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      transition: border-color 0.3s ease;
    }

    input:focus {
      border-color: #2d6a4f;
      outline: none;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #2d6a4f;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #40916c;
    }
  </style>
</head>
<body>

  <h2>Add New Product</h2>

  <!-- This form sends data to connection.php -->
  <form action="addProduct.php" method="POST" enctype="multipart/form-data">
    <label>Product Name:</label>
    <input type="text" name="name" required>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" required>

    <label>Category:</label>
    <input type="text" name="category" required>

    <label>Search Tag:</label>
    <input type="text" name="search_tag" required>

    <label>Upload Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit" name="submit">Add Product</button>
  </form>

</body>
</html>
