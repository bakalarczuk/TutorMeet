<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $db = new Database();
    $functions = new Functions();
    $link = $db->connect();
    $sql = "INSERT INTO notifications (sender,subject,message,date,recipient,attachement)
                                        VALUES (?,?,?,?,?,?)";
    $stmt = mysqli_prepare($link, $sql);

    $sender = mysqli_real_escape_string($link, $_POST['sender']);
    $subject = mysqli_real_escape_string($link, $_POST['subject']);
    $message = mysqli_real_escape_string($link, $_POST['message']);
    $date = date('Y-m-d H:i:s');
    $recipient = mysqli_real_escape_string($link, $_POST['recipient']);
    if($_FILES){
    $target_dir = "../uploads/";
    $fileurl="";
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.<br>";
  $uploadOk = 0;
  $fileurl = $target_file;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.<br>";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    //echo "The file ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded.";
    $fileurl = $target_file;
  } else {
    echo "Sorry, there was an error uploading your file.<br>";
  }
}
    }
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "isssis", $sender, $subject, $message, $date, $recipient, $fileurl);

    if (mysqli_stmt_execute($stmt)) {
        echo "sent";
        if (array_key_exists('mentor', $_POST))
            $functions->SendEmailToUser($recipient, "userId", "TutorMeet - notification", "You've got new message in TutorMeet");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }
    mysqli_stmt_close($stmt);

    mysqli_close($link);
}