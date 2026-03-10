<?php
session_start();
require_once '../includes/config.php';

if ($_POST) {
    $db = new Database();
    $link = $db->connect();

    if ($_SESSION['privilege'] == 5) :
        $clause = "AND users.userLogin='" . $_SESSION['username'] . "' AND calendar.mentorid =" . $_POST["userid"];
    elseif ($_SESSION["privilege"] == 6) :
        $clause = "AND calendar.aplicants regexp '[[:<:]]" . $_POST["userid"] . "[[:>:]]'";
    elseif ($_SESSION["privilege"] == 4 || $_SESSION["privilege"] == 3 || $_SESSION["privilege"] == 1) :
        $clause = "";
    else :
        $clause =  "none";
    endif;

    // Prepare a select statement
    $sql = "SELECT * FROM calendar RIGHT JOIN mentors ON calendar.mentorid = mentors.mentorId RIGHT JOIN users ON mentors.mentorId = users.recordId WHERE calendar.id IS NOT NULL " . $clause;
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Set parameters
        mysqli_stmt_execute($stmt)
            or die("Unable to execute query: " . $stmt->error);
        $rslt = mysqli_stmt_get_result($stmt);
        $calendar = array();
        while ($c = mysqli_fetch_assoc($rslt)) {
            $start_date = strtotime($c['date'] . ' ' . $c['time']);
            $roomname = $c["userEmail"] . "_" . $start_date;
            $roomname = str_replace("@", "", $roomname);

            $c['roomname'] = md5($roomname);
            $c['displayname'] = $_SESSION['realname'];
            $c['starttime'] = $start_date * 1000;
            $c['endtime'] = ($start_date + ($c['duration'] * 60)) * 1000;
            $calendar[] = $c;
        }

        echo json_encode($calendar);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    // Close statement
    mysqli_stmt_close($stmt);
    // Close connection
    mysqli_close($link);
}