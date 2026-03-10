<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $db = new Database();
    $link = $db->connect();

    $checksql = "SELECT * FROM invoices WHERE userid = " . $_POST['userid'] . " AND Month(date) in (" . date('m', strtotime('last day of previous month')) . ")  AND YEAR(date) = " . date('Y', strtotime('last day of previous month'));
    //echo $checksql;
    if ($stmt = mysqli_prepare($link, $checksql)) {

        mysqli_stmt_execute($stmt)
                or die("Unable to execute query: " . $stmt->error);

        $rslt = mysqli_stmt_get_result($stmt);
        while ($check = mysqli_fetch_array($rslt)) {
            $sqldel = "DELETE FROM invoices WHERE invoiceId = " . $check["invoiceId"];
            if ($stmt2 = mysqli_prepare($link, $sqldel)) {

                mysqli_stmt_execute($stmt2)
                        or die("Unable to execute query: " . $stmt2->error);
            }
        }
    }

    $sql = "INSERT INTO invoices (userid,date,rate,items,costs,subtotal,total,daily,italy) VALUES (?,?,?,?,?,?,?,?,?)";

    $stmt3 = mysqli_prepare($link, $sql);

    $userid = mysqli_real_escape_string($link, $_POST['userid']);
    $date = mysqli_real_escape_string($link, $_POST['date']);
    $items = empty(mysqli_real_escape_string($link, $_POST['hours'])) ? 0 : mysqli_real_escape_string($link, $_POST['hours']);

    $daily = empty(mysqli_real_escape_string($link, $_POST['daily'])) ? 0 : mysqli_real_escape_string($link, $_POST['daily']);
    $italy = empty(mysqli_real_escape_string($link, $_POST['italy'])) ? 0 : mysqli_real_escape_string($link, $_POST['italy']);

    $costs = empty(mysqli_real_escape_string($link, $_POST['costs'])) ? 0 : mysqli_real_escape_string($link, $_POST['costs']);

    $rate = empty(mysqli_real_escape_string($link, $_POST['rate'])) ? 0 : mysqli_real_escape_string($link, $_POST['rate']);


    $subtotal = $rate * $items;
    $total = $costs + $subtotal;

    if ($total > 0) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt3, "isdddddii", $userid, $date, $rate, $items, $costs, $subtotal, $total, $daily, $italy);

        if (mysqli_stmt_execute($stmt3)) {
            echo "saved";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }

        $invoiceid = mysqli_insert_id($link);

        mysqli_stmt_close($stmt3);

        mysqli_close($link);

        generatePDF($userid, $date, $rate, $items, $costs, $subtotal, $total, $invoiceid, $daily, $italy);
    }
}

function generatePDF($userid, $date, $rate, $hours, $costs, $subtotal, $total, $invoiceid, $daily, $italy) {
    $functions = new Functions();
    $db = new Database();
    $link = $db->connect();

    $sql = "SELECT * FROM users WHERE userId = " . $userid . " AND userPrivilege = 5";

    $invoiceno = 1;

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

                    $template = $costs == 0 ? file_get_contents("../templates/_tutor_no_costs.html") : file_get_contents("../templates/_tutor.html");
                    $content = str_replace("{realname}", $user['userName'] . " " . $user['userSurname'], $template);
                    $content = str_replace("{address}", $mentor['street'] . " " . $mentor['streetno'] . "/" . $mentor['localno'], $content);
                    $content = str_replace("{town}", $mentor['postalcode'] . " " . $mentor['town'], $content);
                    $content = str_replace("{account}", $mentor['accountno'], $content);

                    $content = str_replace("{bankname}", $mentor['bankname'], $content);
                    $content = str_replace("{iban}", $mentor['iban'], $content);
                    $content = str_replace("{swift}", $mentor['swift'], $content);


                    $content = str_replace("{date}", $date, $content);
                    $content = str_replace("{invoiceno}", $_POST['invoiceno'] . "/" . date('F/Y', strtotime('last day of previous month')), $content);

                    $content = str_replace("{invoicetype}", "Tutor", $content);
                    $content = str_replace("{period}", date('F Y', strtotime('last day of previous month')), $content);

                    $content = str_replace("{currency}", $mentor['currency'], $content);
                    $content = str_replace("{rate}", number_format($rate, 2), $content);
                    $content = str_replace("{hours}", $hours, $content);

                    $content = str_replace("{reimbursement}", number_format($costs, 2), $content);
                    $content = str_replace("{subtotal}", number_format($subtotal, 2), $content);
                    $content = str_replace("{costsubtotal}", number_format($costs, 2), $content);

                    $content = str_replace("{total}", $costs == 0 ? number_format($subtotal, 2) : number_format($total, 2), $content);
                    $content = str_replace("{hours}", $hours, $content);
                }
            }
        }
    }

    $yfolder = date('Y', strtotime('last day of previous month'));
    $mfolder = date('F', strtotime('last day of previous month'));

    if (!is_dir('../invoices')) {
        mkdir('../invoices', 0777, true);
    }
    if (!is_dir('../invoices/' . $yfolder)) {
        mkdir('../invoices/' . $yfolder, 0777, true);
    }
    if (!is_dir('../invoices/' . $yfolder . '/' . $mfolder)) {
        mkdir('../invoices/' . $yfolder . '/' . $mfolder, 0777, true);
    }


    require('../TCPDF/tcpdf.php');
    $tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set default monospaced font
    $tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set title of pdf
    $tcpdf->SetTitle($usr['realname']);

    // set margins
    $tcpdf->SetMargins(15, 15, 15, 15);
    $tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set header and footer in pdf
    $tcpdf->setPrintHeader(false);
    $tcpdf->setPrintFooter(false);
    $tcpdf->setListIndentWidth(3);

    // set auto page breaks
    $tcpdf->SetAutoPageBreak(TRUE, 11);

    // set image scale factor
    $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $tcpdf->AddPage();

    $tcpdf->SetFont('times', '', 10.5);

    $tcpdf->writeHTML($content, true, false, false, false, '');

    $functions->SaveUrl($usr['realname'], $usr['email'], $_POST['send'], $daily, $italy, $invoiceid, "/admin/invoices/" . $yfolder . "/" . $mfolder . "/Invoice_" . str_replace(" ", "_", $usr['realname']) . "_" . date('F_Y', strtotime('last day of previous month')) . '.pdf');

    //Close and output PDF document
    $tcpdf->Output("../invoices/" . $yfolder . "/" . $mfolder . "/Invoice_" . str_replace(" ", "_", $usr['realname']) . "_" . date('F_Y', strtotime('last day of previous month')) . '.pdf', 'F');
}
