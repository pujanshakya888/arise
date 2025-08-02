<?php
session_start();
include("includes/connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle both regular users and tender users
    $user_id = null;
    if (isset($_POST['user_id']) && $_POST['user_id'] !== '') {
        $user_id = intval($_POST['user_id']);
    } elseif (isset($_POST['tender_user_id']) && $_POST['tender_user_id'] !== '') {
        // For now, we'll set user_id to NULL for tender users since the reporting table
        // only has a foreign key to the users table, not tender_users table
        // This could be improved with a database schema change
        $user_id = null;
    }
    
    $report_text = isset($_POST['report_text']) ? trim($_POST['report_text']) : '';
    $report_image = null;
    $report_video = null;

    if (empty($report_text)) {
        die("Report text is required.");
    }

    // Handle image upload
    if (isset($_FILES['report_image']) && $_FILES['report_image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp = $_FILES['report_image']['tmp_name'];
        $image_name = basename($_FILES['report_image']['name']);
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_image_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_ext, $allowed_image_exts)) {
            $target_dir = "uploads/";
            $target_file = $target_dir . uniqid('img_') . '.' . $image_ext;
            if (move_uploaded_file($image_tmp, $target_file)) {
                $report_image = $target_file;
            } else {
                die("Failed to upload image.");
            }
        } else {
            die("Invalid image file type.");
        }
    }

    // Handle video upload
    if (isset($_FILES['report_video']) && $_FILES['report_video']['error'] === UPLOAD_ERR_OK) {
        $video_tmp = $_FILES['report_video']['tmp_name'];
        $video_name = basename($_FILES['report_video']['name']);
        $video_ext = strtolower(pathinfo($video_name, PATHINFO_EXTENSION));
        $allowed_video_exts = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];

        if (in_array($video_ext, $allowed_video_exts)) {
            $target_dir = "uploads/";
            $target_file = $target_dir . uniqid('vid_') . '.' . $video_ext;
            if (move_uploaded_file($video_tmp, $target_file)) {
                $report_video = $target_file;
            } else {
                die("Failed to upload video.");
            }
        } else {
            die("Invalid video file type.");
        }
    }

    // Insert into reporting table
    $stmt = $conn->prepare("INSERT INTO reporting (user_id, report_text, report_image, report_video) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $report_text, $report_image, $report_video);

    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>alert('Reported successfully.'); window.location.href = 'index1.php';</script>";
        exit();
    } else {
        die("Failed to save report: " . $conn->error);
    }
} else {
    die("Invalid request method.");
}
?>
