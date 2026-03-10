<?php

$functions = new Functions();

$db = new Database();
$link = $db->connect();

$result = '<div id="error">
        </div>
        <div class="col-lg-12">
    <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
        <div class="au-card-title">
            <div class="bg-overlay bg-overlay--blue"></div>
            <h3>
                <i class="zmdi zmdi-comment-text"></i>Canceled sessions list</h3>
        </div>
        <div class="au-inbox-wrap js-inbox-wrap">
            <div class="au-message js-list-load">';

$sql3 = "SELECT * FROM calendar WHERE canceled = 1 ORDER BY date DESC, time DESC";
if ($stmt3 = mysqli_prepare($link, $sql3)) {

    mysqli_stmt_execute($stmt3)
        or die("Unable to execute query: " . $stmt3->error);

    $rslt3 = mysqli_stmt_get_result($stmt3);
    while ($calendar = mysqli_fetch_array($rslt3)) {

        $start_date = strtotime($calendar['date'] . ' ' . $calendar['time']);
        $end_date = strtotime($calendar['date'] . ' ' . $calendar['time']) + ($calendar['duration'] * 60);
        $now_date = strtotime(date("Y-m-d H:i:s"));

        if ($now_date >= $start_date && $now_date < $end_date) $disabled = "";
        else $disabled = "disabled";

        $user = $functions->GetUser($calendar['canceleduserid'], "userId");
        $template = file_get_contents(("templates/_canceled.html"));
        $template = str_replace("{date}", $calendar['date'], $template);
        $template = str_replace("{time}", $calendar['time'], $template);
        $template = str_replace("{canceleduser}", $user['realname'], $template);        
        $template = str_replace("{button}", "", $template);


        $result .= $template;
    }
}
$result .= "
            </div>
        </div>
    </div>
</div>";

echo $result;