$('document').ready(function () {
    /* validation */
    $("#summary-edit-form").validate({
        rules: {
            summary: {
                required: true,
                minlength: 30
            }
        },
        messages: {
            summary: {
                required: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Provide a meeting summary</span>",
                minlength: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i> Meeting summary Needs To Be Minimum of 30 Characters</span>"
            }
        },
        submitHandler: submitEditForm
    });
    /* validation */

    /* form submit */
    function submitEditForm() {
        var dane = $("#summary-edit-form").serialize();
        $.ajax({
            type: 'POST',
            url: '/admin/functions/summaryedit.php',
            data: dane,
            beforeSend: function () {
                $("#error").fadeOut();
                $("#btn-submit").html('<span class="glyphicon glyphicon-transfer"></span>   sending ...');
            },
            success: function (w) {
                if (w == "saved") {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-success"><i class="fas fa-info-circle"></i> Summary successfully saved<br><br> TutorMeet will redirect you automatically.<br>Do not use your browser\'s back or forward buttons.</div>');
                        setTimeout(function () {
                            window.location.href = "/admin/conferences";
                        }, 3000);
                    });
                } else {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i>' + w + '</div>');
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