<?php

$functions = new Functions();

$db = new Database();
$link = $db->connect();

$sql = "SELECT * FROM users WHERE userId = {$_SESSION['id']} AND userPrivilege = 6";

$result = '<div id="error">
        </div>
        <div class="col-lg-12">
    <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
        <div class="au-card-title">
            <div class="bg-overlay bg-overlay--blue"></div>
            <h3>
                <i class="zmdi zmdi-comment-text"></i>Applicant\'s planned sessions list</h3>
        </div>
        <div class="au-inbox-wrap js-inbox-wrap">
            <div class="au-message js-list-load">';

if ($stmt = mysqli_prepare($link, $sql)) {

    mysqli_stmt_execute($stmt)
        or die("Unable to execute query: " . $stmt->error);

    $rslt = mysqli_stmt_get_result($stmt);
    while ($user = mysqli_fetch_array($rslt)) {
        $usr = $functions->GetUser($user['userId'], "userId");
        $sql2 = "SELECT * FROM aplicants WHERE aplicantId = " . $user['recordId'];
        if ($stmt2 = mysqli_prepare($link, $sql2)) {

            mysqli_stmt_execute($stmt2)
                or die("Unable to execute query: " . $stmt2->error);

            $rslt2 = mysqli_stmt_get_result($stmt2);
            while ($aplicant = mysqli_fetch_array($rslt2)) {
                $sql3 = "SELECT * FROM calendar WHERE aplicants regexp '[[:<:]]" . $aplicant['aplicantId'] . "[[:>:]]' AND canceled = 0 ORDER BY date DESC, time DESC";
                if ($stmt3 = mysqli_prepare($link, $sql3)) {

                    mysqli_stmt_execute($stmt3)
                        or die("Unable to execute query: " . $stmt3->error);

                    $rslt3 = mysqli_stmt_get_result($stmt3);
                    while ($calendar = mysqli_fetch_array($rslt3)) {

                        $start_date = strtotime($calendar['date'] . ' ' . $calendar['time']);
                        $end_date = strtotime($calendar['date'] . ' ' . $calendar['time']) + ($calendar['duration'] * 60);
                        $now_date = strtotime(date("Y-m-d H:i:s"));
                        $session = $functions->GetSession($calendar["id"]);

                        if ($calendar['accepted'] == 0) :
                            $buttons = '<button type="button" class="btn btn-info btn-circle btn-md" onclick="window.location.href = \'accept/' . $calendar["id"] . '\';"><i class="far fa-calendar-times"></i></button>';
                        elseif ($calendar['accepted'] == 1) :
                            $buttons = '<button type="button" class="btn btn-success btn-circle btn-md" disabled><i class="far fa-calendar-check"></i></button>';
                        else :
                            $buttons = "";
                        endif;

                        if ($now_date >= $start_date && $now_date < $end_date) $disabled = "";
                        else $disabled = "disabled";
                        if ($now_date >= $start_date && $now_date < $end_date && $calendar['accepted'] == 1) {
                            if ($session != null && !empty($session['summary'])) {
                                $buttons .= '<button type="button" class="btn btn-danger btn-circle btn-md" disabled><i class="fas fa-video-slash"></i></button>';
                            } else {
                                $buttons .= '<button type="button" class="btn btn-success btn-circle btn-md" id="btn-{roomname}"
                        onclick="window.location.href = \'session/{roomname}/{displayname}/{starttime}/' . $calendar["id"] . '\';"><i class="fas fa-video"></i></button>';
                            }
                        } else {
                            $buttons .= '<button type="button" class="btn btn-danger btn-circle btn-md" disabled><i class="fas fa-video-slash"></i></button>';
                            if (($now_date - $start_date) / 60 / 60 < -48) {
                                $buttons .=
                                    '<button type="button" class="btn btn-danger btn-circle btn-md" onclick="window.location.href = \'cancel/' . $calendar["id"] . "/" . $_SESSION['id'] . '\';"><i class="fas fa-trash-alt"></i></button>';
                            }
                            if (($now_date - $start_date) / 60 / 60 < -3) {
                                $buttons .= '<button type="button" class="btn btn-info btn-circle btn-md" onclick="window.location.href = \'transfer/' . $calendar["id"] . "/" . $_SESSION['id'] . '\';"><i class="fas fa-exchange-alt"></i></button>';
                            }
                        }

                        if (($session != null && $session["summary"] == "") || ($session != null && $session["approved"] == 0)) {
                            $buttons .= '<button type="button" class="btn btn-success btn-circle btn-md" id="btn-summary-edit"
                        onclick="window.location.href = \'sessionedit/' . $session["sessionId"] . '\';"><i class="fas fa-edit"></i></button>';
                        }
                        $_SESSION["ap"] = $calendar['aplicants'];

                        $template = file_get_contents(("templates/_conferences.html"));
                        $template = str_replace("{date}", $calendar['date'], $template);
                        $template = str_replace("{time}", $calendar['time'], $template);
                        $template = str_replace("{description}", $calendar['description'], $template);
                        $template = str_replace("{duration}", $calendar['duration'], $template);


                        $template = str_replace("{group}", "Tutor", $template);
                        $template = str_replace("{aplicants}", $functions->GetUserExt($calendar['mentorid'], 5)["realname"], $template);

                        $template = str_replace("{success}", ($now_date > $end_date ? "danger" : "success"), $template);
                        $template = str_replace("{disabled}", $disabled, $template);
                        $template = str_replace("{buttons}", $buttons, $template);

                        $roomname = $functions->GetUserExt($calendar['mentorid'], 5)["email"] . "_" . $start_date;
                        $roomname = str_replace("@", "", $roomname);

                        $template = str_replace("{roomname}", md5($roomname), $template);
                        $template = str_replace("{displayname}", $_SESSION['realname'], $template);
                        $template = str_replace("{starttime}", $start_date, $template);

                        $result .= $template;
                    }
                }
            }
        }
    }
}

$result .= "
            </div>
        </div>
    </div>
</div>";

echo $result;