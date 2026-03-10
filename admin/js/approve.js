$('document').ready(function () {
    /* validation */


    $("#approve-msg-form").validate({
        rules: {
            subject: {
                required: true,
                minlength: 3
            },
            message: {
                required: true,
                minlength: 3
            },
        },
        messages: {
            subject: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
            message: "<span style='color: #f00;'><i class='fas fa-exclamation-circle'></i></span>",
        },
        submitHandler: SendApproveMessage
    });

    function SendApproveMessage() {
        var data = $("#approve-msg-form").serialize();
        $.ajax({
            type: 'POST',
            url: '/admin/functions/message.php',
            data: data + "&mentor=1",
            beforeSend: function () {
                $("#error").fadeOut();
            },
            success: function (data) {
                if (data == "sent") {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-success"><i class="fas fa-info-circle"></i> Message sent succesfuly</div>');
                        $("#approve-msg").val("");
                        setTimeout(function () {
                            $("#error").fadeOut();
                            $("#error").html('');
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