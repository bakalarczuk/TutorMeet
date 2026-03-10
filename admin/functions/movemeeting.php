<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $db = new Database();
    $functions = new Functions();
    $link = $db->connect();

    $sql = "UPDATE calendar SET date=?, time=? WHERE id=?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $_POST['date'], $_POST['time'], $_POST['calid']);

    // Bind variables to the prepared statement as parameters

    if (mysqli_stmt_execute($stmt)) {
            echo "saved";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }
    mysqli_stmt_close($stmt);

    mysqli_close($link);
}
