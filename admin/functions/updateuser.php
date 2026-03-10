<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $func = new Functions();
    $db = new Database();
    $link = $db->connect();

    $userid = mysqli_real_escape_string($link, $_POST['userid']);
    $block = $_POST['blocked'];

    $sql = "UPDATE users SET blocked=? WHERE userId=?";
    $stmt = mysqli_prepare($link, $sql);

    $blocked = $block;

    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "ii", $blocked, $userid);

    if (mysqli_stmt_execute($stmt)) {
        echo "saved";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);



    mysqli_close($link);
} else {
    echo "Nothing to do";
}