<?php
// Initialize the session
session_start();

// Include config file
require_once "admin/includes/config.php";
//echo password_hash("default", PASSWORD_DEFAULT);
$db = new Database;
$link = $db->connect();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) :
    header("location: index.php");
    exit;
else :
    if (!isset($_SESSION["first"]) || $_SESSION["first"] !== 1) :
        header("location: admin/index.php");
        exit;
    else :
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

// Check if username is empty
            if (empty(trim($_POST["newpass"]))) {
                $newpass_err = "Please enter new password.";
            } else {
                $newpass = trim($_POST["newpass"]);
            }

// Check if password is empty
            if (empty(trim($_POST["repeatpass"]))) {
                $repeatpass_err = "Please enter your repeatpass.";
            } else {
                $repeatpass = trim($_POST["repeatpass"]);
            }


// Check if password is empty
            if ((trim($_POST["newpass"])) !== (trim($_POST["repeatpass"]))) {
                $nomatchpass_err = "Passwords doesn't match.";
            } else {
                $repeatpass = trim($_POST["repeatpass"]);
            }
//var_dump($_POST);
            if (!isset($_POST['rodo1']))
                $rodo1_err = "No agreement for clause";
            if (!isset($_POST['rodo2']))
                $rodo2_err = "No agreement for clause";
//            if (isset($_POST['bio']))
//                if (strlen($_POST['bio']) == 0 && $_SESSION['privilege'] == 5)
//                    $bio_err = "Please provide bio";
//            if (isset($_POST['bio']))
//                if (strlen($_POST['bio']) < 30 && $_SESSION['privilege'] == 5)
//                    $bio_err = "Please provide longer bio";
// Validate credentials
            if (empty($newpass_err) && empty($repeatpass_err) && empty($nomatchpass_err) && empty($rodo1_err) && empty($rodo2_err) && empty($bio_err)) {
// Prepare a select statement
                $password = password_hash(mysqli_real_escape_string($link, $_POST['newpass']), PASSWORD_DEFAULT);
                $sql = "UPDATE users SET userPass = ?, first = 0  WHERE userId = ?";

                if ($stmt = mysqli_prepare($link, $sql)) {
// Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "si", $password, $_SESSION['id']);

// Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {
// Store result
                        if ($_SESSION['privilege'] == 5) {
                            $sql2 = "UPDATE mentors SET bio = ? WHERE mentorId = ?";
                            if ($stmt2 = mysqli_prepare($link, $sql2)) {
// Bind variables to the prepared statement as parameters
                                mysqli_stmt_bind_param($stmt2, "si", $_POST['bio'], $_SESSION['recid']);

// Attempt to execute the prepared statement
                                if (mysqli_stmt_execute($stmt2)) {
                                    header("location: admin/index.php");
                                }
                            }
                        } else {
                            header("location: admin/index.php");
                        }
                    } else {
// Display an error message if username doesn't exist
                        echo "Error: " . $sql . "<br>" . mysqli_error($link);
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
        ?>
        <!DOCTYPE html>
        <html>

            <head>
                <title>Change Password Page</title>
                <!--Made with love by Mutiullah Samim -->

                <!--Bootsrap 4 CDN-->
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
                      integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

                <!--Fontawesome CDN-->
                <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
                      integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

                <!--Custom styles-->
                <link rel="stylesheet" type="text/css" href="style.css">
            </head>

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

            <body>

                <div class="container">
                    <div class="d-flex justify-content-center h-100">
                        <div class="card">
                            <div class="card-header">
                                <h3>&nbsp;</h3>
                                <div class="d-flex justify-content-end social_icon">
                                    <img src="images/Login_logo.png" id="icon" alt="Logo Icon" />
                                </div>
                            </div>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="card-body">
                                    <div class="input-group form-group">
                                        <div class="input-group-prepend <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                            <span class="input-group-text" onclick="reveal('newpass');"><i
                                                    class="fas fa-key"></i></span>
                                        </div>
                                        <input type="password" name="newpass" class="form-control" id="newpass" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                               placeholder="New Password">

                                    </div>
                                    <div class="input-group form-group">
                                        <div class="input-group-prepend <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                            <span class="input-group-text" onclick="reveal('passrepeat');"><i
                                                    class="fas fa-key"></i></span>
                                        </div>
                                        <input type="password" name="repeatpass" class="form-control" id="passrepeat" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                               placeholder="Repeat Password">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="Login" class="btn float-right login_btn">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div style="color: #FFF; font-size: 11px; margin: 10px;">
                                        <div class="row">
                                            <input type="checkbox" name="rodo1" />&nbsp;I have read and accept all the terms and conditions of use and the notifications of the <a href="https://tutormeet.pl/privacy-policy/" style="color: #00dd1c;" target="_blank">Privacy Policy</a>.
                                        </div>
                                        <div class="row">
                                            <input type="checkbox" name="rodo2" />&nbsp;I consent to the processing of my personal data by TutorMeet for the purpose of receiving their educational offers and services.
                                        </div>
                                        <div class="row">
                                            <input type="checkbox" name="rodo3" />&nbsp;I consent to the processing of my personal data by TutorMeet for marketing materials to be sent to the contact details provided.
                                        </div>
                                        <!--<div class="row">
                                            <input type="checkbox" name="face" />&nbsp;Wizerunek
                                        </div> -->
                                    </div>
                                </div>
                                <div style="color: #F00; font-size: 11px; margin: 10px;">
                                    <div class="justify-content-center">
                                        <div>Password should be at least 8 characters long<br>
Password should contain at least 1 capital letter<br>
Password should contain at least 1 lowercase letter<br>
Password should contain at least 1 special character<br>
Password should contain at least 1 numeric character
                                        </div>
                                        <?php echo (!empty($newpass_err)) ? $newpass_err."<br>" : ''; ?>
                                        <?php echo (!empty($repeatpass_err)) ? $repeatpass_err."<br>" : ''; ?>
                                        <?php echo (!empty($nomatchpass_err)) ? $nomatchpass_err."<br>" : ''; ?>
                                        <?php echo (!empty($rodo1_err)) ? $rodo1_err."<br>" : ''; ?>
                                        <?php echo (!empty($rodo2_err)) ? $rodo2_err."<br>" : ''; ?>                                        
                                        <?php //if ($_SESSION['privilege'] == 5) echo (!empty($bio_err)) ? $bio_err."<br>" : ''; ?>

                                    </div>
                                </div>
                                <?php if ($_SESSION['privilege'] == 5): ?>
                                    <div style="color: #Fff; font-size: 11px; margin: 10px;">
                                        <label>Bio</label>
                                        <textarea class="input-group form-group" rows="5" name="bio" style="color: #000; font-size: 12px;"></textarea>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </body>

        </html>
    <?php
    endif;
endif;
