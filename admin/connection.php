<?php
// ========== DATABASE CONNECTION ==========
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "organicfarming";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<p style='color:red;'>❌ Connection failed: " . $conn->connect_error . "</p>");
}

// ========== FORM SUBMISSION ==========
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $search_tag = $_POST['search_tag'];

    // Folder for image upload
    $target_dir = "uploads/";
    $file_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $file_name;

    // Create folder if not exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Validate image file
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "<p style='color:red;'>❌ File is not a valid image.</p>";
        exit;
    }

    // Upload file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert into DB
        $sql = "INSERT INTO products (name, price, category, search_tag, image)
                VALUES ('$name', '$price', '$category', '$search_tag', '$file_name')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color:green;'>✅ Product added successfully!</p>";
        } else {
            echo "<p style='color:red;'>❌ Database Error: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to upload image.</p>";
    }
} else {
  
}


$signup_error = $login_error = "";

if (isset($_POST['signup'])) {
    // SIGN UP
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $birth = $_POST['birth'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
        $signup_error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, mobile, birth, email, address, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $name, $mobile, $birth, $email, $address, $hashed_password);
        if($stmt->execute()){
            $_SESSION['user_email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $signup_error = "Error registering user!";
        }
    }
}

if (isset($_POST['login'])) {
    // LOGIN
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows == 1){
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if(password_verify($password, $hashed_password)){
            $_SESSION['user_email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Invalid password!";
        }
    } else {
        $login_error = "Email not registered!";
    }
}

if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $birth = $_POST['birth'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
        $signup_error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, mobile, birth, email, address, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $name, $mobile, $birth, $email, $address, $hashed_password);
        if($stmt->execute()){
            $_SESSION['user_email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $signup_error = "Error registering user!";
        }
    }
}

// LOGIN LOGIC
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows == 1){
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if(password_verify($password, $hashed_password)){
            $_SESSION['user_email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Invalid password!";
        }
    } else {
        $login_error = "Email not registered!";
    }
}


$signup_error = $login_error = "";

// SIGN UP LOGIC
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $birth = $_POST['birth'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows > 0){
        $signup_error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, mobile, birth, email, address, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $name, $mobile, $birth, $email, $address, $hashed_password);
        if($stmt->execute()){
            $_SESSION['user_email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $signup_error = "Error registering user!";
        }
    }
}

// LOGIN LOGIC
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows == 1){
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if(password_verify($password, $hashed_password)){
            $_SESSION['user_email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Invalid password!";
        }
    } else {
        $login_error = "Email not registered!";
    }
}
?>
