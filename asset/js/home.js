$(document).ready(function () {
    $("#amount").on("input", function () {
        calculator();
    });
    
    $("#amount_buy_t").on("input", function () {
        calculator_buy();
    });
    
    $("#amount_sell_t").on("input", function () {
        calculator_sell();
    });
    

    $("#cur_name").on("change", function () {
        calculator();
    });

    $('#users').data('countToOptions', {
        formatter: function (value, options) {
            return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
        }
    });

    // start all the timers
    $('.timer').each(count);

    $('#buy_btn').on("click", function () {
        var amount = $("#amount").val();
        var coin_type = $("#cur_name").val();
        var order_type = "2";


        if (!isNaN(amount) && amount > 0) {
            $("#order_amount").val(amount);
            $('#coin_type option:eq(' + (coin_type - 1) + ')').prop('selected', true);
            $('#order_type option:eq(' + (order_type - 1) + ')').prop('selected', true);
            $("#shaba_div").attr("class", "disabled_div");
            $("#w_addr_div").attr("class", "enabled_div");
            UIkit.modal("#modal_order").show();


            /*$(".uk-button").attr("disabled", "true");
            $(".spinner-onload").attr("style", "opacity:1");
            $.ajax({
                type: "post",
                url: "./include/insert_order_quick.php",
                datatype: "json",
                data: {
                    arguments: [amount, coin_type, order_type]
                },
                success: function (obj) {
                    $(".uk-button").removeAttr("disabled");
                    $(".spinner-onload").attr("style", "opacity:0");
                    if (obj.ok === "ok") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('ضمن تشکر سفارش شما با موفقیت ثبت گردید.لطفاَ از لیست سفارشات در صفحه داشبورد مراحل پرداخت را تکمیل نمایید.در صورت عدم پرداخت مبلغ سفارش بعد از یک ساعت سفارش شما به صورت خودکار لغو می گردد.<br><span class="uk-icon-navy">یادآوری مهم:</span>با توجه به نوسانات قیمت ارزهای دیجیتال و نرخ برابری آنها با ریال ، قیمت فاکتور شما در لحظه ی پرداخت قطعی محاسبه مجدد می گردد. ');

                        UIkit.modal("#info").show();
                        $("#info").on("hidden", function () {
                            window.location.replace("./dashboard/");
                        });
                    } else {
                        if (obj.error == "bank_error") {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html(' حساب بانکی شما تایید نگردیده است.چنانچه هنوز اطلاعات حساب بانکی خود را ثبت ننموده اید لطفا ابتدا از منوی تنظبمات بانکی اقدام به ثبت اطلاعات حساب بانکی خود نموده و پس از تایید آن مجدد سفارش خود را ثبت نمایید.');
                            UIkit.modal("#info").show();
                            $("#info").on("hidden", function () {
                                window.location.replace("./dashboard/identity/index.php?p=bank");
                            });
                        } else if (obj.error == "id_error") {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html(' مراحل احراز هویت شما کامل انجام نشده و یا هنوز تایید نگردیده است. لطفا ابتدا از منوی احراز هویت اقدام به ثبت اطلاعات هویتی خود نموده و پس از تایید آن مجدد سفارش خود را ثبت نمایید.');
                            UIkit.modal("#info").show();
                            $("#info").on("hidden", function () {
                                window.location.replace("./dashboard/identity/index.php?p=identity");
                            });

                        } else if (obj.error == "login") {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html(' لطفاً ابتدا به سیستم ورود نمایید.');
                            UIkit.modal("#info").show();
                            $("#info").on("hidden", function () {
                                window.location.replace("./login/");
                            });
                        } else if (obj.error == "amount error") {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html(' مبلغ فاکتور شما باید بین 100 الی 49,998,000 تومان باشد.چنانچه قصد خرید در مبالغ بیش از سقف ذکر شده را دارید لطفا سفارش خود را قالب چند سفارش ثبت نمایید.');
                            UIkit.modal("#modal_order").hide();
                            UIkit.modal("#info").show();
                        } else {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html('ثبت سفارش با مشکل مواجه گردید.لطفاَ پس از مدتی مجدد تلاش نمایید.');

                            UIkit.modal("#info").show();
                            $("#info").on("hidden", function () {
                                window.location.reload();
                            });
                        }
                    }
                }
            });*/
        }
    });


    $('#sell_btn').on("click", function () {
        var amount = $("#amount").val();
        var coin_type = $("#cur_name").val();
        var order_type = "1";
        if (!isNaN(amount) && amount > 0) {
            $("#shaba_div").attr("class", "enabled_div");
            $("#w_addr_div").attr("class", "disabled_div");
            $("#order_amount").val(amount);
            $('#coin_type option:eq(' + (coin_type - 1) + ')').prop('selected', true);
            $('#order_type option:eq(' + (order_type - 1) + ')').prop('selected', true);

            UIkit.modal("#modal_order").show();

            /*$(".uk-button").attr("disabled", "true");
            $(".spinner-onload").attr("style", "opacity:1");
            $.ajax({
                type: "post",
                url: "./include/insert_order_quick.php",
                datatype: "json",
                data: {
                    arguments: [amount, coin_type, order_type]
                },
                success: function (obj) {
                    $(".uk-button").removeAttr("disabled");
                    $(".spinner-onload").attr("style", "opacity:0");
                    if (obj.ok === "ok") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html('ضمن تشکر سفارش شما با موفقیت ثبت گردید.لطفاَ از لیست سفارشات در صفحه داشبورد مراحل پرداخت را تکمیل نمایید.در صورت عدم پرداخت مبلغ سفارش بعد از یک ساعت سفارش شما به صورت خودکار لغو می گردد.<br><span class="uk-icon-navy">یادآوری مهم:</span>با توجه به نوسانات قیمت ارزهای دیجیتال و نرخ برابری آنها با ریال ، قیمت فاکتور شما در لحظه ی پرداخت قطعی محاسبه مجدد می گردد. ');

                        UIkit.modal("#info").show();
                        $("#info").on("hidden", function () {
                            window.location.replace("./dashboard/");
                        });
                    } else {
                        if (obj.error == "bank_error") {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html(' حساب بانکی شما تایید نگردیده است.چنانچه هنوز اطلاعات حساب بانکی خود را ثبت ننموده اید لطفا ابتدا از منوی تنظبمات بانکی اقدام به ثبت اطلاعات حساب بانکی خود نموده و پس از تایید آن مجدد سفارش خود را ثبت نمایید.');
                            UIkit.modal("#info").show();
                            $("#info").on("hidden", function () {
                                window.location.replace("./dashboard/identity/index.php?p=bank");
                            });
                        } else if (obj.error == "id_error") {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html(' مراحل احراز هویت شما کامل انجام نشده و یا هنوز تایید نگردیده است. لطفا ابتدا از منوی احراز هویت اقدام به ثبت اطلاعات هویتی خود نموده و پس از تایید آن مجدد سفارش خود را ثبت نمایید.');
                            UIkit.modal("#info").show();
                            $("#info").on("hidden", function () {
                                window.location.replace("./dashboard/identity/index.php?p=identity");
                            });

                        } else if (obj.error == "login") {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html(' لطفاً ابتدا به سیستم ورود نمایید.');
                            UIkit.modal("#info").show();
                            $("#info").on("hidden", function () {
                                window.location.replace("./login/");
                            });
                        } else {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html('ثبت سفارش با مشکل مواجه گردید.لطفاَ پس از مدتی مجدد تلاش نمایید.');

                            UIkit.modal("#info").show();
                            $("#info").on("hidden", function () {
                                window.location.reload();
                            });
                        }
                    }
                }
            });*/
        }
    });

    $(".order-btn").on("click", function () {
        var coin_id = this.id.substring(3);
        $('#coin_type option:eq(' + (coin_id - 1) + ')').prop('selected', true);
        UIkit.modal("#modal_order").show();

    });

    $("#order_amount").on("keypress", function () {
        if ($("#new_order_error").html() != "")
            $("#new_order_error").html("");
        if ($("#bank_shaba_error").html() != "")
            $("#bank_shaba_error").html("");

        return isNumber(event);
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

    $("#submit_order").click(function () {
        var valid = submit_order();
    });

    /*$("#language").on("change",function() {        
        var lang_id = $("#language").val();
        if(lang_id === "1") {
            window.location.replace("https://esaraafi.ir/");
            
        } else if(lang_id === "2") {
             window.location.replace("https://en.esaraafi.ir/");            
        }        
    });*/
});

function count(options) {
    var $this = $(this);
    options = $.extend({}, options || {}, $this.data('countToOptions') || {});
    $this.countTo(options);
}

function calculator() {
    var amount = $("#amount").val();
    var coin = $("#cur_name").val();
    if (!isNaN(amount) && amount > 0) {
        $.ajax({
            url: "./include/calculator.php",
            type: "post",
            datatype: "json",
            data: {
                arguments: [amount, coin]
            },
            success: function (obj) {
                if (obj.ok === 'ok') {
                    $("#amount_buy_t").val(obj.buy);
                    $("#amount_sell_t").val(obj.sell);
                    $("#buy_btn").html("خرید از ما = " + obj.buy);
                    $("#sell_btn").html("فروش به ما = " + obj.sell);
                }
            }
        });
    }
}

function calculator_buy() {
    var amount = $("#amount_buy_t").val();
    var coin = $("#cur_name").val();
    if (!isNaN(amount) && amount > 0) {
        $.ajax({
            url: "./include/calculator_buy.php",
            type: "post",
            datatype: "json",
            data: {
                arguments: [amount, coin]
            },
            success: function (obj) {
                if (obj.ok === 'ok') {
                    $("#amount").val(obj.amount);
                    $("#amount_sell_t").val(obj.sell);
                    $("#buy_btn").html("خرید از ما = " + obj.buy);
                    $("#sell_btn").html("فروش به ما = " + obj.sell);
                }
            }
        });
    }

}

function calculator_sell() {
    var amount = $("#amount_sell_t").val();
    var coin = $("#cur_name").val();
    if (!isNaN(amount) && amount > 0) {
        $.ajax({
            url: "./include/calculator_sell.php",
            type: "post",
            datatype: "json",
            data: {
                arguments: [amount, coin]
            },
            success: function (obj) {
                if (obj.ok === 'ok') {
                    $("#amount").val(obj.amount);
                    $("#amount_buy_t").val(obj.buy);
                    $("#buy_btn").html("خرید از ما = " + obj.buy);
                    $("#sell_btn").html("فروش به ما = " + obj.sell);
                }
            }
        });
    }

}

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
            url: "./include/insert_order.php",
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
                        window.location.replace("./dashboard/");
                    });
                } else {
                    if (obj.error == "bank_error") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html(' حساب بانکی شما تایید نگردیده است.چنانچه هنوز اطلاعات حساب بانکی خود را ثبت ننموده اید لطفا ابتدا از منوی تنظبمات بانکی اقدام به ثبت اطلاعات حساب بانکی خود نموده و پس از تایید آن مجدد سفارش خود را ثبت نمایید.');
                        UIkit.modal("#modal_order").hide();
                        UIkit.modal("#info").show();
                        $("#info").on("hidden", function () {
                            window.location.replace("./dashboard/identity/index.php?p=bank");
                        });
                    } else if (obj.error == "id_error") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html(' مراحل احراز هویت شما کامل انجام نشده و یا هنوز تایید نگردیده است. لطفا ابتدا از منوی احراز هویت اقدام به ثبت اطلاعات هویتی خود نموده و پس از تایید آن مجدد سفارش خود را ثبت نمایید.');
                        UIkit.modal("#modal_order").hide();
                        UIkit.modal("#info").show();
                        $("#info").on("hidden", function () {
                            window.location.replace("./dashboard/identity/index.php?p=identity");
                        });
                    } else if (obj.error == "login") {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html(' لطفاً ابتدا به سیستم ورود نمایید.');
                        UIkit.modal("#info").show();
                        $("#info").on("hidden", function () {
                            window.location.replace("./login/");
                        });
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
