<?php
session_start();
include("includes/header1.php");


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Anonymous Reporting</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 700px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }
    form {
      display: flex;
      flex-direction: column;
    }
    label {
      margin-top: 15px;
      font-weight: 600;
      color: #555;
    }
    textarea {
      width: 100%;
      padding: 12px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-top: 6px;
      resize: vertical;
      font-family: inherit;
      box-sizing: border-box;
    }
    input[type="file"] {
      margin-top: 6px;
    }
    button[type="submit"] {
      margin-top: 25px;
      padding: 14px;
      font-size: 1.1rem;
      font-weight: 600;
      background-color: #333;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button[type="submit"]:hover {
      background-color: #555;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Anonymous Report</h2>
    <form action="report_anonymous_process.php" method="post" enctype="multipart/form-data">
      <label for="report_text">Report Text</label>
      <textarea id="report_text" name="report_text" rows="4" placeholder="Write your report here..." required></textarea>

      <label for="report_image">Report Image</label>
      <input type="file" id="report_image" name="report_image" accept="image/*" />

      <label for="report_video">Report Video</label>
      <input type="file" id="report_video" name="report_video" accept="video/*" />

      <button type="submit">Submit Report</button>
    </form>
  </div>
</body>
</html>
