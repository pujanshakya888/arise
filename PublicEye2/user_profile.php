<?php
session_start();
include("includes/header1.php");


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT first_name, last_name, phone, email, profile_picture, username, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $phone, $email, $profile_picture, $username, $address);
$stmt->fetch();
$stmt->close();

$full_name = trim($first_name . ' ' . $last_name);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Profile</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f2f2f2;
      margin: 0;
      padding: 20px;
    }

    .profile-container {
      max-width: 800px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 14px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #222;
      margin-bottom: 30px;
    }

    .picture-container {
      text-align: center;
      margin-bottom: 25px;
    }

    .picture-container img {
      width: 180px;
      height: 180px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
      cursor: pointer;
      transition: transform 0.3s;
      border: 3px solid #ddd;
    }

    .picture-container img:hover {
      transform: scale(1.05);
      border-color: #00b0f0;
      box-shadow: 0 8px 20px rgba(0,176,240,0.6);
    }

    .info-label {
      font-weight: bold;
      margin-top: 15px;
      font-size: 1.1rem;
      color: #555;
    }

    .info-value {
      background: #f7f7f7;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
      font-size: 1rem;
    }

    .logout-btn {
      display: block;
      margin: 30px auto 0;
      background-color: black;
      color: white;
      padding: 12px 24px;
      font-size: 1rem;
      border: none;
      border-radius: 10px;
      cursor: pointer;
    }

    .logout-btn:hover {
      background-color: #333;
    }

    /* Modal */
    #modalOverlay {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.8);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }

    #modalOverlay img {
      max-width: 90%;
      max-height: 90%;
      border-radius: 12px;
    }
  </style>
</head>
<body>

  <div class="profile-container">
    <h2>User Profile</h2>

    <?php if (!empty($profile_picture)): ?>
      <div class="picture-container">
        <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" id="profilePicture" loading="lazy">
        <p style="color: #666; font-size: 14px;">Click to enlarge</p>
      </div>
    <?php endif; ?>

    <div class="info-label">Full Name</div>
    <div class="info-value"><?php echo htmlspecialchars($full_name); ?></div>

    <div class="info-label">Phone Number</div>
    <div class="info-value"><?php echo htmlspecialchars($phone); ?></div>

    <div class="info-label">Email</div>
    <div class="info-value"><?php echo htmlspecialchars($email); ?></div>

    
    <div class="info-label">Address</div>
    <div class="info-value"><?php echo htmlspecialchars($address); ?></div>

    <div class="info-label">Username</div>
    <div class="info-value"><?php echo htmlspecialchars($username); ?></div>

    <form action="logout.php" method="post">
      <button class="logout-btn" type="submit">Logout</button>
    </form>
  </div>

  <div id="modalOverlay" onclick="this.style.display='none'">
    <img src="" id="modalImage" alt="Full Size Profile Picture">
  </div>

  <script>
    const profilePic = document.getElementById('profilePicture');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalImage = document.getElementById('modalImage');

    if (profilePic) {
      profilePic.addEventListener('click', () => {
        modalImage.src = profilePic.src;
        modalOverlay.style.display = 'flex';
      });
    }
  </script>

</body>
</html>
