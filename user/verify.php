<?php
include '../admin/connection.php';

if (!isset($_GET['token'])) {
    die("Invalid verification link.");
}

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE verification_token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($id, $is_verified);
    $stmt->fetch();

    if ($is_verified) {
        echo "Your account is already verified. <a href='login.php'>Login here</a>.";
        exit();
    }

    $stmt_update = $conn->prepare("UPDATE users SET is_verified=1, verification_token='' WHERE id=?");
    $stmt_update->bind_param("i", $id);
    $stmt_update->execute();

    echo "Your account has been verified! <a href='login.php'>Login now</a>.";
} else {
    echo "Invalid verification link.";
}
?>
