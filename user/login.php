<?php
session_start();
include '../admin/connection.php';

$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login / Sign Up | Organic Farmers</title>
<style>
body{font-family:"Poppins",sans-serif;background:#fafff3;margin:0;padding:0;display:flex;justify-content:center;align-items:center;height:100vh;}
.container{width:400px;background:white;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.2);padding:30px;position:relative;}
h2{color:#3e8e41;text-align:center;margin-bottom:20px;}
input{width:100%;padding:10px;margin:8px 0;border-radius:5px;border:1px solid #ccc;}
button{padding:10px 20px;background:#5cb85c;color:white;border:none;border-radius:5px;cursor:pointer;margin-top:10px;width:100%;}
button:hover{background:#4aa63a;}
.error{color:red;margin-bottom:10px;text-align:center;}
.toggle-btns{display:flex;justify-content:center;margin-bottom:20px;gap:10px;}
.toggle-btns button{width:auto;padding:8px 15px;cursor:pointer;}
form{display:none;transition: all 0.5s ease;}
form.active{display:block;}
</style>
</head>
<body>

<div class="container">

<div class="toggle-btns">
    <button id="showLogin">Sign In</button>
    <button id="showSignup">Sign Up</button>
</div>

<!-- LOGIN FORM -->
<form id="loginForm" method="POST" class="active">
    <h2>Login</h2>
    <?php if($login_error) echo "<p class='error'>$login_error</p>"; ?>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Sign In</button>
</form>

<!-- SIGN UP FORM -->
<form id="signupForm" method="POST">
    <h2>Sign Up</h2>
    <?php if(isset($_SESSION['signup_error'])) { echo "<p class='error'>{$_SESSION['signup_error']}</p>"; unset($_SESSION['signup_error']); } ?>
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="text" name="mobile" placeholder="Mobile Number" required>
    <input type="date" name="birth" placeholder="Date of Birth" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="address" placeholder="Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="signup">Sign Up</button>
</form>

</div>

<script>
// Toggle forms
const loginForm = document.getElementById('loginForm');
const signupForm = document.getElementById('signupForm');
const showLogin = document.getElementById('showLogin');
const showSignup = document.getElementById('showSignup');

showLogin.addEventListener('click', () => {
    loginForm.classList.add('active');
    signupForm.classList.remove('active');
});

showSignup.addEventListener('click', () => {
    signupForm.classList.add('active');
    loginForm.classList.remove('active');
});
</script>

</body>
</html>
