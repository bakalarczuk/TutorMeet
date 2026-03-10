$('document').ready(function () {
    /* validation */
    $("#assign-form").validate({
        rules: {
            aplicant: {
                required: true
            },
            mentorid: {
                required: true
            },
            hours: {
                required: true
            },
            country: {
                required: true
            }
        },
        messages: {
            aplicant: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>"
            },
            mentorid: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>"
            },
            hours: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>"
            },
            country: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>"
            }
        },
        submitHandler: submitForm
    });
    /* validation */

    /* form submit */
    function submitForm() {
        var data = $("#assign-form").serialize();
        $.ajax({
            type: 'POST',
            url: 'functions/assign.php',
            data: data,
            beforeSend: function () {
                $("#error").fadeOut();
            },
            success: function (data) {
                if (data == "saved") {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-success"><i class="fas fa-info-circle"></i> Successfully saved<br><br> TutorMeet will redirect you automatically.<br>Do not use your browser\'s back or forward buttons.</div>');
                        setTimeout(function () {
                            window.location.href = "/admin";
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