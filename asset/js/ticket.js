$(document).ready(function () {
    $("#save_bottom").click(function () {
        validation();
    });
});


function validation() {
    $("#error1").html("");
    $("#error2").html("");
    var subject = $("#subject").val();
    var description = $("#description").val();
    if (subject.length == 0) {
        $("#error1").html("* عنوان الرامی می باشد.");
        return false;
    }
    if (description.length == 0) {
        $("#error2").html("* توضیحات الزامی می باشد.");
        return false;
    }

    if (subject.length > 200)
        subject = subject.substr(0, 200);
    var ticket_type = $("#ticket_type").val();
    $(".uk-button").attr("disabled", "true");
    $(".spinner-onload").attr("style", "opacity:1");

    $.ajax({
        type: "post",
        url: "../../include/insert_ticket.php",
        datatype: "json",
        data: {
            arguments: [subject, description, ticket_type]
        },
        success: function (obj) {
            $(".uk-button").removeAttr("disabled");
            $(".spinner-onload").attr("style", "opacity:0");
            if (obj.ok === "ok") {
                $("#info_title").html("پیام سیستم");
                $("#info_msg").html("تیکت شما با موفقیت ثبت گردید.");
                UIkit.modal("#info").show();
                send_email_to_support(obj.ticket_id, obj.ticket_type, obj.subject, obj.description, obj.create_date, obj.create_time);
                $("#info").on("hidden", function () {
                    window.location.replace("../../dashboard/support/");
                });
            } else {
                $("#info_title").html("پیام سیستم");
                $("#info_msg").html("ثبت تیکت با مشکل مواجه گردید.لطفا پس از مدتی مجدد تلاش نمایید.");
                UIkit.modal("#info").show();

            }
        }

    });
}

function reply(elem) {
    var ticket_id = elem.id.substring(2);
    $.ajax({
        type: "post",
        url: "../../include/reply_ticket_select.php",
        datatype: "json",
        data: {
            arguments: [ticket_id]
        },
        success: function (obj) {
            if (obj.ok === "ok") {
                window.location.replace("../../dashboard/ticket-reply/");
            } else {
                if (obj.error === "error_id") {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("تیکت انتخاب شده معتبر نمی باشد.");
                    UIkit.modal("#info").show();
                }
            }

        }
    });
}

function send_email_to_support(ticket_id, ticket_type, subject, description, create_date, create_time) {
    $.ajax({
        type: "post",
        url: "../../include/send_email_to_support.php",
        datatype: "json",
        data: {
            arguments: [ticket_id, ticket_type, subject, description, create_date, create_time]
        },
        success: function (obj) {}
    });
}
