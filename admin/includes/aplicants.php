<?php

require_once('includes/functions.php');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

$sql = "SELECT * FROM users WHERE userPrivilege = 6";
$invoiceno = 1;
$duration = 0;
$functions = new Functions();

$result = '<div id="error"></div><div class="col-lg-12">
    <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
        <div class="au-card-title" style="background-image:url(\'images/bg-title-02.jpg\');">
            <div class="bg-overlay bg-overlay--blue"></div>
            <h3>
                <i class="zmdi zmdi-comment-text"></i>Applicant list</h3>';
if ($_GET['func'] != "applicant_meetings") :
    $result .=
        '<button class="au-btn-plus-upper green" onclick="window.location.href = \'adduser\';">
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
        $sql2 = "SELECT * FROM aplicants WHERE aplicantId = " . $user['recordId'];
        if ($stmt2 = mysqli_prepare($link, $sql2)) {

            mysqli_stmt_execute($stmt2)
                or die("Unable to execute query: " . $stmt2->error);

            $rslt2 = mysqli_stmt_get_result($stmt2);
            while ($aplicant = mysqli_fetch_array($rslt2)) {

                
        
                $template = file_get_contents("templates/_aplicantitem.html");
                $template = str_replace("{avatar}", $usr['avatar'], $template);
                $template = str_replace("{realname}", $usr['realname'], $template);
                $template = str_replace("{street}", $aplicant['street'], $template);
                $template = str_replace("{streetno}", $aplicant['streetno'], $template);
                $template = str_replace("{localno}", $aplicant['localno'], $template);
                $template = str_replace("{postalcode}", $aplicant['postalcode'], $template);
                $template = str_replace("{town}", $aplicant['town'], $template);
                $template = str_replace("{hours}", $aplicant['hours'], $template);
                if ($aplicant['type'] == 0) $selected0 = "selected";
                else $selected0 = "";
                if ($aplicant['type'] == 1) $selected1 = "selected";
                else $selected1 = "";


                $template = str_replace("{userid}", $user['userId'], $template);
                $template = str_replace("{mentorid}", $aplicant['aplicantId'], $template);

                $template = str_replace("{selected0}", $selected0, $template);
                $template = str_replace("{selected1}", $selected1, $template);
                $template = str_replace("{email}", $usr['email'], $template);
                $template = str_replace("{idname}", str_replace(" ", "_", $usr['realname']) . $aplicant['aplicantId'], $template);

                if ($_GET['func'] == 'applicant_meetings') :
                    $template = str_replace("{btnText}", "Show sessions", $template);
                    $template = str_replace("{open}", 'onclick = "window.location.href=\'/admin/applicant_sessions/' . $aplicant['aplicantId'] . '\';"', $template);
                else :
                    $template = str_replace("{btnText}", "Open", $template);
                    $template = str_replace("{open}", 'onclick = "$(\'#more-' . str_replace(" ", "_", $usr['realname']) . $aplicant['aplicantId'] . '\').toggle(\'slow\');"', $template);
                endif;
                
                $sql3 = "SELECT * FROM assignments WHERE aplicantid = " . $aplicant['aplicantId'];
                if ($stmt3 = mysqli_prepare($link, $sql3)) {

                    mysqli_stmt_execute($stmt3)
                        or die("Unable to execute query: " . $stmt3->error);
$assgn = "";
                    $rslt3 = mysqli_stmt_get_result($stmt3);
                    while ($assign = mysqli_fetch_array($rslt3)) {  
                        $mntr = $functions->GetUser($assign['mentorid'], "recordId")['realname'];
                        $assgn .= "<strong>Tutor: " .$mntr."</strong><br/>";
                        $assgn .= "Purchased: " .$assign["assigned"]." | ";
                        $assgn .= "Used: " .$assign["hours"]." | ";
                        $assgn .= "Left: " .$assign["hoursleft"]."<br/>";

                    }
                    $template = str_replace("{mentors}", $assgn, $template);
                }
                
                $result .= $template;
            }
        }
    }
}

$result .= "</div>
        </div>
    </div>
</div>";

echo $result;