<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_POST) {
    $func = new Functions();
    $db = new Database();
    $link = $db->connect();

    $userid = mysqli_real_escape_string($link, $_POST['userid']);
    $mentorid = mysqli_real_escape_string($link, $_POST['mentorid']);

    $sql = "UPDATE mentors SET currency=?, street=?, streetno=?, localno=?, town=?, postalcode=?, phone=?, note=?, bankname=?, iban=?, swift=?, accountno=?, contract=?, bio=? WHERE mentorId = " . $mentorid;
    $stmt = mysqli_prepare($link, $sql);

    $p_currency = mysqli_real_escape_string($link, $_POST['currency']);
    $p_street = mysqli_real_escape_string($link, $_POST['street']);
    $p_streetno = mysqli_real_escape_string($link, $_POST['streetno']);
    $p_localno = mysqli_real_escape_string($link, $_POST['localno']);
    $p_town = mysqli_real_escape_string($link, $_POST['town']);
    $p_postalcode = mysqli_real_escape_string($link, $_POST['postalcode']);
    $p_note = mysqli_real_escape_string($link, $_POST['note']);    
    $p_bio = mysqli_real_escape_string($link, $_POST['bio']);    
    $p_phone = mysqli_real_escape_string($link, $_POST['phone']);

    $p_accountno = mysqli_real_escape_string($link, $_POST['accountno']);
    $p_contract = mysqli_real_escape_string($link, $_POST['contract']);
    $p_bank = mysqli_real_escape_string($link, $_POST['bankname']);
    $p_iban = mysqli_real_escape_string($link, $_POST['iban']);
    $p_swift = mysqli_real_escape_string($link, $_POST['swift']);



    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "ssssssssssssis", $p_currency, $p_street, $p_streetno, $p_localno, $p_town, $p_postalcode, $p_phone, $p_note, $p_bank, $p_iban, $p_swift, $p_accountno, $p_contract, $p_bio);

    if (mysqli_stmt_execute($stmt)) {
        $sql2 = "UPDATE users SET userEmail=? WHERE userId = " . $userid;
        $stmt2 = mysqli_prepare($link, $sql2);
        $u_email = mysqli_real_escape_string($link, $_POST['email']);

        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt2, "s", $u_email);

        if (mysqli_stmt_execute($stmt2)) {
            echo "saved";
        } else {
            echo "Error: " . $sql2 . "<br>" . mysqli_error($link);
        }

        mysqli_stmt_close($stmt2);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);



    mysqli_close($link);
} else {
    echo "Nothing to do";
}