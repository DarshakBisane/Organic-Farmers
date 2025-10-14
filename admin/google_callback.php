<?php
session_start();
include 'admin/connection.php';

// Get Google token and verify
$google_id_token = $_POST['credential'] ?? '';

if($google_id_token){
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $google_id_token;
    $response = file_get_contents($url);
    $user_data = json_decode($response, true);

    $email = $user_data['email'];
    $name = $user_data['name'] ?? '';
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows == 0){
        // New user → insert
        $stmt = $conn->prepare("INSERT INTO users (name, email, mobile, birth, address, password) VALUES (?, ?, '', '', '', '')");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
    }

    $_SESSION['user_email'] = $email;
    header("Location: index.php");
    exit();
}
?>
