$('document').ready(function () {
    /* validation */
    $("#message-form").validate({
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
        submitHandler: submitForm
    });
    /* validation */

    /* form submit */
    function submitForm() { 
        var sender = $("#sender").val();
        var message = $("#message").val();
    var subject = $('#subject').val();
    var recipient = $("#recipient").val();
    var form_data = new FormData();
    form_data.append('file', $('#uploadFile').prop('files')[0]);
    form_data.append('sender',sender);
    form_data.append('message',message);
    form_data.append('subject',subject);
    form_data.append('recipient',recipient);
        $.ajax({

            type: 'POST',
            url: 'functions/message.php',
    dataType: 'text',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
            beforeSend: function () {
                $("#error").fadeOut();
                $("#btn-submit").html('<span class="glyphicon glyphicon-transfer"></span>   sending ...');
            },
            success: function (data) {
                if (data == "sent") {
                    $("#error").fadeIn(1000, function () {
                        $("#error").html('<div class="alert alert-success"><i class="fas fa-info-circle"></i> Message sent succesfuly</div>');
                        $("#btn-submit").html('send');
                        $("#message-form").find("input[type=text], input[type=hidden], textarea").val("");
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
        return false;
    }
});