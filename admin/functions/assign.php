<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $functions = new Functions();
    $db = new Database();
    $link = $db->connect();

    $aplicant = intval($_POST['aplicant']);
    $mentor = intval($_POST['mentorid']);
    $hours = intval($_POST['hours']);
    $country = intval($_POST['country']);

    $h = 0;

    $assignid = $functions->CheckIfAssign($mentor, $aplicant);
    //echo $assignid;
    if ($assignid == -1) {
        $sql = "INSERT INTO assignments (mentorid,aplicantid,assigned,hours,hoursleft,country) VALUES (?,?,?,?,?,?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiiiii", $mentor, $aplicant, $hours, $h, $hours, $country);

            if (mysqli_stmt_execute($stmt)) {
                echo "saved";
            } else {
                error_log("SQL Error: " . mysqli_error($link));
                echo "Error processing request.";
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    } else {

        $sql = "UPDATE assignments SET assigned=assigned+?, hoursleft=hoursleft+? WHERE mentorid=? AND aplicantid=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiii", $hours, $hours, $mentor, $aplicant);

            if (mysqli_stmt_execute($stmt)) {
                echo "saved";
            } else {
                error_log("SQL Error: " . mysqli_error($link));
                echo "Error processing request.";
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    }
} else {
    echo "Acces denied";
}