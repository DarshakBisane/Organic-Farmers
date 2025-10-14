<?php
    include ("header.php");

session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profile | Organic Farmers</title>
<style>
body{font-family:"Poppins",sans-serif;background:#fafff3;margin:0;padding:0;}
.container{max-width:600px;margin:50px auto;padding:20px;background:white;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
h1{color:#3e8e41;}
p{margin:10px 0;}
</style>
</head>
<body>
<div class="container">
    <h1>Profile</h1>
    <p><strong>Name:</strong> <?php echo $_SESSION['user_name']; ?></p>
    <p><strong>Email:</strong> <?php echo $_SESSION['user_email']; ?></p>
    <a href="logout.php">Logout</a>
</div>
</body>
</html>
