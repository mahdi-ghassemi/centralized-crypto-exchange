$(document).ready(function(){
   $('#orders').DataTable({
        "language": {
            "url": "../../asset/js/persian.json"
        },
        "order": [[1, "desc"]]
    });
    
    $("#new_ticket").click(function() {
        window.location.replace("../../dashboard/ticket/");        
    });
    
});

function view(elem) {
    var id = elem.id;
    $.ajax({
        type: "post",
        url: "../../include/view_ticket.php",
        datatype: "json",
        data: { arguments: [id]},
        success: function(obj) {
            if (obj.ok === "ok") {
                window.location.replace("../../dashboard/view-ticket/");                
            } else {
                if(obj.error === "error_id") {
                    $("#info_title").html("System Message");
                    $("#info_msg").html("Your selected ticket not valid.");
                    UIkit.modal("#info").show();
                }
            }
            
        }
    });
}