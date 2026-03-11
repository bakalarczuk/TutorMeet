<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $func = new Functions();
    $db = new Database();
    $link = $db->connect();
    
    $user = $func->GetUser($_POST['userid'], "userId");
    
    if($user["recid"] !=null){
       if($user["userPrivilege"] == 5){
           $func->RemoveMentor($_POST['userid']);
       }elseif($user["userPrivilege"] == 6){
           $func->RemoveAplicant($_POST['userid']);
       }
    }

    $userid = mysqli_real_escape_string($link, $_POST['userid']);

    //stmt
    $sql = "DELETE FROM users WHERE userId=?";
    $stmt = mysqli_prepare($link, $sql);

    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $userid);

    if (mysqli_stmt_execute($stmt)) {
        echo "saved";
    } else {
        error_log("SQL Error: " . mysqli_error($link)); echo "Error processing request.";
    }

    mysqli_stmt_close($stmt);
//end stmt


    mysqli_close($link);
} else {
    echo "Nothing to do";
}