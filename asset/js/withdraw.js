$(document).ready(function () {
    $('#orders').DataTable({
        "language": {
            "url": "../../asset/js/persian.json"
        },
        "lengthMenu": [25, 50, 75, 100]
    });



    $("#order_now").click(function () {
        UIkit.modal("#modal_order").show();
    });

    $("#submit_order").click(function () {
        submit_order();
    });

    $("#submit_w").click(function () {
        submit_w();
    });

    $("#order_amount").on("keypress", function () {
        if ($("#new_order_error").html() != "")
            $("#new_order_error").html("");
        return isNumber(event);
    });

    $("#amount").on("keypress", function () {
        if ($("#amount_err").html() != "")
            $("#amount_err").html("");
        return isNumber(event);
    });

    $("#amount").on("input", function () {
        var amount = $("#amount").val();
        if (amount > 0) {
            var take_amount = amount - net_fee;
            $("#take_amount").html(take_amount.toFixed(9));
        } else {
            var take_amount = 0;
            $("#take_amount").html(take_amount.toFixed(9));
        }
    });

    $("#sms_code").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 13) {
            submit_sms_verify();
            return false;
        } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    $("#sms_code").on("input", function () {
        $(".alert-danger").attr("style", "display:none;");
        $(".alert-info").attr("style", "display:none;");
    });


    $("#g_code_v").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 13) {
            submit_ga_verify();
            return false;
        } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    $("#g_code_v").on("input", function () {
        $(".alert-danger").attr("style", "display:none;");
        $(".alert-info").attr("style", "display:none;");
    });

    $("#submit_ga_verify").click(function () {
        submit_ga_verify();
    });

    $("#submit_sms_verify").click(function () {
        submit_sms_verify();
    });



});

function submit_order() {
    var amount = $("#order_amount").val();
    var bank_id = $("#bank_shaba").val();
    var user_address = u_w_a;
    var valid = true;
    $("#new_order_error").html("");
    if (parseFloat(amount) == 0 || amount.length == 0 || isNaN(amount)) {
        $("#new_order_error").html("خطا: مقدار سفارش نامعتبر می باشد.");
        valid = false;
    }
    
    if (bank_id == null ) {
        $("#new_order_error").html("خطا: شماره شبا باید انتخاب گردد.اطلاعات بانکی شما تکمیل یا تایید نگردیده است.");
        valid = false;
    }
    if (valid) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/insert_sell_order_from_withdraw.php",
            datatype: "json",
            data: {
                arguments: [amount, bank_id,user_address]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok === "ok") {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html('ضمن تشکر سفارش شما با موفقیت ثبت گردید.');
                    UIkit.modal("#modal_order").hide();
                    UIkit.modal("#info").show();
                } else {
                    if (obj.error == "no balance") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('موجودی شما برای ثبت این سفارش کافی نمی باشد.لطفا مقدار صحیح را وارد نمایید.');
                        UIkit.modal("#modal_order").hide();
                        UIkit.modal("#info").show();
                    } else if (obj.error == "wallet_invalid") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('آدرس کیف پول اشتباه می باشد.');
                        UIkit.modal("#modal_order").hide();
                        UIkit.modal("#info").show();
                    } else if (obj.error == "coin_balance_error") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('موجودی سامانه برای انجام این سفارش در حال حاضر کافی نمی باشد.جهت افزایش موجودی برای انجام سفارش خود می توانید از طریق واتساپ با پشتیبانی تماس حاصل نمایید.');
                        UIkit.modal("#modal_order").hide();
                        UIkit.modal("#info").show();
                    } else {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('ثبت سفارش با مشکل مواجه گردید.لطفاَ پس از مدتی مجدد تلاش نمایید.');
                        UIkit.modal("#modal_order").hide();
                        UIkit.modal("#info").show();
                        $("#info").on("hidden", function () {
                            window.location.reload();
                        });
                    }
                }
            }
        });
    }
}

function submit_w() {
    var amount = $("#amount").val();
    var address = $("#address").val();

    var user_address = u_w_a;

    var valid = true;
    if (parseFloat(amount) == 0 || amount.length == 0 || isNaN(amount)) {
        $("#amount_err").html("خطا: مقدار درخواستی نامعتبر می باشد.");
        valid = false;
    }
    if (address.length == 0) {
        $("#address_err").html("خطا: آدرس گیرنده الزامی می باشد.");
        valid = false;
    }

    if (valid) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/check_withdraw_address_amount.php",
            datatype: "json",
            data: {
                arguments: [amount, address, user_address]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == "ok") {
                    if (obj.mobile_2af == "1") {
                        UIkit.modal("#modal-sms-verify").show();
                    }
                    if (obj.ga_2af == "1") {
                        UIkit.modal("#modal-ga-check").show();
                    }
                } else {
                    if (obj.error == 'email confirm') {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('آدرس ایمیل شما تایید نگردیده است.لطفاً ابتدا از منوی احراز هویت ایمیل خود را تایید نموده و سپس مجدد برای برداشت اقدام نمایید.');
                        UIkit.modal("#info").show();
                    }
                    if (obj.error == 'tow_fa confirm') {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('ورود دو مرحله ای برای حساب شما فعال نمی باشد.لطفاً ابتدا از منوی تنظیمات امنیتی ورود دو مرحله ای را فعال نموده و سپس مجدد برای برداشت اقدام نمایید.');
                        UIkit.modal("#info").show();
                    }
                    if (obj.error == 'no balance') {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('موجودی حساب شما کافی نمی باشد.');
                        UIkit.modal("#info").show();
                    }
                    if (obj.error == 'amount minimum') {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html(' حداقل مقدار قابل برداشت برای کیف پول شما ' + obj.minimum_w + ' واحد می باشد. ');
                        UIkit.modal("#info").show();
                    }
                    if (obj.error == 'address invalid') {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('آدرس کیف پول گیرنده اشتباه می باشد.');
                        UIkit.modal("#info").show();
                    }
                    if (obj.error == 'user wallet invalid') {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('آدرس کیف پول فرستنده اشتباه می باشد.');
                        UIkit.modal("#info").show();
                    }
                }
            }
        });
    }
}

function submit_sms_verify() {
    var v_code = $("#sms_code").val();
    var amount = $("#amount").val();
    var address = $("#address").val();

    var user_address = u_w_a;
    var valid1 = true;
    $(".alert-danger").attr("style", "display:none;");
    if (v_code.length < 1) {
        $(".alert-danger").attr("style", "display:block;");
        valid1 = false;
    }
    if (valid1 == true) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/check_sms_verify_code_for_withdraw.php",
            datatype: "json",
            data: {
                arguments: [v_code, amount, address, user_address]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html('یک ایمیل حاوی لینک تایید برداشت برای شما ارسال گردید.لطفاً به صندوق ورودی ایمیل خود مراجعه نموده و با کلیک بر روی لینک ارسالی، درخواست برداشت را تایید نمایید.لینک ارسالی فقط 24 ساعت برای شما فعال خواهد بود.');
                    UIkit.modal("#modal-sms-verify").hide();
                    UIkit.modal("#info").show();
                    $("#info").on("hidden", function () {
                        window.location.replace("../../dashboard/wallet/");
                    });

                } else {
                    if (obj.error == 'code error') {
                        $(".alert-danger").attr("style", "display:block;");
                        return false;
                    }
                    if (obj.error == 'insert error') {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('ثبت درخواست برداشت با مشکل مواجه گردید.لطفاً پس از مدتی مجدد تلاش نمایید.');
                        UIkit.modal("#info").show();
                    }
                }
            }
        });
    }
}

function submit_ga_verify() {
    var v_code = $("#g_code_v").val();
    var amount = $("#amount").val();
    var address = $("#address").val();

    var user_address = u_w_a;
    var valid1 = true;
    $(".alert-danger").attr("style", "display:none;");
    if (v_code.length < 1) {
        $(".alert-danger").attr("style", "display:block;");
        valid1 = false;
    }
    if (valid1 == true) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/check_ga_verify_for_withdraw.php",
            datatype: "json",
            data: {
                arguments: [v_code, amount, address, user_address]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html('یک ایمیل حاوی لینک تایید برداشت برای شما ارسال گردید.لطفاً به صندوق ورودی ایمیل خود مراجعه نموده و با کلیک بر روی لینک ارسالی، درخواست برداشت را تایید نمایید.لینک ارسالی فقط 24 ساعت برای شما فعال خواهد بود.');
                    UIkit.modal("#modal-ga-check").hide();
                    UIkit.modal("#info").show();
                    $("#info").on("hidden", function () {
                        window.location.replace("../../dashboard/wallet/");
                    });
                } else {
                    if (obj.error == 'code error') {
                        $(".alert-danger").attr("style", "display:block;");
                        return false;
                    }
                    if (obj.error == 'insert error') {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('ثبت درخواست برداشت با مشکل مواجه گردید.لطفاً پس از مدتی مجدد تلاش نمایید.');
                        UIkit.modal("#info").show();
                    } else
                        alert(obj.error);
                }
            }
        });
    }
}
