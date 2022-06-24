$(document).ready(function () {
    $('#orders').DataTable({
        "language": {
            "url": "../asset/js/persian.json"
        },
        "order": [[0, "desc"]]
    });

    $('body').on('click', '#new_order', function () {
        UIkit.modal("#modal_order").show();
    });

    $("#order_amount").on("keypress", function () {
        if ($("#new_order_error").html() != "")
            $("#new_order_error").html("");
        if ($("#bank_shaba_error").html() != "")
            $("#bank_shaba_error").html("");

        return isNumber(event);
    });

    $("#submit_order").click(function () {
        var valid = submit_order();
    });

    $(".cnl").on("click", function () {
        var order_id = this.id.substring(2);
        $("#vid2").val('');
        $("#vid2").val(order_id);
        UIkit.modal("#del-modal").show();
    });

    $(".pay").on("click", function () {
        var order_id = this.id.substring(2);
        pay_order(order_id);
        //$("#pid2").val('');
        //$("#pid2").val(order_id);
        //UIkit.modal("#pay-modal").show();
    });

    $(".info").on("click", function () {
        var order_id = this.id.substring(2);
        show_invoice(order_id);
    });

    $("#submit_delete_order").on("click", function () {
        delete_order();
    });

    $("#submit_pay_order").on("click", function () {
        pay_order();
    });

    $("#order_type").on("change", function () {
        $("#new_order_error").html("");
        $("#bank_shaba_error").html("");
        $("#wallet_address_error").html("");
        var order_type = $("#order_type").val();
        if (order_type == "1") {
            $("#shaba_div").attr("class", "enabled_div");
            $("#w_addr_div").attr("class", "disabled_div");
        }
        if (order_type == "2") {
            $("#shaba_div").attr("class", "disabled_div");
            $("#w_addr_div").attr("class", "enabled_div");
        }

    });

    $("#coin_type").on("change", function () {
        $("#new_order_error").html("");
        $("#bank_shaba_error").html("");

    });


});


function submit_order() {
    var amount = $("#order_amount").val();
    var coin_type = $("#coin_type").val();
    var order_type = $("#order_type").val();
    var bank_shaba = $("#bank_shaba").val();
    var wallet = $("#wallet_address").val();
    var valid = true;
    $("#new_order_error").html("");
    $("#bank_shaba_error").html("");
    $("#wallet_address_error").html("");
    if (parseFloat(amount) == 0 || amount.length == 0 || isNaN(amount)) {
        $("#new_order_error").html("خطا: مقدار سفارش نامعتبر می باشد.");
        valid = false;
    }
    if (order_type == "1" && bank_shaba == null) {
        $("#bank_shaba_error").html("خطا: حساب بانکی شما  تایید نگردیده است.");
        valid = false;
    }
    if (order_type == "2" && wallet.length == 0) {
        $("#wallet_address_error").html("خطا: آدرس کیف پول خود را وارد نمایید.");
        valid = false;
    }
    if (valid) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../include/insert_order.php",
            datatype: "json",
            data: {
                arguments: [amount, coin_type, order_type, bank_shaba, wallet]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok === "ok") {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html('ضمن تشکر سفارش شما با موفقیت ثبت گردید.لطفاَ از لیست سفارشات در صفحه داشبورد مراحل پرداخت را تکمیل نمایید.در صورت عدم پرداخت مبلغ سفارش بعد از یک ساعت سفارش شما به صورت خودکار لغو می گردد.<br><span class="uk-icon-navy">یادآوری مهم:</span>با توجه به نوسانات قیمت ارزهای دیجیتال و نرخ برابری آنها با ریال ، قیمت فاکتور شما در لحظه ی پرداخت قطعی محاسبه مجدد می گردد. ');
                    UIkit.modal("#modal_order").hide();
                    UIkit.modal("#info").show();
                    $("#info").on("hidden", function () {
                        window.location.reload();
                    });
                } else {
                    if (obj.error == "bank_error") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html(' حساب بانکی شما تایید نگردیده است.چنانچه هنوز اطلاعات حساب بانکی خود را ثبت ننموده اید لطفا ابتدا از منوی تنظبمات بانکی اقدام به ثبت اطلاعات حساب بانکی خود نموده و پس از تایید آن مجدد سفارش خود را ثبت نمایید.');
                        UIkit.modal("#modal_order").hide();
                        UIkit.modal("#info").show();
                    } else if (obj.error == "id_error") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html(' مراحل احراز هویت شما کامل انجام نشده و یا هنوز تایید نگردیده است. لطفا ابتدا از منوی احراز هویت اقدام به ثبت اطلاعات هویتی خود نموده و پس از تایید آن مجدد سفارش خود را ثبت نمایید.');
                        UIkit.modal("#modal_order").hide();
                        UIkit.modal("#info").show();
                    } else if (obj.error == "amount error") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html(' مبلغ فاکتور شما باید بین 100 الی 49,998,000 تومان باشد.چنانچه قصد خرید در مبالغ بیش از سقف ذکر شده را دارید لطفا سفارش خود را قالب چند سفارش ثبت نمایید.');
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

function delete_order() {
    var order_id = $("#vid2").val();
    if (order_id.length > 0) {        
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../include/delete_temp_order.php",
            datatype: "json",
            data: {
                arguments: [order_id]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("فاکتور با موفقیت کنسل گردید.");
                    UIkit.modal("#info").show();
                    $("#info").on("hidden", function () {
                        window.location.reload();
                    });
                } else {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("متاسفانه کنسل نمودن فاکتور با مشکل مواجه گردید.لطفا با پشتیبانی تناس بگیرید.");
                    UIkit.modal("#info").show();
                }
            }
        });

    } else {
        $("#info_title").html("پیام سیستم");
        $("#info_msg").html("کنسل نمودن این فاکتور مقدور نمی باشد.");
        UIkit.modal("#info").show();
    }
}

function pay_order(order_id) {
    //var order_id = $("#pid2").val();
    if (order_id.length > 0) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../include/set_pay_data.php",
            datatype: "json",
            data: {
                arguments: [order_id]
            },
            success: function (obj) {
                if (obj.ok === 'ok') {
                    var order_type = obj.order_type;
                    if (order_type == "1") {
                        var coin_id = obj.coin_id;
                        if (coin_id == "1") { //bitcoin
                            window.location.replace("../bitcoin/");
                        }
                        if (coin_id == "2") { //webmoney
                            window.location.replace("../webmoney/");
                        }
                        if (coin_id == "3") { //tether
                            window.location.replace("../tether/");
                        }
                    }
                    if (order_type == "2") {
                        if (obj.error == 'amount error') {
                            $(".uk-button").removeAttr("disabled");
                            $(".spinner-onload").attr("style", "opacity:0");
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html("مبلغ سفارش باید بین 100 الی  49998000 تومان باشد.");
                            UIkit.modal("#info").show();
                        } else
                            window.location.replace("../dashboard/payment/");
                    }
                } else {
                    $(".uk-button").removeAttr("disabled");
                    $(".spinner-onload").attr("style", "opacity:0");
                    alert(obj.error);
                }

            }
        });
    } else {
        $(".uk-button").removeAttr("disabled");
        $(".spinner-onload").attr("style", "opacity:0");
        $("#info_title").html("پیام سیستم");
        $("#info_msg").html("متاسفانه پرداخت فاکتور با مشکل مواجه گردید.لطفا با پشتیبانی تناس بگیرید.");
        UIkit.modal("#info").show();
    }
}

function show_invoice(order_id) {
    if (order_id.length > 0) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../include/create_invoice.php",
            datatype: "json",
            data: {
                arguments: [order_id]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#inv_order_id").html("");
                    $("#inv_order_date").html("");
                    $("#inv_order_time").html("");
                    $("#inv_order_type").html("");
                    $("#inv_coin_type").html("");
                    $("#inv_order_status").html("");
                    $("#inv_coin_amount").html("");
                    $("#inv_amount_usd").html("");
                    $("#inv_usd_toman").html("");
                    $("#inv_fee_karmozd").html("");
                    $("#inv_fee_network").html("");
                    $("#inv_fee_shaparak").html("");
                    $("#inv_amount_toman").html("");
                    $("#inv_wallet_addr").html("");
                    $("#inv_tx_id").html("");
                    $("#inv_card_no").html("");
                    $("#inv_shaba").html("");
                    $("#inv_bank_res").html("");
                    $("#inv_pay_date").html("");
                    $("#inv_pay_time").html("");

                    $("#inv_order_id").html(order_id);
                    $("#inv_order_date").html(obj.order_date);
                    $("#inv_order_time").html(obj.order_time);
                    $("#inv_order_type").html(obj.order_type_title);
                    $("#inv_coin_type").html(obj.coin_name_fa);
                    $("#inv_order_status").html(obj.order_status_title);
                    $("#inv_coin_amount").html(obj.amount + ' ' + obj.symbol);
                    $("#inv_amount_usd").html(obj.amount_usd);
                    $("#inv_usd_toman").html(obj.fee_us);
                    $("#inv_fee_karmozd").html(obj.fee_karmozd);
                    $("#inv_fee_network").html(obj.fee_network);
                    $("#inv_fee_shaparak").html(obj.fee_shaparak);
                    $("#inv_amount_toman").html(obj.amount_toman);
                    $("#inv_wallet_addr").html(obj.addr);
                    $("#inv_tx_id").html(obj.txID);
                    $("#inv_card_no").html(obj.card_number);
                    $("#inv_shaba").html("IR" + obj.shaba);
                    $("#inv_bank_res").html(obj.site_invoice_number);
                    $("#inv_pay_date").html(obj.site_pay_date);
                    $("#inv_pay_time").html(obj.site_pay_time);
                    UIkit.modal("#modal_invoice").show();
                } else {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("دسترسی به اطلاعات این سفارش در حال حاضر مقدور نمی باشد.لطفا پس از مدت کوتاهی مجدد تلاش نمایید.");
                    UIkit.modal("#info").show();


                }
            }
        });

    } else {
        $("#info_title").html("پیام سیستم");
        $("#info_msg").html("مشاهده فاکتور با مشکل موجه گردید.");
        UIkit.modal("#info").show();
    }
}
