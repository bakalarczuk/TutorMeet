<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';


if ($_POST) {
    $functions = new Functions();
    $db = new Database();

    $link = $db->connect();

    $username = mysqli_real_escape_string($link, $_POST['email']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = password_hash(mysqli_real_escape_string($link, $_POST['password']), PASSWORD_DEFAULT);
    $userPrivilege = mysqli_real_escape_string($link, $_POST['privilege']);
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $lastname = mysqli_real_escape_string($link, $_POST['lastname']);
    $joining_date = date('Y-m-d H:i:s');
    $sql = "SELECT * FROM users WHERE userEmail = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_useremail);

        // Set parameters
        $param_useremail = $email;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            // Check if username exists, if yes then verify password
            if (mysqli_stmt_num_rows($stmt) == 1) {
                echo "1";
            } else {
                // Insert user
                if ($userPrivilege == 5) {
                    $sql = "INSERT INTO mentors (currency, street, streetno, localno, town, postalcode,phone,bankname, iban, swift, note, accountno, contract)
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $stmt2 = mysqli_prepare($link, $sql);

                    $p_currency = mysqli_real_escape_string($link, $_POST['currency']);
                    $p_street = mysqli_real_escape_string($link, $_POST['street']);
                    $p_streetno = mysqli_real_escape_string($link, $_POST['streetno']);
                    $p_localno = mysqli_real_escape_string($link, $_POST['localno']);
                    $p_town = mysqli_real_escape_string($link, $_POST['town']);
                    $p_postalcode = mysqli_real_escape_string($link, $_POST['postalcode']);
                    $p_note = mysqli_real_escape_string($link, $_POST['note']);
                    $p_accountno = mysqli_real_escape_string($link, $_POST['account']);
                    $p_contract = mysqli_real_escape_string($link, $_POST['contract']);
                    $p_phone = mysqli_real_escape_string($link, $_POST['phone']);

                    $p_iban = mysqli_real_escape_string($link, $_POST['iban']);
                    $p_swift = mysqli_real_escape_string($link, $_POST['swift']);
                    $p_bankname = mysqli_real_escape_string($link, $_POST['bankname']);

                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt2, "ssssssssssssi", $p_currency, $p_street, $p_streetno, $p_localno, $p_town, $p_postalcode, $p_phone, $p_bankname, $p_iban, $p_swift, $p_note, $p_accountno, $p_contract);

                    if (mysqli_stmt_execute($stmt2)) {

                        $recordid = mysqli_stmt_insert_id($stmt2);

                        $sql = "INSERT INTO users (userLogin, userPass, userName, userSurname, userEmail, userPrivilege, joiningDate, recordId)
                                        VALUES (?,?,?,?,?,?,?,?)";
                        $stmt3 = mysqli_prepare($link, $sql);

                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt3, "sssssisi", $username, $password, $name, $lastname, $email, $userPrivilege, $joining_date, $recordid);

                        if (mysqli_stmt_execute($stmt3)) {
                            $userid = mysqli_stmt_insert_id($stmt3);

                            $sql2 = "INSERT INTO rates (userid, rate, daily)
                                        VALUES (?,?,?)";
                            $stmt4 = mysqli_prepare($link, $sql2);

                            // Bind variables to the prepared statement as parameters
                            mysqli_stmt_bind_param($stmt4, "iss", $recordid, $_POST['rate'], $_POST['daily']);
                            if (mysqli_stmt_execute($stmt4)) {

                                echo "registered";
                                $msg = "Registration to TutorMeet system. Your login is ".$email.", and temporary password is: " . $_POST['password'] . ".<br>Change it at first login on https://" . $_SERVER['SERVER_NAME'];
                                $functions->SendEmail($email, "TutorMeet Registration", $msg);
                            } else {
                                echo "Error: " . $sql2 . "<br>" . mysqli_error($link);
                            }
                        } else {
                            echo "Error: " . $sql . "<br>" . mysqli_error($link);
                        }
                        mysqli_stmt_close($stmt3);
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($link);
                    }
                    mysqli_stmt_close($stmt2);
                } elseif ($userPrivilege == 6) {
                    $sql = "INSERT INTO aplicants (street, streetno, localno, town, postalcode, phone,  type, programm, studiesyear,birthyear,language)
                        VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                    $stmt2 = mysqli_prepare($link, $sql);

                    $p_street = mysqli_real_escape_string($link, $_POST['street']);
                    $p_streetno = mysqli_real_escape_string($link, $_POST['streetno']);
                    $p_localno = mysqli_real_escape_string($link, $_POST['localno']);
                    $p_town = mysqli_real_escape_string($link, $_POST['town']);
                    $p_postalcode = mysqli_real_escape_string($link, $_POST['postalcode']);
                    $p_type = mysqli_real_escape_string($link, $_POST['type']);
                    $p_programm = mysqli_real_escape_string($link, $_POST['programm']);
                    $p_studiesyear = mysqli_real_escape_string($link, $_POST['studiesyear']);
                    $p_birthyear = mysqli_real_escape_string($link, $_POST['birthyear']);
                    $p_language = mysqli_real_escape_string($link, $_POST['language']);
                    $p_phone = mysqli_real_escape_string($link, $_POST['phone']);

                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt2, "sssssssssss", $p_street, $p_streetno, $p_localno, $p_town, $p_postalcode, $p_phone, $p_type
                            , $p_programm, $p_studiesyear, $p_birthyear, $p_language);

                    if (mysqli_stmt_execute($stmt2)) {

                        $recordid = mysqli_stmt_insert_id($stmt2);

                        $sql = "INSERT INTO users (userLogin, userPass, userName, userSurname, userEmail, userPrivilege, joiningDate, recordId)
                                        VALUES (?,?,?,?,?,?,?,?)";
                        $stmt3 = mysqli_prepare($link, $sql);

                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt3, "sssssisi", $username, $password, $name, $lastname, $email, $userPrivilege, $joining_date, $recordid);

                        if (mysqli_stmt_execute($stmt3)) {
                            echo "registered";
                            $msg = "Registration to TutorMeet system. Your login is ".$email.", and temporary password is: " . $_POST['password'] . ". Change it at first login on https://" . $_SERVER['SERVER_NAME'];
                            $functions->SendEmail($email, "TutorMeet Registration", $msg);
                        } else {
                            echo "Error: " . $sql . "<br>" . mysqli_error($link);
                        }
                        mysqli_stmt_close($stmt3);
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($link);
                    }
                    mysqli_stmt_close($stmt2);
                } elseif ($userPrivilege == 9) {
                    $sql = "INSERT INTO parents (street, streetno, localno, town, postalcode, aplicantid)
                        VALUES (?,?,?,?,?,?)";
                    $stmt2 = mysqli_prepare($link, $sql);

                    $p_street = mysqli_real_escape_string($link, $_POST['street']);
                    $p_streetno = mysqli_real_escape_string($link, $_POST['streetno']);
                    $p_localno = mysqli_real_escape_string($link, $_POST['localno']);
                    $p_town = mysqli_real_escape_string($link, $_POST['town']);
                    $p_postalcode = mysqli_real_escape_string($link, $_POST['postalcode']);
                    $p_aplicant = mysqli_real_escape_string($link, $_POST['aplicant']);

                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt2, "sssssi", $p_street, $p_streetno, $p_localno, $p_town, $p_postalcode, $p_aplicant);

                    if (mysqli_stmt_execute($stmt2)) {

                        $recordid = mysqli_stmt_insert_id($stmt2);

                        $sql = "INSERT INTO users (userLogin, userPass, userName, userSurname, userEmail, userPrivilege, joiningDate, recordId)
                                        VALUES (?,?,?,?,?,?,?,?)";
                        $stmt3 = mysqli_prepare($link, $sql);

                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt3, "sssssisi", $username, $password, $name, $lastname, $email, $userPrivilege, $joining_date, $recordid);

                        if (mysqli_stmt_execute($stmt3)) {
                            echo "registered";
                            $msg = "Registration to TutorMeet system. Your login is ".$email.", and temporary password is: " . $_POST['password'] . ". Change it at first login on https://" . $_SERVER['SERVER_NAME'];
                            $functions->SendEmail($email, "TutorMeet Registration", $msg);
                        } else {
                            echo "Error: " . $sql . "<br>" . mysqli_error($link);
                        }
                        mysqli_stmt_close($stmt3);
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($link);
                    }
                    mysqli_stmt_close($stmt2);
                } else {

                    $sql = "INSERT INTO users (userLogin, userPass, userName, userSurname, userEmail, userPrivilege) "
                            . "VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt4 = mysqli_prepare($link, $sql);
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt4, "sssssi", $username, $password, $name, $lastname, $email, $userPrivilege);

                    if (mysqli_stmt_execute($stmt4)) {
                        echo "registered";
                        $msg = "Registration to TutorMeet system. Your login is ".$email.", and temporary password is: " . $_POST['password'] . ". Change it at first login on https://" . $_SERVER['SERVER_NAME'];
                        $functions->SendEmail($email, "TutorMeet Registration", $msg);
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($link);
                    }
                    mysqli_stmt_close($stmt4);
                }
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}