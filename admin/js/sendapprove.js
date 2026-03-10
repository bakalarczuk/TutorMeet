$('document').ready(function () {
    /* validation */
    $("#approve-edit-form").validate({
        submitHandler: SendApproveMessage
    });

    function SendApproveMessage() {
        var data = $("#approve-edit-form").serialize();
        $.ajax({
            type: 'POST',
            url: '/admin/functions/approve.php',
            data: data,
            beforeSend: function () {
                $("#error").fadeOut();
            },
            success: function (data) {
                if (data == "sent") {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-success"><i class="fas fa-info-circle"></i> Session approved<br><br> TutorMeet will redirect you automatically.<br>Do not use your browser\'s back or forward buttons.</div>');
                        setTimeout(function () {
                            window.location.href = "/admin/meetings";
                        }, 3000);
                    });
                } else {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i>' + data + '</div>');
                    });
                }
            }
        });
    }
});