<?php
session_start();
include("includes/header3.php");

if (!isset($_SESSION['tender_user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href = 'tender_login.php';</script>";
    exit;
}

$tender_user_id = $_SESSION['tender_user_id'];

// Sanitize helper
function clean($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $post_text = clean($_POST['post_text'] ?? '');

    // Handle file uploads
    $post_image = null;
    $post_video = null;

    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle image upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['post_image']['tmp_name'];
        $originalName = basename($_FILES['post_image']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedImage = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowedImage)) {
            $newFilename = uniqid('post_img_') . '.' . $ext;
            $dest = $uploadDir . $newFilename;

            if (move_uploaded_file($tmpName, $dest)) {
                $post_image = $newFilename;
            } else {
                echo "<script>alert('Error uploading image.'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Invalid image file type. Only JPG, PNG, GIF allowed.'); window.history.back();</script>";
            exit;
        }
    }

    // Handle video upload
    if (isset($_FILES['post_video']) && $_FILES['post_video']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['post_video']['tmp_name'];
        $originalName = basename($_FILES['post_video']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedVideo = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];

        if (in_array($ext, $allowedVideo)) {
            $newFilename = uniqid('post_vid_') . '.' . $ext;
            $dest = $uploadDir . $newFilename;

            if (move_uploaded_file($tmpName, $dest)) {
                $post_video = $newFilename;
            } else {
                echo "<script>alert('Error uploading video.'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Invalid video file type. Allowed types: MP4, AVI, MOV, WMV, FLV, MKV.'); window.history.back();</script>";
            exit;
        }
    }

    // Insert post into database
    $stmt = $conn->prepare("INSERT INTO tender_project_posts (tender_user_id, post_text, post_image, post_video) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $tender_user_id, $post_text, $post_image, $post_video);

    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>alert('Post submitted successfully.'); window.location.href = 'tender_post.php';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to submit post. Please try again.'); window.history.back();</script>";
        exit;
    }
} else if (isset($_POST['delete_post']) && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);

    // Verify post belongs to user
    $verifyStmt = $conn->prepare("SELECT tender_user_id FROM tender_project_posts WHERE id = ?");
    $verifyStmt->bind_param("i", $post_id);
    $verifyStmt->execute();
    $verifyStmt->bind_result($owner_id);
    if ($verifyStmt->fetch()) {
        if ($owner_id == $tender_user_id) {
            $verifyStmt->close();

            // Delete post
            $deleteStmt = $conn->prepare("DELETE FROM tender_project_posts WHERE id = ?");
            $deleteStmt->bind_param("i", $post_id);
            if ($deleteStmt->execute()) {
                $deleteStmt->close();
                echo "success";
                exit;
            } else {
                $error = $conn->error;
                $deleteStmt->close();
                echo "Failed to delete post. Error: " . $error;
                exit;
            }
        } else {
            $verifyStmt->close();
            echo "Unauthorized.";
            exit;
        }
    } else {
        $verifyStmt->close();
        echo "Post not found.";
        exit;
    }
} else {
    echo "Invalid request.";
}
