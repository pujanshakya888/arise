<?php
session_start();
include("includes/header1.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$report_type = isset($_GET['type']) ? $_GET['type'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Report Options</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>

    h1 {
      color: #333;
      margin-bottom: 40px;
      font-size: 2.5rem;
      font-weight: 700;
      text-align: center;
      width: 100%;
    }
    .button-container {
      display: flex;
      justify-content: center;
      gap: 60px;
      max-width: 600px;
      margin: 40px auto 0 auto;
      padding: 40px;
      background: #ffffff;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      transition: box-shadow 0.3s ease;
    }
    .button-container:hover {
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }
    a.button {
      display: inline-block;
      padding: 20px 50px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      font-weight: 700;
      font-size: 1.25rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 123, 255, 0.6);
      transition: background-color 0.4s ease, transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }
    a.button:hover {
      background-color: #0056b3;
      box-shadow: 0 6px 20px rgba(0, 86, 179, 0.8);
      transform: scale(1.1);
      animation: pulse 1.2s infinite;
    }
    @keyframes pulse {
      0% {
        box-shadow: 0 6px 20px rgba(0, 86, 179, 0.8);
      }
      50% {
        box-shadow: 0 8px 30px rgba(0, 86, 179, 1);
      }
      100% {
        box-shadow: 0 6px 20px rgba(0, 86, 179, 0.8);
      }
    }
  </style>
</head>
<body>
  <h1>Select Report Type</h1>
  <div class="button-container">
    <a href="report_normal.php?type=normal" class="button">Normal Report</a>
    <a href="report_anonymous.php?type=anonymous" class="button">Anonymous Report</a>
  </div>
</body>
</html>
