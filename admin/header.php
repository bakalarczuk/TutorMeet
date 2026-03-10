<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$functions = new Functions();
?>
<!-- HEADER DESKTOP-->
<header class="header-desktop2">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="header-wrap2">
                <div class="logo d-block d-lg-none">
                    <a href="#">
                        <img src="/admin/images/logo.png" alt="TutorMeet" />
                    </a>
                </div>
                <span style="color: #fff;font-size:11px;">
                    <?php
                    if ($_SESSION['privilege'] == 6):
                        echo "<b>Assigned Tutors:</b> ".$functions->GetMentorForApplicant($_SESSION['recid']);
                    elseif ($_SESSION['privilege'] == 5):
                        echo "<b>Assigned Applicants:</b> ".$functions->GetAplicantForMentor($_SESSION['recid']);
                    endif;
                    ?>
                </span>
            </div>
        </div>
    </div>
</header>
<!-- END HEADER DESKTOP-->