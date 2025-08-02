<?php
 session_start();
include("includes/header3.php"); 

// Sanitize helper
function clean($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = clean($_POST['username']);
    $password = $_POST['password']; // raw password for verification

    // Prepare statement to get user data
    $stmt = $conn->prepare("SELECT id, password, form_filled FROM tender_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password, $form_filled);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Start session and set session variables
           
            $_SESSION['tender_user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['user_type'] = 'tender_user';

            if ($form_filled == 0) {
                header("Location: tender_form.php");
                exit();
            } else {
                header("Location: tender_dashboard.php");
                exit();
            }
        } else {
            echo "<script>alert('Incorrect password.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Username not found.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
