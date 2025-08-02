<?php
include("includes/header2.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $project_name = $_POST['project_name'];
  $username = $_POST['username'];
  $plain_password = $_POST['password'];
  $password = password_hash($plain_password, PASSWORD_DEFAULT);

  // Check if username exists
  $checkStmt = $conn->prepare("SELECT id FROM tender_users WHERE username = ?");
  $checkStmt->bind_param("s", $username);
  $checkStmt->execute();
  $checkStmt->store_result();

  if ($checkStmt->num_rows > 0) {
    echo "<script>alert('Username already exists. Please choose a different one.'); window.history.back();</script>";
    exit;
  }

  // Insert new tender user WITHOUT contact_phone_label and contact_email_label
  $stmt = $conn->prepare("INSERT INTO tender_users (project_name, username, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $project_name, $username, $password);

  if ($stmt->execute()) {
    $tender_user_id = $stmt->insert_id;

    // Insert only custom fields as labels with empty values (to be filled later by tender user)
    foreach ($_POST as $key => $value) {
      if (strpos($key, 'custom_label_') === 0 && !empty(trim($value))) {
        $stmt2 = $conn->prepare("INSERT INTO tender_custom_fields (tender_user_id, field_label, field_value) VALUES (?, ?, '')");
        $stmt2->bind_param("is", $tender_user_id, $value);
        $stmt2->execute();
      }
    }

    // Show credentials card
    ?>
    <style>
      .credential-card {
        max-width: 400px;
        margin: 50px auto;
        padding: 30px;
        border: 2px solid #333;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
        background: #fff;
        text-align: center;
      }
      .credential-card h2 {
        margin-bottom: 20px;
        color: #333;
      }
      .credential-card p {
        font-size: 18px;
        margin: 12px 0;
        color: #555;
      }
      .credential-card .label {
        font-weight: 600;
        color: #000;
      }
      .credential-card .btn-back {
        margin-top: 25px;
        padding: 12px 25px;
        background-color: #333;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
      }
      .credential-card .btn-back:hover {
        background-color: #555;
      }
    </style>

    <div class="credential-card">
      <h2>Tender Holder Credentials</h2>
      <p><span class="label">Project Name:</span> <?= htmlspecialchars($project_name) ?></p>
      <p><span class="label">Username:</span> <?= htmlspecialchars($username) ?></p>
      <p><span class="label">Password:</span> <?= htmlspecialchars($plain_password) ?></p>
      <a href="admin_dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
    <?php
    exit;
  } else {
    echo "<script>alert('Failed to create tender holder. Please try again.'); window.history.back();</script>";
  }
}
?>
