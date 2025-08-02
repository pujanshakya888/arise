<?php
session_start(); // Always at the top

include("includes/connect.php");

// $header_to_include = "includes/header2.php"; // default

if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
    $header_to_include = "includes/header2.php";
} else {
    $header_to_include = "includes/header1.php";
}

include($header_to_include);

// Optional: For debugging only - remove in production
// echo '<pre>Session variables: ';
// print_r($_SESSION);
// echo '</pre>';

$inside_valley_locations = ['Gongabhu', 'Samakushi', 'Chakrapath', 'Ramshapath'];

// Initialize filter variables
$valley_filter = isset($_GET['valley_filter']) ? $_GET['valley_filter'] : '';
$inside_location = isset($_GET['inside_location']) ? $_GET['inside_location'] : '';

// Build base query with join to tender_form_data for Location
$query = "SELECT tu.id, tu.project_name, tu.created_at
          FROM tender_users tu
          LEFT JOIN tender_form_data tfd ON tu.id = tfd.tender_user_id AND tfd.field_label = 'Project Location'";

// Add filtering based on valley_filter
if ($valley_filter === 'inside') {
    // Filter projects where Location is in inside_valley_locations
    $placeholders = implode("','", array_map('addslashes', $inside_valley_locations));
    if ($inside_location && in_array($inside_location, $inside_valley_locations)) {
        // Filter by specific inside location
        $query .= " WHERE tfd.field_value = '" . addslashes($inside_location) . "'";
    } else {
        // Filter by any inside valley location
        $query .= " WHERE tfd.field_value IN ('$placeholders')";
    }
} elseif ($valley_filter === 'outside') {
    // Filter projects where Location is NOT in inside_valley_locations or Location is NULL
    $placeholders = implode("','", array_map('addslashes', $inside_valley_locations));
    $query .= " WHERE (tfd.field_value NOT IN ('$placeholders') OR tfd.field_value IS NULL)";
}

// Order by created_at desc
$query .= " ORDER BY tu.created_at DESC";

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

    /* Additional styles for filter form */
    .filter-form {
      width: 90%;
      max-width: 1000px;
      margin: 0 auto 30px auto;
      background-color: #fff;
      padding: 15px 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      gap: 20px;
      font-family: 'Poppins', sans-serif;
    }

    .filter-form label {
      font-weight: 600;
      color: #333;
    }

    .filter-form select {
      padding: 8px 12px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    .filter-form button {
      padding: 8px 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 600;
      font-size: 14px;
    }

    .filter-form button:hover {
      background-color: #0056b3;
    }
  </style>
  <script>
    function toggleInsideLocation() {
      var valleySelect = document.getElementById('valley_filter');
      var insideLocationDiv = document.getElementById('inside_location_div');
      if (valleySelect.value === 'inside') {
        insideLocationDiv.style.display = 'inline-block';
      } else {
        insideLocationDiv.style.display = 'none';
      }
    }
    window.onload = function() {
      toggleInsideLocation();
    };
  </script>
</head>
<body>
  <h1>All Projects</h1>

  <form method="get" class="filter-form" onsubmit="return true;">
    <label for="valley_filter">Filter:</label>
    <select name="valley_filter" id="valley_filter" onchange="toggleInsideLocation()">
      <option value="" <?php if ($valley_filter === '') echo 'selected'; ?>>-- All --</option>
      <option value="inside" <?php if ($valley_filter === 'inside') echo 'selected'; ?>>Inside of Valley</option>
      <option value="outside" <?php if ($valley_filter === 'outside') echo 'selected'; ?>>Outside of Valley</option>
    </select>

    <div id="inside_location_div" style="display:none;">
      <label for="inside_location">Location:</label>
      <select name="inside_location" id="inside_location">
        <option value="">-- Select Location --</option>
        <?php
          foreach ($inside_valley_locations as $loc) {
            $selected = ($inside_location === $loc) ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($loc) . "\" $selected>" . htmlspecialchars($loc) . "</option>";
          }
        ?>
      </select>
    </div>

    <button type="submit">Apply</button>
  </form>

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
              <a href="post.php?id=<?php echo $row['id']; ?>">
                View Posts
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
