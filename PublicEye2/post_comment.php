<?php
session_start();
include("includes/connect.php"); // Ensure $conn is available

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// POST Request: Insert comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['post_id'], $_POST['comment_text'])) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit;
    }

    $post_id = intval($_POST['post_id']);
    $comment_text = trim($_POST['comment_text']);

    if (empty($comment_text)) {
        echo json_encode(['success' => false, 'message' => 'Empty comment']);
        exit;
    }

    // Get tender_user_id for the post
    $tender_user_id = null;
    $tenderStmt = $conn->prepare("SELECT tender_user_id FROM tender_project_posts WHERE id = ?");
    $tenderStmt->bind_param("i", $post_id);
    $tenderStmt->execute();
    $tenderStmt->bind_result($tender_user_id);
    $tenderStmt->fetch();
    $tenderStmt->close();

    if ($tender_user_id === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid post_id']);
        exit;
    }

    // Insert the comment
    $stmt = $conn->prepare("INSERT INTO post_comments (post_id, user_id, tender_user_id, comment_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $post_id, $user_id, $tender_user_id, $comment_text);
    
    if ($stmt->execute()) {
        // Get updated comment count
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM post_comments WHERE post_id = ?");
        $countStmt->bind_param("i", $post_id);
        $countStmt->execute();
        $countStmt->bind_result($comment_count);
        $countStmt->fetch();
        $countStmt->close();

        // Get user name from users table (assuming contractor_name column exists)
        $nameStmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE id = ? LIMIT 1");
        $nameStmt->bind_param("i", $user_id);
        $nameStmt->execute();
        $nameStmt->bind_result($full_name);
        $nameStmt->fetch();
        $nameStmt->close();

        echo json_encode([
            'success' => true,
            'comment_count' => $comment_count,
            'user_name' => $full_name
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert comment']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// GET Request: Fetch comments
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['post_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing post_id']);
        exit;
    }

    $post_id = intval($_GET['post_id']);

    $stmt = $conn->prepare("
        SELECT c.comment_text, CONCAT(u.first_name, ' ', u.last_name) AS full_name, u.profile_picture
        FROM post_comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = [
            'comment_text' => $row['comment_text'],
            'user_name' => $row['full_name'],
            'profile_picture' => $row['profile_picture']
        ];
    }

    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

// If neither POST nor GET
echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
?>
