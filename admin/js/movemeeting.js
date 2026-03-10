$('document').ready(function () {
    /* validation */
    $("#move-meeting-form").validate({
        rules: {
            date: {
                required: true
            },
            time: {
                required: true
            },
            reason: {
                required: true
            }
        },
        messages: {
            date: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Provide proper date</span>"
            },
            time: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Provide proper time</span>"
            },
            title: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Provide title</span>"
            }
        },
        submitHandler: submitMoveForm
    });
    /* validation */

    /* form submit */
    function submitMoveForm() {
        var data = $("#move-meeting-form").serialize();
        alert(data);
        $.ajax({
            type: 'POST',
            url: '/admin/functions/movemeeting.php',
            data: data,
            beforeSend: function () {
                $("#error").fadeOut();
                $("#btn-submit").html('<span class="glyphicon glyphicon-transfer"></span>   sending ...');
            },
            success: function (data) {
                if (data == "saved") {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-success"><i class="fas fa-info-circle"></i> Successfully saved<br><br> TutorMeet will redirect you automatically.<br>Do not use your browser\'s back or forward buttons.</div>');
                        setTimeout(function () {
                            window.location.href = "/admin/calendar";
                        }, 3000);
                    });
                } else {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' + data + '</div>');
                        setTimeout(function () {
                            $("#error").fadeOut();
                        }, 3000);
                    });
                }
            }
        });
        return false;
    }
});