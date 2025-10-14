<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ========== DATABASE CONNECTION ==========
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "organicfarming";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<p style='color:red;'>❌ Connection failed: " . $conn->connect_error . "</p>");
}

// ========== ADD PRODUCT ==========
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

// ========== USER SIGNUP & LOGIN ==========
$signup_error = $login_error = "";

// PASSWORD LOGIN
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, is_verified FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $hashed_password, $is_verified);
        $stmt->fetch();
        if (!$is_verified) {
            $login_error = "Please verify your email before login.";
        } elseif (!empty($hashed_password) && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header("Location: ../user/index.php");
            exit();
        } else {
            $login_error = "Invalid password!";
        }
    } else {
        $login_error = "Email not registered!";
    }
}

// PASSWORD SIGNUP
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $birth = $_POST['birth'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
        $signup_error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, mobile, birth, email, address, password, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sissss", $name, $mobile, $birth, $email, $address, $hashed_password);
        if($stmt->execute()){
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            header("Location: ../user/index.php");
            exit();
        } else {
            $signup_error = "Error registering user!";
        }
    }
}
?>
