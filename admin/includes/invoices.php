<?php

require_once('includes/functions.php');

$functions = new Functions;

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

$sql = "SELECT * FROM users WHERE userPrivilege = 5";

$invoiceno = 1;

$result = '<div id="error">
        </div>
        <div class="col-lg-12">
    <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
        <div class="au-card-title">
            <div class="bg-overlay bg-overlay--blue"></div>
            <div class="au-btn-container">
<button type="button" class="btn btn-success" onclick="SendAll(\'generate\');" id="btn-send">Generate</button>
<button type="button" class="btn btn-success" onclick="SendAll(\'send\');" id="btn-send-all">Send All No Accounting</button>
<button type="button" class="btn btn-danger" onclick="SendAll(\'sendacc\');" id="btn-send-acc">Send All</button></div>
            <h3>
                <i class="zmdi zmdi-comment-text"></i>Tutors invoices list</h3>
        </div>
        <div>Filters:
<div class="col-lg-3">
                            <div class="form-group">
                                <label>Tutor:</label>';
$result .= $functions->GetMentors(1);
$result .= '</div>
                        </div>        
</div>
        <div class="au-inbox-wrap js-inbox-wrap">
            <div class="au-message js-list-load">';

if ($stmt = mysqli_prepare($link, $sql)) {

    mysqli_stmt_execute($stmt)
            or die("Unable to execute query: " . $stmt->error);

    $rslt = mysqli_stmt_get_result($stmt);
    while ($user = mysqli_fetch_array($rslt)) {
        $usr = $functions->GetUser($user['userId'], "userId");
        $sql2 = "SELECT * FROM mentors WHERE mentorId = " . $user['recordId'];
        if ($stmt2 = mysqli_prepare($link, $sql2)) {

            mysqli_stmt_execute($stmt2)
                    or die("Unable to execute query: " . $stmt2->error);

            $rslt2 = mysqli_stmt_get_result($stmt2);
            while ($mentor = mysqli_fetch_array($rslt2)) {
                if ($functions->GetHourSessionsCount($mentor['mentorId']) > 0 || $functions->GetDailySessions($mentor['mentorId']) > 0 || $functions->GetItalySessionsCount($mentor['mentorId']) > 0)
                    $result .= "<div class='card'>";
                $duration = 0;
                $sql3 = "SELECT * FROM rates WHERE userid = " . $mentor['mentorId'];
                if ($stmt3 = mysqli_prepare($link, $sql3)) {

                    mysqli_stmt_execute($stmt3)
                            or die("Unable to execute query: " . $stmt3->error);

                    $rslt3 = mysqli_stmt_get_result($stmt3);
                    if ($functions->GetHourSessionsCount($mentor['mentorId']) > 0) {
                        while ($rate = mysqli_fetch_array($rslt3)) {

                            $r = $rate == null ? 0 : $rate['rate'];

                            $template = file_get_contents(("templates/_invoice_list.html"));
                            $template = str_replace("{avatar}", $usr['avatar'], $template);
                            $template = str_replace("{name}", $usr['realname'], $template);
                            $template = str_replace("{rate}", $r, $template);
                            $template = str_replace("{currency}", $mentor['currency'], $template);
                            $template = str_replace("{hourstext}", "Items", $template);
                            $template = str_replace("{hours}", $functions->GetHours($functions->GetHourSessions($mentor['mentorId'])), $template);
                            $template = str_replace("{notapprovedhours}", $functions->GetHours($functions->GetNotAproovedSessions($mentor['mentorId'])) > 0 ? "Not approved hours: " . $functions->GetHours($functions->GetNotAproovedSessions($mentor['mentorId'])) : "", $template);
                            $template = str_replace("{costs}", $functions->GetCosts($user['userId']), $template);

                            $template = str_replace("{date}", date('Y-m-d', strtotime('last day of previous month')), $template);

                            $template = str_replace("{formid}", "hour-invoice-form" . $invoiceno, $template);
                            $template = str_replace("{invoiceno}", $invoiceno, $template);
                            $template = str_replace("{userid}", $user['userId'], $template);

                            $template = str_replace("{daily}", "0", $template);
                            $template = str_replace("{italy}", "0", $template);

                            $template = str_replace("{indicator}", '<i class="fas fa-hourglass-end"></i>', $template);

                            $template = str_replace("{button}", '<button type="button" class="btn btn-success au-input--full" id="btn-submit" onclick="SubmitForm(\'#hour-invoice-form'. $invoiceno.'\');">Update</button>', $template);
                        
                            $result .= $template;

                            $invoiceno++;
                        }
                    }
                }

                $sql31 = "SELECT * FROM rates WHERE userid = " . $mentor['mentorId'];
                if ($stmt31 = mysqli_prepare($link, $sql31)) {

                    mysqli_stmt_execute($stmt31)
                            or die("Unable to execute query: " . $stmt31->error);

                    $rslt31 = mysqli_stmt_get_result($stmt31);
                    if ($functions->GetDailySessions($mentor['mentorId']) > 0) {
                        while ($rate = mysqli_fetch_array($rslt31)) {

                            $r = $rate == null ? 0 : $rate['daily'];

                            $template = file_get_contents(("templates/_invoice_list.html"));
                            $template = str_replace("{avatar}", $usr['avatar'], $template);
                            $template = str_replace("{name}", $usr['realname'], $template);
                            $template = str_replace("{rate}", $r, $template);
                            $template = str_replace("{currency}", $mentor['currency'], $template);
                            $template = str_replace("{hourstext}", "Items", $template);
                            $template = str_replace("{hours}", $functions->GetDailySessions($mentor['mentorId']), $template);
                            $template = str_replace("{notapprovedhours}", $functions->GetHours($functions->GetNotAproovedSessions($mentor['mentorId'])) > 0 ? "Not approved hours: " . $functions->GetHours($functions->GetNotAproovedSessions($mentor['mentorId'])) : "", $template);
                            $template = str_replace("{costs}", $functions->GetCosts($user['userId']), $template);

                            $template = str_replace("{date}", date('Y-m-d', strtotime('last day of previous month')), $template);

                            $template = str_replace("{formid}", "daily-invoice-form" . $invoiceno, $template);
                            $template = str_replace("{invoiceno}", $invoiceno, $template);
                            $template = str_replace("{userid}", $user['userId'], $template);

                            $template = str_replace("{daily}", "1", $template);
                            $template = str_replace("{italy}", "0", $template);

                            $template = str_replace("{indicator}", '<i class="fas fa-calendar-day"></i>', $template);
                            $template = str_replace("{button}", '<button type = "button" class = "btn btn-success au-input--full" id = "btn-submit" onclick = "SubmitForm(\'#daily-invoice-form'. $invoiceno.'\');">Update</button >', $template);
                                    $result .= $template;

                            $invoiceno++;
                        }
                    }
                }

                $sql311 = "SELECT * FROM rates WHERE userid = " . $mentor['mentorId'];
                if ($stmt311 = mysqli_prepare($link, $sql311)) {

                    mysqli_stmt_execute($stmt311)
                            or die("Unable to execute query: " . $stmt311->error);

                    $rslt311 = mysqli_stmt_get_result($stmt311);
                    if ($functions->GetItalySessionsCount($mentor['mentorId']) > 0) {
                        while ($rate = mysqli_fetch_array($rslt311)) {

                            $r = $rate == null ? 0 : $rate['rate'];

                            $template = file_get_contents(("templates/_invoice_list.html"));
                            $template = str_replace("{avatar}", $usr['avatar'], $template);
                            $template = str_replace("{name}", $usr['realname'], $template);
                            $template = str_replace("{rate}", $r, $template);
                            $template = str_replace("{currency}", $mentor['currency'], $template);
                            $template = str_replace("{hourstext}", "Items", $template);
                            $template = str_replace("{hours}", $functions->GetHours($functions->GetItalySessions($mentor['mentorId'])), $template);
                            $template = str_replace("{notapprovedhours}", $functions->GetHours($functions->GetNotAproovedSessions($mentor['mentorId'])) > 0 ? "Not approved hours: " . $functions->GetHours($functions->GetNotAproovedSessions($mentor['mentorId'])) : "", $template);
                            $template = str_replace("{costs}", $functions->GetCosts($user['userId']), $template);

                            $template = str_replace("{date}", date('Y-m-d', strtotime('last day of previous month')), $template);

                            $template = str_replace("{formid}", "italy-invoice-form" . $invoiceno, $template);
                            $template = str_replace("{invoiceno}", $invoiceno, $template);
                            $template = str_replace("{userid}", $user['userId'], $template);

                            $template = str_replace("{indicator}", '<i class="fas fa-italic"></i>', $template);

                            $template = str_replace("{daily}", "0", $template);
                            $template = str_replace("{italy}", "1", $template);
                            $template = str_replace("{button}", '', $template);

                            $result .= $template;

                            $invoiceno++;
                        }
                    }
                }
                if ($functions->GetHourSessionsCount($mentor['mentorId']) > 0 || $functions->GetDailySessions($mentor['mentorId']) > 0 || $functions->GetItalySessionsCount($mentor['mentorId']) > 0)
                    $result .= "</div>";
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
