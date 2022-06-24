$(document).ready(function () {
    $('#orders').DataTable({
        "language": {
            "url": "../../asset/js/persian.json"
        },
        "lengthMenu": [25, 50, 75, 100]
    });

    $("#copy_addr_btn").click(function () {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($("#address").html()).select();
        document.execCommand("copy");
        $temp.remove();
        UIkit.notification({
            message: '<span class="shabnam">آدرس در حافظه موقت ذخیره گردید.</span>',
            status: 'primary',
            pos: 'bottom-left',
            timeout: 5000
        });
    });

    $("#order_now").click(function () {
        UIkit.modal("#modal_order").show();
    });

    $("#submit_order").click(function () {
        submit_order();
    });

    $("#order_amount").on("keypress", function () {
        if ($("#new_order_error").html() != "")
            $("#new_order_error").html("");
        return isNumber(event);
    });

});

function submit_order() {
    var amount = $("#order_amount").val();    
    var wallet = $("#wallet_address").val();
    var valid = true;
    $("#new_order_error").html("");    
    if (parseFloat(amount) == 0 || amount.length == 0 || isNaN(amount)) {
        $("#new_order_error").html("خطا: مقدار سفارش نامعتبر می باشد.");
        valid = false;
    }    
    if (valid) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/insert_buy_order_from_deposit.php",
            datatype: "json",
            data: {
                arguments: [amount, wallet]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok === "ok") {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html('ضمن تشکر سفارش شما با موفقیت ثبت گردید.لطفاَ از لیست سفارشات در صفحه داشبورد مراحل پرداخت را تکمیل نمایید.در صورت عدم پرداخت مبلغ سفارش بعد از یک ساعت سفارش شما به صورت خودکار لغو می گردد.<br><span class="uk-icon-navy">یادآوری مهم:</span>با توجه به نوسانات قیمت ارزهای دیجیتال و نرخ برابری آنها با ریال ، قیمت فاکتور شما در لحظه ی پرداخت قطعی محاسبه مجدد می گردد. ');
                    UIkit.modal("#modal_order").hide();
                    UIkit.modal("#info").show();                    
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


