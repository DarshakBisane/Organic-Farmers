<?php
include '../admin/connection.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
if ($query !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR search_tag LIKE ? ORDER BY id DESC");
    $like = "%$query%";
    $stmt->bind_param("ss", $like, $like);
} else {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT 30");
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
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
} else {
    echo "<p style='text-align:center;color:gray;'>No products found.</p>";
}
?>
