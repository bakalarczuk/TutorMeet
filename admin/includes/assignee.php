<?php


require_once 'includes/config.php';
require_once 'includes/functions.php';

$functions = new Functions();

$db = new Database();
$link = $db->connect();

$user = $_SESSION['recid'];
$where = $_SESSION['privilege'] == 5 ? "mentorid = " . $user: "aplicantid = " . $user;

echo '<div class="card"><div class="card-body">';
$sql = "SELECT * FROM assignments WHERE " . $where;
if ($stmt = mysqli_prepare($link, $sql)) {

    if (mysqli_stmt_execute($stmt)) {
        $rslt = mysqli_stmt_get_result($stmt);
        while ($asignee = mysqli_fetch_array($rslt)) {
            $template =
                file_get_contents("templates/_assignee_item.html");
            $template = str_replace("{realname}", $_SESSION['privilege'] == 5 ?$functions->GetUserExt($asignee['aplicantid'], 6)['realname'] : $functions->GetUserExt($asignee['mentorid'], 5)['realname'], $template);
            $template = str_replace("{assignedhours}", $asignee['assigned'], $template);
            $template = str_replace("{hours}", $asignee['hours'], $template);
            $template = str_replace("{hoursleft}", $asignee['hoursleft'], $template);
            $template = str_replace("{person}", $_SESSION['privilege'] == 5 ? " Applicant":"Tutor", $template);
            echo $template;
        }
    } else {
        error_log("SQL Error: " . mysqli_error($link)); echo "Error processing request.";
    }
}
echo '</div></div>';
mysqli_stmt_close($stmt);
mysqli_close($link);