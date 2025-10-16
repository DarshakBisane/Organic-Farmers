<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include ("../admin/connection.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organic Farmers | Login / Signup</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}
body{
    font-family:"Poppins",sans-serif;
    background:#f6f8f4;
    display:flex;
    align-items:center;
    justify-content:center;
    min-height:100vh;
}
.container {
    width: 400px;
    min-height: 520px;
    background: rgba(255, 255, 255, 0.16);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 40px 30px; /* Add more padding top & bottom */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: 0.3s ease;
    margin-top: 50px;
    margin-bottom: 50px;
}

form.active {
    display: flex;
    flex-direction: column;
    align-items: center;
    animation: fadeIn 0.3s ease;
    margin-top: 10px; /* add space above form content */
    margin-bottom: 10px; /* add space below form content */
}

h2 {
    text-align: center;
    margin-bottom: 20px; /* increase space below heading */
    color: #2e7d32;
}

input{
    width:100%;
    padding:12px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ccc;
    transition:0.3s;
}
input:focus{
    border-color:#66bb6a;
    box-shadow:0 0 6px rgba(102,187,106,0.3);
}
button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:25px;
    background:#2e7d32;
    color:white;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}
button:hover{
    transform:scale(1.04);
    background:#1b5e20;
}
form{
    display:none;
    width:100%;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(15px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* ===== Floating Toggle ===== */
.toggle-floating {
    position: fixed;
    top: 15px;
    right: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,0.9);
    padding: 6px 12px;
    border-radius: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 999;
    cursor: pointer;
}
.toggle-floating span{
    font-weight:500;
    color:#2e7d32;
}
.toggle-btn{
    width:50px;
    height:25px;
    background:#ccc;
    border-radius:25px;
    position:relative;
    transition:0.4s;
}
.toggle-btn::before{
    content:"";
    position:absolute;
    width:21px;
    height:21px;
    border-radius:50%;
    background:white;
    top:2px;
    left:2px;
    transition:0.4s;
}
.toggle-btn.active{
    background:#2e7d32;
}
.toggle-btn.active::before{
    left:27px;
}

/* Toast */
.toast{
    position:fixed;
    top:25px;
    right:-400px;
    min-width:250px;
    padding:12px 18px;
    border-radius:8px;
    color:white;
    font-size:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.3);
    transition:all 0.6s ease;
    z-index:1000;
}
.toast.show{
    right:25px;
}
.toast.success{
    background:#2e7d32;
}
.toast.error{
    background:#c62828;
}

/* Avatar */
.avatar{
    width:80px;
    height:80px;
    border-radius:50%;
    margin-bottom:20px;
    object-fit:cover;
    border:2px solid #2e7d32;
}
</style>
</head>
<body>

<div class="toast <?php echo $toast_type; ?> <?php echo $toast_message ? 'show' : ''; ?>">
    <?php echo htmlspecialchars($toast_message); ?>
</div>

<!-- Floating toggle -->
<div class="toggle-floating" id="floatingToggle">
    <span id="toggleTextFloating"><?php echo ($show_form=='signup') ? 'Sign Up' : 'Sign In'; ?></span>
    <div class="toggle-btn <?php echo ($show_form=='signup')?'active':''; ?>" id="toggleBtnFloating"></div>
</div>

<div class="container">
    <!-- Avatar Image -->
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Person" class="avatar">

    <form id="loginForm" method="POST" class="<?php echo ($show_form=='login')?'active':''; ?>">
        <h2>Welcome Back 🌱</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Sign In</button>
    </form>

    <form id="signupForm" method="POST" class="<?php echo ($show_form=='signup')?'active':''; ?>">
        <h2>Create Account 🍃</h2>
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="mobile" placeholder="Mobile" required>
        <input type="date" name="birth" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="address" placeholder="Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="signup">Sign Up</button>
    </form>

    <form id="verifyForm" method="POST" class="<?php echo ($show_form=='verify')?'active':''; ?>">
        <h2>Verify Your Email</h2>
        <p style="text-align:center;color:#2e7d32;">An OTP has been sent to your email.</p>
        <input type="text" name="otp" placeholder="Enter 6-digit OTP" required>
        <button type="submit" name="verify">Verify OTP</button>
    </form>
</div>

<script>
const loginForm=document.getElementById('loginForm');
const signupForm=document.getElementById('signupForm');
const verifyForm=document.getElementById('verifyForm');
const toggleBtnFloating=document.getElementById('toggleBtnFloating');
const toggleTextFloating=document.getElementById('toggleTextFloating');
const floatingToggle=document.getElementById('floatingToggle');

floatingToggle.addEventListener('click',()=>{
    toggleBtnFloating.classList.toggle('active');
    if(toggleBtnFloating.classList.contains('active')){
        signupForm.classList.add('active');
        loginForm.classList.remove('active');
        verifyForm.classList.remove('active');
        toggleTextFloating.textContent='Sign Up';
    }else{
        loginForm.classList.add('active');
        signupForm.classList.remove('active');
        verifyForm.classList.remove('active');
        toggleTextFloating.textContent='Sign In';
    }
});

// Toast auto-hide
const toast=document.querySelector('.toast');
if(toast && toast.classList.contains('show')){
  setTimeout(()=>{ toast.classList.remove('show'); },4000);
}
</script>
</body>
</html>
