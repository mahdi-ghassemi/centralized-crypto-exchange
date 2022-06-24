$(document).ready(function (){    
    $("#submit").click(function() {
        validation();        
    });
    
});


function validation() {
    $("#error1").html("");
    $("#error2").html("");    
    var description = $("#description").val(); 
    var ticket_id = $("#t_id").val();
    if(description.length == 0) {
        $("#error2").html("* توضیحات الزامی می باشد.");
        return false;
    }   
    
    $.ajax({
        type: "post",
        url: "../../include/reply_ticket.php",
        datatype: "json",
        data: { arguments: [description,ticket_id]},
        success: function(obj) {
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
