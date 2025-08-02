<?php
session_start();
include("includes/header1.php");

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href = 'login.php';</script>";
    exit;
}

if (!isset($_SESSION['user_type'])) {
    $_SESSION['user_type'] = 'user';
}

$user_id = $_SESSION['user_id'];
$full_name = "User";

// Fetch full name
$sql = "SELECT first_name, last_name FROM users WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name);
if ($stmt->fetch()) {
    $full_name = $first_name . ' ' . $last_name;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard</title>
  <link rel="stylesheet" href="signup_login.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 0;
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .dashboard-container {
      background: white;
      width: 95%;
      max-width: 650px;
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      padding: 40px 25px;
      margin-top: 100px;
      text-align: center;
      animation: slideIn 0.8s ease;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      color: #2c3e50;
    }

    p {
      color: #555;
      font-size: 16px;
      margin-top: 10px;
    }

    .report-button {
      display: inline-block;
      background-color: #3498db;
      color: white;
      padding: 12px 24px;
      margin-top: 25px;
      border: none;
      border-radius: 30px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .report-button:hover {
      background-color: #2980b9;
    }

    @media screen and (max-width: 480px) {
      .dashboard-container {
        padding: 20px;
      }

      h2 {
        font-size: 22px;
      }

      .report-button {
        padding: 10px 20px;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($full_name); ?> </h2>
    <p>You are logged in as a public user. Here you can explore projects, provide feedback, or report misconduct related to public tenders.</p>

    <p>Want to explore the ongoing public projects?</p>
    <a href="project.php" class="report-button">📂 View Projects</a>
  </div>
</body>
</html>
