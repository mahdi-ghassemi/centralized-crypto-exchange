$(document).ready(function () {
    $('#orders').DataTable({
        "language": {
            "url": "../../asset/js/persian.json"
        },
        "order": [[0, "desc"]]
    });

    $("#new_robot").click(function () {
        $("#key_error").html("");
        $("#api_key").val("");
        $("#secret_key").val("");
        $("#low_risk").prop("checked", true);
        $("#no_limit").prop("checked", true);
        $("#short_trade").prop("checked", true);
        UIkit.modal("#modal_new_bot_step1").show();
    });

    $("#go_step2").click(function () {
        UIkit.modal("#modal_new_bot_step1").hide();
        UIkit.modal("#modal_new_bot_step2").show();
    });

    $("#go_step1").click(function () {
        UIkit.modal("#modal_new_bot_step2").hide();
        UIkit.modal("#modal_new_bot_step1").show();
    });

    $("#api_key").on("input", function () {
        $("#key_error").html("");
    });

    $("#api_key").on("input", function () {
        $("#secret_key").html("");
    });

    $("#go_step3").click(function () {
        var name = $("#name").val();
        var api_key = $("#api_key").val();
        var secret_key = $("#secret_key").val();
        var exchange_id = $("#exchange_id").val();
        var risk = $('input[name="risk_profit_choice"]:checked').val();
        var limitation = $('input[name="limit_choice"]:checked').val();
        var time_trade = $('input[name="time_choice"]:checked').val();
        var coin_pair_id = $("#coin_pair_id").val();
        var valid = true;
        if (name.length == 0) {
            $("#key_error").html("خطا: نام ربات الزامی می باشد.");
            valid = false;
        }
        if (api_key.length == 0 && exchange_id == 1) {
            $("#key_error").html("خطا: کلید رابط الزامی می باشد.");
            valid = false;
        }
        if (secret_key.length == 0 && exchange_id == 1) {
            $("#key_error").html("خطا: کلید محرمانه الزامی می باشد.");
            valid = false;
        }

        if (valid) {
            $(".uk-button").attr("disabled", "true");
            $(".spinner-onload").attr("style", "opacity:1");
            $.ajax({
                type: "post",
                url: "../../include/insert_bot.php",
                datatype: "json",
                data: {
                    arguments: [api_key, secret_key, exchange_id, risk, limitation, time_trade, coin_pair_id,name]
                },
                success: function (obj) {
                    $(".uk-button").removeAttr("disabled");
                    $(".spinner-onload").attr("style", "opacity:0");
                    if (obj.ok === "ok") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('تبریک ، ربات شما با موفقیت ایجاد گردید.');
                        UIkit.modal("#modal_new_bot_step2").hide();
                        UIkit.modal("#info").show();
                        $("#info").on("hidden", function () {
                            window.location.reload();
                        });
                    } else {
                        if (obj.error === "key error") {
                            $("#key_error").html("خطا: کلید معتبر نمی باشد.");
                        }
                    }
                },
                error: function () {
                    $(".uk-button").removeAttr("disabled");
                    $(".spinner-onload").attr("style", "opacity:0");
                    $("#key_error").html("خطا: کلیدها معتبر نمی باشند.");
                }
            });
        }
    });

    $("#exchange_id").on("change", function () {
        var exchange_id = $("#exchange_id").val();
        $.ajax({
            type: "post",
            url: "../../include/get_bot_coin_pair_by_exch_id.php",
            datatype: "json",
            data: {
                arguments: [exchange_id]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok === "ok") {
                    var coin_pairs = [];
                    coin_pairs = obj.coin_pairs;
                    var sel = $("#coin_pair_id");
                    sel.empty();
                    for (var i = 0; i < coin_pairs.length; i++) {
                        sel.append($('<option></option>').attr('value', coin_pairs[i]['id']).text(coin_pairs[i]['title_fa'] + ' ( ' + coin_pairs[i].title_en + ' )'));
                    }
                    if (exchange_id == 1) {
                        $("#api_key").attr("placeholder", "کلید رابط را وارد نمایید");
                        $("#secret_key").attr("placeholder", "کلید محرمانه را وارد نمایید");
                        $("#api_key").prop("disabled", false);
                        $("#secret_key").prop("disabled", false);
                        $("#api_key").focus();
                    }
                    if (exchange_id == 2) {
                        $("#api_key").attr("placeholder", "احتیاج به وارد کردن کلید رابط نمی باشد");
                        $("#secret_key").attr("placeholder", "احتیاج به وارد کردن کلید محرمانه نمی باشد");
                        $("#api_key").prop("disabled", true);
                        $("#secret_key").prop("disabled", true);
                    }
                }
            }
        });
    });
});
