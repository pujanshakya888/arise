<?php
include("includes/header3.php");
session_start();

if (!isset($_SESSION['tender_user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href = 'tender_login.php';</script>";
    exit;
}

$tender_user_id = $_SESSION['tender_user_id'];

// Sanitize helper
function clean($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle all custom fields (text)
    $custom_fields = $_POST['custom_fields'] ?? [];

    // Debug: print custom_fields
    error_log("Custom fields received: " . print_r($custom_fields, true));

    $check_stmt = $conn->prepare("SELECT id FROM tender_form_data WHERE tender_user_id = ? AND field_label = ?");
    if (!$check_stmt) {
        error_log("Prepare check_stmt failed: " . $conn->error);
    }
    $update_stmt = $conn->prepare("UPDATE tender_form_data SET field_value = ? WHERE id = ?");
    if (!$update_stmt) {
        error_log("Prepare update_stmt failed: " . $conn->error);
    }
    $insert_stmt = $conn->prepare("INSERT INTO tender_form_data (tender_user_id, field_label, field_value) VALUES (?, ?, ?)");
    if (!$insert_stmt) {
        error_log("Prepare insert_stmt failed: " . $conn->error);
    }

    foreach ($custom_fields as $field_label => $field_value_raw) {
        $field_value = clean($field_value_raw);

        $check_stmt->bind_param("is", $tender_user_id, $field_label);
        if (!$check_stmt->execute()) {
            error_log("Execute check_stmt failed: " . $check_stmt->error);
        }
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->bind_result($existing_id);
            $check_stmt->fetch();
            $update_stmt->bind_param("si", $field_value, $existing_id);
            if (!$update_stmt->execute()) {
                error_log("Execute update_stmt failed: " . $update_stmt->error);
            }
        } else {
            $insert_stmt->bind_param("iss", $tender_user_id, $field_label, $field_value);
            if (!$insert_stmt->execute()) {
                error_log("Execute insert_stmt failed: " . $insert_stmt->error);
            }
        }

        $check_stmt->free_result();
    }

    // Handle contact phone and email separately
    $contact_phone = clean($_POST['custom_fields']['Contact Number'] ?? '');
    $contact_email = clean($_POST['custom_fields']['Contact Email'] ?? '');

    // Update or insert contact phone
    $stmt_phone_check = $conn->prepare("SELECT id FROM tender_form_data WHERE tender_user_id = ? AND field_label = 'Contact Number'");
    $stmt_phone_check->bind_param("i", $tender_user_id);
    $stmt_phone_check->execute();
    $stmt_phone_check->store_result();
    if ($stmt_phone_check->num_rows > 0) {
        $stmt_phone_check->bind_result($phone_id);
        $stmt_phone_check->fetch();
        $stmt_phone_update = $conn->prepare("UPDATE tender_form_data SET field_value = ? WHERE id = ?");
        $stmt_phone_update->bind_param("si", $contact_phone, $phone_id);
        $stmt_phone_update->execute();
        $stmt_phone_update->close();
    } else {
        $stmt_phone_insert = $conn->prepare("INSERT INTO tender_form_data (tender_user_id, field_label, field_value) VALUES (?, 'Contact Number', ?)");
        $stmt_phone_insert->bind_param("is", $tender_user_id, $contact_phone);
        $stmt_phone_insert->execute();
        $stmt_phone_insert->close();
    }
    $stmt_phone_check->close();

    // Update or insert contact email
    $stmt_email_check = $conn->prepare("SELECT id FROM tender_form_data WHERE tender_user_id = ? AND field_label = 'Contact Email'");
    $stmt_email_check->bind_param("i", $tender_user_id);
    $stmt_email_check->execute();
    $stmt_email_check->store_result();
    if ($stmt_email_check->num_rows > 0) {
        $stmt_email_check->bind_result($email_id);
        $stmt_email_check->fetch();
        $stmt_email_update = $conn->prepare("UPDATE tender_form_data SET field_value = ? WHERE id = ?");
        $stmt_email_update->bind_param("si", $contact_email, $email_id);
        $stmt_email_update->execute();
        $stmt_email_update->close();
    } else {
        $stmt_email_insert = $conn->prepare("INSERT INTO tender_form_data (tender_user_id, field_label, field_value) VALUES (?, 'Contact Email', ?)");
        $stmt_email_insert->bind_param("is", $tender_user_id, $contact_email);
        $stmt_email_insert->execute();
        $stmt_email_insert->close();
    }

    // ✅ Handle photo upload (if any)
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $tmpName = $_FILES['picture']['tmp_name'];
        $originalName = basename($_FILES['picture']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $newFilename = uniqid('pic_') . '.' . $ext;
            $dest = $uploadDir . $newFilename;

            if (move_uploaded_file($tmpName, $dest)) {
                // Save filename to tender_users table
                $stmt = $conn->prepare("UPDATE tender_users SET picture = ? WHERE id = ?");
                $stmt->bind_param("si", $newFilename, $tender_user_id);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "<script>alert('Error uploading photo.'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Invalid file type. Only JPG, PNG, GIF allowed.'); window.history.back();</script>";
            exit;
        }
    }

    // ✅ Mark form_filled = 1
    $stmt = $conn->prepare("UPDATE tender_users SET form_filled = 1 WHERE id = ?");
    $stmt->bind_param("i", $tender_user_id);
    $stmt->execute();
    $stmt->close();

    // ✅ Close all remaining statements
    $check_stmt->close();
    $update_stmt->close();
    $insert_stmt->close();
    $conn->close();

    // ✅ Redirect to dashboard
    header("Location: tender_dashboard.php");
    exit;
} else {
    echo "Invalid Request.";
}
?>
