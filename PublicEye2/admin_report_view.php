<?php
session_start();
include("includes/connect.php");
include("includes/header2.php");

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Fetch all reports with user details
$stmt = $conn->prepare("
    SELECT r.id, r.user_id, r.report_text, r.report_image, r.report_video, r.created_at, 
           u.first_name, u.last_name, u.profile_picture
    FROM reporting r 
    LEFT JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC
");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Report View</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: #f2f2f2;
      margin: 0;
      padding: 30px;
    }
    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }
    
    .report {
      background: #fafafa;
      padding: 20px;
      margin-bottom: 25px;
      border-radius: 12px;
      border: 1px solid #ddd;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .report-header {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      position: relative;
    }

    .profile-pic {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
      border: 2px solid #ccc;
    }

    .anonymous-pic {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background-color: #000;
      margin-right: 15px;
      border: 2px solid #ccc;
    }

    .user-info {
      display: flex;
      flex-direction: column;
    }

    .user-name {
      font-size: 1.3rem;
      font-weight: 600;
      color: #333;
    }

    .report-date {
      margin-left:auto;
      font-size: 0.9rem;
      color: #888;
    }

    .report-text {
      font-size: 1.1rem;
      color: #333;
      margin-bottom: 15px;
      white-space: pre-wrap;
    }

    .report-media {
      margin-bottom: 15px;
    }

    .report-media img,
    .report-media video {
      max-width: 100%;
      max-height: 300px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .no-reports {
      text-align: center;
      font-size: 1.2rem;
      color: #666;
      padding: 40px 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Report Submissions</h1>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($report = $result->fetch_assoc()): 
        // Use the report text directly (no decoding needed)
        $report_text = $report['report_text'];
        
        // Check if report is anonymous (user_id is NULL)
        $is_anonymous = is_null($report['user_id']);
        $user_name = $is_anonymous ? 'Anonymous' : trim($report['first_name'] . ' ' . $report['last_name']);
        $profile_picture = $is_anonymous ? null : $report['profile_picture'];
      ?>
        <div class="report">
          <div class="report-header">
            <?php if ($is_anonymous): ?>
              <div class="anonymous-pic"></div>
            <?php elseif (!empty($profile_picture)): ?>
              <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" class="profile-pic" alt="Profile">
            <?php else: ?>
              <div class="profile-pic" style="background-color: #ccc;"></div>
            <?php endif; ?>

            <div class="user-info">
              <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
            </div>

            <div class="report-date">
              <?php
                $date = new DateTime($report['created_at'], new DateTimeZone('Asia/Kathmandu'));
                $now = new DateTime('now', new DateTimeZone('Asia/Kathmandu'));
                $diff = $now->diff($date);

                if ($diff->y > 0) {
                    echo $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
                } elseif ($diff->m > 0) {
                    echo $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
                } elseif ($diff->d > 0) {
                    echo $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
                } elseif ($diff->h > 0) {
                    echo $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                } elseif ($diff->i > 0) {
                    echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                } else {
                    echo 'Just now';
                }
              ?>
            </div>
          </div>

          <div class="report-text"><?php echo nl2br(htmlspecialchars($report_text)); ?></div>

          <div class="report-media">
            <?php if (!empty($report['report_image'])): ?>
              <img src="uploads/<?php echo htmlspecialchars($report['report_image']); ?>" alt="Report Image">
            <?php endif; ?>
            <?php if (!empty($report['report_video'])): ?>
              <video controls>
                <source src="uploads/<?php echo htmlspecialchars($report['report_video']); ?>" type="video/mp4">
                Your browser does not support the video tag.
              </video>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="no-reports">
        No reports have been submitted yet.
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
