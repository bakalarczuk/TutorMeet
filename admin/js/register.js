$('document').ready(function () {
    /* validation */
    $("#register-form").validate({
        rules: {
            name: {
                required: true,
                minlength: 3
            },
            lastname: {
                required: true,
                minlength: 3
            },
            username: {
                required: true,
                minlength: 3
            },
            password: {
                required: true,
                minlength: 8,
                maxlength: 15
            },
            cpassword: {
                required: true,
                equalTo: '#password'
            },
            email: {
                required: true,
                email: true
            },
            privilege: {
                required: true
            },
            street: {
                required: function () {
                    return $('#privilege').val() != 6;
                }
            },
            streetno: {
                required: function () {
                    return $('#privilege').val() != 6;
                }
            },
            postalcode: {
                required: function () {
                    return $('#privilege').val() != 6;
                }
            },
            town: {
                required: function () {
                    return $('#privilege').val() != 6;
                }
            },
            account: {
                required: true
            },
            rate: {
                required: true
            },
            type: {
                required: function () {
                    return $('#privilege').val() == 6;
                }
            },
            contract: {
                required: function () {
                    return $('#privilege').val() == 5;
                }
            },
        },
        messages: {
            name: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Enter a Valid Name</span>",
            lastname: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Enter a Valid Last Name</span>",
            username: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Enter a Valid Username</span>",
            password: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Provide a Password</span>",
                minlength: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Password Needs To Be Minimum of 8 Characters</span>"
            },
            email: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Enter a Valid Email</span>",
            cpassword: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Retype Your Password</span>",
                equalTo: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Password Mismatch! Retype</span>"
            },
            street: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
            streetno: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
            postalcode: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
            town: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
            account: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
            rate: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
            type: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
            contract: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
        },
        submitHandler: submitForm
    });
    /* validation */

    /* form submit */
    function submitForm() {
        var data = $("#register-form").serialize();
        $.ajax({

            type: 'POST',
            url: 'functions/register.php',
            data: data,
            beforeSend: function () {
                $("#error").fadeOut();
                $("#btn-submit").html('<span class="glyphicon glyphicon-transfer"></span>   sending ...');
            },
            success: function (data) {
                if (data == 1) {

                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-danger"><span><i class="fas fa-exclamation-circle"></i> Account with this email exists. Try with different email.</span></div>');
                    });
                } else if (data == "registered") {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-success"><i class="fas fa-info-circle"></i> New user registered succesfuly</div>');
                        $("#register-form").find("input[type=text], input[type=hidden], input[type=email], input[type=password], textarea").val("");
                        setTimeout(function () {
                            $("#error").fadeOut();
                            $("#error").html('');
                            location.reload();
                        }, 1000);
                    });
                } else {

                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' + data + '</div>');
                    });
                }
            }
        });
        return false;
    }
    /* form submit */

    $('select#privilege').on('change', function () {
        if (this.value == 5) {
            $.ajax({

                type: 'POST',
                url: 'functions/getcontent.php',
                data: {//dane do wysyłki
                    filename: "mentor"
                },
                success: function (data) {
                    $("#userDataContent").html(data);
                }
            });
        } else if (this.value == 6) {
            $.ajax({

                type: 'POST',
                url: 'functions/getcontent.php',
                data: {//dane do wysyłki
                    filename: "aplikant"
                },
                success: function (data) {
                    $("#userDataContent").html(data);
                }
            });
        } else if (this.value == 9) {
            $.ajax({

                type: 'POST',
                url: 'functions/getcontent.php',
                data: {//dane do wysyłki
                    filename: "rodzic"
                },
                success: function (data) {
                    $("#userDataContent").html(data);
                }
            });
        } else {
            $("#userDataContent").html('');
        }
    });
});

function SubmitMentor(form) {
    var data = $(form).serialize();
    $.ajax({
        type: 'POST',
        url: '/admin/functions/updatementor.php',
        data: data,
        beforeSend: function () {
            $("#error").fadeOut();
            $("#error").html('');
        },
        success: function (data) {
            if (data == "saved") {
                $("#error").fadeIn(1000, function () {
                    $("#error").html('<div class="alert alert-success"><i class="fas fa-info-circle"></i> Data updated succesfuly</div>');
                    $("#register-form").find("input[type=text], input[type=hidden], input[type=email], input[type=password], textarea").val("");
                    setTimeout(function () {
                        $("#error").fadeOut();
                        $("#error").html('');
                    }, 3000);
                });
            } else {
                $("#error").fadeIn(1000, function () {
                    $("#error").html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' + data + '</div>');
                });
            }
        }
    });
    return false;
}

function SubmitAplicant(form) {
    var data = $(form).serialize();
    $.ajax({
        type: 'POST',
        url: '/admin/functions/updateaplicant.php',
        data: data,
        beforeSend: function () {
            $("#error").fadeOut();
            $("#error").html('');
        },
        success: function (data) {
            if (data == "saved") {
                $("#error").fadeIn(1000, function () {
                    $("#error").html('<div class="alert alert-success"><i class="fas fa-info-circle"></i> Data updated succesfuly</div>');
                    $("#register-form").find("input[type=text], input[type=hidden], input[type=email], input[type=password], textarea").val("");
                    setTimeout(function () {
                        $("#error").fadeOut();
                        $("#error").html('');
                    }, 3000);
                });
            } else {
                $("#error").fadeIn(1000, function () {
                    $("#error").html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' + data + '</div>');
                });
            }
        }
    });
    return false;
}

function UpdateUser(block, id) {
    var data = "blocked=" + block + "&userid=" + id;
    $.ajax({
        type: 'POST',
        url: '/admin/functions/updateuser.php',
        data: data,
        beforeSend: function () {
            $("#error").fadeOut();
            $("#error").html('');
        },
        success: function (data) {
            if (data == "saved") {
                                $('#allusersdiv').load(document.URL +  ' #allusersdiv');
                  $.alert({
                    title: 'User updated!',
                    content: 'User data updated.',
                  });
            } else {
                $("#error").fadeIn(3000, function () {
                    $("#error").html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' + data + '</div>');
                });
            }
        }
    });
    return false;
}


function DeleteUser(id, name) {
    $.confirm({
        title: 'User deletion!',
        content: 'You\'re trying to delete user:\n\
  <strong>' + name + '</strong>.\n\nAre you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-red'
            },
            startSession: {
                text: 'Yes',
                btnClass: 'btn-green',
                action: function () {
                    var data = "userid=" + id;
                    $.ajax({
                        type: 'POST',
                        url: '/admin/functions/deleteuser.php',
                        data: data,
                        beforeSend: function () {
                            $("#error").fadeOut();
                            $("#error").html('');
                        },
                        success: function (data) {
                            if (data == "saved") {
                                $('#allusersdiv').load(document.URL +  ' #allusersdiv');
                  $.alert({
                    title: 'User deleted!',
                    content: 'User data deleted.',
                  });
                            } else {
                                $("#error").fadeIn(3000, function () {
                                    $("#error").html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' + data + '</div>');
                                });
                            }
                        }
                    });
                }
            }
        }
    });
    return false;
}
function GenerateNewPass(id, name) {
    $.confirm({
        title: 'User password reset!',
        content: 'You\'re trying to reset password for user:\n\
  <strong>' + name + '</strong>.\n\nAre you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-red'
            },
            startSession: {
                text: 'Yes',
                btnClass: 'btn-green',
                action: function () {
                    var data = "userid=" + id;
                    $.ajax({
                        type: 'POST',
                        url: '/admin/functions/userpassreset.php',
                        data: data,
                        beforeSend: function () {
                            $("#error").fadeOut();
                            $("#error").html('');
                        },
                        success: function (data) {
                            if (data == "saved") {
                                $('#allusersdiv').load(document.URL +  ' #allusersdiv');
                  $.alert({
                    title: 'Password reset!',
                    content: 'The user\'s password has been successfully changed.',
                  });
                            } else {
                                $("#error").fadeIn(3000, function () {
                                    $("#error").html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' + data + '</div>');
                                });
                            }
                        }
                    });
                }
            }
        }
    });
    return false;
}

