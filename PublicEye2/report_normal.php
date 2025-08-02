<?php
session_start();
include("includes/header1.php");

// Check which type of user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$tender_user_id = isset($_SESSION['tender_user_id']) ? $_SESSION['tender_user_id'] : null;
$picture = '';
$username = '';

if ($user_id) {
    // This is a regular user, fetch data from users table
    $stmt = $conn->prepare("SELECT profile_picture as picture, username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $picture = $row['picture'];
        $username = $row['username'];
    }
    $stmt->close();
} elseif ($tender_user_id) {
    // This is a tender user, fetch data from tender_users table
    $stmt = $conn->prepare("SELECT picture, username FROM tender_users WHERE id = ?");
    $stmt->bind_param("i", $tender_user_id);
    $stmt->execute();
    $stmt->bind_result($picture, $username);
    $stmt->fetch();
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reporting</title>
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
    .header {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    .profile-pic {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 20px;
      border: 2px solid #ddd;
    }
    .project-name {
      font-size: 1.5rem;
      font-weight: 600;
      color: #222;
    }
    .post-date {
      font-size: 0.9rem;
      color: #888;
      margin-left: auto;
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
    input[type="text"],
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
    <div class="header">
      <?php if ($user_id || $tender_user_id): ?>
        <?php if (!empty($picture)): ?>
          <img src="uploads/<?php echo htmlspecialchars($picture); ?>" alt="Profile Picture" class="profile-pic" loading="lazy" />
        <?php else: ?>
          <div class="profile-pic" style="background-color:#ccc;"></div>
        <?php endif; ?>
        <div>
          <div style="font-size: 1.8rem; font-weight: 700; color: #222; margin-bottom: 8px;">Reporting as: <?php echo htmlspecialchars($username); ?></div>
        </div>
      <?php else: ?>
        <div style="font-size: 1.5rem; font-weight: 600; color: #555; margin-bottom: 20px;">Reporting Anonymously</div>
      <?php endif; ?>
    </div>




    <form action="report_normal_process.php" method="post" enctype="multipart/form-data">
      <?php if ($user_id): ?>
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>" />
      <?php elseif ($tender_user_id): ?>
        <input type="hidden" name="tender_user_id" value="<?php echo htmlspecialchars($tender_user_id); ?>" />
      <?php endif; ?>

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
