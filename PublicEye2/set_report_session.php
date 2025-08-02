<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['project_name'])) {
        $project_name = trim($_POST['project_name']);
        if ($project_name !== '') {
            $_SESSION['report_project_name'] = $project_name;
            echo "OK";
            exit;
        }
    }
}

http_response_code(400);
echo "Invalid request";
?>
