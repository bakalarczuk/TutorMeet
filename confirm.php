<?php
// Include config file
require_once "admin/includes/config.php";

// Define variables and initialize with empty values
$db = new Database;
$link = $db->connect();

if ($_GET) {
    //echo $_GET['q'] . "<br>" . $_GET['t'];

    $sql_pass = "UPDATE users SET userPass = ? WHERE userLogin = ?";
    $userpass = password_hash(mysqli_real_escape_string($link, $_POST['password']), PASSWORD_DEFAULT);
    if ($stmt_pass = mysqli_prepare($link, $sql_pass)) {
        mysqli_stmt_bind_param($stmt_pass, "ss", $userpass, $username);
        $output = "";
        mysqli_stmt_execute($stmt_pass)
                or die("Unable to execute query: " . $stmt->error);

        $output = 'Password has been changed<br>Click <a href="/" style="text-decoration: none; color: #FFC312;">here</a> to go to login page<br>or wait <span id="seconds">5</span> second(s)';
    }

    mysqli_stmt_close($stmt_pass);
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html>

    <head>
        <title>Reset Password</title>
        <!--Made with love by Mutiullah Samim -->

        <!--Bootsrap 4 CDN-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
              integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

        <!--Fontawesome CDN-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
              integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

        <!--Custom styles-->
        <link rel="stylesheet" type="text/css" href="style.css">
        <script>
            // Countdown timer for redirecting to another URL after several seconds

            var seconds = 4; // seconds for HTML
            var foo; // variable for clearInterval() function

            function redirect() {
                document.location.href = '/';
            }

            function updateSecs() {
                document.getElementById("seconds").innerHTML = seconds;
                seconds--;
                if (seconds == -1) {
                    clearInterval(foo);
                    redirect();
                }
            }

            function countdownTimer() {
                foo = setInterval(function () {
                    updateSecs()
                }, 1000);
            }
<?php if (!empty($output)): ?>
                countdownTimer();
<?php endif; ?>
        </script>
    </head>

    <body>
        <div class="container">
            <div class="d-flex justify-content-center h-100">
                <div class="card">
                    <div class="card-header">
                        <h3>Change Password</h3>
                        <div class="d-flex justify-content-end social_icon_right">
                            <img src="images/Login_logo.png" id="icon" alt="Logo Icon" />
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <?php echo (!empty($output)) ? "<span style='color:#00ff00;'>" . $output . "</span>" : ''; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

