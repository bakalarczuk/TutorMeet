<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: admin/index.php");
    exit;
}

// Include config file
require_once "admin/includes/config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

$db = new Database;
$link = $db->connect();

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT userId, userLogin, userPass, userName, userSurname, userEmail, userPrivilege, recordId, first, blocked FROM users WHERE userLogin = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $userRealName, $userRealSurname, $userEmail, $userPrivilege, $recid, $first, $blocked);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, check if user is not blocked
                            if ($blocked == 0) {
                                session_start();

                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["realname"] = $userRealName . " " . $userRealSurname;
                                $_SESSION["privilege"] = $userPrivilege;
                                $_SESSION["email"] = $userEmail;
                                $_SESSION["avatar"] = gravatar($userEmail);
                                $_SESSION["recid"] = $recid;
                                $_SESSION["first"] = $first;

                                // Redirect user to welcome page
                                header("location: welcome.php");
                            } else {
                                echo '<script language="javascript">';
                                echo 'alert("Your account is blocked.\nPlease contact with Administration.")';
                                echo '</script>';
                            }
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "Wrong password";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
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
        <title>Login Page</title>
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
        </script>
    </head>

    <body>
        <div class="container">
            <div class="d-flex justify-content-center h-100">
                <div class="card">
                    <div class="card-header">
                        <h3>Sign In</h3>
                        <div class="d-flex justify-content-end social_icon">
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
                                <input type="password" name="password" class="form-control" id="pass-control"
                                       placeholder="password">
                            </div>
                            <!--<div class="row align-items-center remember">
                                    <input type="checkbox">Remember Me
                                </div>-->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <a href="reset.php" style="text-decoration: none; color: #FFC312;">Reset password</a>

                                    </div>
                                    <div class="col-lg-4">
                                        <input type="submit" value="Login" class="btn float-right login_btn">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
                            <?php echo (!empty($username_err)) ? $username_err : ''; ?>
                            <?php echo (!empty($password_err)) ? $password_err : ''; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>

</html>