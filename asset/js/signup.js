$(document).ready(function () {
    $("#mobile").on("keypress", function () {
        return isNumber_without_dot(event);
    });

    $("#mobile").on("input", function () {
        var elem = document.getElementById('mobile');
        check_error(elem);
    });

    $("#email").on("input", function () {
        var elem = document.getElementById('email');
        check_error(elem);
    });

    $("#password").on("input", function () {
        var elem = document.getElementById('password');
        check_error(elem);
    });

    $("#passwordrep").on("input", function () {
        var elem = document.getElementById('passwordrep');
        check_error(elem);
    });

    $("#rules").on("change", function () {
        var r = $("#rules_err").html();
        if (r.length > 0)
            check_error('rules');
    });

    $("#submit").on("click", function () {
        var valid = 0;
        valid = validation();
        if (valid == 0) {
            signup();
        }
    });

    $("#captcha_code").on("input", function () {
        var elem = document.getElementById('captcha_code');
        check_error(elem);
    });

    $("#captcha_code").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 13) {
            var valid = 0;
            valid = validation();
            if (valid == 0) {
                signup();
            }
            return false;
        } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

});

function validation() {
    //var mobile = $("#mobile").val();
    var email = $("#email").val();
    var password = $("#password").val();
    var re_password = $("#passwordrep").val();
    var captcha = $('#captcha_code').val();

    if (captcha.length === 0) {
        $("#captcha_err").html("خطا : کد امنیتی الزامی است.");
        return -1;
    }


    if (email.length === 0) {
        $("#mobile_err").html("خطا : آدرس ایمیل الزامی است.");
        return -1;
    }

    /*if (mobile.substring(0, 2) != "09" || mobile.length != 11 || isNaN(mobile)) {
        $("#mobile_err").html("خطا : شماره همراه اشتباه می باشد.");
        return -1;
    }*/


    if (password.length === 0) {
        $("#password_err").html("خطا : کلمه عبور الزامی است.");
        return -1;
    }


    if (password.length < 5) {
        $("#password_err").html("خطا : کلمه عبور کوتاه می باشد.");
        return -1;
    }


    if (re_password.length === 0) {
        $("#passwordrep_err").html("خطا : تکرار کلمه عبور الزامی است.");
        return -1;
    }


    if (password !== re_password) {
        $("#passwordrep_err").html("خطا : کلمه عبور و تکرار آن یکسان نمی باشند.");
        return -1;
    }


    if (!document.getElementById('rules').checked) {
        $("#rules_err").html("خطا : قوانین سایت پذیرفته نشده اند.");
        return -1;
    }

    if (email.length > 0) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        var validEmail = re.test(String(email).toLowerCase());
        if (!validEmail) {
            $("#email_err").html("خطا : آدرس ایمیل معتبر نمی باشد.");
            return -1;
        }
    }

    return 0;
}

function signup() {
    //var mobile = $("#mobile").val();
    var email = $("#email").val();
    var password = $("#password").val();
    var captcha = $('#captcha_code').val();
    var refer = $('#refer').val();
    var token = $("#token").val();
    if (refer == "")
        refer = "0";
    /*if (email == "")
        email = "0";*/

    $(".uk-button").attr("disabled", "true");
    $(".spinner-onload").attr("style", "opacity:1");
    $.ajax({
        type: "post",
        url: "../include/check_email_json.php",
        datatype: "json",
        data: {
            arguments: [captcha, email, token]
        },
        success: function (obj) {
            $(".uk-button").removeAttr("disabled");
            $(".spinner-onload").attr("style", "opacity:0");
            if (obj.OK === "OK") {
                $.ajax({
                    type: "post",
                    url: "../include/insert_user_json.php",
                    datatype: "json",
                    data: {
                        arguments: [email, password, refer]
                    },
                    success: function (obj) {
                        if (obj.ok == "ok") {
                            location.replace("../dashboard/");
                            exit();
                        } else {
                            $("#info_title").html("پیام سیستم");
                            $("#info_msg").html('متاسفانه ثبت نام شما با مشکل مواجه گردیده است.لطفاً پس از مدتی مجدد تلاش نمایید و در صورت عدم موفقیت با پشتیبانی تماس بگیرید.');
                            UIkit.modal("#info").show();
                        }
                    }
                });
            } else if (obj.error === "email exist") {
                $("#email_err").html("خطا : آدرس ایمیل موجود می باشد.");

            } else if (obj.error === "username exist") {
                $("#mobile_err").html("خطا : شماره همراه تکراری می باشد.");

            } else if (obj.error === "cap error") {
                $("#captcha_err").html("خطا : کد امنیتی صحیح نمی باشد.");
            } else if (obj.error == 'g_recap_error') {
                $("#captcha_code_err").html("خطا : کد امنیتی گوگل صحیح نمی باشد.");
            } else {
                $("#info_title").html("پیام سیستم");
                $("#info_msg").html('متاسفانه ثبت نام شما با مشکل مواجه گردیده است.لطفاً پس از مدتی مجدد تلاش نمایید و در صورت عدم موفقیت با پشتیبانی تماس بگیرید.');
                UIkit.modal("#info").show();

            }
        }
    });
}

function send_email_to_admin(order_id) {
    var mail_type = 'new user';
    $.ajax({
        type: "post",
        url: "../include/send_email_to_admin.php",
        datatype: "json",
        data: {
            arguments: [mail_type, order_id]
        },
        success: function (obj) {}
    });
}
