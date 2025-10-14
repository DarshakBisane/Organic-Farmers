<?php
session_start();
include 'connection.php';

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
        // New user → insert with verification token
        $verification_token = md5($email . rand());
        $stmt = $conn->prepare("INSERT INTO users (name, email, is_verified, verification_token) VALUES (?, ?, 0, ?)");
        $stmt->bind_param("sss", $name, $email, $verification_token);
        $stmt->execute();

        // Since mail() won't work on localhost, display the verification link
        $verify_link = "http://localhost/OrganicFarming/Organic-Farmers/user/verify.php?token=$verification_token";
        echo "Please verify your account by clicking this link: <a href='$verify_link'>$verify_link</a>";
        exit();
    }

    // If user already exists, log them in
    $_SESSION['user_email'] = $email;
    header("Location: ../user/index.php");
    exit();
}
?>
