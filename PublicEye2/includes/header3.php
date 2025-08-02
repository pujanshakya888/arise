<?php 

include_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Public Eye</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="includes/include.css">
  <style>
  @media (max-width: 850px) {
    nav .dropdown > a {
      display: flex;
      justify-content: center;
      align-items: center;
    }
  }
</style>
</head>
<body>

<header>
  <div class="logo">
    <a href="tender_dashboard.php">
      <img src="logo.png" alt="Public Eye Logo">
    </a>
  </div>
  <nav id="nav-links">
    <a href="tender_dashboard.php" class="nav-item">Home</a>
    <a href="tender_post.php" class="nav-item">Projects</a>

    <a href="#" class="nav-item">Announcements</a>
    <a href="#" class="nav-item">Contact</a>
    <a href="tender_profile.php" class="nav-item">Profile</a>
  </nav>

  <div class="hamburger" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
  </div>
</header>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const nav = document.getElementById('nav-links');
    const hamburger = document.querySelector('.hamburger');
    const dropdowns = document.querySelectorAll('.dropdown > a');

    function toggleMenu() {
      if (!nav.classList.contains('showing')) {
        nav.classList.add('showing');
        setTimeout(() => {
          nav.classList.add('visible');
        }, 10);
      } else {
        nav.classList.remove('visible');
        setTimeout(() => {
          nav.classList.remove('showing');
        }, 400);
      }
    }

    hamburger.addEventListener('click', toggleMenu);

    dropdowns.forEach(dropdown => {
      dropdown.addEventListener('click', (e) => {
        if (window.innerWidth <= 850) {
          e.preventDefault();
          dropdown.parentElement.classList.toggle('show-mobile');
        }
      });
    });
  });
</script>

</body>
</html>
