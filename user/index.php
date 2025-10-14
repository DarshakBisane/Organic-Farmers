<?php include '../admin/connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organic Farmers | Home</title>
<link rel="stylesheet" href="style.css">
<script src="main.js" defer></script>
<style>
/* Basic styles + slider + products */
body{font-family:"Poppins",sans-serif;background:#fafff3;margin:0;padding:0;}
nav{background:linear-gradient(90deg,#5cb85c,#8fd400);padding:10px 20px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;}
.nav-links{display:flex;gap:20px;list-style:none;}
.nav-links a{text-decoration:none;color:white;font-weight:500;}
.menu-toggle{display:none;font-size:1.8rem;color:white;cursor:pointer;}
@media(max-width:768px){.nav-links{display:none;flex-direction:column;background:#5cb85c;position:absolute;top:60px;right:0;width:200px;padding:15px;}.nav-links.active{display:flex;}.menu-toggle{display:block;}}
.slider{width:100%;height:500px;overflow:hidden;position:relative;margin-top:10px;}
.slider img{width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;opacity:0;transition:opacity 1s ease-in-out;}
.slider img.active{opacity:1;}
.products{padding:50px 5%;display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;}
.product-card{background:white;border-radius:15px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.product-card img{width:100%;height:200px;object-fit:cover;}
.product-info{padding:15px;text-align:center;}
.product-info h3{color:#3e8e41;}
footer{background:#3e8e41;color:white;text-align:center;padding:20px;margin-top:40px;}
</style>
</head>
<body>

<nav>
    <div class="logo">🌱 Organic Farmers</div>
    <div class="menu-toggle" id="menuToggle">⋮</div>

    <ul class="nav-links" id="navLinks">
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <?php if(isset($_SESSION['user_name'])): ?>
            <li><a href="profile.php"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
        <?php else: ?>
            <li><a href="login.php">Sign In</a></li>
        <?php endif; ?>
        <li><a href="#">Contact</a></li>
    </ul>
</nav>


<div class="slider" id="slider">
  <img src="https://images.unsplash.com/photo-1501004318641-b39e6451bec6" class="slide active">
  <img src="https://images.unsplash.com/photo-1498654200943-1088dd4438ae" class="slide">
  <img src="https://images.unsplash.com/photo-1511690743698-d9d85f2fbf38" class="slide">
</div>

<section class="products">
<?php
$limit = 30;
$stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT ?");
$stmt->bind_param("i",$limit);
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
}else{ echo "<p style='text-align:center;color:gray;'>No products found.</p>"; }
?>
</section>

<footer>
<p>© 2025 Organic Farmers. All rights reserved.</p>
</footer>

<script>
const slides = document.querySelectorAll("#slider .slide");
let current = 0;
setInterval(() => { slides[current].classList.remove("active"); current=(current+1)%slides.length; slides[current].classList.add("active"); }, 4000);
</script>
</body>
</html>
