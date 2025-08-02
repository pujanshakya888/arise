<?php
include("includes/header.php"); // DB connection

function clean($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = clean($_POST['first_name']);
    $last_name  = clean($_POST['last_name']);
    $email      = clean($_POST['email']);
    $phone      = clean($_POST['phone']);
    $address    = clean($_POST['address']);
    $username   = clean($_POST['username']);
    $password   = $_POST['password'];

    // Check if all required fields are filled
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($address) || empty($username) || empty($password)) {
        echo "<script>alert('All fields are required except profile picture. Please fill all fields.'); window.history.back();</script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email or username already exists
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
    $stmt_check->bind_param("ss", $email, $username);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Email or username already taken
        echo "<script>alert('Email or username already exists. Please choose another.'); window.history.back();</script>";
        $stmt_check->close();
        $conn->close();
        exit;
    }
    $stmt_check->close();

    // Profile picture upload handling (same as your code)
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $tmpFile = $_FILES['profile_picture']['tmp_name'];
        $originalName = basename($_FILES['profile_picture']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newFileName = uniqid("profile_", true) . '.' . $extension;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($tmpFile, $uploadPath)) {
            $profile_picture = $newFileName; // Store just filename to save in DB
        } else {
            echo "Error uploading profile picture.";
        }
    } else if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "File upload error code: " . $_FILES['profile_picture']['error'];
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, address, profile_picture, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $phone, $address, $profile_picture, $username, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful! Redirecting to login...'); window.location.href = 'login.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
