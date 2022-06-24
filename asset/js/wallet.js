$(document).ready(function () {
    $('#orders').DataTable({
        "language": {
            "url": "../../asset/js/persian.json"
        },
        "order": [[0, "desc"]]
    });

    $("#new_wallet").click(function () {
        UIkit.modal("#modal_wallet").show();
    });

    $("#submit_wallet").click(function () {
        submit_wallet();
    });

    $("#alias").on("input", function () {
        var elem = document.getElementById("alias");
        check_error(elem);
    });
});

function submit_wallet() {
    var alias = $("#alias").val();
    var coin_id = $("#coin_type").val();
    if (alias.length > 3) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/create_user_wallet.php",
            datatype: "json",
            data: {
                arguments: [alias, coin_id]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("کیف پول شما با موفقیت ایجاد گردید.");
                    UIkit.modal("#modal_wallet").hide();
                    UIkit.modal("#info").show();
                    $("#info").on("hidden", function () {
                        window.location.reload();
                    });
                } else {
                    alert(obj.error);
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("ایجاد کیف پول با مشکل مواجه گردید.");
                    UIkit.modal("#modal_wallet").hide();
                    UIkit.modal("#info").show();
                }
            }
        });

    } else {
        $("#alias_err").html("عنوان کیف پول باید حداقل 4 کارکتر باشد.");
        return false;
    }
}
