<?php include 'admin/connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Organic Farmers | Fresh & Natural</title>
  <link rel="stylesheet" href="style.css">
  <script src="main.js" defer></script>
  <style>
    /* ====== BASIC STYLES ====== */
    *{margin:0;padding:0;box-sizing:border-box;font-family:"Poppins",sans-serif;}
    body{background-color:#fafff3;color:#333;overflow-x:hidden;}
    nav{background:linear-gradient(90deg,#5cb85c,#8fd400);padding:10px 20px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:1000;}
    .logo{font-size:1.6rem;font-weight:bold;color:white;letter-spacing:1px;}
    .nav-links{display:flex;gap:20px;list-style:none;transition:all .3s ease-in-out;}
    .nav-links a{text-decoration:none;color:white;font-weight:500;transition:color .3s;}
    .nav-links a:hover{color:#1f1f1f;}
    .search-container{position:relative;}
    .search-input{padding:6px 30px 6px 10px;border-radius:20px;border:none;outline:none;}
    .search-btn{position:absolute;right:5px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#5cb85c;font-size:1.1rem;}
    .menu-toggle{display:none;font-size:1.8rem;color:white;cursor:pointer;}
    @media(max-width:768px){
      .nav-links{display:none;flex-direction:column;background:#5cb85c;position:absolute;top:60px;right:0;width:200px;padding:15px;}
      .nav-links.active{display:flex;animation:fadeIn .5s ease;}
      .menu-toggle{display:block;}
      .search-container{display:none;}
    }

    /* ===== SLIDER ===== */
    .slider{width:100%;height:500px;overflow:hidden;position:relative;margin-top:10px;}
    .slider img{width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;opacity:0;transition:opacity 1s ease-in-out;}
    .slider img.active{opacity:1;}

    /* ===== PRODUCTS ===== */
    .products{padding:50px 5%;display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;}
    .product-card{background:white;border-radius:15px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.1);transition:transform .4s ease,box-shadow .3s ease;animation:fadeInUp 1s ease;}
    .product-card:hover{transform:translateY(-10px);box-shadow:0 8px 20px rgba(0,0,0,0.2);}
    .product-card img{width:100%;height:200px;object-fit:cover;}
    .product-info{padding:15px;text-align:center;}
    .product-info h3{color:#3e8e41;}

    /* ===== FOOTER ===== */
    footer{background:#3e8e41;color:white;text-align:center;padding:20px;margin-top:40px;}

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp{from{opacity:0;transform:translateY(30px);}to{opacity:1;transform:translateY(0);}}
    @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
  </style>
</head>

<body>
  <!-- ===== NAVBAR ===== -->
  <nav>
    <div class="logo">🌱 Organic Farmers</div>


    <div class="menu-toggle" id="menuToggle">⋮</div>

    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="login.php">Sigh In </a></li>
      <li><a href="#">Contact</a></li>
    </ul>
  </nav>

  <!-- ===== SLIDER ===== -->
  <div class="slider" id="slider">
    <img src="https://images.unsplash.com/photo-1501004318641-b39e6451bec6" alt="Farm 1" class="slide active">
    <img src="https://images.unsplash.com/photo-1498654200943-1088dd4438ae" alt="Farm 2" class="slide">
    <img src="https://images.unsplash.com/photo-1511690743698-d9d85f2fbf38" alt="Farm 3" class="slide">
  </div>

  <!-- ===== PRODUCTS SECTION ===== -->
  <section class="products">
    <?php
    $limit = 30;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    if ($search != '') {
        $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR search_tag LIKE ? ORDER BY id DESC");
        $likeSearch = "%$search%";
        $stmt->bind_param("ss", $likeSearch, $likeSearch);
    } else {
        $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
    }

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
    echo "<p style='text-align:center;color:gray;'>No products found.</p>";
}
?>
  </section>

  <!-- ===== FOOTER ===== -->
  <footer>
    <p>© 2025 Organic Farmers. All rights reserved.</p>
  </footer>

  <!-- ===== JS ===== -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Mobile Menu Toggle
      const menuToggle = document.getElementById("menuToggle");
      const navLinks = document.getElementById("navLinks");

      menuToggle.addEventListener("click", () => {
        navLinks.classList.toggle("active");
      });

      // Search Simulation
      document.getElementById("searchForm").addEventListener("submit", (e) => {
        const query = e.target.search.value.trim();
        if (!query) e.preventDefault(); // prevent empty search
      });

      // Simple Slider
      const slides = document.querySelectorAll("#slider .slide");
      let current = 0;

      setInterval(() => {
        slides[current].classList.remove("active");
        current = (current + 1) % slides.length;
        slides[current].classList.add("active");
      }, 4000);
    });
  </script>
</body>
</html>
