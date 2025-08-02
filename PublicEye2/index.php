<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Public Eye | Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }

    .left {
      text-align: center;
    }

    .p_hover {
      opacity: 0;
      max-height: none;
      overflow: visible;
      transform: translateX(-50px);
      transition: none;
      animation: slideInLeft 1s ease-out forwards;
      animation-delay: 0.6s;
    }



    h1, h2 {
      cursor: pointer;
      animation: slideInLeft 1s ease-out forwards;
      animation-delay: 0.3s;
      opacity: 0;
    }

    @keyframes slideInLeft {
      from {
        opacity: 0;
        transform: translateX(-50px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

      .button-container {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    gap: 20px;

    /* New: prepare for animation */
    opacity: 0;
    transform: translateY(20px);
    animation: buttonsFadeSlideIn 0.6s forwards ease-out;
    animation-delay: 1.2s; /* starts after headings */
  }

  .button-container a {
    padding: 10px 20px;
    text-decoration: none;
    color: white;
    background-color: black;
    border-radius: 6px;
    font-weight: 600;
    transition: background 0.3s ease;
  }

  .button-container a:hover {
    background-color: white;
    color: black;
    border: 2px solid black;
  }

  @keyframes buttonsFadeSlideIn {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  </style>
</head>
<body>
  <?php include("includes/header.php"); ?>

  <main>
    <div class="left" id="hoverContainer">
      <h1 id="hoverTrigger1">Welcome to Public Eye</h1><br>
      <h2 id="hoverTrigger2">Bringing Public Projects into the Public Eye</h3><br>
      <h3 class="p_hover">This platform promotes transparency and public accountability in government projects.</h3><br>

      <!-- Buttons -->
      <div class="button-container">
        <a href="login_choice.php">Login</a>
        <a href="signup.php">Sign Up</a>
      </div>
    </div>
  </main>

  <img src="includes/logo.png" alt="" style="display: none;">

  <script>
  const container = document.getElementById('hoverContainer');
  const h1 = document.getElementById('hoverTrigger1');
  const h3 = document.getElementById('hoverTrigger2');

  </script>
</body>
</html>
