$(window).on('load', function () {
    var wmz = function () {
        var tmp = null;
        $.ajax({
            'async': false,
            'type': "POST",
            'global': false,
            'dataType': 'json',
            'url': "../include/get_wmz.php",
            'success': function (obj) {
                tmp = obj.wmz;
            }
        });
        return tmp;
    }();

    var amount = function () {
        var tmp = null;
        $.ajax({
            'async': false,
            'type': "POST",
            'global': false,
            'dataType': 'json',
            'url': "../include/get_wmz_amount.php",
            'success': function (obj) {
                tmp = obj.amount;
            }
        });
        return tmp;
    }();

    if (document.getElementsByClassName("button_pay-container") != null)
        $('.button_pay-container').remove();
    window.webmoney.widgets().button.create({
            lang: 'en',
            data: {
                amount: amount,
                purse: wmz,
                desc: 'eSaraafi'
            },
            style: {
                theme: 'wm',
                showAmount: true
            }
        })
        .on('paymentComplete', function (data) {
            // your code
            insert_data(data);
        })
        .mount('wm_pay')
});

function insert_data(data) {
    $.ajax({
        type: "post",
        url: "../include/insert_pay_data.php",
        datatype: "json",
        data: {
            webmoney_data: data
        },
        success: function (obj) {
            if (obj.ok == 'ok') {
                $("#info_title").html("پیام سیستم");
                $("#info_msg").html("با تشکر، پرداخت شما با موفقیت انجام گردید. مبلغ فاکتور شما در اسرع وقت به حساب شما واریز خواهد شد.شما می توانید از صفحه داشبورد وضعیت سفارش خود را پیگیری نمایید.");
                UIkit.modal("#info").show();
                $("#info").on("hidden", function () {
                    window.location.replace("../dashboard/");
                });
            } else {
                if (obj.error == 'data error' || obj.error == 'update error') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("پرداخت شما دچار مشکل گردیده است. چنانچه مبلغ فاکتور از حساب وبمانی شما کسر گردیده است، لطفاً با پشتیبانی تناس بگیرید.");
                    UIkit.modal("#info").show();
                    $("#info").on("hidden", function () {
                        window.location.replace("../dashboard/");
                    });
                }
            }
        }
    });
}
