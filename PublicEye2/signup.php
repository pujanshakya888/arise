<?php include("includes/header.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup Form</title>
 <link rel="stylesheet" href="signup_login.css">
</head>
<body>
  <div class="form-container">
    <form action="signup_process.php" method="POST" enctype="multipart/form-data">
      <fieldset class="form-section">
        <legend>SignUp</legend>
        <div class="row">
          <input type="text" name="first_name" placeholder="First Name" required />
          <input type="text" name="last_name" placeholder="Last Name" required />
        </div>
        <input type="email" name="email" placeholder="Email" required />
        <input type="tel" id="phone" name="phone" placeholder="+977XXXXXXXXX" required maxlength="14" />
        <input type="text" name="address" placeholder="Address" required />
        <br>
        <!-- Profile Picture Upload (Optional) -->
      <label for="profilePic" style="margin-top: 20px; margin-left:10px;display: block; font-size: 1.1rem;">
        Upload Profile Picture (Optional)<br>
        <small style="font-weight: normal; color: #666;">You can upload a photo for your profile (JPG, PNG).</small>
      </label>

      <input type="file" name="profile_picture" id="profilePic" accept="image/*"
        style="margin-top: 10px; border: 2px dashed #ccc; padding: 20px; border-radius: 10px; width: 100%; background-color: #f9f9f9;" />

      <p style="font-size: 0.9rem; color: #555; margin-top: 5px;margin-left:10px;">
        Please avoid uploading sensitive documents. Make sure the photo is appropriate and you have the rights to use it.
      </p>
<br>
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />

      </fieldset>
      <button type="submit" id="submitBtn">SignUp</button>
      <br>
         <div style="margin-top: 15px; text-align: center;">
          <br>
           <a href="login.php" style="color: #007bff; text-decoration: none;">Login as Normal Account</a>
           <span style="margin: 0 8px;">/</span>
           <a href="tender_login.php" style="color: #007bff; text-decoration: none;">Login as Tender Account</a>
</div>
    </form>
    
  </div>
<script>
  const phoneInput = document.getElementById('phone');
  const prefix = "+977";

  // Always start with +977
  phoneInput.value = prefix;

  phoneInput.addEventListener("input", function () {
    // Ensure +977 stays at start
    if (!this.value.startsWith(prefix)) {
      this.value = prefix;
    }

    // Extract digits after +977 and allow only 10
    const digits = this.value.slice(prefix.length).replace(/\D/g, '').slice(0, 10);
    this.value = prefix + digits;
  });

  // Prevent deletion of +977 part
  phoneInput.addEventListener("keydown", function (e) {
    if (this.selectionStart <= prefix.length && (e.key === "Backspace" || e.key === "Delete")) {
      e.preventDefault();
    }
  });

  // Refill +977 if empty on focus
  phoneInput.addEventListener("focus", function () {
    if (this.value.trim() === "") {
      this.value = prefix;
    }
  });
</script>

</body>
</html>
