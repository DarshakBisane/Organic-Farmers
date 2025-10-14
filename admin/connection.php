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

// ==================== ADD PRODUCT ====================
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $search_tag = $_POST['search_tag'];

    $target_dir = "uploads/";
    $file_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $file_name;

    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "<p style='color:red;'>❌ File is not a valid image.</p>";
        exit;
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO products (name, price, category, search_tag, image) VALUES ('$name', '$price', '$category', '$search_tag', '$file_name')";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color:green;'>✅ Product added successfully!</p>";
        } else {
            echo "<p style='color:red;'>❌ Database Error: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to upload image.</p>";
    }
}

// ==================== USER SIGNUP (EMAIL OTP) ====================
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
        $_SESSION['signup_error'] = "Email already registered! Please sign in.";
        header("Location: ../user/login.php?show=login");
        exit();
    }

    // Generate OTP and store in session
    $_SESSION['otp'] = rand(100000, 999999);
    $_SESSION['temp_user_data'] = [
        'name' => $name,
        'mobile' => $mobile,
        'birth' => $birth,
        'email' => $email,
        'address' => $address,
        'password' => $hashed_password
    ];

    // Send OTP email via PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'darshak123321@gmail.com'; // Replace with your Gmail
        $mail->Password = 'wvlq ghzc sabe rugy';    // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('darshak123321@gmail.com', 'OrganicFarm');
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for OrganicFarm Signup';
        $mail->Body = "
            <h2>Hello $name!</h2>
            <p>Your OTP for OrganicFarm signup is: <strong>{$_SESSION['otp']}</strong></p>
            <p>Do not share this OTP with anyone.</p>
        ";

        $mail->send();
        header("Location: ../user/verify.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['signup_error'] = "Signup successful, but OTP could not be sent.";
        header("Location: ../user/login.php?show=signup");
        exit();
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
            $_SESSION['login_error'] = "Please verify your email before logging in.";
            header("Location: ../user/login.php?show=login");
            exit();
        }

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header("Location: ../user/index.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password!";
        }
    } else {
        $_SESSION['login_error'] = "Email not registered!";
    }

    header("Location: ../user/login.php?show=login");
    exit();
}
?>
