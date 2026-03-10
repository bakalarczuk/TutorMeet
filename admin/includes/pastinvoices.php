<?php

require_once('includes/functions.php');

$functions = new Functions;

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(isset($_GET['mentor'])) $condition = " AND recordId=".mysql_real_escape_string ($_GET['mentor']);
else $condition="";

$sql = "SELECT * FROM users WHERE userPrivilege = 5".$condition;
//echo $sql;
$invoiceno = 1;

$result = '<div id="error">
        </div>
        <div class="col-lg-12">
    <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
        <div class="au-card-title">
            <div class="bg-overlay bg-overlay--blue"></div>
            <h3>
                <i class="zmdi zmdi-comment-text"></i>Tutors past invoices</h3>
        </div><div>Filters:
        <div class="row">
<div class="col-lg-3">
                            <div class="form-group">
                                <label>Tutors:</label>';
$result .= $functions->GetMentors(1).'</div></div>
<div class="col-lg-3">
                            <div class="form-group">
                                <label>Period:</label>';
$result .= $functions->GetDatas().'</div></div>
<div class="col-lg-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                               <button type="button" class="btn btn-success au-input--full">Filter</button>
                                </div></div>';
$result .= '</div>        
</div>
        <div class="au-inbox-wrap js-inbox-wrap">
            <div class="au-message js-list-load">';

if ($stmt = mysqli_prepare($link, $sql)) {

    mysqli_stmt_execute($stmt)
            or die("Unable to execute query: " . $stmt->error);

    $rslt = mysqli_stmt_get_result($stmt);
    while ($user = mysqli_fetch_array($rslt)) {
        $usr = $functions->GetUser($user['userId'], "userId");
        $sql2 = "SELECT * FROM mentors WHERE mentorId = " . ($user['recordId']);
        if ($stmt2 = mysqli_prepare($link, $sql2)) {

            mysqli_stmt_execute($stmt2)
                    or die("Unable to execute query: " . $stmt2->error);

            $rslt2 = mysqli_stmt_get_result($stmt2);
            while ($mentor = mysqli_fetch_array($rslt2)) {
                $result .= "<div class='card'>";
                $duration = 0;
                $sql3 = "SELECT * FROM invoices WHERE userid = " . ($user["userId"]) . " AND NOT italy=1 AND NOT daily=1 ORDER BY date DESC";
                //echo $sql3;
                if ($stmt3 = mysqli_prepare($link, $sql3)) {

                    mysqli_stmt_execute($stmt3)
                            or die("Unable to execute query: " . $stmt3->error);
                    //echo $sql3;
                    $rslt3 = mysqli_stmt_get_result($stmt3);
                    while ($rate = mysqli_fetch_array($rslt3)) {

                        $r = $rate == null ? 0 : $rate['rate'];
                        $c = $rate == null ? 0 : $rate['costs'];

                        $template = file_get_contents(("templates/_past_invoice_list.html"));
                        $template = str_replace("{avatar}", $usr['avatar'], $template);
                        $template = str_replace("{name}", $usr['realname'], $template);
                        $template = str_replace("{rate}", $r, $template);
                        $template = str_replace("{currency}", $mentor['currency'], $template);

                        $template = str_replace("{hours}", $rate['items'], $template);
                        $template = str_replace("{costs}", $rate['costs'], $template);
                        $totaldiv = ($_GET['func'] != "settlements") ?
                                '<div class="col-lg-1">
                                <div class="form-group">
                                    <label>Subtotal</label>
                                    <input class="au-input au-input--full" type="text" name="subtotal" value="{subtotal}"
                                        {readonly}>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Total</label>
                                    <input class="au-input au-input--full" type="text" name="total" value="{total}" {readonly}>
                                </div>
                            </div>' : "";

                        $template = str_replace("{totaldiv}", $totaldiv, $template);
                        $template = str_replace("{subtotal}", $rate['subtotal'], $template);
                        $template = str_replace("{total}", $rate['total'], $template);
                        $template = str_replace("{costs}", $rate['costs'], $template);
                        $template = str_replace("{url}", $rate['url'], $template);

                        $template = str_replace("{date}", date('Y-m-d', strtotime($rate['date'])), $template);
                        $formid = "update-invoice-form" . $rate['invoiceId'];
                        $template = str_replace("{formid}", $formid, $template);
                        $template = str_replace("{userid}", $user['userId'], $template);
                        $template = str_replace("{invoiceid}", $rate['invoiceId'], $template);
                        $template = str_replace("{invoiceno}", $rate['invoiceId'], $template);
                        $buttons = '<button type="button" class="btn btn-success au-input--full" id="btn-submit" onclick="UpdateForm(\'#' . $formid . '\');">Update</button>';
                        $template = str_replace("{button}", $buttons, $template);
                        $template = str_replace("{daily}", $rate['daily'], $template);
                        $template = str_replace("{italy}", $rate['italy'], $template);
                            $template = str_replace("{indicator}", '<i class="fas fa-hourglass-end"></i>', $template);
                        $result .= $template;

                        $invoiceno++;
                    }
                }
                
                $sql3 = "SELECT * FROM invoices WHERE userid = " .  ($user["userId"]) . " AND NOT italy=1 AND daily=1 ORDER BY date DESC";
                //echo $sql3;
                if ($stmt3 = mysqli_prepare($link, $sql3)) {

                    mysqli_stmt_execute($stmt3)
                            or die("Unable to execute query: " . $stmt3->error);
                    //echo $sql3;
                    $rslt3 = mysqli_stmt_get_result($stmt3);
                    while ($rate = mysqli_fetch_array($rslt3)) {

                        $r = $rate == null ? 0 : $rate['rate'];
                        $c = $rate == null ? 0 : $rate['costs'];

                        $template = file_get_contents(("templates/_past_invoice_list.html"));
                        $template = str_replace("{avatar}", $usr['avatar'], $template);
                        $template = str_replace("{name}", $usr['realname'], $template);
                        $template = str_replace("{rate}", $r, $template);
                        $template = str_replace("{currency}", $mentor['currency'], $template);

                        $template = str_replace("{hours}", $rate['items'], $template);
                        $template = str_replace("{costs}", $rate['costs'], $template);
                        $totaldiv = ($_GET['func'] != "settlements") ?
                                '<div class="col-lg-1">
                                <div class="form-group">
                                    <label>Subtotal</label>
                                    <input class="au-input au-input--full" type="text" name="subtotal" value="{subtotal}"
                                        {readonly}>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Total</label>
                                    <input class="au-input au-input--full" type="text" name="total" value="{total}" {readonly}>
                                </div>
                            </div>' : "";

                        $template = str_replace("{totaldiv}", $totaldiv, $template);
                        $template = str_replace("{subtotal}", $rate['subtotal'], $template);
                        $template = str_replace("{total}", $rate['total'], $template);
                        $template = str_replace("{costs}", $rate['costs'], $template);
                        $template = str_replace("{url}", $rate['url'], $template);

                        $template = str_replace("{date}", date('Y-m-d', strtotime($rate['date'])), $template);
                        $formid = "update-invoice-form" . $rate['invoiceId'];
                        $template = str_replace("{formid}", $formid, $template);
                        $template = str_replace("{userid}", $user['userId'], $template);
                        $template = str_replace("{invoiceid}", $rate['invoiceId'], $template);
                        $template = str_replace("{invoiceno}", $rate['invoiceId'], $template);
                        $buttons = '<button type="button" class="btn btn-success au-input--full" id="btn-submit" onclick="UpdateForm(\'#' . $formid . '\');">Update</button>';
                        $template = str_replace("{button}", $buttons, $template);
                        $template = str_replace("{daily}", $rate['daily'], $template);
                        $template = str_replace("{italy}", $rate['italy'], $template);
                            $template = str_replace("{indicator}", '<i class="fas fa-calendar-day"></i>', $template);


                        $result .= $template;

                        $invoiceno++;
                    }
                }
                
                $sql3 = "SELECT * FROM invoices WHERE userid = " .  ($user["userId"]) . " AND italy=1 AND NOT daily=1 ORDER BY date DESC";
                //echo $sql3;
                if ($stmt3 = mysqli_prepare($link, $sql3)) {

                    mysqli_stmt_execute($stmt3)
                            or die("Unable to execute query: " . $stmt3->error);
                    //echo $sql3;
                    $rslt3 = mysqli_stmt_get_result($stmt3);
                    while ($rate = mysqli_fetch_array($rslt3)) {

                        $r = $rate == null ? 0 : $rate['rate'];
                        $c = $rate == null ? 0 : $rate['costs'];

                        $template = file_get_contents(("templates/_past_invoice_list.html"));
                        $template = str_replace("{avatar}", $usr['avatar'], $template);
                        $template = str_replace("{name}", $usr['realname'], $template);
                        $template = str_replace("{rate}", $r, $template);
                        $template = str_replace("{currency}", $mentor['currency'], $template);

                        $template = str_replace("{hours}", $rate['items'], $template);
                        $template = str_replace("{costs}", $rate['costs'], $template);
                        $totaldiv = ($_GET['func'] != "settlements") ?
                                '<div class="col-lg-1">
                                <div class="form-group">
                                    <label>Subtotal</label>
                                    <input class="au-input au-input--full" type="text" name="subtotal" value="{subtotal}"
                                        {readonly}>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Total</label>
                                    <input class="au-input au-input--full" type="text" name="total" value="{total}" {readonly}>
                                </div>
                            </div>' : "";

                        $template = str_replace("{totaldiv}", $totaldiv, $template);
                        $template = str_replace("{subtotal}", $rate['subtotal'], $template);
                        $template = str_replace("{total}", $rate['total'], $template);
                        $template = str_replace("{costs}", $rate['costs'], $template);
                        $template = str_replace("{url}", $rate['url'], $template);

                        $template = str_replace("{date}", date('Y-m-d', strtotime($rate['date'])), $template);
                        $formid = "update-invoice-form" . $rate['invoiceId'];
                        $template = str_replace("{formid}", $formid, $template);
                        $template = str_replace("{userid}", $user['userId'], $template);
                        $template = str_replace("{invoiceid}", $rate['invoiceId'], $template);
                        $template = str_replace("{invoiceno}", $rate['invoiceId'], $template);
                        $buttons = '<button type="button" class="btn btn-success au-input--full" id="btn-submit" onclick="UpdateForm(\'#' . $formid . '\');">Update</button>';
                        $template = str_replace("{button}", $buttons, $template);
                        $template = str_replace("{daily}", $rate['daily'], $template);
                        $template = str_replace("{italy}", $rate['italy'], $template);
                            $template = str_replace("{indicator}", '<i class="fas fa-italic"></i>', $template);


                        $result .= $template;

                        $invoiceno++;
                    }
                }
                
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
