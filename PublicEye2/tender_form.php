<?php
include("includes/header3.php");
session_start();

if (!isset($_SESSION['tender_user_id'])) {
    header("Location: tender_login.php");
    exit;
}

$tender_user_id = $_SESSION['tender_user_id'];

// Fetch project name and picture
$stmt = $conn->prepare("SELECT project_name, picture FROM tender_users WHERE id = ?");
$stmt->bind_param("i", $tender_user_id);
$stmt->execute();
$stmt->bind_result($project_name, $picture);
$stmt->fetch();
$stmt->close();

// Get custom fields
$fields = [];
$stmt = $conn->prepare("SELECT id, field_label FROM tender_custom_fields WHERE tender_user_id = ?");
$stmt->bind_param("i", $tender_user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $fields[$row['id']] = $row['field_label'];
}
$stmt->close();

// Get saved field values
$saved_values = [];
$stmt2 = $conn->prepare("SELECT field_label, field_value FROM tender_form_data WHERE tender_user_id = ?");
$stmt2->bind_param("i", $tender_user_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($row = $res2->fetch_assoc()) {
    $saved_values[$row['field_label']] = $row['field_value'];
}
$stmt2->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tender Form</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: #f5f5f5;
    }
    .page-content {
      display: flex;
      justify-content: center;
      padding: 40px 20px;
      min-height: 100vh;
    }
    .form-container {
      width: 100%;
      max-width: 700px;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      animation: fadeSlideUp 1s ease forwards;
      opacity: 0;
      transform: translateY(30px);
    }
    @keyframes fadeSlideUp {
      to { opacity: 1; transform: translateY(0); }
    }
    label {
      display: block;
      margin-top: 20px;
      font-weight: 600;
    }
    input, textarea {
      width: 100%;
      padding: 12px 16px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-top: 6px;
      box-sizing: border-box;
    }
    input:focus, textarea:focus {
      border-color: #00b0f0;
      outline: none;
    }
    fieldset {
      border: none;
      margin-bottom: 30px;
    }
    legend {
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: 15px;
    }
    .error {
      color: red;
      font-size: 0.9em;
    }
    .budget-container {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .budget-container span {
      font-weight: 600;
    }
    button {
      background-color: black;
      color: white;
      border: none;
      padding: 14px 25px;
      font-size: 1.1rem;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 20px;
      width: 100%;
    }
    button:hover {
      background-color: #333;
    }
  </style>
</head>
<body>

<div class="page-content">
  <div class="form-container">
    <form method="post" action="tender_form_process.php" enctype="multipart/form-data" id="tenderForm">
      <fieldset>
        <legend>Project Info</legend>

        <label>Project Name (readonly)</label>
        <input type="text" name="project_name" value="<?php echo htmlspecialchars($project_name); ?>" readonly />

        <?php
        $standard_labels = ['Contractor Name', 'Contact Number', 'Contact Email', 'Profile Picture', 'Budget', 'Project Location', 'Start Date', 'Expected End Date'];

        foreach ($standard_labels as $label) {
            $value = $saved_values[$label] ?? '';
            $input_type = (stripos($label, 'date') !== false) ? 'date' : 'text';
            $field_name = strtolower(str_replace(' ', '_', $label));

            // Custom rendering for Profile Picture
            if ($label === 'Profile Picture') {
                echo '<label for="' . $field_name . '">' . htmlspecialchars($label) . '</label>';
                if (!empty($picture)) {
                    echo '<div style="margin-bottom: 10px;">';
                    echo '<img src="uploads/' . htmlspecialchars($picture) . '" alt="Current Picture" style="max-width: 150px; max-height: 150px; border-radius: 8px;">';
                    echo '</div>';
                }
                echo '<input type="file" id="' . $field_name . '" name="picture" accept="image/*" />';
                continue;
            }

            // Custom rendering for Budget
            if ($label === 'Budget') {
                echo '<label for="' . $field_name . '">' . htmlspecialchars($label) . '</label>';
                echo '<div class="budget-container">';
                echo '<span>Rs</span>';
                echo '<input type="text" id="' . $field_name . '" name="custom_fields[' . htmlspecialchars($label) . ']" value="' . htmlspecialchars($value) . '" required maxlength="15" />';
                echo '</div>';
                echo '<span id="' . $field_name . '_error" class="error"></span>';
                continue;
            }

            // Custom rendering for Project Location
            if ($label === 'Project Location') {
                echo '<label for="' . $field_name . '">' . htmlspecialchars($label) . '</label>';
                echo '<input type="text" id="' . $field_name . '" name="custom_fields[' . htmlspecialchars($label) . ']" value="' . htmlspecialchars($value) . '" required />';
                echo '<span id="' . $field_name . '_error" class="error"></span>';
                continue;
            }

            // Change input type to email for email
            if ($label === 'Contact Email') {
                $input_type = 'email';
            }

            echo '<label for="' . $field_name . '">' . htmlspecialchars($label) . '</label>';
            echo '<input type="' . $input_type . '" id="' . $field_name . '" name="custom_fields[' . htmlspecialchars($label) . ']" value="' . htmlspecialchars($value) . '" ';
            if ($label === 'Contact Number') echo 'maxlength="10" ';
            if (!in_array($input_type, ['date'])) echo 'required ';
            echo '/>';
            echo '<span id="' . $field_name . '_error" class="error"></span>';
        }
        ?>
      </fieldset>

      <fieldset>
        <legend>Additional Details</legend>
        <?php if (!empty($fields)) : ?>
          <?php foreach ($fields as $field_id => $label) :
              if (in_array($label, $standard_labels)) continue;
              $value = $saved_values[$label] ?? '';
          ?>
            <label for="custom_field_<?php echo $field_id; ?>"><?php echo htmlspecialchars($label); ?></label>
            <textarea id="custom_field_<?php echo $field_id; ?>" name="custom_fields[<?php echo htmlspecialchars($label); ?>]" rows="3" required><?php echo htmlspecialchars($value); ?></textarea>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No additional fields configured by the admin.</p>
        <?php endif; ?>
      </fieldset>

      <button type="submit">Submit</button>
    </form>
  </div>
</div>

<!-- ✅ JavaScript Validation -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const phoneInput = document.getElementById('contact_number');
  const emailInput = document.getElementById('contact_email');
  const budgetInput = document.getElementById('budget');

  phoneInput.addEventListener('input', () => {
    const phoneError = document.getElementById('contact_number_error');
    const phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phoneInput.value.trim())) {
      phoneError.textContent = 'Phone number must be exactly 10 digits.';
    } else {
      phoneError.textContent = '';
    }
  });

  emailInput.addEventListener('input', () => {
    const emailError = document.getElementById('contact_email_error');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailInput.value.trim())) {
      emailError.textContent = 'Enter a valid email address.';
    } else {
      emailError.textContent = '';
    }
  });

  budgetInput.addEventListener('input', () => {
    const budgetError = document.getElementById('budget_error');
    const value = budgetInput.value.trim();
    if (!/^\d+(\.\d{1,2})?$/.test(value)) {
      budgetError.textContent = 'Enter a valid amount (e.g., 100000.00)';
    } else {
      budgetError.textContent = '';
    }
  });

  document.getElementById('tenderForm').addEventListener('submit', function (e) {
    const phoneError = document.getElementById('contact_number_error').textContent;
    const emailError = document.getElementById('contact_email_error').textContent;
    const budgetError = document.getElementById('budget_error').textContent;

    if (phoneError || emailError || budgetError) {
      e.preventDefault();
      alert("Please fix the errors before submitting.");
    }
  });
});
</script>

</body>
</html>
