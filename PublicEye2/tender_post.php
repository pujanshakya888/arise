<?php
session_start();
include("includes/header3.php");

if (!isset($_SESSION['tender_user_id'])) {
    header("Location: tender_login.php");
    exit;
}

$tender_user_id = $_SESSION['tender_user_id'];

// Handle delete post request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post']) && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);

    // Clear output buffer to avoid extra whitespace or output
    if (ob_get_length()) {
        ob_end_clean();
    }

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
}

// Fetch project info and contractor name from tender_form_data
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
    .post-button-container {
      text-align: right;
      margin-bottom: 20px;
    }
    .post-button-container a {
      padding: 10px 20px;
      background-color: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }
    .post-button-container a:hover {
      background-color: #0056b3;
    }

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
  </style>
</head>
<body>
  <div class="container">
    <h1>Your Project Posts</h1>
    <div class="post-button-container">
      <a href="tender_project_post.php">+ Post</a>
    </div>

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
      ?>

      <div class="post" data-post-id="<?php echo $post_id; ?>">
    <div class="post-header" style="position: relative;">
      <?php if (!empty($picture)): ?>
        <a href="tender_profile.php" style="display:inline-block;">
          <img src="uploads/<?php echo htmlspecialchars($picture); ?>" class="profile-pic" alt="Profile">
        </a>
      <?php else: ?>
        <a href="tender_profile.php" style="display:inline-block;">
          <div class="profile-pic" style="background-color: #ccc;"></div>
        </a>
      <?php endif; ?>

      <div class="project-info">
        <div class="project-name"><a href="tender_profile.php" style="color: #333; text-decoration: none;"><?php echo htmlspecialchars($project_name); ?></a></div>
        <div class="contractor-name" style="font-weight: 600; color: #444;">Contractor Name: <a href="tender_profile.php" style="color: #444; text-decoration: none;"><?php echo htmlspecialchars($contractor_name); ?></a></div>
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
      <div class="post-menu" style="position: absolute; top: 2px; right: 10px; cursor: pointer; user-select: none;">
        &#x22EE;
        <div class="post-menu-dropdown" style="display: none; position: absolute; right: 0; background: white; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 100;">
          <button class="delete-post-btn" style="background: none; border: none; padding: 10px 20px; width: 150px; text-align: left; cursor: pointer; font-size: 14px; color: #d9534f;">
            Delete Post
          </button>
        </div>
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
              <button type="button" class="like-btn">Like</button>
              <button type="button" class="comment-btn">Comment</button>
            </div>
          </div>

          <div class="comments-section" style="display:none; margin-top: 15px; border-top: 1px solid #ccc; padding-top: 10px;">
            <div class="comments-list"></div>
          </div>
        </div>
      <?php endwhile; ?>

  </div>

  <script>
    document.querySelectorAll('.post-menu').forEach(menu => {
      menu.addEventListener('click', function(event) {
        event.stopPropagation();
        const dropdown = this.querySelector('.post-menu-dropdown');
        const isVisible = dropdown.style.display === 'block';
        // Close all other dropdowns
        document.querySelectorAll('.post-menu-dropdown').forEach(dd => dd.style.display = 'none');
        dropdown.style.display = isVisible ? 'none' : 'block';
      });
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', () => {
      document.querySelectorAll('.post-menu-dropdown').forEach(dd => dd.style.display = 'none');
    });

      // Handle delete post button click
    document.querySelectorAll('.delete-post-btn').forEach(button => {
      button.addEventListener('click', function(event) {
        event.stopPropagation();
        if (!confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
          return;
        }
        const postDiv = this.closest('.post');
        const postId = postDiv.getAttribute('data-post-id');

        fetch('tender_post.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            delete_post: '1',
            post_id: postId
          })
        })
        .then(response => response.text())
        .then(data => {
          if (data.trim() === 'success') {
            postDiv.remove();
          } else {
            alert('Failed to delete post. Please try again.');
          }
        })
        .catch(() => {
          alert('Error deleting post. Please try again.');
        });
      });
    });
  </script>

  <script>
    document.querySelectorAll('.like-btn').forEach(button => {
      button.addEventListener('click', function() {
        const postDiv = this.closest('.post');
        const postId = postDiv.getAttribute('data-post-id');
        const likeCountSpan = postDiv.querySelector('.counts span:first-child');
        const btn = this;

        fetch('post_like.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({ post_id: postId })
        })
        .then(response => response.text())
        .then(text => {
          if (text.trim() === 'liked') {
            btn.textContent = 'Unlike';
            let count = parseInt(likeCountSpan.textContent) || 0;
            count++;
            likeCountSpan.textContent = `👍 ${count} Likes`;
          } else if (text.trim() === 'unliked') {
            btn.textContent = 'Like';
            let count = parseInt(likeCountSpan.textContent) || 1;
            count--;
            likeCountSpan.textContent = `👍 ${count} Likes`;
          } else {
            alert('Error updating like. Please try again.');
          }
        })
        .catch(() => alert('Error connecting to server.'));
      });
    });

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
  </script>
</body>
</html>
