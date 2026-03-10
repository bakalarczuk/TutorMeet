<?php
session_start();
require_once "../includes/config.php";
require_once "../includes/functions.php";

if ($_POST) {
    $functions = new Functions();
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
            echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
    }
    $functions->SendMailTo($_SESSION['id'], "Session summary", "Session ended with summary for mentor " . $_SESSION['realname'], 4);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    echo "Acces denied";
}