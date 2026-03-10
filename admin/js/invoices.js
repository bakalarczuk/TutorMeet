/* form submit */
function SubmitForm(form) {
    $.confirm({
        title: 'Invoice update!',
        content: 'You are trying to update an invoice<br>Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-red',
                action: function () {
                    location.reload();
                }
            },
            startSession: {
                text: 'Yes',
                btnClass: 'btn-green',
                action: function () {
                    var data = $(form).serialize();
                    $.ajax({
                        type: "POST",
                        url: "functions/invoice.php",
                        data: data + "&send=1",
                        beforeSend: function () {
                            $("#error").fadeOut();
                            $("#btn-submit").html("Updating ...");
                        },
                        success: function (data) {
                            if (data == "saved") {
                                $("#error").fadeIn(1000, function () {
                                    $("#btn-submit").html("Update");
                                    $("#error").html(
                                            '<div class="alert alert-success"><i class="fas fa-info-circle"></i> Invoice saved succesfuly</div>'
                                            );
                                    setTimeout(function () {
                                        $("#error").fadeOut();
                                        location.reload();
                                    }, 2000);
                                });
                            } else {
                                $("#error").fadeIn(1000, function () {
                                    $("#btn-submit").html("Update");
                                    $("#error").html(
                                            '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' +
                                            data +
                                            "</div>"
                                            );
                                    setTimeout(function () {
                                        $("#error").fadeOut();
                                        location.reload();
                                    }, 2000);
                                });
                            }
                        },
                    });
                }
            }
        }
    });

    return false;
}

function UpdateForm(form) {
    $.confirm({
        title: 'Invoice update!',
        content: 'You are trying to update an invoice<br>Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-red',
                action: function () {
                    location.reload();
                }
            },
            startSession: {
                text: 'Yes',
                btnClass: 'btn-green',
                action: function () {
                    var data = $(form).serialize();
                    $.ajax({
                        type: "POST",
                        url: "functions/updateinvoice.php",
                        data: data,
                        beforeSend: function () {
                            $("#error").fadeIn(1000, function () {
                                $("#error").html(
                                        '<div class="alert alert-success"><i class="fas fa-info-circle"></i> Updating...</div>'
                                        );
                            });
                        },
                        success: function (data) {
                            if (data == "saved") {
                                $("#error").fadeIn(1000, function () {
                                    $("#error").html(
                                            '<div class="alert alert-success"><i class="fas fa-info-circle"></i> Invoice updated succesfuly</div>'
                                            );
                                    setTimeout(function () {
                                        $("#error").fadeOut();
                                        location.reload();
                                    }, 2000);
                                });
                            } else {
                                $("#error").fadeIn(1000, function () {
                                    $("#error").html(
                                            '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' +
                                            data +
                                            "</div>"
                                            );
                                    setTimeout(function () {
                                        $("#error").fadeOut();
                                        location.reload();
                                    }, 2000);
                                });
                            }
                        },
                    });
                }
            }
        }
    });

    return false;
}

function SendAll(method) {
    $("form").each(function () {
        var data = $(this).serialize();

        var btntext = "";
        var btntextnext = "";

        btntextnext =
                method == "send" ?
                "Send All No Accounting" :
                method == "sendacc" ?
                "Send All" :
                "Generate";
        btntext =
                method == "send" ?
                "#btn-send-all" :
                method == "sendacc" ?
                "#btn-send-acc" :
                "#btn-send";
        var send =
                method == "send" ?
                "&send=1" :
                method == "sendacc" ?
                "&send=2" :
                "&send=0";

        $.ajax({
            type: "POST",
            url: "functions/invoice.php",
            data: data + send,
            beforeSend: function () {
                $("#error").fadeOut();
                $(btntext).html("Sending ...");
            },
            success: function (data) {
                if (data == "saved") {
                    $("#error").fadeIn(1000, function () {
                        $(btntext).html(btntextnext);
                        $("#error").html(
                                '<div class="alert alert-success"><i class="fas fa-info-circle"></i> Invoice saved succesfuly</div>'
                                );
                        setTimeout(function () {
                            $("#error").fadeOut();
                            $("#error").html('');
                        }, 2000);
                    });
                } else if (data == "") {
                    $("#error").fadeIn(1000, function () {
                        $(btntext).html(btntextnext);
                        $("#error").html(
                                '<div class="alert alert-info"><i class="fas fa-info-circle"></i> There was nothing to do</div>'
                                );
                        setTimeout(function () {
                            $("#error").fadeOut();
                            $("#error").html('');
                        }, 2000);
                    });
                } else {
                    $("#error").fadeIn(1000, function () {
                        $(btntext).html(btntextnext);
                        $("#error").html(
                                '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' +
                                data +
                                "</div>"
                                );
                        setTimeout(function () {
                            $("#error").fadeOut();
                            $("#error").html('');
                        }, 2000);
                    });
                }
            },
        });
    });
}