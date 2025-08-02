<?php
session_start();
include("includes/header2.php");

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    echo "<script>alert('Please log in first.'); window.location.href = 'login.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
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
      margin-top: 100px; /* Push content below header */
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

    .admin-button {
      display: inline-block;
      background-color: #3498db;
      color: white;
      padding: 12px 24px;
      margin: 15px 10px;
      border: none;
      border-radius: 30px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .admin-button:hover {
      background-color: #2980b9;
    }

    @media screen and (max-width: 480px) {
      .dashboard-container {
        padding: 20px;
      }

      h2 {
        font-size: 22px;
      }

      .admin-button {
        padding: 10px 20px;
        font-size: 14px;
        margin: 10px 5px;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h2>Welcome, Admin</h2>
    <p>You are now logged in to the admin dashboard. Use this panel to manage projects and reports.</p>

    <p>What would you like to do?</p>
    <a href="admin_project.php" class="admin-button">📋 View Projects</a>
    <a href="admin_report_view.php" class="admin-button">📝 View Reports</a>
  </div>
</body>
</html>
