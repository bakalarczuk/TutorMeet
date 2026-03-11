<?php
// Include config file
require_once "admin/includes/config.php";

$db = new Database;
$link = $db->connect();
$output = "";
$show_form = false;
$token_valid = false;

// Step 1: User clicks the link from email (GET request) — show password form
// Step 2: User submits new password (POST request) — change password

$username = $_GET['q'] ?? '';
$token = $_GET['t'] ?? '';

if (!empty($username) && !empty($token)) {
    // Verify the token: hash it and compare with the stored hash
    $token_hash = hash('sha256', $token);

    $sql_check = "SELECT id, expires_at FROM password_resets WHERE userLogin = ? AND token_hash = ? AND used = 0 LIMIT 1";
    if ($stmt = mysqli_prepare($link, $sql_check)) {
        mysqli_stmt_bind_param($stmt, "ss", $username, $token_hash);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($row) {
            // Check if token has expired
            if (strtotime($row['expires_at']) > time()) {
                $token_valid = true;
                $reset_id = $row['id'];
            } else {
                $output = "This reset link has expired. Please request a new one.";
            }
        } else {
            $output = "Invalid or already used reset link.";
        }
    }

    // Token is valid — handle form display or password change
    if ($token_valid) {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['password'])) {
            $new_password = $_POST['password'];

            // Update the user's password
            $sql_pass = "UPDATE users SET userPass = ? WHERE userLogin = ?";
            $userpass = password_hash($new_password, PASSWORD_DEFAULT);
            if ($stmt_pass = mysqli_prepare($link, $sql_pass)) {
                mysqli_stmt_bind_param($stmt_pass, "ss", $userpass, $username);
                if (mysqli_stmt_execute($stmt_pass)) {
                    // Mark token as used so it cannot be reused
                    $sql_used = "UPDATE password_resets SET used = 1 WHERE id = ?";
                    if ($stmt_used = mysqli_prepare($link, $sql_used)) {
                        mysqli_stmt_bind_param($stmt_used, "i", $reset_id);
                        mysqli_stmt_execute($stmt_used);
                        mysqli_stmt_close($stmt_used);
                    }
                    $output = 'Password has been changed<br>Click <a href="/" style="text-decoration: none; color: #FFC312;">here</a> to go to login page<br>or wait <span id="seconds">5</span> second(s)';
                } else {
                    $output = "Error changing password. Please try again.";
                }
                mysqli_stmt_close($stmt_pass);
            }
        } else {
            // Show the password form
            $show_form = true;
        }
    }
} else {
    $output = "Invalid request.";
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html>

    <head>
        <title>Reset Password</title>

        <!--Bootsrap 4 CDN-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
              integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

        <!--Fontawesome CDN-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
              integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

        <!--Custom styles-->
        <link rel="stylesheet" type="text/css" href="style.css">
        <script>
            var seconds = 4;
            var foo;

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

            function reveal(elementid) {
                var x = document.getElementById(elementid);
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }
<?php if (!empty($output) && !$show_form): ?>
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
                        <?php if ($show_form): ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="reveal('pass-control');"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="password" class="form-control" id="pass-control"
                                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                           placeholder="New password" required>
                                </div>
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" onclick="reveal('rpass-control');"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="rpassword" class="form-control" id="rpass-control"
                                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                           placeholder="Repeat password" required>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Change Password" class="btn float-right login_btn">
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="form-group">
                                <?php echo (!empty($output)) ? "<span style='color:#00ff00;'>" . $output . "</span>" : ''; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <?php if ($show_form): ?>
                            <div style="font-size: 11px; color: #fff;">Password should be at least 8 characters long<br>
                                Password should contain at least 1 capital letter<br>
                                Password should contain at least 1 lowercase letter<br>
                                Password should contain at least 1 special character<br>
                                Password should contain at least 1 numeric character
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
