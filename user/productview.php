<?php
include 'admin/connection.php';

// Check if 'id' is set in URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid product.");
}

$product_id = $_GET['id'];

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $product['name']; ?> | Organic Farmers</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body{font-family:"Poppins",sans-serif;background:#fafff3;color:#333;margin:0;padding:0;}
    .container{max-width:900px;margin:50px auto;padding:20px;background:white;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
    img{width:100%;max-height:500px;object-fit:cover;border-radius:10px;}
    h1{color:#3e8e41;margin-top:20px;}
    p{margin:10px 0;font-size:1.1rem;}
    a.back{display:inline-block;margin-top:20px;padding:10px 15px;background:#5cb85c;color:white;border-radius:5px;text-decoration:none;}
    a.back:hover{background:#4aa63a;}
  </style>
</head>
<body>

<div class="container">
    <img src="admin/uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
    <h1><?php echo $product['name']; ?></h1>
    <p><strong>Category:</strong> <?php echo $product['category']; ?></p>
    <p><strong>Price:</strong> ₹<?php echo $product['price']; ?></p>
    <p><strong>Description:</strong> <?php echo $product['description']; ?></p>
    <a href="index.php" class="back">← Back to Home</a>
</div>

</body>
</html>
