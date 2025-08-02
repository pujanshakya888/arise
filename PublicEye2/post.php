<?php

session_start();
include("includes/header1.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Use logged-in regular user id
$tender_user_id = null;

// Check if project id is passed via GET and store in session
if (isset($_GET['id'])) {
    $_SESSION['selected_project_id'] = intval($_GET['id']);
}

if (!isset($_SESSION['selected_project_id'])) {
    // No project selected, redirect to project list
    header("Location: project.php");
    exit;
}

$tender_user_id = $_SESSION['selected_project_id'];

// Database connection assumed in header1.php or elsewhere, else include here
// include("includes/connect.php");

// Fetch project info and contractor name from tender_users table
$stmt = $conn->prepare("SELECT project_name, picture FROM tender_users WHERE id = ?");
$stmt->bind_param("i", $tender_user_id);
$stmt->execute();
$stmt->bind_result($project_name, $picture);
$stmt->fetch();
$stmt->close();

// Fetch contractor name from tender_form_data table
$contractor_name = '';
$stmt2 = $conn->prepare("SELECT field_value FROM tender_form_data WHERE tender_user_id = ? AND field_label = 'Contractor Name' LIMIT 1");
$stmt2->bind_param("i", $tender_user_id);
$stmt2->execute();
$stmt2->bind_result($contractor_name);
$stmt2->fetch();
$stmt2->close();

// Fetch all posts
$postStmt = $conn->prepare("SELECT id, post_text, post_image, post_video, created_at FROM tender_project_posts WHERE tender_user_id = ? ORDER BY created_at DESC");
$postStmt->bind_param("i", $tender_user_id);
$postStmt->execute();
$postResult = $postStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Project Posts</title>
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
    }
    /* Removed post button container */

    .post {
      background: #fafafa;
      padding: 20px;
      margin-bottom: 25px;
      border-radius: 12px;
      border: 1px solid #ddd;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .post-header {
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

    .project-info {
      display: flex;
      flex-direction: column;
    }

    .project-name {
      font-size: 1.3rem;
      font-weight: 600;
      color: #333;
    }

    .contractor-name {
      font-size: 1rem;
      color: #666;
      font-weight: 600;
    }

    .post-text {
      font-size: 1.1rem;
      color: #333;
      margin-bottom: 15px;
      white-space: pre-wrap;
    }

    .post-media {
      margin-bottom: 15px;
    }

    .post-media img,
    .post-media video {
      max-width: 100%;
      max-height: 300px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .post-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid #ddd;
      padding-top: 10px;
      font-size: 0.95rem;
    }

    .counts {
      display: flex;
      gap: 20px;
      color: #666;
      font-weight: 500;
    }

    .actions {
      display: flex;
      gap: 15px;
    }

    .actions button {
      padding: 6px 14px;
      font-size: 0.9rem;
      font-weight: 600;
      background: #e8e8e8;
      color: #333;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .actions button:hover {
      background-color: #d0d0d0;
    }

    .comments-section {
      margin-top: 15px;
      border-top: 1px solid #ccc;
      padding-top: 10px;
      display: none;
    }

    .comment {
      margin-bottom: 10px;
      font-size: 0.9rem;
      color: #444;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    .comment-profile-pic {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid #ccc;
      flex-shrink: 0;
    }

    .comment-user-name {
      font-weight: 600;
      color: #333;
      margin-right: 6px;
    }

    .comment-text {
      color: #555;
      white-space: pre-wrap;
      flex: 1;
    }

    .comment-input {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }

    .comment-input input[type="text"] {
      flex-grow: 1;
      padding: 6px 10px;
      font-size: 0.9rem;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .comment-input button {
      padding: 6px 14px;
      font-size: 0.9rem;
      font-weight: 600;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .comment-input button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1><a href="project_details.php?id=<?php echo $tender_user_id; ?>" style="color: inherit; text-decoration: none;">Posts of Project: <?php echo htmlspecialchars($project_name); ?></a></h1>

    <?php while ($post = $postResult->fetch_assoc()): ?>
      <?php
        $post_id = $post['id'];

        $likeCount = 0;
        $commentCount = 0;

        $likeStmt = $conn->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ?");
        $likeStmt->bind_param("i", $post_id);
        $likeStmt->execute();
        $likeStmt->bind_result($likeCount);
        $likeStmt->fetch();
        $likeStmt->close();

        $commentStmt = $conn->prepare("SELECT COUNT(*) FROM post_comments WHERE post_id = ?");
        $commentStmt->bind_param("i", $post_id);
        $commentStmt->execute();
        $commentStmt->bind_result($commentCount);
        $commentStmt->fetch();
        $commentStmt->close();

// Check if current user liked this post
$userLiked = false;
$checkLikeStmt = $conn->prepare("SELECT 1 FROM post_likes WHERE post_id = ? AND user_id = ? LIMIT 1");
$checkLikeStmt->bind_param("ii", $post_id, $user_id);
$checkLikeStmt->execute();
$checkLikeStmt->store_result();
if ($checkLikeStmt->num_rows > 0) {
  $userLiked = true;
}
$checkLikeStmt->close();
      ?>

      <div class="post" data-post-id="<?php echo $post_id; ?>">
        <div class="post-header" style="position: relative;">
          <?php if (!empty($picture)): ?>
<a href="project_details.php?id=<?php echo $tender_user_id; ?>" style="display:inline-block;">
              <img src="uploads/<?php echo htmlspecialchars($picture); ?>" class="profile-pic" alt="Profile">
            </a>
          <?php else: ?>
            <a href="project_details.php" style="display:inline-block;">
              <div class="profile-pic" style="background-color: #ccc;"></div>
            </a>
          <?php endif; ?>

          <div class="project-info">
<div class="project-name"><a href="project_details.php?id=<?php echo $tender_user_id; ?>" style="color: #333; text-decoration: none;"><?php echo htmlspecialchars($project_name); ?></a></div>
<div class="contractor-name" style="font-weight: 600; color: #444;">Contractor Name: <a href="project_details.php?id=<?php echo $tender_user_id; ?>" style="color: #444; text-decoration: none;"><?php echo htmlspecialchars($contractor_name); ?></a></div>
          </div>

          <div class="post-date" style="margin-left:auto; font-size: 0.9rem; color: #888;">
            <?php
              $date = new DateTime($post['created_at'], new DateTimeZone('Asia/Kathmandu'));
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

        <div class="post-text"><?php echo nl2br(htmlspecialchars($post['post_text'])); ?></div>

        <div class="post-media">
          <?php if (!empty($post['post_image'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($post['post_image']); ?>" alt="Post Image">
          <?php endif; ?>
          <?php if (!empty($post['post_video'])): ?>
            <video controls>
              <source src="uploads/<?php echo htmlspecialchars($post['post_video']); ?>" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          <?php endif; ?>
        </div>

        <div class="post-footer">
          <div class="counts">
            <span class="like-count">👍 <?php echo $likeCount; ?> Likes</span>
            <span class="comment-count">💬 <?php echo $commentCount; ?> Comments</span>
          </div>
          <div class="actions">
            <button type="button" class="like-btn"><?php echo $userLiked ? 'Unlike' : 'Like'; ?></button>
            <button type="button" class="comment-btn">Comment</button>
          </div>
        </div>

        <div class="comments-section">
          <div class="comments-list"></div>
          <div class="comment-input">
            <input type="text" placeholder="Write a comment..." />
            <button type="button" class="submit-comment-btn">Comment</button>
          </div>
        </div>
      </div>
    <?php endwhile; ?>

  </div>

  <script>
    // Like button click handler
    document.querySelectorAll('.like-btn').forEach(button => {
      button.addEventListener('click', function() {
        const postDiv = this.closest('.post');
        const postId = postDiv.getAttribute('data-post-id');
        const likeCountSpan = postDiv.querySelector('.like-count');
        const isLiked = this.textContent === 'Unlike';
        const btn = this;

        fetch('post_like.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'post_id=' + encodeURIComponent(postId),
        })
        .then(response => response.text())
        .then(text => {
          console.log("Server response:", text);
          if (text.trim() === "liked") {
            btn.textContent = "Unlike";
            const count = parseInt(likeCountSpan.textContent) || 0;
            likeCountSpan.textContent = "👍 " + (count + 1) + " Likes";
          } else if (text.trim() === "unliked") {
            btn.textContent = "Like";
            const count = parseInt(likeCountSpan.textContent) || 1;
            likeCountSpan.textContent = "👍 " + (count - 1) + " Likes";
          } else {
            alert("Error updating like. Please try again.");
          }
        })
        .catch(() => {
          alert("Error connecting to the server.");
        });
      });
    });

    // Comment button click handler
    document.querySelectorAll('.comment-btn').forEach(button => {
      button.addEventListener('click', function() {
        const postDiv = this.closest('.post');
        const commentsSection = postDiv.querySelector('.comments-section');
        if (commentsSection.style.display === 'block') {
          commentsSection.style.display = 'none';
          return;
        }
        commentsSection.style.display = 'block';

        const commentsList = commentsSection.querySelector('.comments-list');
        commentsList.innerHTML = 'Loading comments...';

        const postId = postDiv.getAttribute('data-post-id');

        fetch('post_comment.php?post_id=' + postId)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              commentsList.innerHTML = '';
            data.comments.forEach(comment => {
                const div = document.createElement('div');
                div.className = 'comment';

                const img = document.createElement('img');
                img.className = 'comment-profile-pic';
                img.src = comment.profile_picture ? 'uploads/' + comment.profile_picture : 'default-profile.png';
                img.alt = comment.user_name + ' profile picture';

                const nameSpan = document.createElement('span');
                nameSpan.className = 'comment-user-name';
                nameSpan.textContent = comment.user_name;

                const textDiv = document.createElement('div');
                textDiv.className = 'comment-text';
                textDiv.textContent = comment.comment_text;

                div.appendChild(img);
                div.appendChild(nameSpan);
                div.appendChild(textDiv);

                commentsList.appendChild(div);
              });
            } else {
              commentsList.innerHTML = 'Failed to load comments.';
            }
          })
          .catch(() => {
            commentsList.innerHTML = 'Error loading comments.';
          });
      });
    });

    // Submit comment button click handler
    document.querySelectorAll('.submit-comment-btn').forEach(button => {
      button.addEventListener('click', function() {
        const commentInput = this.parentElement.querySelector('input[type="text"]');
        const commentText = commentInput.value.trim();
        if (!commentText) {
          alert('Please enter a comment.');
          return;
        }
        const postDiv = this.closest('.post');
        const postId = postDiv.getAttribute('data-post-id');
        const commentsList = postDiv.querySelector('.comments-list');
        const commentBtn = postDiv.querySelector('.comment-btn');

        fetch('post_comment.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            post_id: postId,
            comment_text: commentText
          })
        })
        .then(response => response.json())
        .then(data => {
          console.log("Comment submission response:", data);
          if (data.success) {
            const div = document.createElement('div');
            div.className = 'comment';
            div.textContent = data.user_name + ': ' + commentText;
            commentsList.appendChild(div);
            commentInput.value = '';
            // Update comment count
            const commentCountSpan = postDiv.querySelector('.comment-count');
            commentCountSpan.textContent = '💬 ' + data.comment_count + ' Comments';
          } else {
            alert('Failed to add comment. Please try again.');
          }
        })
        .catch(() => {
          alert('Error adding comment. Please try again.');
        });
      });
    });
  </script>
</body>
</html>
