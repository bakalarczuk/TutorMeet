<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $func = new Functions();
    $db = new Database();
    $link = $db->connect();

    $userid = mysqli_real_escape_string($link, $_POST['userid']);
$user = $func->GetUser($userid, "userId");
    $sql = "UPDATE users SET userPass=? WHERE userId=?";
    $stmt = mysqli_prepare($link, $sql);

    $newpass= $func->generatePassword(12);
    $password = password_hash($newpass, PASSWORD_DEFAULT);
    
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "si", $password, $userid);

    if (mysqli_stmt_execute($stmt)) {
        echo "saved";
        $msg = "TutorMeet system password reset. Your temporary password is: " . $newpass . ". If you confirm password change, please click on link <a href='https://".$_SERVER['SERVER_NAME']."/confirm.php?q=".$userid."&t=".$newpass."'>here</a>";
        $func->SendEmail($user["email"], "TutorMeet password reset", $msg);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);



    mysqli_close($link);
} else {
    echo "Nothing to do";
}