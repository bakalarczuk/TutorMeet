<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $functions = new Functions();
    $db = new Database();
    $link = $db->connect();

    $aplicant = mysqli_real_escape_string($link, $_POST['aplicant']);
    $mentor = mysqli_real_escape_string($link, $_POST['mentorid']);
    $hours = mysqli_real_escape_string($link, $_POST['hours']);
    $country = mysqli_real_escape_string($link, $_POST['country']);

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
                echo "Error: " . $sql . "<br>" . mysqli_error($link);
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    } else {

        $sql = "UPDATE assignments SET assigned=assigned+" . $hours . ", hoursleft=hoursleft + " . $hours . "";
        if ($stmt = mysqli_prepare($link, $sql)) {

            if (mysqli_stmt_execute($stmt)) {
                echo "saved";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($link);
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    }
} else {
    echo "Acces denied";
}