<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $func = new Functions();
    $db = new Database();
    $link = $db->connect();

    $userid = intval($_POST['userid']);
    $mentorid = intval($_POST['mentorid']);

    $sql = "UPDATE aplicants SET street=?, streetno=?, localno=?, town=?, type=? WHERE aplicantId = ?";
    $stmt = mysqli_prepare($link, $sql);

    $p_street = $_POST['street'];
    $p_streetno = $_POST['streetno'];
    $p_localno = $_POST['localno'];
    $p_town = $_POST['town'];
    $p_contract = intval($_POST['type']);

    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "ssssii", $p_street, $p_streetno, $p_localno, $p_town, $p_contract, $mentorid);

    if (mysqli_stmt_execute($stmt)) {
        $sql2 = "UPDATE users SET userEmail=? WHERE userId = ?";
        $stmt2 = mysqli_prepare($link, $sql2);
        $u_email = $_POST['email'];

        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt2, "si", $u_email, $userid);

        if (mysqli_stmt_execute($stmt2)) {
            echo "saved";
        } else {
            error_log("SQL Error: " . mysqli_error($link));
            echo "Error processing request.";
        }

        mysqli_stmt_close($stmt2);
    } else {
        error_log("SQL Error: " . mysqli_error($link));
        echo "Error processing request.";
    }

    mysqli_stmt_close($stmt);



    mysqli_close($link);
} else {
    echo "Nothing to do";
}