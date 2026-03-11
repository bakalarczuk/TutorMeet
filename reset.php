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

    // Validate credentials
    if (empty($username_err)){
        $output = "";
        $email = $func->GetEmail($username);
        if ($email == "Wrong user") {
            $output = "No user with this user name. Please try again.";
        } else {
            // Generate cryptographically secure token
            $reset_token = bin2hex(random_bytes(32));
            // Store only the HASH of the token in the database
            $token_hash = hash('sha256', $reset_token);
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Invalidate any previous unused tokens for this user
            $sql_invalidate = "UPDATE password_resets SET used = 1 WHERE userLogin = ? AND used = 0";
            if ($stmt_inv = mysqli_prepare($link, $sql_invalidate)) {
                mysqli_stmt_bind_param($stmt_inv, "s", $username);
                mysqli_stmt_execute($stmt_inv);
                mysqli_stmt_close($stmt_inv);
            }

            // Save the token hash to the database
            $sql_token = "INSERT INTO password_resets (userLogin, token_hash, expires_at) VALUES (?, ?, ?)";
            if ($stmt_token = mysqli_prepare($link, $sql_token)) {
                mysqli_stmt_bind_param($stmt_token, "sss", $username, $token_hash, $expires_at);
                mysqli_stmt_execute($stmt_token);
                mysqli_stmt_close($stmt_token);
            }

            // Send the plain token in the email link (NOT the hash)
            $reset_link = 'https://' . htmlspecialchars($_SERVER['SERVER_NAME'], ENT_QUOTES, 'UTF-8')
                        . '/confirm.php?q=' . urlencode($username) . '&t=' . $reset_token;
            $func->SendEmail($email, "TutorMeet password change",
                "TutorMeet system password reset.<br><br>"
                . "Click <a href='" . $reset_link . "'>here</a> to set a new password.<br><br>"
                . "This link expires in 1 hour. If you did not request this, ignore this email.");
            $output = "Reset link sent to your email.<br>Check your inbox.";
        }
    }
}

function gravatar($email) {
    $email = md5(strtolower(trim($email)));
    $gravurl = "https://www.gravatar.com/avatar/$email?&s=360";
    return '<img src="' . $gravurl . '" alt="' . htmlspecialchars($_SESSION["realname"] ?? '', ENT_QUOTES, 'UTF-8') . '">';
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
                                       value="<?php echo htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                            </div>
                            <div class="form-group">
                                <input type="submit" value="Send Reset Link" class="btn float-right login_btn">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
<?php echo (!empty($username_err)) ? $username_err . "<br>" : ''; ?>
                            <?php echo (!empty($output)) ? "<span style='color:#00ff00;'>" . $output . "</span>" : ''; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>

</html>