<?php

require_once('includes/functions.php');

$functions = new Functions;

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

$sql = "SELECT * FROM users WHERE recordId = " . $_SESSION['recid'] . " AND userPrivilege = 5";

$invoiceno = 1;

$result = '<div id="error">
        </div>
        <div class="col-lg-12">
    <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
        <div class="au-card-title">
            <div class="bg-overlay bg-overlay--blue"></div>
            <h3>
                <i class="zmdi zmdi-comment-text"></i>Tutor settlements</h3>
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

                $duration = 0;
                $sql3 = "SELECT * FROM invoices WHERE userid = " . $user["userId"] . " ORDER BY date DESC";
                if ($stmt3 = mysqli_prepare($link, $sql3)) {

                    mysqli_stmt_execute($stmt3)
                            or die("Unable to execute query: " . $stmt3->error);

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
                        $template = str_replace("{url}", $rate['url'], $template);

                        $template = str_replace("{date}", date('Y-m-d', strtotime($rate['date'])), $template);

                        $template = str_replace("{formid}", "update-invoice-form" . $rate['invoiceId'], $template);
                        $template = str_replace("{userid}", $user['userId'], $template);
                        $template = str_replace("{invoiceid}", $rate['invoiceId'], $template);
                        $template = str_replace("{invoiceno}", $rate['invoiceId'], $template);
                        $readonly = ($_GET['func'] == "settlements") ? "readonly" : "";
                        $template = str_replace("{totaldiv}", '<div class="col-lg-1">
                                <div class="form-group">
                                    <label>Subtotal</label>
                                    <input class="au-input au-input--full" type="text" name="subtotal" value="' . $rate['subtotal'] . '"
                                        ' . $readonly . '>
                                </div></div>', $template);

                        $template = str_replace("{readonly}", $readonly, $template);
                        $button = ($_GET['func'] == "settlements") ?
                                "<label>Total</label><input class='au-input au-input--full' type='text' name='total'
                            value='" . $rate['total'] . "' readonly>" :
                                '<button type="button" class="btn btn-success au-input--full" id="btn-submit" onclick="UpdateForm(\'#update-invoice-form\');">Update</button>';
                        $template = str_replace("{button}", $button, $template);
                        if ($rate['daily'] == 0 && $rate['italy'] == 0) :
                            $indicator = '<i class="fas fa-hourglass-end"></i>';
                        elseif ($rate['daily'] == 1 && $rate['italy'] == 0):
                            $indicator = '<i class="fas fa-calendar-day"></i>';
                        else :
                            $indicator = '<i class="fas fa-italic"></i>';
                        endif;

                        $template = str_replace("{indicator}", $indicator, $template);
                        $result .= $template;

                        $invoiceno++;
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
