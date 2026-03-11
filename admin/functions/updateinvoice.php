<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $db = new Database();
    $link = $db->connect();

    $sql = "UPDATE invoices SET rate = ?,items = ?,costs = ?,subtotal = ?,total = ? WHERE invoiceId = ?";

    $stmt3 = mysqli_prepare($link, $sql);

    $userid = intval($_POST['userid']);
    $invoiceid_param = intval($_POST['invoiceid']);
    $rate = empty($_POST['rate']) ? 0 : floatval($_POST['rate']);
    $items = empty($_POST['hours']) ? 0 : floatval($_POST['hours']);
    $costs = empty($_POST['costs']) ? 0 : floatval($_POST['costs']);

    $subtotal = $rate * $items;
    $total = $costs + $subtotal;

    if ($total > 0) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt3, "dddddi", $rate, $items, $costs, $subtotal, $total, $invoiceid_param);

        if (mysqli_stmt_execute($stmt3)) {
            echo "saved";
        } else {
            error_log("SQL Error: " . mysqli_error($link));
            echo "Error processing request.";
        }

        mysqli_stmt_close($stmt3);

        mysqli_close($link);

        generatePDF($userid, $invoiceid_param);
    }
}

function generatePDF($userid, $invoiceid)
{
    $functions = new Functions();
    $db = new Database();
    $link = $db->connect();

    $sql = "SELECT * FROM users WHERE userId = ? AND userPrivilege = 5";

    $invoiceno = 1;
    $yfolder = "";
    $mfolder = "";
    $content = "";
    $userid_int = intval($userid);

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $userid_int);

        mysqli_stmt_execute($stmt)
            or die("Unable to execute query.");

        $rslt = mysqli_stmt_get_result($stmt);
        while ($user = mysqli_fetch_array($rslt)) {
            $usr = $functions->GetUser($user['userId'], "userId");
            $sql2 = "SELECT * FROM mentors WHERE mentorId = ?";
            if ($stmt2 = mysqli_prepare($link, $sql2)) {
                $record_id = intval($user['recordId']);
                mysqli_stmt_bind_param($stmt2, "i", $record_id);

                mysqli_stmt_execute($stmt2)
                    or die("Unable to execute query.");

                $rslt2 = mysqli_stmt_get_result($stmt2);
                while ($mentor = mysqli_fetch_array($rslt2)) {
                    $sql3 = "SELECT * FROM invoices WHERE invoiceid = ?";
                    if ($stmt3 = mysqli_prepare($link, $sql3)) {
                        $invoiceid_int = intval($invoiceid);
                        mysqli_stmt_bind_param($stmt3, "i", $invoiceid_int);

                        mysqli_stmt_execute($stmt3)
                            or die("Unable to execute query.");

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
                                mkdir('../invoices', 0755, true);
                            }
                            if (!is_dir('../invoices/' . $yfolder)) {
                                mkdir('../invoices/' . $yfolder, 0755, true);
                            }
                            if (!is_dir('../invoices/' . $yfolder . '/' . $mfolder)) {
                                mkdir('../invoices/' . $yfolder . '/' . $mfolder, 0755, true);
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