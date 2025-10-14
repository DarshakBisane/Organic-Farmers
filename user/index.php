<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include '../admin/connection.php';

// Handle search query
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organic Farmers | Home</title>
<link rel="stylesheet" href="style.css">
<style>
/* ===== BASIC STYLES ===== */
body{font-family:"Poppins",sans-serif;background:#fafff3;margin:0;padding:0;}
nav{background:linear-gradient(90deg,#5cb85c,#8fd400);padding:10px 20px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:1000;}
.logo{font-size:1.6rem;font-weight:bold;color:white;}
.nav-links{display:flex;gap:20px;list-style:none;align-items:center;}
.nav-links a{text-decoration:none;color:white;font-weight:500;}
.menu-toggle{display:none;font-size:1.8rem;color:white;cursor:pointer;}

/* ===== SEARCH BAR ===== */
.nav-links form{display:flex;}
.nav-links input[type="text"]{padding:5px 10px;border-radius:20px;border:none;outline:none;}
.nav-links button{padding:5px 10px;margin-left:5px;border:none;border-radius:5px;background:white;color:#5cb85c;cursor:pointer;font-weight:bold;}

/* ===== MOBILE ===== */
@media(max-width:768px){
  .nav-links{display:none;flex-direction:column;background:#5cb85c;position:absolute;top:60px;right:0;width:200px;padding:15px;z-index:9999;}
  .nav-links.active{display:flex;}
  .menu-toggle{display:block;}
  .nav-links form{flex-direction:column;margin-top:10px;}
  .nav-links input[type="text"]{width:100%;margin-bottom:5px;}
  .nav-links button{width:100%;margin-left:0;}
}

/* ===== SLIDER ===== */
.slider{width:100%;height:500px;overflow:hidden;position:relative;margin-top:10px;}
.slider img{width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;opacity:0;transition:opacity 1s ease-in-out;}
.slider img.active{opacity:1;}

/* ===== PRODUCTS ===== */
.products{padding:50px 5%;display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;}
.product-card{background:white;border-radius:15px;padding:3px; overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.product-card img{width:100%;height:200px;object-fit:cover;border-radius : 12px;}
.product-info{padding:15px;text-align:center;}
.product-info h3{color:#3e8e41;}

/* ===== FOOTER ===== */
footer{background:#3e8e41;color:white;text-align:center;padding:20px;margin-top:40px;}
</style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav>
  <div class="logo">🌱 Organic Farmers</div>
  <div> <!-- SEARCH FORM -->
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
  </div>
  <div class="menu-toggle" id="menuToggle">⋮</div>
  <ul class="nav-links" id="navLinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="products.php">Products</a></li>
    <li>
      <?php if(isset($_SESSION['user_name'])): ?>
        <a href="profile.php"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
      <?php else: ?>
        <a href="login.php">Sign In</a>
      <?php endif; ?>
    </li>
    <li><a href="#">Contact</a></li>
  </ul>
</nav>

<!-- ===== SLIDER ===== -->
<div class="slider" id="slider">
  <img src="https://images.unsplash.com/photo-1501004318641-b39e6451bec6" class="slide active">
  <img src="https://images.unsplash.com/photo-1498654200943-1088dd4438ae" class="slide">
  <img src="https://images.unsplash.com/photo-1511690743698-d9d85f2fbf38" class="slide">
</div>

<!-- ===== PRODUCTS ===== -->
<section class="products">
<?php
$limit = 30;
if($search != ''){
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR search_tag LIKE ? ORDER BY id DESC");
    $likeSearch = "%$search%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
} else {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT ?");
    $stmt->bind_param("i",$limit);
}
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    while($row=$result->fetch_assoc()){
        echo "<div class='product-card'>
            <a href='productview.php?id={$row['id']}'>
                <img src='../admin/uploads/{$row['image']}' alt='{$row['name']}'>
                <div class='product-info'>
                    <h3>{$row['name']}</h3>
                    <p>Category: {$row['category']}</p>
                    <p>Price: ₹{$row['price']}</p>
                </div>
            </a>
        </div>";
    }
}else{
    echo "<p style='text-align:center;color:gray;'>No products found.</p>";
}
?>
</section>

<footer>
<p>© 2025 Organic Farmers. All rights reserved.</p>
</footer>

<!-- ===== JS ===== -->
<script>
// ===== MOBILE MENU TOGGLE =====
const menuToggle = document.getElementById("menuToggle");
const navLinks = document.getElementById("navLinks");
menuToggle.addEventListener("click", () => {
    navLinks.classList.toggle("active");
});

// ===== SLIDER =====
const slides = document.querySelectorAll("#slider .slide");
let current = 0;
setInterval(() => {
    slides[current].classList.remove("active");
    current = (current + 1) % slides.length;
    slides[current].classList.add("active");
}, 4000);
</script>

</body>
</html>
