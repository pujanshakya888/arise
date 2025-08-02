<?php
include("includes/header3.php");
session_start();

if (!isset($_SESSION['tender_user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href = 'tender_login.php';</script>";
    exit;
}

$tender_user_id = $_SESSION['tender_user_id'];
$contractor_name = "Contractor";

// Fetch contractor name from tender_form_data with correct field_label
$sql = "SELECT field_value FROM tender_form_data 
        WHERE tender_user_id = ? AND field_label = 'Contractor Name' LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tender_user_id);
$stmt->execute();
$stmt->bind_result($contractor_name);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tender Dashboard</title>
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

    .post-button {
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

    .post-button:hover {
      background-color: #2980b9;
    }

    @media screen and (max-width: 480px) {
      .dashboard-container {
        padding: 20px;
      }

      h2 {
        font-size: 22px;
      }

      .post-button {
        padding: 10px 20px;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($contractor_name); ?> </h2>
    <p>You are now logged in to your project dashboard. Use this panel to update the public and authorities about your project progress, issues, or any other important notes.</p>

    <p>Want to post about your project?</p>
    <a href="tender_post.php" class="post-button">📢 Post Update</a>
  </div>
</body>
</html>
