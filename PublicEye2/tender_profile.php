<?php
include("includes/header3.php");
session_start();

if (!isset($_SESSION['tender_user_id'])) {
    header("Location: tender_login.php");
    exit;
}

$tender_user_id = $_SESSION['tender_user_id'];

// Fetch project info and picture from tender_users table
$stmt = $conn->prepare("SELECT project_name, username, picture FROM tender_users WHERE id = ?");
$stmt->bind_param("i", $tender_user_id);
$stmt->execute();
$stmt->bind_result($project_name, $username, $picture);
$stmt->fetch();
$stmt->close();

// Fetch all submitted form data fields and their values
$fields = [];
$stmt2 = $conn->prepare("SELECT field_label, field_value FROM tender_form_data WHERE tender_user_id = ?");
$stmt2->bind_param("i", $tender_user_id);
$stmt2->execute();
$result = $stmt2->get_result();
while ($row = $result->fetch_assoc()) {
    $fields[] = $row;
}
$stmt2->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Tender Profile</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 20px;
      color: #333;
    }
    .profile-container {
      max-width: 800px;
      margin: 40px auto;
      background: #fff;
      padding: 30px 40px 40px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
      color: #222;
    }
    .info-block {
      margin-bottom: 20px;
    }
    .info-label {
      font-weight: 600;
      margin-bottom: 8px;
      font-size: 1.1rem;
      color: #555;
    }
    .info-value {
      background: #f7f7f7;
      padding: 12px 18px;
      border-radius: 8px;
      white-space: pre-wrap;
      font-size: 1rem;
      box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.1);
    }
    .field {
      margin-bottom: 25px;
    }

    /* Picture styling */
    .picture-container {
      text-align: center;
      margin-bottom: 30px;
    }
    .picture-container img {
      width: 180px;
      height: 180px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
      cursor: pointer;
      transition: transform 0.3s ease;
      border: 3px solid #ddd;
    }
    .picture-container img:hover {
      transform: scale(1.05);
      border-color: #00b0f0;
      box-shadow: 0 8px 20px rgba(0,176,240,0.6);
    }

    /* Modal styles */
    #modalOverlay {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.8);
      z-index: 1000;
      justify-content: center;
      align-items: center;
      cursor: zoom-out;
    }
    #modalOverlay img {
      max-width: 90vw;
      max-height: 90vh;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
    }

    .logout-btn {
      display: block;
      margin: 40px auto 0;
      background-color: black;
      color: white;
      border: none;
      padding: 14px 30px;
      font-size: 1.1rem;
      border-radius: 10px;
      cursor: pointer;
      text-align: center;
      transition: background-color 0.3s ease;
      width: 200px;
    }
    .logout-btn:hover {
      background-color: #333;
    }
  </style>
</head>
<body>

  <div class="profile-container">
    <h2>Your Profile</h2>

    <?php if (!empty($picture)): ?>
      <div class="picture-container">
        <img src="uploads/<?php echo htmlspecialchars($picture); ?>" alt="Uploaded Picture" id="profilePicture" loading="lazy" />
        <p style="margin-top:8px; color:#666;">Click image to enlarge</p>
      </div>
    <?php endif; ?>

    <div class="info-block">
      <div class="info-label">Project Name</div>
      <div class="info-value"><?php echo htmlspecialchars($project_name); ?></div>
    </div>

    <div class="info-block">
      <div class="info-label">Username</div>
      <div class="info-value"><?php echo htmlspecialchars($username); ?></div>
    </div>

    <?php if (!empty($fields)) : ?>
      <h3 style="margin-top:40px; font-weight:700; border-bottom: 2px solid black; padding-bottom: 8px;">Submitted Information</h3><br>
      <?php foreach ($fields as $field) : ?>
        <div class="field">
          <div class="info-label"><?php echo htmlspecialchars($field['field_label']); ?></div>
          <div class="info-value"><?php echo nl2br(htmlspecialchars($field['field_value'])); ?></div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No submitted details available.</p>
    <?php endif; ?>

    <form method="post" action="logout.php">
      <button class="logout-btn" type="submit">Logout</button>
    </form>
  </div>

  <!-- Modal for enlarged image -->
  <div id="modalOverlay" onclick="this.style.display='none'">
    <img src="" alt="Full Size Picture" id="modalImage" />
  </div>

  <script>
    const profilePic = document.getElementById('profilePicture');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalImage = document.getElementById('modalImage');

    if(profilePic){
      profilePic.addEventListener('click', () => {
        modalImage.src = profilePic.src;
        modalOverlay.style.display = 'flex';
      });
    }
  </script>

</body>
</html>
