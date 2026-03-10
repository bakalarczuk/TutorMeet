<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';

// Include config file
require_once "config.php";

class Functions {

    function GetPrivileges() {
        $db = new Database();
        $link = $db->connect();
        $privilege = "<select class='au-input--full au-select' id='privilege' name='privilege'>";
        $r = $_SESSION['privilege'] == 1 ? 1 : $_SESSION['privilege'] + 1;
        $sql = "SELECT * FROM privileges WHERE privilegeId >= ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $r);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $privilege .= "<option value=''>Select</option>";

            while ($row = mysqli_fetch_array($rslt)) {
                if ($row['privilegeId'] < 7)
                    $privilege .= "<option value='" . $row['privilegeId'] . "'>" . $row['privilegeName'] . "</option>";
            }
            $privilege .= "</select>";
            mysqli_stmt_close($stmt);
        } else {
            $privilege = "Oops! Something went wrong. Please try again later.";
        }

        mysqli_close($link);
        return $privilege;
    }

    function PrivilegeName($id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "SELECT * FROM privileges WHERE privilegeId = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $priv_id);

            // Set parameters
            $priv_id = $id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $name);
                    if (mysqli_stmt_fetch($stmt)) {
                        return $name;
                    }
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
        // Close connection
        mysqli_close($link);
    }

    function GetAplicants($multi = false) {
        $multiple = $multi == true ? "multiple" : "";
        $multisize = $multi == true ? "size = '5'" : "";
        $size = $multi == true ? "" : "au-select";
        $name = $multi == true ? "aplicant[]" : "aplicant";
        $db = new Database();
        $link = $db->connect();
        $privilege = "<select class='au-input--full " . $size . "' " . $multisize . " id='aplicant' name='" . $name . "' " . $multiple . ">";

        if (isset($_SESSION['recid'])) {
            $sql = "SELECT * FROM assignments WHERE mentorid = ?";
        } else {
            $sql = "SELECT * FROM assignments";
        }

        if ($stmt = mysqli_prepare($link, $sql)) {
            if (isset($_SESSION['recid'])) {
                mysqli_stmt_bind_param($stmt, "i", $_SESSION['recid']);
            }
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_array($rslt)) {
                $user = $this->GetUserExt($row['aplicantid'], 6);
                $privilege .= "<option value='" . $row['aplicantid'] . "'>" . $user['realname'] . "</option>";
            }
            $privilege .= "</select>";
            mysqli_stmt_close($stmt);
        } else {
            $privilege = "Oops! Something went wrong. Please try again later.";
        }

        mysqli_close($link);
        return $privilege;
    }

    function GetMentors($filter = 0) {
        $privilege = "<select class='au-input--full au-select' id='mentor' name='mentorid'>";
        $sql = "SELECT * FROM users WHERE userPrivilege = 5";
        $db = new Database();
        $link = $db->connect();

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            if ($filter == 1) {
                $privilege .= "<option value=''>All Tutors</option>";
            }
            while ($row = mysqli_fetch_array($rslt)) {
                $privilege .= "<option value='" . $row['recordId'] . "'>" . $row['userName'] . " " . $row['userSurname'] . "</option>";
            }
            $privilege .= "</select>";
            mysqli_stmt_close($stmt);
        } else {
            $privilege = "Oops! Something went wrong. Please try again later.";
        }

        mysqli_close($link);
        return $privilege;
    }

    function GetAllApplicants($filter = 0) {
        $privilege = "<select class='au-input--full au-select' id='aplicant' name='aplicant'>";
        $sql = "SELECT * FROM users WHERE userPrivilege = 6";
        $db = new Database();
        $link = $db->connect();

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            if ($filter == 1) {
                $privilege .= "<option value=''>All Tutors</option>";
            }
            while ($row = mysqli_fetch_array($rslt)) {
                $privilege .= "<option value='" . $row['recordId'] . "'>" . $row['userName'] . " " . $row['userSurname'] . "</option>";
            }
            $privilege .= "</select>";
            mysqli_stmt_close($stmt);
        } else {
            $privilege = "Oops! Something went wrong. Please try again later.";
        }

        mysqli_close($link);
        return $privilege;
    }

    function GetDatas() {
        $db = new Database();
        $link = $db->connect();
        $privilege = "<select class='au-input--full au-select' name='recipient'>";
        $sql = "SELECT DISTINCT YEAR(date) AS Year, MONTH(date) AS Month FROM invoices";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $privilege .= "<option value=''>All Periods</option>";
            $rslt = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_array($rslt)) {
                $privilege .= "<option value='" . $row['Month'] . " " . $row['Year'] . "'>" . $row['Month'] . " " . $row['Year'] . "</option>";
            }
            $privilege .= "</select>";
            mysqli_stmt_close($stmt);
        } else {
            $privilege = "Oops! Something went wrong. Please try again later.";
        }

        mysqli_close($link);
        return $privilege;
    }

    function GetRecipients() {
        $db = new Database();
        $link = $db->connect();
        $privilege = "<select class='au-input--full au-select' id='recipient' name='recipient'>";
        $sql = "SELECT * FROM users";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_array($rslt)) {
                $privilege .= "<option value='" . $row['userId'] . "'>" . $row['userName'] . " " . $row['userSurname'] . "</option>";
            }
            $privilege .= "</select>";
            mysqli_stmt_close($stmt);
        } else {
            $privilege = "Oops! Something went wrong. Please try again later.";
        }

        mysqli_close($link);
        return $privilege;
    }

    function GetMessages() {
        $db = new Database();
        $link = $db->connect();
        $messages = '<div class="col-lg-12">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url(\"images/bg-title-02.jpg\");">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                        <h3>
                                            <i class="zmdi zmdi-comment-text"></i>Inbox</h3>
                                        <button class="au-btn-plus" onclick="window.location.href = \'message\';">
                                            <i class="zmdi zmdi-plus"></i>
                                        </button>
                                    </div>
                                        <div class="au-inbox-wrap js-inbox-wrap">
                                            <div class="au-message js-list-load">';
        $sql = "SELECT * FROM notifications WHERE recipient = ? ORDER BY date DESC";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $found = false;
            while ($row = mysqli_fetch_array($rslt)) {
                $found = true;
                $user = $this->GetUser($row["sender"], "userId");
                $messages .= '
    <div class="card">
        <div class="card-body">
            <div class="au-message__item-text">
                <div class="text">
                    <h5 class="name">' . htmlspecialchars($user['realname'], ENT_QUOTES, 'UTF-8') . '</h5>
                    <p>' . htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') . '</p>
                </div>
            </div>
            <div class="au-message__item-time">
                <span>' . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . '</span>
            </div>
        <div class="text">' . htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8') . '</div>
        <div class="text">Attached file: <a href="' . str_replace("..", "//" . $_SERVER['HTTP_HOST'] . "/admin", $row['attachement']) . '" target="_blank">' . str_replace("../uploads/", "", $row['attachement']) . '</a></div>
        </div>
                </div>';
            }
            if (!$found) {
                $messages .= 'No data</div>
                </div>
                </div>
                </div>';
            } else {
                $messages .= '</div>
                </div>
                </div>
                </div>';
            }
            mysqli_stmt_close($stmt);
        } else {
            return "Oops! Something went wrong. Please try again later.";
        }

        mysqli_close($link);
        return $messages;
    }

    function GetUsers() {
        $db = new Database();
        $link = $db->connect();
        $messages = '<div class="col-lg-12" id="allusersdiv">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url(\"images/bg-title-02.jpg\");">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                    <h3>
                                            <i class="zmdi zmdi-comment-text"></i>All Users</h3>
                                        <button class="au-btn-plus" onclick="window.location.href = \'adduser\';">
                                            <i class="zmdi zmdi-plus"></i>
                                        </button>
                                    </div>
                                        <div class="au-inbox-wrap js-inbox-wrap">
                                            <div class="au-message js-list-load">';
        $sql = "SELECT * FROM users ORDER BY userSurname DESC";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $found = false;
            while ($row = mysqli_fetch_array($rslt)) {
                $found = true;
                $user = $this->GetUser($row["userId"], "userId");
                $blocked = $user["blocked"] == 1 ? "bg-danger-light" : "";
                $btntext = $user["blocked"] == 1 ? "Unblock" : "Block";
                $blockbtnaction = $user["blocked"] == 1 ? "UpdateUser(0, " . $user["id"] . ")" : "UpdateUser(1, " . $user["id"] . ")";
                $messages .= '<div id="error">
</div>
    <div class="card">
        <div class="card-body ' . $blocked . '">
                        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                <div class="text">
                    <h5 class="name">' . $user['realname'] . '</h5>
                </div>
            </div>
        </div>
            <div class="col-lg-2">
                            <div class="form-group">
                                <label></label>
                                <button type="button" class="btn btn-success au-input--full" id="btn-submit"
                                    onclick="' . $blockbtnaction . '">' . $btntext . '</button>
                            </div>
</div>
            <div class="col-lg-2">
                            <div class="form-group">
                                <label></label>
                                <button type="button" class="btn btn-success au-input--full" id="btn-submit"
                                    onclick="DeleteUser(' . $user["id"] . ',\'' . $user['realname'] . '\')">Remove</button>
                            </div>
</div>
            <div class="col-lg-2">
                            <div class="form-group">
                                <label></label>
                                <button type="button" class="btn btn-success au-input--full" id="btn-submit"
                                    onclick="GenerateNewPass(' . $user["id"] . ',\'' . $user['realname'] . '\')">Reset Pass</button>
                            </div>
</div>
                </div>
        </div>
                </div>';
            }
            if (!$found) {
                $messages .= 'No data</div>
                </div>
                </div>
                </div>';
            } else {
                $messages .= '</div>
                </div>
                </div>
                </div>';
            }
            mysqli_stmt_close($stmt);
        } else {
            return "Oops! Something went wrong. Please try again later.";
        }

        mysqli_close($link);
        return $messages;
    }

    function GetUser($id, $columnName, $message = "") {
        $db = new Database();
        $link = $db->connect();
        // Prepare a select statement
        $sql = "SELECT userId, userLogin, userPass, userName, userSurname, userEmail, userPrivilege, recordId, blocked FROM users WHERE " . $columnName . " = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_userid);

            // Set parameters
            $param_userid = ($id);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $userRealName, $userRealSurname, $userEmail, $userPrivilege, $recordid, $blocked);
                    if (mysqli_stmt_fetch($stmt)) {
                        $user = array();
                        $user["id"] = $id;
                        $user["recid"] = $recordid;
                        $user["username"] = $username;
                        $user["email"] = $userEmail;
                        $user["realname"] = $userRealName . " " . $userRealSurname;
                        $user["privilege"] = $userPrivilege;
                        $user["avatar"] = $this->GetGravatar($userEmail, $user['realname']);
                        $user["blocked"] = $blocked;
                        return $user;
                    }
                } else {
                    // Display an error message if username doesn't exist
                    return $message;
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
        // Close connection
        mysqli_close($link);
    }

    function GetUserExt($id, $privilege) {
        $db = new Database();
        $link = $db->connect();
        // Prepare a select statement
        $sql = "SELECT userId, userLogin, userPass, userName, userSurname, userEmail, userPrivilege, recordId FROM users WHERE recordId = ? AND userPrivilege = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_userid, $param_privilege);

            // Set parameters
            $param_userid = ($id);
            $param_privilege = ($privilege);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $userRealName, $userRealSurname, $userEmail, $userPrivilege, $recordId);
                    if (mysqli_stmt_fetch($stmt)) {
                        $user = array();
                        $user["id"] = $id;
                        $user["username"] = $username;
                        $user["realname"] = $userRealName . " " . $userRealSurname;
                        $user["privilege"] = $userPrivilege;
                        $user["email"] = $userEmail;
                        $user["recid"] = $recordId;
                        $user["avatar"] = $this->GetGravatar($userEmail, $user['realname']);
                        return $user;
                    }
                } else {
                    // Display an error message if username doesn't exist
                    echo "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
        // Close connection
        mysqli_close($link);
    }

    function GetEmail($id) {
        $db = new Database();
        $link = $db->connect();
        // Prepare a select statement
        $sql = "SELECT userEmail FROM users WHERE userLogin = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_userid);

            // Set parameters
            $param_userid = ($id);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $userEmail);
                    if (mysqli_stmt_fetch($stmt)) {
                        return $userEmail;
                    }
                } else {
                    return "Wrong user";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
        // Close connection
        mysqli_close($link);
    }

    function GetGravatar($email, $name) {
        $email = md5(strtolower(trim($email)));
        $gravurl = "http://www.gravatar.com/avatar/$email?&s=360";
        return '<img src="' . $gravurl . '" alt="' . $name . '">';
    }

    function GetHours($seconds) {
        if (empty($seconds) || !is_numeric($seconds)) {
            return false;
        }

        $minutes = round($seconds / 60);
        $hours = floor($minutes / 60);
        $remainMinutes = ($minutes % 60) / 60;

        return round($hours + $remainMinutes, 2);
    }

    function GetHourSessions($mentorid) {
        $db = new Database();
        $link = $db->connect();
        $month = (int) date('m', strtotime('last day of previous month'));
        $year = (int) date('Y', strtotime('last day of previous month'));
        $sql = "SELECT sessions.duration AS dur FROM sessions INNER JOIN calendar ON sessions.calendarid=calendar.id WHERE sessions.mentorid = ? AND Month(sessions.startdate) in (?) AND Month(sessions.enddate) in (?) AND YEAR(sessions.startdate) = ? AND YEAR(sessions.enddate) = ? AND NOT calendar.daily=1 AND sessions.approved=1";
        $duration = 0;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiiii", $mentorid, $month, $month, $year, $year);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            while ($session = mysqli_fetch_array($rslt)) {
                $duration += $session['dur'];
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $duration;
    }

    function GetHourSessionsCount($mentorid) {
        $db = new Database();
        $link = $db->connect();
        $month = (int) date('m', strtotime('last day of previous month'));
        $year = (int) date('Y', strtotime('last day of previous month'));
        $sql = "SELECT * FROM sessions INNER JOIN calendar ON sessions.calendarid=calendar.id WHERE sessions.mentorid = ? AND Month(sessions.startdate) in (?) AND Month(sessions.enddate) in (?) AND YEAR(sessions.startdate) = ? AND YEAR(sessions.enddate) = ? AND NOT calendar.daily=1 AND sessions.approved=1";
        $count = 0;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiiii", $mentorid, $month, $month, $year, $year);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $count = mysqli_num_rows($rslt);
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $count;
    }

    function GetDailySessions($mentorid) {
        $db = new Database();
        $link = $db->connect();
        $month = (int) date('m', strtotime('last day of previous month'));
        $year = (int) date('Y', strtotime('last day of previous month'));
        $sql = "SELECT * FROM sessions INNER JOIN calendar ON sessions.calendarid=calendar.id WHERE sessions.mentorid = ? AND Month(sessions.startdate) in (?) AND Month(sessions.enddate) in (?) AND YEAR(sessions.startdate) = ? AND YEAR(sessions.enddate) = ? AND NOT calendar.italy=1 AND calendar.daily = 1 AND sessions.approved = 1";
        $count = 0;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiiii", $mentorid, $month, $month, $year, $year);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $count = mysqli_num_rows($rslt);
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $count;
    }

    function GetItalySessions($mentorid) {
        $db = new Database();
        $link = $db->connect();
        $month = (int) date('m', strtotime('last day of previous month'));
        $year = (int) date('Y', strtotime('last day of previous month'));
        $sql = "SELECT sessions.duration AS dur FROM sessions INNER JOIN calendar ON sessions.calendarid=calendar.id WHERE sessions.mentorid = ? AND Month(sessions.startdate) in (?) AND Month(sessions.enddate) in (?) AND YEAR(sessions.startdate) = ? AND YEAR(sessions.enddate) = ? AND calendar.italy = 1 AND NOT calendar.daily=1 AND sessions.approved = 1";
        $duration = 0;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiiii", $mentorid, $month, $month, $year, $year);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            while ($session = mysqli_fetch_array($rslt)) {
                $duration += $session['dur'];
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $duration;
    }

    function GetItalySessionsCount($mentorid) {
        $db = new Database();
        $link = $db->connect();
        $month = (int) date('m', strtotime('last day of previous month'));
        $year = (int) date('Y', strtotime('last day of previous month'));
        $sql = "SELECT * FROM sessions INNER JOIN calendar ON sessions.calendarid=calendar.id WHERE sessions.mentorid = ? AND Month(sessions.startdate) in (?) AND Month(sessions.enddate) in (?) AND YEAR(sessions.startdate) = ? AND YEAR(sessions.enddate) = ? AND calendar.italy=1 AND NOT calendar.daily=1 AND sessions.approved=1";
        $count = 0;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiiii", $mentorid, $month, $month, $year, $year);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $count = mysqli_num_rows($rslt);
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $count;
    }

    function GetNotAproovedSessions($mentorid) {
        $db = new Database();
        $link = $db->connect();
        $month = (int) date('m', strtotime('last day of previous month'));
        $year = (int) date('Y', strtotime('last day of previous month'));
        $sql = "SELECT * FROM sessions WHERE mentorid = ? AND Month(startdate) in (?) AND Month(enddate) in (?) AND YEAR(startdate) = ? AND YEAR(enddate) = ?";
        $duration = 0;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iiiii", $mentorid, $month, $month, $year, $year);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            while ($session = mysqli_fetch_array($rslt)) {
                if (!$session['approved'])
                    $duration += $session['duration'];
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $duration;
    }

    function GetCosts($userid) {
        $db = new Database();
        $link = $db->connect();
        $month = (int) date('m', strtotime('last day of previous month'));
        $year = (int) date('Y', strtotime('last day of previous month'));
        $sql4 = "SELECT * FROM invoices WHERE userid = ? AND Month(date) in (?) AND YEAR(date) = ?";
        $c = 0;

        if ($stmt = mysqli_prepare($link, $sql4)) {
            mysqli_stmt_bind_param($stmt, "iii", $userid, $month, $year);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt4 = mysqli_stmt_get_result($stmt);
            while ($costs = mysqli_fetch_array($rslt4)) {
                $c = $costs['costs'];
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $c;
    }

    function StartSession($start, $mentor, $aplicant, $calid) {
        $db = new Database();
        $link = $db->connect();
        $sql = "INSERT INTO sessions (mentorid, aplicantid, calendarid, startdate) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $mentor, $aplicant, $calid, $start);

        mysqli_stmt_execute($stmt)
                or die("Unable to execute query: " . $stmt->error);

        $_SESSION["session_last_id"] = mysqli_insert_id($link);
        $sql2 = "INSERT INTO tempsessions (sesid) VALUES (?)";
        if ($stmt2 = mysqli_prepare($link, $sql2)) {
            mysqli_stmt_bind_param($stmt2, "i", $_SESSION["session_last_id"]);

            mysqli_stmt_execute($stmt2)
                    or die("Unable to execute query: " . $stmt2->error);
        }
        mysqli_stmt_close($stmt2);
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    }

    function EndSession($id, $end, $duration) {
        $db = new Database();
        $link = $db->connect();
        $sql = "UPDATE sessions SET enddate = ?, duration = ? WHERE sessionId = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssi", $end, $duration, $id);

            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
    }

    function AcceptMeeting($id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "UPDATE calendar SET accepted = 1 WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);
        }
        echo "Session accepted";
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    }

    function GetSession($calid) {
    $db = new Database();
    $link = $db->connect();
    $sql = "SELECT * FROM sessions WHERE calendarid = ?";

    $result = null;

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $calid);
        mysqli_stmt_execute($stmt)
            or die("Unable to execute query: " . $stmt->error);

        $rslt = mysqli_stmt_get_result($stmt);
        $result = mysqli_fetch_array($rslt); 

        mysqli_stmt_close($stmt); 
    }

    mysqli_close($link); 
    return $result;     
}


    function CancelSession($sesid, $userid) {
        $db = new Database();
        $link = $db->connect();
        $sql = "UPDATE calendar SET canceled = 1, canceleduserid = ?, canceltime = ? WHERE id = ?";

        $stmt = mysqli_prepare($link, $sql)
                or die("Unable to execute query " . $sql);
        $canceltime = date("Y-m-d H:i:s");
        mysqli_stmt_bind_param($stmt, "isi", $userid, $canceltime, $sesid);
        mysqli_stmt_execute($stmt)
                or die("Unable to execute query: " . $stmt->error);

        mysqli_stmt_close($stmt);
        mysqli_close($link);

        echo "Session canceled succesfully";
    }

    function EditSession($id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "SELECT * FROM sessions WHERE sessionId = ?";
        $result = null;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $result = mysqli_fetch_array($rslt);
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $result;
    }

    function SaveUrl($name, $email, $type, $daily, $italy, $id, $url) {
        $db = new Database();
        $link = $db->connect();
        $sql = "UPDATE invoices SET url = ? WHERE invoiceId = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $url, $id);

            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        $attachement = str_replace("/admin", "../", $url);
        $this->SendInvoice($name, $email, $type, $daily, $italy, $attachement);
    }

    function SendInvoice($name, $email, $type, $daily, $italy, $url) {

        if ($type == 1) {
            if ($daily == 1) {
                $to = 'ksiegowosc@tutormeet.pl';
                $cc = $email;
                //$bcc = '';
            } elseif ($daily == 0) {
                $to = 'ksiegowosc@tutormeet.pl';
                $to2 = 'accounting@tutormeet.pl';
                $cc = $email;
                //$bcc = '';
            }
            try {
                $mail = new PHPMailer(true);
                $mail->From = "no_reply@tutormeet.pl";
                $mail->FromName = "TutorMeet";
//Recipients
                $mail->addAddress($to);
                if (isset($to2))
                    $mail->addAddress($to2);
                $mail->addCC($email);
                //$mail->addBCC($bcc);
                // Attachments
                $mail->addAttachment($url);         // Add attachments
                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Invoice for ' . $name;
                $mail->Body = 'An invoice for ' . $name;
                $mail->AltBody = 'An invoice for ' . $name;

                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
        if ($type == 2) {
            if ($daily == 1) {
                $to = 'ksiegowosc@tutormeet.pl';
                $to2 = 'accounting@tutormeet.pl';
                //$cc = $email;
                //$bcc = '';
            } elseif ($daily == 0) {
                $to = 'ksiegowosc@tutormeet.pl';
                $to2 = 'accounting@tutormeet.pl';
                //$cc = $email;
                //$bcc = '';
            }

            try {
                $mail = new PHPMailer(true);
                $mail->From = "no_reply@tutormeet.pl";
                $mail->FromName = "TutorMeet";
//Recipients
                $mail->addAddress($to);
                $mail->addAddress($to2);
                $mail->addCC($email);
                //$mail->addBCC($bcc);
                // Attachments
                $mail->addAttachment($url);         // Add attachments
                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Invoice for ' . $name;
                $mail->Body = 'An invoice for ' . $name;
                $mail->AltBody = 'An invoice for ' . $name;

                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }

    function GetMeetings($id, $mentor = null) {
        $db = new Database();
        $link = $db->connect();
        $messages = '<div class="col-lg-12">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url(\"images/bg-title-02.jpg\");">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                        
                                       <h3>
                                            <i class="zmdi zmdi-comment-text"></i>All ' . $id['realname'] . ' Sessions</h3>';
        if ($_SESSION["privilege"] == 5 || $_SESSION["privilege"] == 6) :
            $messages .= '<button class="au-btn-plus" onclick="window.location.href = \'addmeeting\';">
                                            <i class="zmdi zmdi-plus"></i>
                                        </button>';
        endif;
        $messages .= '</div>
                                        <div class="au-inbox-wrap js-inbox-wrap">
                                            <div class="au-message js-list-load">';
        $clause = "";
        $param_id = null;
        if ($_SESSION['privilege'] == 5) :
            $clause = "WHERE mentorid = ?";
            $param_id = $id;
        elseif ($_SESSION["privilege"] == 6) :
            $clause = "WHERE aplicants regexp ?";
            $param_id = '[[:<:]]' . $id . '[[:>:]]';
        elseif ($_SESSION["privilege"] == 4 || $_SESSION["privilege"] == 3 || $_SESSION["privilege"] == 1) :
            if ($mentor == "mentor") :
                $clause = "WHERE mentorid = ?";
                $param_id = $id['recid'];
            elseif ($mentor == "aplicant") :
                $clause = "WHERE aplicants regexp ?";
                $param_id = '[[:<:]]' . $id['recid'] . '[[:>:]]';
            else :
                $clause = "";
            endif;
        else :
            $clause = "none";
        endif;
        $sql = "SELECT * FROM calendar " . $clause . " ORDER BY date DESC, time DESC";
        if ($stmt = mysqli_prepare($link, $sql)) {
            if ($param_id !== null) {
                mysqli_stmt_bind_param($stmt, "s", $param_id);
            }
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $found = false;
            while ($row = mysqli_fetch_array($rslt)) {
                $found = true;
                        $start = strtotime($row['date'] . " " . $row["time"]);
                        $end = $start + ($row["duration"]);
                        $now = strtotime(date("Y-m-d H:i:s"));
                        $session = $this->GetSession($row['id']);
                        if ($session == null) :
                            $url = "window.location.href='/admin/offline/" . $row['id'] . "'";
                            $summary = $start < $now ? '<p>Empty meeting</p>' : 'Planned meeting';
                            $approved = $start < $now ? '<button type="button" class="btn btn-success au-input--full" id="btn-submit"
                                        onclick="' . $url . '">Add</button>' : '';
                            $class = "bg-danger-light";
                        else :
                            $url = "window.location.href='/admin/approve/" . $session['sessionId'] . "'";
                            $summary = '<p>' . $session['summary'] . '</p>';
                            $approved = $start < $now ? '<button type="button" class="btn btn-success au-input--full" id="btn-submit"
                                        onclick="' . $url . '">Show</button>' : '';
                            $class = $session['approved'] == "0" ? "bg-warning-light" : "bg-success-light";
                        endif;

                        $messages .= '
                            <div class="card">
                                <div class="card-body ' . $class . '">
                                    <div class="au-message__item-text">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div>
                                                <h5 class="name">' . date('Y-m-d', $start) . '</h5>
                                                <h5>' . date('H:i', $start) . " - " . date('H:i', $end) . '</h5>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div>
                                                <h5>' . $row['title'] . '</h5>
                                                <h6>' . $row['description'] . '</h6>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div>
                                                <h5>';
                        if ($mentor == "aplicant"):
                            $messages .= $this->GetUserExt($row['mentorid'], 5)['realname'];
                        elseif ($mentor == "mentor"):
                            $messages .= $this->GetUserExt($row['aplicants'], 6)['realname'];
                        endif;
                        $messages .= '</h5>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div>
                                                <p>' . $summary . '</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div>
                                                <p>' . $approved . '</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>';
                    }
            if ($found) {
                $messages .= '</div>
                </div>
                </div>
                </div>';
            } else {
                $messages = "No data";
            }
            mysqli_stmt_close($stmt);
        } else {
            $messages = "Oops! Something went wrong. Please try again later.";
        }

        mysqli_close($link);
        return $messages;
    }

    function SendMessage($sender, $recipient, $subject, $message) {
        $db = new Database();
        $link = $db->connect();
        $sql = "INSERT INTO notifications (sender,subject,message,date,recipient)
                                        VALUES (?,?,?,?,?)";
        $stmt = mysqli_prepare($link, $sql);
        $date = date('Y-m-d H:i:s');

        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "isssi", $sender, $subject, $message, $date, $recipient);

        if (mysqli_stmt_execute($stmt)) {
            //echo "sent";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);

        mysqli_close($link);
    }

    function SendMailToMentor($to, $subject, $txt) {
        $user = $this->GetUserExt($to, 5);
        $recipient = $user['email'];
        try {
            $mail = new PHPMailer(true);
            $mail->From = "no_reply@tutormeet.pl";
            $mail->FromName = "TutorMeet";
//Recipients
            $mail->addAddress($recipient);
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $txt;
            $mail->AltBody = $txt;

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function SendEmailToUser($to, $column, $subject, $txt) {
        $user = $this->GetUser($to, $column);
        $recipient = $user['email'];
        try {
            $mail = new PHPMailer(true);
            $mail->From = "no_reply@tutormeet.pl";
            $mail->FromName = "TutorMeet";
//Recipients
            $mail->addAddress($recipient);
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $txt;
            $mail->AltBody = $txt;

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function SendEmail($to, $subject, $msg) {
        try {
            $mail = new PHPMailer(true);
            $mail->From = "no_reply@tutormeet.pl";
            $mail->FromName = "TutorMeet";
//Recipients
            $mail->addAddress($to);
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $msg;
            $mail->AltBody = $msg;

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function SendMailTo($sender, $subject, $msg, $privilege) {
        $db = new Database();
        $link = $db->connect();
        $sql = "SELECT * FROM users WHERE userPrivilege = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $privilege);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            while ($user = mysqli_fetch_array($rslt)) {
                $this->SendMessage($sender, $user['userId'], $subject, $msg);

                $recipient = $user['userEmail'];
                try {
                    $mail = new PHPMailer(true);
                    $mail->From = "no_reply@tutormeet.pl";
                    $mail->FromName = "TutorMeet";
//Recipients
                    $mail->addAddress($recipient);
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = $subject;
                    $mail->Body = $msg;
                    $mail->AltBody = $msg;

                    $mail->send();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    }

    function CheckDaily($id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "SELECT * FROM rates WHERE userId = ? AND daily IS NOT NULL";
        $rowcount = 0;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $rowcount = mysqli_num_rows($rslt);
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $rowcount;
    }

    function CheckHours($id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "SELECT * FROM assignments WHERE aplicantid = ?";
        $result = false;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $hours = mysqli_fetch_array($rslt);
            $result = $hours['hoursleft'] > 0;
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $result;
    }

    function GetItaly($mentorid, $id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "SELECT * FROM assignments WHERE mentorid = ? AND aplicantid = ?";
        $result = 0;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $mentorid, $id);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $hours = mysqli_fetch_array($rslt);
            $result = $hours['country'] != null ? $hours['country'] : 0;
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $result;
    }

    function CheckIfAssign($mentorid, $id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "SELECT assignid FROM assignments WHERE mentorid = ? AND aplicantid = ?";
        $result = -1;

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $mentorid, $id);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            $assign = mysqli_fetch_array($rslt);
            $result = is_null($assign) ? -1 : $assign['assignid'];
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $result;
    }

    function getRandomBytes($nbBytes = 32) {
        $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);
        if (false !== $bytes && true === $strong) {
            return $bytes;
        } else {
            throw new \Exception("Unable to generate secure token from OpenSSL.");
        }
    }

    function generatePassword($length) {
        return substr(preg_replace("/[^a-zA-Z0-9]/", "", base64_encode($this->getRandomBytes($length + 1))), 0, $length);
    }

    function GetMentorForApplicant($id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "SELECT * FROM assignments WHERE aplicantid = ?";
        $user = "";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            while ($ass = mysqli_fetch_array($rslt)) {
                $user .= $this->GetUser($ass['mentorid'], "recordId")['realname'] . ", ";
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $user;
    }

    function GetAplicantForMentor($id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "SELECT * FROM assignments WHERE mentorid = ?";
        $user = "";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);

            $rslt = mysqli_stmt_get_result($stmt);
            while ($ass = mysqli_fetch_array($rslt)) {
                $user .= $this->GetUser($ass['aplicantid'], "recordId")['realname'] . ", ";
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
        return $user;
    }

    function RemoveMentor($id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "DELETE FROM mentors WHERE mentorId = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
    }

    function RemoveAplicant($id) {
        $db = new Database();
        $link = $db->connect();
        $sql = "DELETE FROM aplicants WHERE aplicantId = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt)
                    or die("Unable to execute query: " . $stmt->error);
            mysqli_stmt_close($stmt);
        }

        mysqli_close($link);
    }

    function ceilToFraction($duration, $denominator = 1) {
        $x = $duration * $denominator;
        $x = ceil($x);
        $x = $x / $denominator;

        return $x * 60 * 60;
    }

}
