<?php
session_start();
include '../admin/connection.php';

if(!isset($_SESSION['temp_user_data'])){
    header("Location: login.php");
    exit();
}

$error = "";

if(isset($_POST['verify'])){
    $entered_otp = trim($_POST['otp']);

    if($entered_otp == $_SESSION['otp']){
        $data = $_SESSION['temp_user_data'];

        $stmt = $conn->prepare("INSERT INTO users (name, mobile, birth, email, address, password, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sssssss", $data['name'], $data['mobile'], $data['birth'], $data['email'], $data['address'], $data['password']);
        $stmt->execute();

        // Set session for logged-in user
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_name'] = $data['name'];
        $_SESSION['user_email'] = $data['email'];

        unset($_SESSION['temp_user_data'], $_SESSION['otp']);
        header("Location: index.php");
        exit();
    } else {
        $error = "❌ Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Verify OTP | OrganicFarm</title>
<style>
body{font-family:'Poppins',sans-serif;background:#fafff3;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
.container{background:white;padding:30px;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.2);}
input,button{padding:10px;width:100%;margin:10px 0;border-radius:5px;}
button{background:#3e8e41;color:white;border:none;cursor:pointer;}
.error{color:red;text-align:center;}
</style>
</head>
<body>
<div class="container">
<h2>Enter OTP sent to your email</h2>
<?php if($error) echo "<p class='error'>$error</p>"; ?>
<form method="POST">
<input type="text" name="otp" placeholder="Enter 6-digit OTP" required>
<button type="submit" name="verify">Verify</button>
</form>
</div>
</body>
</html>
