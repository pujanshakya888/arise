<?php include("includes/header.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Form</title>
  <link rel="stylesheet" href="signup_login.css">
</head>
<body>
  <div class="form-container">
    <form action="tender_login_process.php" method="POST">
      <fieldset class="form-section">
        <legend>Tender Login</legend>
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
      </fieldset>
      <button type="submit" id="loginBtn">Login</button>
      
     
      <div style="margin-top: 15px; text-align: center;">
        <a href="login.php" style="color: #007bff; text-decoration: none;">Login as Normal Account</a>
        <span style="margin: 0 8px;">/</span>
  <a href="signup.php" style="color: #007bff; text-decoration: none;">Sign up</a>
      </div>
    </form>
  </div>
</body>
</html>
