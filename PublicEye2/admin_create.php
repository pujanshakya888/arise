<?php include("includes/header2.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Tender Holder</title>
  <link rel="stylesheet" href="signup.css">
  <style>
    .form-box {
      max-width: 700px;
      margin: 30px auto;
      background: #fff;
      padding: 40px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      border-radius: 15px;
    }

    .form-box h2 {
      margin-bottom: 30px;
      font-size: 28px;
      font-weight: 600;
      text-align: center;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    input[type="text"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    .custom-fields {
      margin-top: 30px;
    }

    .custom-field-group {
      margin-bottom: 20px;
      border: 1px dashed #aaa;
      padding: 15px;
      border-radius: 10px;
      background: #fafafa;
    }

    .btn {
      display: block;
      width: 100%;
      padding: 14px;
      font-size: 18px;
      font-weight: 600;
      background-color: #333;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn:hover {
      background-color: #555;
    }

    .add-field-btn {
      margin-top: 10px;
      padding: 8px 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Create Tender Holder & Define Form Labels</h2>
    <form action="admin_process_create.php" method="POST">
      <!-- Step 1: Basic Credentials -->
      <div class="form-group">
        <label for="project_name">Project Name</label>
        <input type="text" name="project_name" id="project_name" required>
      </div>

      <div class="form-group">
        <label for="username">Tender Username</label>
        <input type="text" name="username" id="username" required>
      </div>

      <div class="form-group">
        <label for="password">Tender Password</label>
        <input type="password" name="password" id="password" required>
      </div>

      <hr style="margin: 30px 0;">
      <h3>Standard Form Field Labels</h3>

      <!-- Step 2: Only Label Definitions -->
      <div class="form-group">
        <label for="contractor_label">Contractor Name Label</label>
        <input type="text" name="contractor_label" id="contractor_label" value="Contractor Name"readonly>
      </div>

      <div class="form-group">
        <label for="location_label">Project Location Label</label>
        <input type="text" name="location_label" id="location_label" value="Project Location"readonly>
      </div>
      <div class="form-group">
        <label for="contact_phone_label">Contact Number Label</label>
        <input type="text" name="contact_phone_label" id="contact_phone_label" value="Contact Number" readonly>
      </div>
      <div class="form-group">
        <label for="contact_email_label">Contact Email Label</label>
        <input type="text" name="contact_email_label" id="contact_email_label" value="Contact Email" readonly>
      </div>

      <div class="form-group">
        <label for="budget_label">Estimated Budget Label</label>
        <input type="text" name="budget_label" id="budget_label" value="Estimated Budget"readonly>
      </div>

      <div class="form-group">
        <label for="start_date_label">Start Date Label</label>
        <input type="text" name="start_date_label" id="start_date_label" value="Start Date"readonly>
      </div>
      <div class="form-group">
  <label for="end_date_label">Planned End Date Label</label>
  <input type="text" name="end_date_label" id="end_date_label" value="Planned End Date"readonly>
</div>
<div class="form-group">
  <label for="profile">Picture</label>
  <input type="file" name="profile" id="profile">
</div>



      <hr style="margin: 30px 0;">
      <h3>Custom Field Labels</h3>
      <div id="custom-fields" class="custom-fields"></div>

      <button type="button" class="add-field-btn" onclick="addCustomField()">+ Add Custom Label</button>

      <br><br>
      <button type="submit" class="btn">Generate</button>
    </form>
  </div>

  <script>
    let fieldCount = 0;

    function addCustomField() {
      fieldCount++;
      const container = document.getElementById('custom-fields');
      const group = document.createElement('div');
      group.className = 'custom-field-group';
      group.innerHTML = `
        <label>Custom Field ${fieldCount} Label</label>
        <input type="text" name="custom_label_${fieldCount}" placeholder="Enter label name">
      `;
      container.appendChild(group);
    }
  </script>
</body>
</html>
