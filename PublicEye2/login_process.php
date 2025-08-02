<?php
session_start(); 
include("includes/connect.php"); 

// Helper: sanitize input
function clean($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = clean($_POST['username']);
    $password = $_POST['password']; 

    // Admin login shortcut
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['admin'] = true;
        if (!isset($_SESSION['username'])) {
            $_SESSION['username'] = 'admin';
        }
        header("Location: admin_dashboard.php");
        exit();
    }

    // Check user credentials
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id; 
            $_SESSION['username'] = $username;
            $_SESSION['user_type'] = 'user';
            header("Location: index1.php"); 
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
