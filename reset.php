<?php
// Include config file
require_once "admin/includes/config.php";
require_once "admin/includes/functions.php";




// Define variables and initialize with empty values
$db = new Database;
$link = $db->connect();

$func = new Functions;

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter user name.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check if rpassword is empty
    if (empty(trim($_POST["rpassword"]))) {
        $rpassword_err = "Please repeat your password.";
    } else {
        $rpassword = trim($_POST["rpassword"]);
    }

    // Validate credentials
    if (empty($username_err)){// && empty($password_err) && empty($rpassword_err)) {
        $output = "";
        $email = $func->GetEmail($username);
        if ($email == "Wrong user") {
            $output = "No user with this user name. Please try again.";
        } else {
            $func->SendEmail($email, "TutorMeet password change", "TutorMeet system password reset. Your temporary password is: " . $newpass . ". If you confirm password change, please click on link <a href='https://" . $_SERVER['SERVER_NAME'] . "/confirm.php?q=" . $email . "&t=" . $password . "'>here</a>");
            $output = "Password has been changed<br>Wait for redirection in <span id='seconds'>3</span> seconds";
        }
    }
}

function gravatar($email) {
    $email = md5(strtolower(trim($email)));
    $gravurl = "http://www.gravatar.com/avatar/$email?&s=360";
    return '<img src="' . $gravurl . '" alt="' . $_SESSION["realname"] . '">';
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
            function reveal(elementid) {
                var x = document.getElementById(elementid);
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }

            // Countdown timer for redirecting to another URL after several seconds

            var seconds = 2; // seconds for HTML
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
                        <h3>Reset Password</h3>
                        <div class="d-flex justify-content-end social_icon_right">
                            <img src="images/Login_logo.png" id="icon" alt="Logo Icon" />
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="input-group form-group">
                                <div class="input-group-prepend <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" name="username" class="form-control" placeholder="username"
                                       value="<?php echo $username; ?>">

                            </div>
                            <div class="input-group form-group">
                                <div class="input-group-prepend <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                    <span class="input-group-text" onclick="reveal('pass-control');"><i
                                            class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="password" class="form-control" id="pass-control" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                       placeholder="password">
                            </div>
                            <div class="input-group form-group">
                                <div class="input-group-prepend <?php echo (!empty($rpassword_err)) ? 'has-error' : ''; ?>">
                                    <span class="input-group-text" onclick="reveal('rpass-control');"><i
                                            class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="rpassword" class="form-control" id="rpass-control" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                       placeholder="repeat password">
                            </div>
                            <!--<div class="row align-items-center remember">
                                    <input type="checkbox">Remember Me
                                </div>-->
                            <div class="form-group">
                                <input type="submit" value="Reset" class="btn float-right login_btn">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div style="font-size: 11px; color: #fff;">Password should be at least 8 characters long<br>
                            Password should contain at least 1 capital letter<br>
                            Password should contain at least 1 lowercase letter<br>
                            Password should contain at least 1 special character<br>
                            Password should contain at least 1 numeric character
                        </div>
                        <div class="d-flex justify-content-center">
<?php echo (!empty($username_err)) ? $username_err . "<br>" : ''; ?>
                            <?php echo (!empty($password_err)) ? $password_err . "<br>" : ''; ?>
                            <?php echo (!empty($rpassword_err)) ? $rpassword_err . "<br>" : ''; ?>
                            <?php echo (!empty($output)) ? "<span style='color:#00ff00;'>" . $output . "</span>" : ''; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>

</html>