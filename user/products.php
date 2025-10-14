<?php 
    include '../admin/connection.php'; 
    include ("header.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Products | Organic Farmers</title>
<link rel="stylesheet" href="style.css">
<style>
body{font-family:"Poppins",sans-serif;background:#fafff3;color:#333;margin:0;padding:0;}
.products{padding:50px 5%;display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;}
.product-card{background:white;border-radius:15px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.product-card img{width:100%;height:200px;object-fit:cover;}
.product-info{padding:15px;text-align:center;}
.product-info h3{color:#3e8e41;}
</style>
</head>
<body>

<section class="products">
<?php
$stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    while($row=$result->fetch_assoc()){
        echo "<div class='product-card'>
            <a href='productview.php?id={$row['id']}'>
                <img src='../admin/uploads/{$row['image']}'>
                <div class='product-info'>
                    <h3>{$row['name']}</h3>
                    <p>Category: {$row['category']}</p>
                    <p>Price: ₹{$row['price']}</p>
                </div>
            </a>
        </div>";
    }
}else{ echo "<p style='text-align:center;color:gray;'>No products available.</p>"; }
?>
</section>
</body>
</html>
