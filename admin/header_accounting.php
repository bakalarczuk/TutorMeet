<!-- HEADER DESKTOP-->
<header class="header-desktop2">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="header-wrap2">
                <div class="logo d-block d-lg-none">
                    <a href="#">
                        <img src="images/logo.png" alt="TutorMeet" />
                    </a>
                </div>
                <?php if ($_SESSION['privilege'] == 3 || $_SESSION['privilege'] == 1) : ?>
                <div class="header-button2" style="margin-right: 30px;">
                    <div class="header-button-item">
                        <span style="font-size: 20px;" onclick="window.location.href = '/admin/invoices_list';"><i
                                class="fas fa-file-invoice-dollar"></i> Invoices</span>
                    </div>
                </div>
                <div class="header-button2" style="margin-right: 30px;">
                    <div class="header-button-item">
                        <span style="font-size: 20px;" onclick="window.location.href = '/admin/invoices_history';"><i
                                class="fas fa-file-invoice-dollar"></i> Invoices History</span>
                    </div>
                </div>
                <?php endif; ?>
                <div class="header-button2" style="margin-right: 30px;">
                    <div class="header-button-item">
                        <span style="font-size: 20px;" onclick="window.location.href = '/admin/mentors';"><i
                                class="fas fa-user"></i> Tutors</span>
                    </div>
                </div>
                <div class="header-button2" style="margin-right: 30px;">
                    <div class="header-button-item">
                        <span style="font-size: 20px;" onclick="window.location.href = '/admin/applicants';"><i
                                class="fas fa-user-graduate"></i> Applicants</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END HEADER DESKTOP-->