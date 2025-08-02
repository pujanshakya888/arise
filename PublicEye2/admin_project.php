<?php
session_start(); // Always at the top

include("includes/connect.php");
include("includes/header2.php");

// Fetch all projects
$query = "SELECT id, project_name, created_at FROM tender_users ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>All Projects</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    h1 {
      text-align: center;
      color: #333;
      margin-bottom: 40px;
    }

    table {
      border-collapse: collapse;
      margin: 0 auto;
      width: 90%;
      max-width: 1000px;
      background-color: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
    }

    thead {
      background-color: black;
      color: white;
    }

    th, td {
      padding: 15px 20px;
      text-align: left;
    }

    tbody tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    td a {
      text-decoration: none;
      color: #007bff;
      font-weight: 600;
    }

    td a:hover {
      text-decoration: underline;
    }

    .center {
      text-align: center;
      color: #666;
    }
  </style>
</head>
<body>
  <h1>All Projects</h1>

  <?php if ($result && $result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>SN</th>
          <th>Project Name</th>
          <th>Date Created</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $sn = 1; while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $sn++; ?></td>
            <td><?php echo htmlspecialchars($row['project_name']); ?></td>
            <td><?php echo date("Y-m-d", strtotime($row['created_at'])); ?></td>
            <td>
              <a href="admin_project_details.php?id=<?php echo $row['id']; ?>">
                View Details
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="center">No projects found.</p>
  <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>
