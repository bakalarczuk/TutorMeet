<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $db = new Database();
    $link = $db->connect();

    $sql = "UPDATE invoices SET rate = ?,items = ?,costs = ?,subtotal = ?,total = ? WHERE invoiceId = " . $_POST['invoiceid'];

    $stmt3 = mysqli_prepare($link, $sql);

    $userid = mysqli_real_escape_string($link, $_POST['userid']);
    $rate = empty(mysqli_real_escape_string($link, $_POST['rate'])) ? 0 : mysqli_real_escape_string($link, $_POST['rate']);
    $items = empty(mysqli_real_escape_string($link, $_POST['hours'])) ? 0 : mysqli_real_escape_string($link, $_POST['hours']);
    $costs = empty(mysqli_real_escape_string($link, $_POST['costs'])) ? 0 : mysqli_real_escape_string($link, $_POST['costs']);

    $subtotal = $rate * $items;
    $total = $costs + $subtotal;

    if ($total > 0) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt3, "ddddd", $rate, $items, $costs, $subtotal, $total);

        if (mysqli_stmt_execute($stmt3)) {
            echo "saved";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }

        mysqli_stmt_close($stmt3);

        mysqli_close($link);

        generatePDF($userid, $_POST['invoiceid']);
    }
}

function generatePDF($userid, $invoiceid)
{
    $functions = new Functions();
    $db = new Database();
    $link = $db->connect();

    $sql = "SELECT * FROM users WHERE userId = " . $userid . " AND userPrivilege = 5";

    $invoiceno = 1;
    $yfolder = "";
    $mfolder = "";
    $content = "";

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
                    $sql3 = "SELECT * FROM invoices WHERE invoiceid = " . $invoiceid;
                    if ($stmt3 = mysqli_prepare($link, $sql3)) {

                        mysqli_stmt_execute($stmt3)
                            or die("Unable to execute query: " . $stmt3->error);

                        $rslt3 = mysqli_stmt_get_result($stmt3);
                        while ($invoice = mysqli_fetch_array($rslt3)) {
                            $template = $invoice['costs'] == 0 ? file_get_contents("../templates/_tutor_no_costs.html") : file_get_contents("../templates/_tutor.html");
                            $content = str_replace("{realname}", $user['userName'] . " " . $user['userSurname'], $template);
                            $content = str_replace("{address}", $mentor['street'] . " " . $mentor['streetno'] . "/" . $mentor['localno'], $content);
                            $content = str_replace("{town}", $mentor['postalcode'] . " " . $mentor['town'], $content);
                            $content = str_replace("{account}", $mentor['accountno'], $content);


                            $content = str_replace("{date}", date("Y-m-d", strtotime($invoice['date'])), $content);
                            $content = str_replace("{invoiceno}", $invoice['invoiceno'] . "/" . date('F/Y', strtotime($invoice['date'])), $content);

                            $content = str_replace("{invoicetype}", $mentor['contract'] == 1 ? "Tutor" : "Tutor", $content);
                            $content = str_replace("{period}", date('F Y', strtotime($invoice['date'])), $content);

                            $content = str_replace("{currency}", $mentor['currency'], $content);
                            $content = str_replace("{rate}", number_format($invoice['rate'], 2), $content);
                            $content = str_replace("{hours}", $invoice['items'], $content);

                            $content = str_replace("{reimbursement}", number_format($invoice['costs'], 2), $content);
                            $content = str_replace("{subtotal}", number_format($invoice['subtotal'], 2), $content);
                            $content = str_replace("{costsubtotal}", number_format($invoice['costs'], 2), $content);

                            $content = str_replace("{total}", $invoice['costs'] == 0 ? number_format($invoice['subtotal'], 2) : number_format($invoice['total'], 2), $content);
                            $content = str_replace("{hours}", $invoice['items'], $content);

                            $yfolder = date(
                                'Y',
                                strtotime($invoice['date'])
                            );
                            $mfolder = date(
                                'F',
                                strtotime($invoice['date'])
                            );

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

                            $functions->SaveUrl($invoiceid, "/admin/invoices/" . $yfolder . "/" . $mfolder . "/Invoice_" . $usr['realname'] . "_" . date('F_Y', strtotime('last day of previous month')) . '.pdf');

                            //Close and output PDF document
                            $tcpdf->Output("../invoices/" . $yfolder . "/" . $mfolder . "/Invoice_" . $usr['realname'] . "_" . date('F_Y', strtotime('last day of previous month')) . '.pdf', 'F');
                        }
                    }
                }
            }
        }
    }
}