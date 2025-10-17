<?php
session_start();
include '../admin/connection.php';
include 'header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$toast_message = '';
$toast_type = '';

// Handle profile updates
if(isset($_POST['update_profile'])){
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $password = $_POST['password'] ?? '';
    $profile_img = $_FILES['profile_img']['name'] ?? '';

    // Handle image upload
    if(isset($_FILES['profile_img']) && $_FILES['profile_img']['name'] != ''){
        $img_name = $_FILES['profile_img']['name'];
        $tmp_name = $_FILES['profile_img']['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg','jpeg','png','gif'];
        if(in_array($img_ext, $allowed_ext)){
            $new_img_name = 'user_'.$user_id.'.'.$img_ext;
            $upload_path = '../admin/uploads/'.$new_img_name;
            if(move_uploaded_file($tmp_name, $upload_path)){
                $profile_img = $new_img_name;
            }
        }
    }

    // Hash password if entered
    if(!empty($password)){
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, address=?, password=?, profile_img=? WHERE id=?");
        $stmt->bind_param("ssssi",$name,$address,$password_hashed,$profile_img,$user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, address=?, profile_img=? WHERE id=?");
        $stmt->bind_param("sssi",$name,$address,$profile_img,$user_id);
    }

    if($stmt->execute()){
        $toast_message = "Profile updated successfully!";
        $toast_type = "success";
    } else {
        $toast_message = "Database error. Try again.";
        $toast_type = "error";
    }
}

// Fetch latest user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile | Organic Farmers</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
body{font-family:"Poppins",sans-serif;background:#fafff3;margin:0;padding:0;}
.container{max-width:700px;margin:50px auto;padding:30px;background:rgba(255,255,255,0.3);backdrop-filter:blur(10px);border-radius:15px;box-shadow:0 8px 20px rgba(0,0,0,0.1);position:relative;}
h1{color:#2e7d32;text-align:center;margin-bottom:30px;}
.profile-img-top{text-align:center;margin-bottom:30px;}
.profile-img-top img{width:150px;height:150px;object-fit:cover;border-radius:50%;border:3px solid #2e7d32;}
.user-info{display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:30px;}
.user-info div{padding:15px;background:rgba(240,248,240,0.7);border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.05);display:flex;justify-content:space-between;align-items:center;}
.user-info div strong{color:#2e7d32;}
button{padding:10px 20px;border:none;border-radius:25px;cursor:pointer;font-weight:600;transition:0.3s;margin:5px;}
button:hover{transform:scale(1.05);}
.back-btn{background: rgba(255,255,255,0.9); color: #2e7d32; border: none; border-radius: 50%; width: 42px; height: 42px; font-size: 20px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease; z-index: 999;}
.back-btn:hover{background:#2e7d32;color:white;transform:scale(1.1);}
#editForm{display:none;margin-top:20px;}
input, select{width:100%;padding:10px;margin:5px 0;border-radius:8px;border:1px solid #ccc;}
.password-wrapper{position:relative;}
.password-wrapper i{position:absolute;right:10px;top:10px;cursor:pointer;color:#333;}
.toast{position:fixed;top:25px;right:-400px;min-width:250px;padding:12px 18px;border-radius:8px;color:white;font-size:15px;box-shadow:0 4px 12px rgba(0,0,0,0.3);transition:all 0.6s ease;z-index:1000;}
.toast.show{right:25px;}
.toast.success{background:#2e7d32;}
.toast.error{background:#c62828;}
.btn-container{text-align:center;margin-top:20px;}
</style>
</head>
<body>

<div class="toast <?php echo $toast_type; ?> <?php echo $toast_message ? 'show' : ''; ?>">
    <?php echo htmlspecialchars($toast_message); ?>
</div>

<div class="container">
    <button class="back-btn" onclick="window.location.href='index.php'">&#8592;</button>
<h1>My Profile</h1>

<div class="profile-img-top">
    <img src="../admin/uploads/<?php echo $user['profile_img'] ?? 'default.png'; ?>" alt="Profile Image">
</div>

<div class="user-info" id="userInfo">
    <div><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></div>
    <div><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></div>
    <div><strong>Mobile:</strong> <?php echo htmlspecialchars($user['mobile']); ?></div>
    <div><strong>Birth Date:</strong> <?php echo htmlspecialchars($user['birth']); ?></div>
    <div style="grid-column:1/-1;"><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></div>
    <div style="grid-column:1/-1;"><strong>Password:</strong> ******</div>
</div>

<div class="btn-container">
    <button id="editBtn" style="background: rgba(6, 104, 10, 0.7); color:white;">Update Profile</button>
</div>

<div id="editForm">
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" placeholder="Full Name" required>
        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
        <input type="text" value="<?php echo htmlspecialchars($user['mobile']); ?>" readonly>
        <input type="date" name="birth" value="<?php echo htmlspecialchars($user['birth']); ?>" required>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" placeholder="Address" required>
        <div class="password-wrapper">
            <input type="password" name="password" placeholder="New Password (leave blank to keep)" id="passwordField">
            <i class="fa-regular fa-eye" id="togglePassword"></i>
        </div>
        <input type="file" name="profile_img" accept="image/*">
        <div class="btn-container">
            <button type="submit" name="update_profile" style="background: rgba(46,125,50,0.7); color:white;">Save & Exit</button>
            <button type="button" id="cancelBtn" style="background:#c62828;color:white;">Cancel</button>
        </div>
    </form>
</div>

</div>

<script>
const editBtn = document.getElementById('editBtn');
const cancelBtn = document.getElementById('cancelBtn');
const editForm = document.getElementById('editForm');
const userInfo = document.getElementById('userInfo');

editBtn.addEventListener('click', ()=>{
    editForm.style.display = "block";
    userInfo.style.display = "none";
    editBtn.style.display = "none";
});
cancelBtn.addEventListener('click', ()=>{
    editForm.style.display = "none";
    userInfo.style.display = "grid";
    editBtn.style.display = "inline-block";
});

// Toggle password visibility
const togglePasswordIcon = document.getElementById('togglePassword');
const passwordField = document.getElementById('passwordField');

togglePasswordIcon.addEventListener('click', () => {
    if(passwordField.type === "password"){
        passwordField.type = "text";
        togglePasswordIcon.classList.remove('fa-eye');
        togglePasswordIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = "password";
        togglePasswordIcon.classList.remove('fa-eye-slash');
        togglePasswordIcon.classList.add('fa-eye');
    }
});

// Toast auto-hide
const toast=document.querySelector('.toast');
if(toast && toast.classList.contains('show')){
  setTimeout(()=>{ toast.classList.remove('show'); },2600);
}
</script>

</body>
</html>
