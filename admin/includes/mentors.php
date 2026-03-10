<?php

require_once('includes/functions.php');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

$sql = "SELECT * FROM users WHERE userPrivilege = 5";

$invoiceno = 1;
$duration = 0;
$functions = new Functions();

$result = '<div id="error"></div>
<div class="col-lg-12">
    <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
        <div class="au-card-title" style="background-image:url(\'images/bg-title-02.jpg\');">
            <div class="bg-overlay bg-overlay--blue"></div>
            <h3>
                <i class="zmdi zmdi-comment-text"></i>Tutor list</h3>';
if ($_GET['func'] != "meetings") :
    $result .= '<button class="au-btn-plus-upper green" onclick="window.location.href = \'adduser\';">
                <i class="zmdi zmdi-plus white"></i>
            </button>';
endif;
$result .= '</div>
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

                $sql3 = "SELECT * FROM rates WHERE userid = " . $mentor['mentorId'];
                $stmt3 = mysqli_prepare($link, $sql3) or die(mysqli_error($link));

                mysqli_stmt_execute($stmt3)
                        or die("Unable to execute query: " . $stmt3->error);

                $rslt3 = mysqli_stmt_get_result($stmt3);
                $rates = mysqli_fetch_array($rslt3);


                $template = file_get_contents("templates/_mentoritem.html");
                $template = str_replace("{avatar}", $usr['avatar'], $template);
                $template = str_replace("{realname}", $usr['realname'], $template);
                $template = str_replace("{idname}", str_replace(" ", "_", $usr['realname']), $template);

                if ($mentor['currency'] == "GPB")
                    $selectgb = "selected";
                else
                    $selectgb = "";
                if ($mentor['currency'] == "USD")
                    $selectus = "selected";
                else
                    $selectus = "";
                if ($mentor['currency'] == "PLN")
                    $selectpl = "selected";
                else
                    $selectpl = "";
                if ($mentor['currency'] == "EUR")
                    $selecteu = "selected";
                else
                    $selecteu = "";

                if ($mentor['contract'] == 0)
                    $selectedn = "selected";
                else
                    $selectedn = "";
                if ($mentor['contract'] == 1)
                    $selectedl = "selected";
                else
                    $selectedl = "";


                $template = str_replace("{selectgb}", $selectgb, $template);
                $template = str_replace("{selectus}", $selectus, $template);
                $template = str_replace("{selectpl}", $selectpl, $template);
                $template = str_replace("{selecteu}", $selecteu, $template);
                $template = str_replace("{selectedn}", $selectedn, $template);
                $template = str_replace("{selectedl}", $selectedl, $template);

                $rate = isset($rates['rate']) ? $rates['rate'] : 0;

                $template = str_replace("{rate}", $rate, $template);
                $template = str_replace("{accountno}", $mentor['accountno'], $template);
                $template = str_replace("{street}", $mentor['street'], $template);
                $template = str_replace("{streetno}", $mentor['streetno'], $template);
                $template = str_replace("{localno}", $mentor['localno'], $template);
                $template = str_replace("{postalcode}", $mentor['postalcode'], $template);
                $template = str_replace("{town}", $mentor['town'], $template);

                $template = str_replace("{iban}", $mentor['iban'], $template);
                $template = str_replace("{swift}", $mentor['swift'], $template);
                $template = str_replace("{bankname}", $mentor['bankname'], $template);
                $template = str_replace("{email}", $usr['email'], $template);

                $template = str_replace("{userid}", $user['userId'], $template);
                $template = str_replace("{mentorid}", $mentor['mentorId'], $template);

                $template = str_replace("{note}", $mentor['note'], $template);
                $template = str_replace("{bio}", $mentor['bio'], $template);
                $template = str_replace("{phone}", $mentor['phone'], $template);


                if ($_GET['func'] == 'meetings') :
                    $template = str_replace("{btnText}", "Show", $template);
                    $template = str_replace("{open}", 'onclick = "window.location.href=\'/admin/mentor_sessions/' . $mentor['mentorId'] . '\';"', $template);
                else :
                    $template = str_replace("{btnText}", "Open", $template);
                    $template = str_replace("{open}", 'onclick = "$(\'#more-' . str_replace(" ", "_", $usr['realname']) . '\').toggle(\'slow\');"', $template);
                endif;
                $sql4 = "SELECT * FROM assignments WHERE mentorid = " . $mentor['mentorId'];
                if ($stmt4 = mysqli_prepare($link, $sql4)) {

                    mysqli_stmt_execute($stmt4)
                            or die("Unable to execute query: " . $stmt4->error);
                    $assgn = "";
                    $rslt4 = mysqli_stmt_get_result($stmt4);
                    if (mysqli_num_rows($rslt4) == 0) {
                        $assgn = "No assigned applicants";
                    } else {
                        while ($assign = mysqli_fetch_array($rslt4)) {
                            $mntr = $functions->GetUser($assign['aplicantid'], "recordId", "No assigned applicants")['realname'];
                            $assgn .= "<strong>Applicant: " . $mntr . "</strong><br/>";
                            $assgn .= "Purchased: " . $assign["assigned"] . " | ";
                            $assgn .= "Used: " . $assign["hours"] . " | ";
                            $assgn .= "Left: " . $assign["hoursleft"] . "<br/>";
                        }
                    }
                }
                $template = str_replace("{applcnts}", $assgn, $template);
            }
            $result .= $template;
        }
    }
}

$result .= "</div>
        </div>
    </div>
</div>";

echo $result;
