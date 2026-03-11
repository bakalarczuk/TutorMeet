<?php


require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $functions = new Functions();


    $db = new Database();
    $link = $db->connect();
    $sql = "UPDATE sessions SET duration=?, approved = 1 WHERE sessionId = ?";
    $sessionid = mysqli_real_escape_string($link, $_POST['sessionid']);
    $duration = $_POST['duration'] * 60* 60;
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ds", $duration, $sessionid);

        if (mysqli_stmt_execute($stmt)) {
            echo "sent";
            $functions->SendEmailToUser($_POST['recipient'], "userId", "TutorMeet - notification", "Session approved");
        } else {
            error_log("SQL Error: " . mysqli_error($link)); echo "Error processing request.";
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    echo "Acces denied";
}