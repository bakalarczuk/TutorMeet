<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
// Include config file
require_once "includes/functions.php";
$functions = new Functions();
$db = new Database();
$link = $db->connect();


$_SESSION["aplicants"] = $functions->GetAplicants();

include("top.php");
?>

<!-- PAGE CONTAINER-->
<div class="page-container2">

    <?php
    if ($_SESSION['privilege'] == 4 || $_SESSION['privilege'] == 3 || $_SESSION['privilege'] == 1) :
        include('header_accounting.php');
    else :
        if ($_GET['func'] == "session") :
            include('header_session.php');
        else :
            include('header.php');
        endif;
    endif;
    ?>

    <!-- MAIN CONTENT-->
    <div class="main-content main-content--pb30">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <?php
                $content = "";
                if ($_GET) :
                    if ($_GET['func'] == "adduser") :
                        $content = file_get_contents("view/register.html");
                    elseif ($_GET['func'] == "message") :
                        $content = file_get_contents("view/message.html");
                    elseif ($_GET['func'] == "allusers") :
                        $content = $functions->GetUsers();
                    elseif ($_GET['func'] == "inbox") :
                        $content = $functions->GetMessages();
                    elseif ($_GET['func'] == "invoices_list") :
                        include('includes/invoices.php');
                    elseif ($_GET['func'] == "invoices_history") :
                        include('includes/pastinvoices.php');
                    elseif ($_GET['func'] == "mentors") :
                        include('includes/mentors.php');
                    elseif ($_GET['func'] == "aplicants") :
                        include('includes/aplicants.php');
                    elseif ($_GET['func'] == "conference") :
                        if ($_SESSION['privilege'] == 5) :
                            include('includes/conference_mentor.php');
                        elseif ($_SESSION['privilege'] == 6) :
                            include('includes/conference_aplicant.php');
                        else :
                            include('includes/conference_admin.php');
                        endif;
                    elseif ($_GET['func'] == "session") :
                        include('includes/session.php');
                    elseif ($_GET['func'] == "calendar") :
                        include('includes/calendar.php');
                    elseif ($_GET['func'] == "sessionend") :
                        include('includes/sessionend.php');
                    elseif ($_GET['func'] == "sessionedit") :
                        include('includes/sessionedit.php');
                    elseif ($_GET['func'] == "settings") :
                        include('includes/settings.php');
                    elseif ($_GET['func'] == "addmeeting") :
                        include('includes/addmeeting.php');
                    elseif ($_GET['func'] == "transfer") :
                        include('includes/movemeeting.php');
                    elseif ($_GET['func'] == "meetings") :
                        include('includes/mentors.php');
                    elseif ($_GET['func'] == "applicant_meetings") :
                        include('includes/aplicants.php');
                    elseif ($_GET['func'] == "mentor_sessions") :
                        //echo $_GET['id'] . "<br>";
                        $content = $functions->GetMeetings($functions->GetUserExt($_GET['id'], 5), "mentor");
                    elseif ($_GET['func'] == "applicant_sessions") :
                        //echo $_GET['id'] . "<br>";
                        $content = $functions->GetMeetings($functions->GetUserExt($_GET['id'], 6), "aplicant");
                    elseif ($_GET['func'] == "approve") :
                        include('includes/approve.php');
                    elseif ($_GET['func'] == "settlements") :
                        include('includes/settlements.php');
                    elseif ($_GET['func'] == "assign") :
                        include('includes/assign.php');
                    elseif ($_GET['func'] == "assignee") :
                        include('includes/assignee.php');
                    elseif ($_GET['func'] == "cancel") :
                        $content = $functions->CancelSession($_GET['sesid'], $_GET['userid']);
                    elseif ($_GET['func'] == "canceled") :
                        include('includes/conference_admin.php');
                    elseif ($_GET['func'] == "accept") :
                        //echo $_GET['id'] . "<br>";
                        $content = $functions->AcceptMeeting($_GET['id']);
                    elseif ($_GET['func'] == "offline") :
                        $sql = "SELECT * FROM calendar WHERE id = " . mysql_real_escape_string ($_GET['id']);

                        if ($stmt = mysqli_prepare($link, $sql)) {
                            mysqli_stmt_execute($stmt)
                                    or die("Unable to execute query: " . $stmt->error);

                            $rslt = mysqli_stmt_get_result($stmt);

                            $rowcount = mysqli_stmt_affected_rows($stmt);
                            $result = $rowcount > 0 ? mysqli_fetch_array($rslt) : null;
                        }

                        mysqli_stmt_close($stmt);
                        mysqli_close($link);

                        $aplicants = isset($_SESSION["ap"]) ? $_SESSION["ap"] : $result["aplicants"];

                        $_SESSION["starttime"] = time();
                        
                        $content = $functions->StartSession($result['date'] . " " . $result['time'], $result['mentorid'], $aplicants, $_GET['id']);
                        $content = $functions->EndSession($_SESSION["session_last_id"], $result['date'] . " " . $result['time'], 60);
                        echo "<script>window.location = '/admin/mentor_sessions/".$result['mentorid']."'</script>";
                    endif;
                else :
                    echo "<div class='row' style='margin-top: 80px;'><div class='col-lg-12'>";
                    include('includes/calendar.php');
                    echo"</div><div class='col-lg-12'>"; 
                    echo $functions->GetMessages();
                    echo"</div>";


                endif;

                $content = str_replace('{options}', $functions->GetPrivileges(), $content);

                $content = str_replace('{sender}', $_SESSION['id'], $content);
                $content = str_replace('{recipient}', $functions->GetRecipients(), $content);
                $content = str_replace("{pwd}", $functions->generatePassword(12), $content);



                echo $content;
                ?>
            </div>
        </div>
    </div>
    <!-- END MAIN CONTENT-->
</div>
<!-- END PAGE CONTAINER-->
<?php include("bottom.php"); ?>