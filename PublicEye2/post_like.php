<?php
session_start();
include("includes/connect.php"); // Ensure this file has $conn connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo "Unauthorized";
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if post_id is provided
if (!isset($_POST['post_id'])) {
    http_response_code(400); // Bad Request
    echo "Missing post_id";
    exit;
}

$post_id = intval($_POST['post_id']);

// Get tender_user_id for the post
$tender_user_id = null;
$tenderStmt = $conn->prepare("SELECT tender_user_id FROM tender_project_posts WHERE id = ?");
$tenderStmt->bind_param("i", $post_id);
$tenderStmt->execute();
$tenderStmt->bind_result($tender_user_id);
$tenderStmt->fetch();
$tenderStmt->close();

if ($tender_user_id === null) {
    http_response_code(400);
    echo "Invalid post_id";
    exit;
}

// Check if the user has already liked the post
$checkStmt = $conn->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $post_id, $user_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // User has liked — remove the like
    $deleteStmt = $conn->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $post_id, $user_id);
    if ($deleteStmt->execute()) {
        echo "unliked";
    } else {
        http_response_code(500); // Internal Server Error
        echo "Error";
    }
    $deleteStmt->close();
} else {
    // User has not liked — insert the like
    $insertStmt = $conn->prepare("INSERT INTO post_likes (post_id, user_id, tender_user_id) VALUES (?, ?, ?)");
    $insertStmt->bind_param("iii", $post_id, $user_id, $tender_user_id);
    if ($insertStmt->execute()) {
        echo "liked";
    } else {
        http_response_code(500); // Internal Server Error
        echo "Error";
    }
    $insertStmt->close();
}

$checkStmt->close();
$conn->close();
?>
