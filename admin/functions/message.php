<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $db = new Database();
    $functions = new Functions();
    $link = $db->connect();
    $sql = "INSERT INTO notifications (sender,subject,message,date,recipient,attachement)
                                        VALUES (?,?,?,?,?,?)";
    $stmt = mysqli_prepare($link, $sql);

    $sender = intval($_POST['sender']);
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $date = date('Y-m-d H:i:s');
    $recipient = intval($_POST['recipient']);
    $fileurl = "";
    if($_FILES && !empty($_FILES["file"]["name"])){
        $target_dir = "../uploads/";

        // Validate file type - whitelist safe extensions
        $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'png', 'jpg', 'jpeg', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

        // Max file size: 10MB
        $max_file_size = 10 * 1024 * 1024;

        if (!in_array($file_extension, $allowed_extensions)) {
            echo "File type not allowed.<br>";
        } elseif ($_FILES["file"]["size"] > $max_file_size) {
            echo "File too large (max 10MB).<br>";
        } else {
            // Generate safe unique filename to prevent overwrite and path traversal
            $safe_filename = uniqid('file_', true) . '.' . $file_extension;
            $target_file = $target_dir . $safe_filename;

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $fileurl = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.<br>";
            }
        }
    }
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "isssis", $sender, $subject, $message, $date, $recipient, $fileurl);

    if (mysqli_stmt_execute($stmt)) {
        echo "sent";
        if (array_key_exists('mentor', $_POST))
            $functions->SendEmailToUser($recipient, "userId", "TutorMeet - notification", "You've got new message in TutorMeet");
    } else {
        error_log("SQL Error: " . mysqli_error($link));
        echo "Error processing request.";
    }
    mysqli_stmt_close($stmt);

    mysqli_close($link);
}