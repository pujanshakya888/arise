<?php
session_start();

include("includes/header1.php");

// Fetch main project details
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid project ID.";
    exit;
}

$project_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM tender_users WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Project not found.";
    exit;
}

$project = $result->fetch_assoc();

// Fetch filled custom fields from tender_form_data
$cf_stmt = $conn->prepare("SELECT field_label, field_value FROM tender_form_data WHERE tender_user_id = ?");
$cf_stmt->bind_param("i", $project_id);
$cf_stmt->execute();
$custom_fields = $cf_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Project Details - Admin View</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f4f4;
      color: #222;
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    h2 {
      margin-bottom: 20px;
      border-bottom: 2px solid black;
      padding-bottom: 5px;
      color:black;
    }

    .section {
      margin-bottom: 30px;
    }

    .label {
      font-weight: 600;
      color: #444;
      margin-top: 10px;
    }

    .value {
      margin-left: 10px;
      color: #555;
    }

    .field {
      margin-bottom: 10px;
    }

    .custom-section {
      border-top: 1px solid #ddd;
      padding-top: 20px;
    }

    a.button {
      margin-top: 20px;
      display: inline-block;
      padding: 10px 20px;
      background: #007bff;
      color: white;
      border-radius: 5px;
      text-decoration: none;
    }

    a.button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Project: <?php echo htmlspecialchars($project['project_name']); ?></h2>

    <?php if (!empty($project['picture'])) : ?>
      <div class="picture-container" style="text-align:center; margin-bottom:30px;">
        <img src="uploads/<?php echo htmlspecialchars($project['picture']); ?>" alt="User Picture" id="profilePicture" style="width:180px; height:180px; object-fit:cover; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.2); cursor:pointer; border:3px solid #ddd; transition: transform 0.3s ease;">
        <p style="margin-top:8px; color:#666;">Click image to enlarge</p>
      </div>
    <?php endif; ?>

  <?php
  // Extract contractor name and location from custom fields if available
  $contractor_name = '';
  $location = '';
  $other_fields = [];

  if ($custom_fields->num_rows > 0) {
      // Reset pointer to start for multiple loops
      $custom_fields->data_seek(0);
      while ($row = $custom_fields->fetch_assoc()) {
          if (strcasecmp($row['field_label'], 'Contractor Name') === 0) {
              $contractor_name = $row['field_value'];
          } elseif (strcasecmp($row['field_label'], 'Location') === 0) {
              $location = $row['field_value'];
          } else {
              $other_fields[] = $row;
          }
      }
  }
  ?>

  <?php if (!empty($contractor_name)): ?>
    <div class="section custom-section">
      <h2>Contractor Info</h2>
      <div class="field">
        <span class="label">Contractor Name:</span>
        <span class="value"><?php echo nl2br(htmlspecialchars($contractor_name)); ?></span>
      </div>
      <?php
        // Fetch contact number and email from custom fields
        $contact_number = '';
        $contact_email = '';
        foreach ($other_fields as $key => $field) {
            if (strcasecmp($field['field_label'], 'Contact Number') === 0) {
                $contact_number = $field['field_value'];
                unset($other_fields[$key]);
            } elseif (strcasecmp($field['field_label'], 'Contact Email') === 0) {
                $contact_email = $field['field_value'];
                unset($other_fields[$key]);
            }
        }
      ?>
      <?php if (!empty($contact_number)): ?>
        <div class="field">
          <span class="label">Contact Number:</span>
          <span class="value"><?php echo nl2br(htmlspecialchars($contact_number)); ?></span>
        </div>
      <?php endif; ?>
      <?php if (!empty($contact_email)): ?>
        <div class="field">
          <span class="label">Contact Email:</span>
          <span class="value"><?php echo nl2br(htmlspecialchars($contact_email)); ?></span>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="section custom-section">
    <h2>Key Project Details</h2>
    <div class="field">
      <span class="label">Project Name:</span>
      <span class="value"><?php echo htmlspecialchars($project['project_name']); ?></span>
    </div>
    <div class="field">
      <span class="label">Status:</span>
      <span class="value"><?php echo htmlspecialchars($project['status']); ?></span>
    </div>
    <div class="field">
      <span class="label">Created At:</span>
      <span class="value"><?php echo htmlspecialchars($project['created_at']); ?></span>
    </div>
    <?php if (!empty($location)): ?>
      <div class="field">
        <span class="label">Location:</span>
        <span class="value"><?php echo nl2br(htmlspecialchars($location)); ?></span>
      </div>
    <?php endif; ?>
    <?php foreach ($other_fields as $row): ?>
      <div class="field">
        <span class="label"><?php echo htmlspecialchars($row['field_label']); ?>:</span>
        <span class="value"><?php echo nl2br(htmlspecialchars($row['field_value'])); ?></span>
      </div>
    <?php endforeach; ?>
  </div>

    <a href="project.php" class="button">Back to Projects</a>
  </div>

  <!-- Modal for image zoom -->
  <div id="modalOverlay" onclick="this.style.display='none'" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.85); z-index:1000; justify-content:center; align-items:center; cursor:zoom-out;">
    <img src="" alt="Enlarged Photo" id="modalImage" style="max-width:90vw; max-height:90vh; border-radius:12px; box-shadow:0 10px 30px rgba(255,255,255,0.3);">
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

<?php
$stmt->close();
$cf_stmt->close();
$conn->close();
?>
