<?php
session_start();
include("includes/header3.php");

if (!isset($_SESSION['tender_user_id'])) {
    header("Location: tender_login.php");
    exit;
}

$tender_user_id = $_SESSION['tender_user_id'];

// Fetch project name, profile picture, and contractor name from tender_users table
$stmt = $conn->prepare("SELECT project_name, picture, username FROM tender_users WHERE id = ?");
$stmt->bind_param("i", $tender_user_id);
$stmt->execute();
$stmt->bind_result($project_name, $picture, $contractor_name);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Project Post</title>
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
      <?php if (!empty($picture)): ?>
        <img src="uploads/<?php echo htmlspecialchars($picture); ?>" alt="Profile Picture" class="profile-pic" loading="lazy" />
      <?php else: ?>
        <div class="profile-pic" style="background-color:#ccc;"></div>
      <?php endif; ?>
    <div>
      <div style="font-size: 1.8rem; font-weight: 700; color: #222; margin-bottom: 8px;">Project Name: <?php echo htmlspecialchars($project_name); ?></div>
      <div style="font-size: 1.3rem; font-weight: 600; color: #555;">Contractor Name: <?php echo htmlspecialchars($contractor_name); ?></div>
    </div>
    </div>

    <form action="tender_project_post_process.php" method="post" enctype="multipart/form-data">
      <label for="post_text">Post Text</label>
      <textarea id="post_text" name="post_text" rows="4" placeholder="Write your post here..." required></textarea>

      <label for="post_image">Post Image</label>
      <input type="file" id="post_image" name="post_image" accept="image/*" />

      <label for="post_video">Post Video</label>
      <input type="file" id="post_video" name="post_video" accept="video/*" />

      <button type="submit">Submit Post</button>
    </form>
  </div>
</body>
</html>
