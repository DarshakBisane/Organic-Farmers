// Mobile Menu Toggle
document.addEventListener("DOMContentLoaded", () => {
  const menuToggle = document.getElementById("menuToggle");
  const navLinks = document.getElementById("navLinks");

  menuToggle.addEventListener("click", () => {
    navLinks.classList.toggle("active");
  });

  // Search (Front-end Simulation for Demo)
  document.getElementById("searchForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const query = e.target.search.value.trim();
    if (query) {
      alert("Searching for: " + query + " (Connect this with backend database)");
    }
  });
});
