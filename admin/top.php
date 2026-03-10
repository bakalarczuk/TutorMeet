<html itemscope itemtype="http://schema.org/Product" prefix="og: http://ogp.me/ns#" xmlns="http://www.w3.org/1999/html">

    <head>
        <!-- Required meta tags-->
        <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta name="description" content="au theme template">
                    <meta name="author" content="Hau Nguyen">
                        <meta name="keywords" content="au theme template">

                            <!-- Title Page-->
                            <title>Dashboard - <?php echo htmlspecialchars($_SESSION["realname"]); ?></title>

                            <!-- Fontfaces CSS-->
                            <link href="/admin/css/font-face.css" rel="stylesheet" media="all">
                                <link href="/admin/vendor/font-awesome-5/css/all.css" rel="stylesheet" media="all">
                                    <link href="/admin/vendor/font-awesome-5/css/brands.css" rel="stylesheet" media="all">
                                        <link href="/admin/vendor/font-awesome-5/css/solid.css" rel="stylesheet" media="all">
                                            <link href="/admin/vendor/font-awesome-5/css/regular.css" rel="stylesheet" media="all">

                                                <link href="/admin/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

                                                    <!-- Bootstrap CSS-->
                                                    <link href="/admin/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

                                                        <!-- Vendor CSS-->
                                                        <link href="/admin/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
                                                            <link href="/admin/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
                                                                <link href="/admin/vendor/wow/animate.css" rel="stylesheet" media="all">
                                                                    <link href="/admin/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
                                                                        <link href="/admin/vendor/slick/slick.css" rel="stylesheet" media="all">
                                                                            <link href="/admin/vendor/select2/select2.min.css" rel="stylesheet" media="all">
                                                                                <link href="/admin/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
                                                                                    <link href="/admin/vendor/vector-map/jqvmap.min.css" rel="stylesheet" media="all">

                                                                                        <!-- Main CSS-->
                                                                                        <link href="/admin/css/theme.css" rel="stylesheet" media="all">
                                                                                            <link href="/admin/css/calendar.css" rel="stylesheet" media="all">
                                                                                                <link href="/admin/css/jquery.datetimepicker.css" rel="stylesheet" media="all">

                                                                                                    <!-- Jquery JS-->
                                                                                                    <script src="/admin/vendor/jquery-3.2.1.min.js"></script>
                                                                                                    <script src="/admin/js/jquery.datetimepicker.full.js"></script>
                                                                                                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
                                                                                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
                                                                                                        </head>
    
                                                                                                        <body class="animsition">
                                                                                                            <div class="page-wrapper">
                                                                                                                <!-- MENU SIDEBAR-->
                                                                                                                <aside class="menu-sidebar2">
                                                                                                                    <div class="logo">
                                                                                                                        <a href="/admin/">
                                                                                                                            <img src="/admin/images/logo.png" alt="Cool Admin" />
                                                                                                                        </a>
                                                                                                                    </div>
                                                                                                                    <div class="menu-sidebar2__content js-scrollbar1">
                                                                                                                        <div class="account2">
                                                                                                                            <h4 class="name"><?php echo htmlspecialchars($_SESSION["realname"]); ?></h4>
                                                                                                                            <h6><?php echo $functions->PrivilegeName($_SESSION['privilege']); ?></h6>
                                                                                                                            <a href="/logout.php">Sign out</a>
                                                                                                                        </div>
                                                                                                                        <nav class="navbar-sidebar2">
                                                                                                                            <ul class="list-unstyled navbar__list">
                                                                                                                                <li class="<?php echo $_GET["func"] == null ? 'active' : ''; ?>">
                                                                                                                                    <a class="js-arrow" href="/admin/index.php">
                                                                                                                                        <i class="fas fa-tachometer-alt"></i>Dashboard
                                                                                                                                    </a>
                                                                                                                                </li>
                                                                                                                                <?php if ($_SESSION['privilege'] == 3 || $_SESSION['privilege'] == 1) : ?>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "allusers" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/allusers">
                                                                                                                                            <i class="fas fa-list-ul"></i>All Users</a>
                                                                                                                                    </li>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "adduser" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/adduser">
                                                                                                                                            <i class="far fa-plus-circle"></i>Add User</a>
                                                                                                                                    </li>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "settings" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/settings">
                                                                                                                                            <i class="fas fa-cog"></i>Settings</a>
                                                                                                                                    </li>
                                                                                                                                <?php endif; ?>
                                                                                                                                <li class="<?php echo $_GET["func"] == 'message' ? 'active' : ''; ?>">
                                                                                                                                    <a class="js-arrow" href="/admin/message">
                                                                                                                                        <i class="fas fa-paper-plane"></i> Send message
                                                                                                                                    </a>
                                                                                                                                </li>
                                                                                                                                <li class="<?php echo $_GET["func"] == 'inbox' ? 'active' : ''; ?>">
                                                                                                                                    <a class="js-arrow" href="/admin/inbox">
                                                                                                                                        <i class="far fa-envelope"></i> Inbox
                                                                                                                                    </a>
                                                                                                                                </li>
                                                                                                                                <?php if ($_SESSION['privilege'] == 5 || $_SESSION['privilege'] == 6) : ?>
                                                                                                                                    <li class="<?php echo $_GET["func"] == 'conference' ? 'active' : ''; ?>">
                                                                                                                                        <a class="js-arrow" href="/admin/conference">
                                                                                                                                            <i class="fas fa-video"></i> Sessions
                                                                                                                                        </a>
                                                                                                                                    </li>
                                                                                                                                <?php endif; ?>
                                                                                                                                <?php if ($_SESSION['privilege'] == 5 || $_SESSION['privilege'] == 4 || $_SESSION['privilege'] == 3 || $_SESSION['privilege'] == 1 || $_SESSION['privilege'] == 6) : ?>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "calendar" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/calendar">
                                                                                                                                            <i class="far fa-calendar-alt"></i> Show calendar</a>
                                                                                                                                    </li>
                                                                                                                                    <?php if ($_SESSION['privilege'] == 5 || $_SESSION['privilege'] == 4 || $_SESSION['privilege'] == 3||$_SESSION['privilege'] == 1) : ?>
                                                                                                                                        <li class="<?php echo $_GET["func"] == "addmeeting" ? 'active' : ''; ?>">
                                                                                                                                            <a href="/admin/addmeeting">
                                                                                                                                                <i class="far fa-calendar-plus"></i> Add <?php if ($_SESSION['privilege'] < 5) : ?> Offline <?php endif; ?> Session</a>
                                                                                                                                        </li>
                                                                                                                                    <?php endif; ?>
                                                                                                                                <?php endif; ?>
                                                                                                                                <?php if ($_SESSION['privilege'] == 4 || $_SESSION['privilege'] == 3 || $_SESSION['privilege'] == 1) : ?>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "assign" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/assign_applicant">
                                                                                                                                            <i class="far fa-id-badge"></i> Assign Applicant
                                                                                                                                        </a>
                                                                                                                                    </li>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "meetings" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/meetings">
                                                                                                                                            <i class="far fa-calendar-alt"></i> Show Tutor Sessions
                                                                                                                                        </a>
                                                                                                                                    </li>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "applicant_meetings" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/applicant_meetings">
                                                                                                                                            <i class="far fa-calendar-alt"></i> Show Applicant Sessions
                                                                                                                                        </a>
                                                                                                                                    </li>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "canceled" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/canceled">
                                                                                                                                            <i class="far fa-calendar-alt"></i> Show Canceled Sessions
                                                                                                                                        </a>
                                                                                                                                    </li>
                                                                                                                                <?php endif; ?>
                                                                                                                                <?php if ($_SESSION['privilege'] == 5) : ?>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "settlements" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/settlements">
                                                                                                                                            <i class="far fa-money-bill-alt"></i> Show Settlements
                                                                                                                                        </a>
                                                                                                                                    </li>
                                                                                                                                <?php endif; ?>
                                                                                                                                <?php if ($_SESSION['privilege'] == 1 || $_SESSION['privilege'] == 3 || $_SESSION['privilege'] == 4) : ?>
                                                                                                                                <?php endif; ?>
                                                                                                                                <?php if ($_SESSION['privilege'] == 5 || $_SESSION['privilege'] == 6) : ?>
                                                                                                                                    <li class="<?php echo $_GET["func"] == "assignee" ? 'active' : ''; ?>">
                                                                                                                                        <a href="/admin/assignee">
                                                                                                                                            <i class="far fa-id-badge"></i> Show 
                                                                                                                                            <?php if ($_SESSION['privilege'] == 5):
                                                                                                                                                echo ' Assignees';
                                                                                                                                            else:
                                                                                                                                                echo ' Assigned Tutors';
                                                                                                                                            endif;
                                                                                                                                            ?>
                                                                                                                                        </a>
                                                                                                                                    </li>
                                                                                                                                <?php endif; ?>
                                                                                                                                </li>
                                                                                                                                <li style="height:120px;"></li>
                                                                                                                            </ul>
                                                                                                                        </nav>
                                                                                                                    </div>
                                                                                                                </aside>
                                                                                                                <!-- END MENU SIDEBAR-->