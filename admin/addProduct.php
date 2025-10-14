<?php include("connection.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product</title>
<style>
body{font-family:'Poppins',sans-serif;background:linear-gradient(to right,#e6ffed,#f3fff3);margin:0;padding:40px;display:flex;flex-direction:column;align-items:center;}
h2{color:#2d6a4f;text-align:center;margin-bottom:20px;}
form{background:white;padding:30px;border-radius:15px;box-shadow:0 8px 20px rgba(0,0,0,0.1);width:400px;}
input,button{width:100%;padding:10px;margin:8px 0;border-radius:5px;}
button{background:#2d6a4f;color:white;border:none;cursor:pointer;}
button:hover{background:#40916c;}
</style>
</head>
<body>

<h2>Add New Product</h2>
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
