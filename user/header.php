<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- ===== HEADER / NAVBAR ===== -->
<nav class="navbar">
  <div class="logo">🌱 Organic Farmers</div>

  <!-- Search Section -->
  <div class="searchbar">
    <form id="searchForm" class="search-container" autocomplete="off">
      <input type="text" name="search" id="searchInput" placeholder="Search products...">
      <button type="submit" id="searchButton">🔍</button>
      <div id="suggestions"></div>
    </form>
  </div>

  <div class="menu-toggle" id="menuToggle">☰</div>
  <ul class="nav-links" id="navLinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="products.php">Products</a></li>
    <li>
      <?php if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])): ?>
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
  top: 20px;
  justify-self: center;
  width: 90%;
  max-width: 1200px;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 5px 30px;
  border-radius: 50px;
  backdrop-filter: blur(12px);
  background: rgba(57, 74, 61, 0.4);
  box-shadow: 0 4px 15px rgba(0,0,0,0.5);
  margin: 10px auto;
  transition: all 0.3s ease;
}
.navbar:hover { background: rgba(0, 54, 4, 0.9); }

.logo {
  font-size: 1.5rem;
  font-weight: 700;
  color: #fff;
  letter-spacing: 1px;
  transition: transform 0.3s ease;
}
.logo:hover { transform: scale(1.1); }

/* ===== SEARCH BAR ===== */
.searchbar {
  top: 8px;
  flex-grow: 1;
  display: flex;
  justify-content: center;
  position: relative;
}

.search-container {
  display: flex;
  align-items: center;
  background: rgba(255,255,255,0.2);
  backdrop-filter: blur(8px);
  border-radius: 50px;
  padding: 5px 10px;
  transition: 0.3s;
  width: 55%;
  position: relative;
}
.search-container:hover {
  background: rgba(255,255,255,0.3);
}
.search-container input {
  flex: 1;
  padding: 10px 15px;
  border: none;
  outline: none;
  background: transparent;
  color: #fdfcfcff;
  font-size: 1rem;
}
.search-container button {
  background: rgba(0, 0, 0, 0);
  border: none;
  color: rgba(0, 255, 0, 1);
  border-radius: 50%;
  margin-right: 5px;
  width: 30px;
  height: 30px;
  cursor: pointer;
  transition: 0.3s;
}
.search-container button:hover {
  background: rgba(45, 61, 46, 0.52);
  color: white;
}

/* ===== SUGGESTIONS ===== */
#suggestions {
  position: absolute;
  top: 45px;
  left: 0;
  width: 100%;
  background: rgba(255,255,255,0.9);
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  max-height: 200px;
  overflow-y: auto;
  display: none;
}
#suggestions div {
  padding: 10px;
  cursor: pointer;
  transition: background 0.2s;
}
#suggestions div:hover {
  background: rgba(46,125,50,0.2);
}

/* ===== NAV LINKS ===== */
.nav-links {
  display: flex;
  gap: 25px;
  list-style: none;
}
.nav-links li a {
  text-decoration: none;
  color: #fff;
  font-weight: 500;
  transition: 0.3s;
}
.nav-links li a:hover {
  transform: translateY(-2px);
  color: #b5f5b5;
}

/* ===== MOBILE ===== */
.menu-toggle {
  display: none;
  font-size: 2rem;
  color: #fff;
  cursor: pointer;
}
@media (max-width: 768px) {
  .menu-toggle { display: block; }
  .nav-links {
    display: none;
    flex-direction: column;
    position: absolute;
    top: 70px;
    right: 0;
    background: rgba(40,167,69,0.95);
    width: 200px;
    padding: 15px;
    border-radius: 10px 0 0 10px;
  }
  .nav-links.active { display: flex; }
}
</style>

<script>
// ===== MENU TOGGLE =====
document.getElementById("menuToggle").addEventListener("click", function(){
  document.getElementById("navLinks").classList.toggle("active");
});

// ===== AJAX SEARCH =====
const searchInput = document.getElementById('searchInput');
const searchForm = document.getElementById('searchForm');
const suggestionBox = document.getElementById('suggestions');

searchInput.addEventListener('input', function() {
  const query = this.value.trim();
  if (query === '') {
    suggestionBox.style.display = 'none';
    fetchAllProducts();
    return;
  }

  fetch(`search_suggest.php?query=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      suggestionBox.innerHTML = '';
      if (data.length > 0) {
        data.forEach(item => {
          const div = document.createElement('div');
          div.textContent = item;
          div.addEventListener('click', () => {
            searchInput.value = item;
            suggestionBox.style.display = 'none';
            fetchSearchResults(item);
          });
          suggestionBox.appendChild(div);
        });
        suggestionBox.style.display = 'block';
      } else {
        suggestionBox.style.display = 'none';
      }
    });
});

searchForm.addEventListener('submit', e => {
  e.preventDefault();
  fetchSearchResults(searchInput.value.trim());
});

function fetchSearchResults(query) {
  fetch(`search.php?query=${encodeURIComponent(query)}`)
    .then(res => res.text())
    .then(html => {
      document.querySelector('.products').innerHTML = html;
    });
}

function fetchAllProducts() {
  fetch(`search.php`)
    .then(res => res.text())
    .then(html => {
      document.querySelector('.products').innerHTML = html;
    });
}
</script>
