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
</head>
<style>
  @media (max-width: 850px) {
    nav .dropdown > a {
      display: flex;
      justify-content: center;
      align-items: center;
    }
  }

</style>
<body>

<header>
  <div class="logo">
    <a href="index1.php">
<img src="logo.png" alt="Public Eye Logo">


    </a>
  </div>
  <nav id="nav-links">
    <a href="index1.php" class="nav-item">Home</a>
    <a href="project.php" class="nav-item">Projects</a>
    <a href="report.php" class="nav-item">Report</a>
    <a href="#" class="nav-item">Announcements</a>
    <a href="#" class="nav-item">Contact</a>
     <a href="user_profile.php" class="nav-item">Profile</a>
</nav>

    
  </nav>
  <div class="hamburger" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
  </div>
</header>

<script>
  function toggleMenu() {
    document.getElementById('nav-links').classList.toggle('show');
  }
  
</script>
<script>
  function toggleMenu() {
    document.getElementById('nav-links').classList.toggle('show');
  }

  // Dropdown toggle for mobile with toggle behavior
  document.addEventListener('DOMContentLoaded', () => {
    const dropdowns = document.querySelectorAll('.dropdown > a');
    dropdowns.forEach(dropdown => {
      dropdown.addEventListener('click', (e) => {
        if (window.innerWidth <= 768) {
          e.preventDefault(); // prevent navigation
          const parent = dropdown.parentElement;
          // Toggle show-mobile class on click
          if (parent.classList.contains('show-mobile')) {
            parent.classList.remove('show-mobile');
          } else {
            // Close any other open dropdowns
            document.querySelectorAll('.dropdown.show-mobile').forEach(el => {
              if (el !== parent) {
                el.classList.remove('show-mobile');
              }
            });
            parent.classList.add('show-mobile');
          }
        }
      });
    });
  });

  });
</script>
<script>
  function toggleMenu() {
    const nav = document.getElementById('nav-links');

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

  // Mobile dropdown toggle for 'User ▾'
  document.addEventListener('DOMContentLoaded', () => {
    const dropdown = document.querySelector('.dropdown > a');
    if (dropdown) {
      dropdown.addEventListener('click', (e) => {
        if (window.innerWidth <= 850) {
          e.preventDefault();
          dropdown.parentElement.classList.toggle('show-mobile');
        }
      });
    }
  });
</script>



</body>
</html>
