<?php
include '../admin/connection.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$suggestions = [];

if ($query !== '') {
    $stmt = $conn->prepare("SELECT name FROM products WHERE name LIKE ? OR search_tag LIKE ? LIMIT 6");
    $like = "%$query%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $suggestions[] = $row['name'];
    }
}

header('Content-Type: application/json');
echo json_encode($suggestions);
?>
