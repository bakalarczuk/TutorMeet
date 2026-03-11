<?php
require_once "../includes/config.php";
if ($_POST) {
    $db = new Database();
    $link = $db->connect();

    $sessionid = mysqli_real_escape_string($link, $_POST['sessionid']);
    $summary = mysqli_real_escape_string($link, $_POST['summary']);
    $duration = mysqli_real_escape_string($link, $_POST['duration']);

    $db = new Database();
    $link = $db->connect();
    $sql = "UPDATE sessions SET summary = ?, duration = ? WHERE sessionId = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $summary, $duration, $sessionid);

        if (mysqli_stmt_execute($stmt)) {
            echo "saved";
        } else {
            error_log("SQL Error: " . mysqli_error($link)); echo "Error processing request.";
        }
    } else {
        error_log("SQL Error: " . mysqli_error($link)); echo "Error processing request.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    echo "Acces denied";
}