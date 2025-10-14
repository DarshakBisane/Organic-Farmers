<?php
include 'admin/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>All Products | Organic Farmers</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body{font-family:"Poppins",sans-serif;background:#fafff3;color:#333;margin:0;padding:0;}
    nav{background:linear-gradient(90deg,#5cb85c,#8fd400);padding:10px 20px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:1000;}
    .logo{font-size:1.6rem;font-weight:bold;color:white;letter-spacing:1px;}
    .nav-links{display:flex;gap:20px;list-style:none;}
    .nav-links a{text-decoration:none;color:white;font-weight:500;}
    .products{padding:50px 5%;display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;}
    .product-card{background:white;border-radius:15px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.1);transition:transform .4s ease,box-shadow .3s ease;}
    .product-card:hover{transform:translateY(-10px);box-shadow:0 8px 20px rgba(0,0,0,0.2);}
    .product-card img{width:100%;height:200px;object-fit:cover;}
    .product-info{padding:15px;text-align:center;}
    .product-info h3{color:#3e8e41;}
    footer{background:#3e8e41;color:white;text-align:center;padding:20px;margin-top:40px;}
  </style>
</head>

<body>

<!-- ===== NAVBAR ===== -->
<nav>
  <div class="logo">🌱 Organic Farmers</div>
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="product.php">Products</a></li>
    <li><a href="addProduct.php">Add Product</a></li>
  </ul>
</nav>

<!-- ===== PRODUCTS SECTION ===== -->
<section class="products">
<?php
$stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "
        <div class='product-card'>
            <a href='productview.php?id={$row['id']}'>
                <img src='admin/uploads/{$row['image']}' alt='{$row['name']}'>
                <div class='product-info'>
                    <h3>{$row['name']}</h3>
                    <p>Category: {$row['category']}</p>
                    <p>Price: ₹{$row['price']}</p>
                </div>
            </a>
        </div>
        ";
    }
} else {
    echo "<p style='text-align:center;color:gray;'>No products available.</p>";
}
?>
</section>

<footer>
  <p>© 2025 Organic Farmers. All rights reserved.</p>
</footer>

</body>
</html>
