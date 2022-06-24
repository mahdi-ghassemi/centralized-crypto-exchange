$(document).ready(function () {
    //    $("#mobile").on("keypress", function () {
    //        return isNumber_without_dot(event);
    //    });

    $("#email").on("input", function () {
        var elem = document.getElementById('email');
        check_error(elem);
    });


    $("#password").on("input", function () {
        var elem = document.getElementById('password');
        check_error(elem);
    });


    $("#mobile_rp").on("input", function () {
        var elem = document.getElementById('mobile_rp');
        check_error(elem);
    });

    $("#email_rp").on("input", function () {
        var elem = document.getElementById('email_rp');
        check_error(elem);
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
                login();
            }
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

    $("#sms_code_rp").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 13) {
            submit_sms_verify_rp();
            return false;
        } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    $("#mobile_rp").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 13) {
            check_reset();
            return false;
        } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    $("#email_rp").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 13) {
            check_reset();
            return false;
        }
        return true;
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

    $("#submit_sms_verify_rp").click(function () {
        submit_sms_verify_rp();
    });

    $("#send_sms_rp").click(function () {
        check_reset();
    });

    $("#submit").on("click", function () {
        var valid = 0;
        valid = validation();
        if (valid == 0) {
            login();
        }
    });
    
    $("#signup").on("click", function () {
        window.location.replace("../signup/");      
    });

});

function validation() {
    var email = $("#email").val();
    var password = $("#password").val();
    var captcha = $('#captcha_code').val();
    

    if (captcha.length === 0) {
        $("#captcha_code_err").html("خطا : کد امنیتی الزامی است.");
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

    if (email.length > 0) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        var validEmail = re.test(String(email).toLowerCase());
        if (!validEmail) {
            $("#email_err").html("خطا : آدرس ایمیل معتبر نمی باشد.");
            return -1;
        }
    }


    if (password.length === 0) {
        $("#password_err").html("خطا : کلمه عبور الزامی است.");
        return -1;
    }

    return 0;
}

function login() {
    var email = $("#email").val();
    var password = $("#password").val();
    var captcha = $('#captcha_code').val();
    var token = $("#token").val();
    $(".uk-button").attr("disabled", "true");
    $(".spinner-onload").attr("style", "opacity:1");
    $.ajax({
        type: "post",
        url: "../include/check_user_login_json.php",
        datatype: "json",
        data: {
            arguments: [email, password, captcha,token]
        },
        success: function (obj) {
            $(".uk-button").removeAttr("disabled");
            $(".spinner-onload").attr("style", "opacity:0");
            if (obj.ok == 'ok') {
                if (obj.tow_fa == "1") {
                    if (obj.mobile_2af == "1") {
                        send_sms();
                        UIkit.modal("#modal-sms-verify").show();
                    }
                    if (obj.ga_2af == "1") {
                        UIkit.modal("#modal-ga-check").show();
                    }

                } else {
                    location.replace(obj.url);
                    exit();
                }

            } else {
                if (obj.error == 'cap error') {
                    $("#captcha_code_err").html("خطا : کد امنیتی صحیح نمی باشد.");
                } else if (obj.error == 'g_recap_error') {
                    $("#captcha_code_err").html("خطا : کد امنیتی گوگل صحیح نمی باشد.");
                } else {
                    $("#mobile_err").html("خطا : نام کاربری یا کلمه عبور اشتباه می باشد.");
                }
            }
        }
    });
}

function send_sms() {
    $.ajax({
        type: "post",
        url: "../include/send_sms_verify_code_again.php",
        datatype: "json",
        success: function (obj) {
            if (obj.ok == 'ok') {}
        }
    });
}

function send_sms_again() {
    $(".uk-button").attr("disabled", "true");
    $(".spinner-onload").attr("style", "opacity:1");
    $.ajax({
        type: "post",
        url: "../include/send_sms_verify_code_again.php",
        datatype: "json",
        success: function (obj) {
            if (obj.ok == 'ok') {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                $(".alert-info").attr("style", "display:block;");
            }
        }
    });
}

function submit_sms_verify() {
    var v_code = $("#sms_code").val();
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
            url: "../include/check_sms_verify_code_for_login.php",
            datatype: "json",
            data: {
                arguments: [v_code]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    location.replace(obj.url);
                    exit();
                } else {
                    if (obj.error == 'code error') {
                        $(".alert-danger").attr("style", "display:block;");
                        return false;
                    }
                }
            }
        });
    }
}

function submit_ga_verify() {
    var v_code = $("#g_code_v").val();
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
            url: "../include/check_ga_verify_for_login.php",
            datatype: "json",
            data: {
                arguments: [v_code]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    location.replace(obj.url);
                    exit();
                } else {
                    if (obj.error == 'code error') {
                        $(".alert-danger").attr("style", "display:block;");
                        return false;
                    } else
                        alert(obj.error);
                }
            }
        });
    }
}

function check_reset() {
    var mobile = $("#mobile_rp").val();
    var email = $("#email_rp").val();
    var valid = true;

    if (mobile.length == 0) {
        valid = false;
        $("#mobile_rp_err").html("خطا : شماره همراه الزامی است.");
    }
    if (email.length == 0) {
        valid = false;
        $("#email_rp_err").html("خطا : آدرس ایمیل الزامی است.");
    }
    if (valid == true) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../include/reset_password.php",
            datatype: "json",
            data: {
                arguments: [mobile, email]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    UIkit.modal("#modal-sms-verify_rp").hide();
                    UIkit.modal("#modal-sms-verify_reset").show();
                } else {
                    if (obj.error == 'not confirm') {
                        UIkit.modal("#modal-sms-verify_rp").hide();
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html("متاسفانه شماره همراه یا آدرس ایمیل شما تایید نگردیده است و امکان بازیابی کلمه عبور شما از طریق سامانه وجود ندارد.لطفاً جهت بازیابی کلمه عبور خود با پشتیبانی تماس بگیرید.");
                        UIkit.modal("#info").show();
                    }
                    if (obj.error == 'invalid') {
                        UIkit.modal("#modal-sms-verify_rp").hide();
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html("متاسفانه شماره همراه یا آدرس ایمیل شما اشتباه می باشد و امکان بازیابی کلمه عبور شما از طریق سامانه وجود ندارد.لطفاً جهت بازیابی کلمه عبور خود با پشتیبانی تماس بگیرید.");
                        UIkit.modal("#info").show();
                    }
                }
            }
        });
    }
}

function submit_sms_verify_rp() {
    var v_code = $("#sms_code_rp").val();
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
            url: "../include/check_sms_verify_code_for_reset_password.php",
            datatype: "json",
            data: {
                arguments: [v_code]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    UIkit.modal("#modal-sms-verify_reset").hide();
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("کاربر گرامی، کلمه عبور جدید به آدرس ایمیل شما ارسال گردید.");
                    UIkit.modal("#info").show();
                    $("#info").on("hidden", function () {
                        window.location.reload();
                    });

                } else {
                    if (obj.error == 'code error') {
                        $(".alert-danger").attr("style", "display:block;");
                        return false;
                    }
                }
            }
        });
    }
}
