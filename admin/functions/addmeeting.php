<?php

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $db = new Database();
    $functions = new Functions();
    $link = $db->connect();
    if ($functions->CheckHours($_POST['aplicant']) == true) {

        $sql = "INSERT INTO calendar (date,time,title,description,duration,daily,mentorid,aplicants,italy) VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt3 = mysqli_prepare($link, $sql);

        $date = $_POST['date'];
        $time = $_POST['time'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $daily = isset($_POST['daily']) ? $_POST['daily'] : 0;

        $duration = $daily == 0 ? $_POST['hours'] * 60 : 0;
        $mentorid = (!isset($_POST['mentorid'])) ? $functions->GetUser($_SESSION['id'], "userId") : $functions->GetUser($_POST['mentorid'], "recordId");
//                echo $mentorid['recid'];

        $aplicant = $functions->GetUser($_POST['aplicant'], 'recordId');

        $italy = (!isset($_POST['mentorid'])) ? $functions->GetItaly($mentorid['recid'], $_POST['aplicant']) : $functions->GetItaly($_POST['mentorid'], $_POST['aplicant']);

        $aplicants = /* $daily == 1 ? implode(";", $_POST['aplicant']) : */$_POST['aplicant']; //[0];
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt3, "ssssdiisi", $date, $time, $title, $description, $duration, $daily, $mentorid['recid'], $aplicants, $italy);

        if (mysqli_stmt_execute($stmt3)) {
            $sql2 = "UPDATE assignments SET hours = hours + " . $_POST['hours'] . ", hoursleft = hoursleft - " . $_POST['hours'] . " WHERE aplicantid = " . $aplicants;
            $stmt4 = mysqli_prepare($link, $sql2);

            if (mysqli_stmt_execute($stmt4)) {
                echo "saved";
                $mentormsg = "New session planned.\r\n" . $date . " at " . $time . " with " . $aplicant['realname'];
                $applicantmsg = "New session planned. \r\n" . $date . " at " . $time . " with " . $mentorid['realname'] . " \r\n\r\n"
                        . "To accept, please login to TutorMeet and go to Sessions section";

                $functions->SendMessage($mentorid['id'], $_POST['aplicant'], "New session", $applicantmsg);
                $functions->SendEmail($mentorid['email'], "New session", $mentormsg);
                $functions->SendEmail($aplicant['email'], "New session", $applicantmsg);
            } else {
                echo "Error: " . $sql2 . "<br>" . mysqli_error($link);
            }
            mysqli_stmt_close($stmt4);
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        mysqli_stmt_close($stmt3);

        mysqli_close($link);
    } else {
        echo "You have assigned all hours asigned for this applicant";
    }
}
