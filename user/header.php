

<!-- ===== HEADER / NAVBAR ===== -->
<nav class="navbar">
  <div class="logo">🌱 Organic Farmers</div>
  <div class="menu-toggle" id="menuToggle">☰</div>
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

<style>
/* ===== NAVBAR STYLING ===== */
.navbar {
  position: sticky;
  top: 30;
  justify-self: center;
  width: 80%;
  z-index: 1000;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 30px;
  border-radius: 50px;
  backdrop-filter: blur(10px);
  background: rgba(57, 74, 61, 0.39); /* green with slight transparency */
  box-shadow: 0 4px 15px rgba(0,0,0,0.5);
  transition: all 0.3s ease;
}

.navbar:hover {
  background: rgba(0, 54, 4, 0.92);
}

.logo {
  font-size: 1.8rem;
  font-weight: 700;
  color: #fff;
  letter-spacing: 1px;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.2);
}

.nav-links {
  display: flex;
  gap: 25px;
  list-style: none;
  align-items: center;
}

.nav-links li a {
  text-decoration: none;
  font-weight: 500;
  color: #fff;
  padding: 8px 15px;
  border-radius: 8px;
  position: relative;
  transition: all 0.3s ease;
}

.nav-links li a::after {
  content: "";
  position: absolute;
  width: 0%;
  height: 2px;
  bottom: 0;
  left: 0;
  background: #ffffffff;
  transition: width 0.3s ease;
}

.nav-links li a:hover::after {
  width: 100%;
}

.nav-links li a:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-3px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* MOBILE MENU */
.menu-toggle {
  display: none;
  font-size: 2rem;
  color: #fff;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.menu-toggle:hover {
  transform: rotate(90deg);
}

@media (max-width: 768px) {
  .menu-toggle {
    display: block;
  }
  .nav-links {
    display: none;
    flex-direction: column;
    position: absolute;
    top: 70px;
    right: 0;
    background: rgba(40, 167, 69, 0.95);
    width: 200px;
    padding: 15px;
    border-radius: 10px 0 0 10px;
  }
  .nav-links.active {
    display: flex;
  }
  .nav-links li a {
    padding: 10px 15px;
  }
}
</style>

<script>
// ===== MOBILE MENU TOGGLE =====
const menuToggle = document.getElementById("menuToggle");
const navLinks = document.getElementById("navLinks");

menuToggle.addEventListener("click", () => {
  navLinks.classList.toggle("active");
});
</script>
