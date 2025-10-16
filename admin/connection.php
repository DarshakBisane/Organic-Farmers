<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ==================== DATABASE ====================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "organicfarming";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<p style='color:red;'>❌ Connection failed: " . $conn->connect_error . "</p>");
}

// ==================== PHPMailer ====================
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ==================== INITIALIZE VARIABLES ====================
$show_form = $_GET['show'] ?? 'login';
$toast_message = '';
$toast_type = '';

// ==================== USER SIGNUP ====================
if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);
    $birth = trim($_POST['birth']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $toast_message = "Email already registered! Please sign in.";
        $toast_type = "error";
        $show_form = 'login';
    } else {
        // Generate OTP and store temporary user data
        $_SESSION['otp'] = rand(100000, 999999);
        $_SESSION['temp_user_data'] = [
            'name' => $name,
            'mobile' => $mobile,
            'birth' => $birth,
            'email' => $email,
            'address' => $address,
            'password' => $hashed_password
        ];

        // Send OTP via PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'darshak123321@gmail.com';
            $mail->Password = 'wvlq ghzc sabe rugy';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('darshak123321@gmail.com', 'OrganicFarm');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for OrganicFarm Signup';
            $mail->Body = "<h2>Hello $name!</h2><p>Your OTP for signup is: <strong>{$_SESSION['otp']}</strong></p>";
            $mail->send();

            $toast_message = "OTP sent to your email!";
            $toast_type = "success";
            $show_form = 'verify';
        } catch (Exception $e) {
            $toast_message = "Signup successful, but OTP could not be sent.";
            $toast_type = "error";
            $show_form = 'signup';
        }
    }
}

// ==================== OTP VERIFICATION ====================
if (isset($_POST['verify'])) {
    $entered_otp = trim($_POST['otp']);

    if (!isset($_SESSION['otp']) || !isset($_SESSION['temp_user_data'])) {
        $toast_message = "Session expired. Please sign up again.";
        $toast_type = "error";
        $show_form = 'signup';
    } elseif ($entered_otp == $_SESSION['otp']) {
        $data = $_SESSION['temp_user_data'];
        $stmt = $conn->prepare("INSERT INTO users (name, mobile, birth, email, address, password, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("ssssss", $data['name'], $data['mobile'], $data['birth'], $data['email'], $data['address'], $data['password']);

        if ($stmt->execute()) {
            unset($_SESSION['otp'], $_SESSION['temp_user_data']);
            $toast_message = "Account verified successfully! You can now log in.";
            $toast_type = "success";
            $show_form = 'login';
        } else {
            $toast_message = "Database error. Try again.";
            $toast_type = "error";
            $show_form = 'verify';
        }
    } else {
        $toast_message = "Invalid OTP! Please try again.";
        $toast_type = "error";
        $show_form = 'verify';
    }
}

// ==================== USER LOGIN ====================
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, name, password, is_verified FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashed_password, $is_verified);
        $stmt->fetch();

        if (!$is_verified) {
            $toast_message = "Please verify your email before logging in.";
            $toast_type = "error";
            $show_form = 'login';
        } elseif (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $toast_message = "Invalid password!";
            $toast_type = "error";
            $show_form = 'login';
        }
    } else {
        $toast_message = "Email not registered!";
        $toast_type = "error";
        $show_form = 'login';
    }
}
?>
