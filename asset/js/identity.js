$(document).ready(function () {

    $("#2fa_chb").on("change", function () {
        if ($("#2fa_chb").prop("checked") == true) {
            $("#2fa_opt").attr("class", "enabled_div uk-grid-margin uk-first-column");
        } else {
            $("#2fa_opt").attr("class", "disabled_div uk-grid-margin uk-first-column");
            $("#mobile_2af").removeAttr("checked");
            $("#ga_2af").removeAttr("checked");
        }

    });

    $('#bank_id').SumoSelect({
        csvDispCount: 1,
        placeholder: 'یک گزینه را برگزینید...',
        search: true,
        searchText: 'جستجو...',
        noMatch: 'هیچ موردی یافت نشد'
    });

    $('#sec_ques_id').SumoSelect({
        csvDispCount: 1,
        placeholder: 'یک گزینه را برگزینید...',
        search: true,
        searchText: 'جستجو...',
        noMatch: 'هیچ موردی یافت نشد'
    });

    var t = $('#orders').DataTable({
        "language": {
            "url": "../../asset/js/persian.json"
        },
        "order": [[0, "desc"]]
    });

    $("#birthdate").pDatepicker({
        initialValue: false,
        observer: true,
        format: 'YYYY/MM/DD',
        autoClose: true,
        onSelect: function () {
            $("#birthdate_err").html("");
        }
    });



    $("#securtiy").click(function () {
        $("#content").attr("style", "display:block");
        $("#content2").attr("style", "display:none");
        $("#content3").attr("style", "display:none");
        $("#content4").attr("style", "display:none");
        $("#content5").attr("style", "display:none");
    });

    $("#identity").click(function () {
        $("#content").attr("style", "display:none");
        $("#content2").attr("style", "display:block");
        $("#content3").attr("style", "display:none");
        $("#content4").attr("style", "display:none");
        $("#content5").attr("style", "display:none");
    });

    $("#bank").click(function () {
        $("#content").attr("style", "display:none");
        $("#content2").attr("style", "display:none");
        $("#content3").attr("style", "display:block");
        $("#content4").attr("style", "display:none");
        $("#content5").attr("style", "display:none");
    });

    $("#mobile_conf").click(function () {
        $("#content").attr("style", "display:none");
        $("#content2").attr("style", "display:none");
        $("#content3").attr("style", "display:none");
        $("#content4").attr("style", "display:block");
        $("#content5").attr("style", "display:none");
    });

    $("#email_conf").click(function () {
        $("#content").attr("style", "display:none");
        $("#content2").attr("style", "display:none");
        $("#content3").attr("style", "display:none");
        $("#content4").attr("style", "display:none");
        $("#content5").attr("style", "display:block");
    });

    var btn = $("#btn").val();
    if (btn == "2") {
        document.getElementById("identity").click();
        //$("#identity").click(); 
    } else if (btn == "3") {
        document.getElementById("bank").click();
        //$("#bank").click();        
    } else if (btn == "4") {
        document.getElementById("mobile_conf").click();
        //$("#bank").click();        
    }

    $("#pass_chang_btn").click(function () {
        update_password();
    });


    $("#towfa_chang_btn").click(function () {
        update_2fa();
    });

    $("#submit_sms_verify").click(function () {
        submit_sms_verify();
    });

    $("#submit_first_ga_verify").click(function () {
        submit_first_ga_verify();
    });

    $("#submit_ga_verify").click(function () {
        submit_ga_verify();
    });

    $("#send_sms_again").click(function () {
        send_sms_again();
    });

    $("#sec_ques_btn").click(function () {
        save_sec_ques();
    });

    $("#profile_save_btn").click(function () {
        profile_save();
    });

    $("#bank_save_btn").click(function () {
        bank_save();
    });

    $("#check_mobile_confirm_code").click(function () {
        check_mobile_confirm_code();
    });

    $("#send_sms").click(function () {
        var mobile = $("#mobile").val();
        var valid = true;
        if (mobile.length == 0) {
            valid = false;
            $("#mobile_err").html("شماره همراه را وارد نمایید.");
        } else {
            if (mobile.substring(0, 2) != "09" || mobile.length != 11 || isNaN(mobile)) {
                $("#mobile_err").html("شماره همراه اشتباه می باشد.");
                valid = false;
            }
        }
        if (valid) {
            send_sms_again();
        }
    });

    $("#send_sms_again_2").click(function () {
        send_sms_again();
    });

    $("#check_email_confirm_code").click(function () {
        check_email_confirm_code();
    });

    $("#send_email_code").click(function () {
        send_email_code();
    });

    $("#sms_code").on("input", function () {
        $(".alert-danger").attr("style", "display:none;");
        $(".alert-info").attr("style", "display:none;");
    });

    $("#mobile_confirm_code").on("input", function () {
        $(".alert-danger").attr("style", "display:none;");
        $(".alert-info").attr("style", "display:none;");
    });

    $("#email_confirm_code").on("input", function () {
        $(".alert-danger").attr("style", "display:none;");
        $(".alert-info").attr("style", "display:none;");
    });

    $("#g_code").on("input", function () {
        $(".alert-danger").attr("style", "display:none;");
        $(".alert-info").attr("style", "display:none;");
    });

    $("#g_code_v").on("input", function () {
        $(".alert-danger").attr("style", "display:none;");
        $(".alert-info").attr("style", "display:none;");
    });

    $("#sec_answ").on("input", function () {
        var elem = document.getElementById("sec_answ");
        check_error(elem);
    });

    $("#firstname").on("input", function () {
        var elem = document.getElementById("firstname");
        check_error(elem);
    });

    $("#lastname").on("input", function () {
        var elem = document.getElementById("lastname");
        check_error(elem);
    });

    $("#fathername").on("input", function () {
        var elem = document.getElementById("fathername");
        check_error(elem);
    });

    $("#code_meli").on("input", function () {
        var elem = document.getElementById("code_meli");
        check_error(elem);
    });

    $("#card_number").on("input", function () {
        var elem = document.getElementById("card_number");
        check_error(elem);
    });

    $("#shaba").on("input", function () {
        var elem = document.getElementById("shaba");
        check_error(elem);
    });

    $("#acc_number").on("input", function () {
        var elem = document.getElementById("acc_number");
        check_error(elem);
    });


    $("#submit_first_ga_verify").on("input", function () {
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

    $("#g_code").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 13) {
            submit_first_ga_verify();
            return false;
        } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
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

    $("#code_meli").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    $("#card_number").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    $("#acc_number").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    $("#mobile_confirm_code").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 13) {
            check_mobile_confirm_code();
            return false;
        } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    $("#email_confirm_code").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 13) {
            check_email_confirm_code();
            return false;
        } else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

    $("#shaba").on("keypress", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });

});

function update_password() {
    var valid = validation_password();
    if (valid) {
        var password = $("#password").val();
        var new_password = $("#new_password").val();
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/update_password.php",
            datatype: "json",
            data: {
                arguments: [password, new_password]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("به روز رسانی کلمه عبور با موفقیت انجام گردید.");
                    UIkit.modal("#info").show();
                }
                if (obj.error == 'update') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("به روز رسانی کلمه عبور با شکست مواجه گردید.");
                    UIkit.modal("#info").show();
                }
                if (obj.error == 'pass error') {
                    $("#password_err").html("کلمه عبور اشتباه می باشد.");
                }
            }
        });
    }
}

function validation_password() {
    var password = $("#password").val();
    var new_password = $("#new_password").val();
    var re_new_password = $("#re_new_password").val();


    if (password.length < 1) {
        $("#password_err").html("این فیلد ضروری می باشد.");
        return false;
    }

    if (new_password.length < 1) {
        $("#new_password").html("این فیلد ضروری می باشد.");
        return false;
    }

    if (new_password.length > 0) {
        if (re_new_password !== new_password) {
            $("#re_new_password_err").html("کلمه عبور جدید و تکرار آن یکسان نیستند.");
            return false;
        }
    }
    return true;
}

function update_2fa() {
    var tow_fa = "";
    var mobile_2af = "";
    var ga_2af = "";

    if ($("#2fa_chb").prop("checked") == true)
        tow_fa = "1";
    else
        tow_fa = "0";

    if (document.getElementById("mobile_2af") != null) { 
        if ($("#mobile_2af").prop("checked") == true)
            mobile_2af = "1";
        else
            mobile_2af = "0";
    } else
        mobile_2af = "0";


    if ($("#ga_2af").prop("checked") == true)
        ga_2af = "1";
    else
        ga_2af = "0";

    $(".uk-button").attr("disabled", "true");
    $(".spinner-onload").attr("style", "opacity:1");
    $.ajax({
        type: "post",
        url: "../../include/check_current_2fa.php",
        datatype: "json",
        success: function (obj) {
            if (obj.ok == 'ok') {
                var tow_fa_current = obj.tow_fa_current;
                var mobile_2af_current = obj.mobile_2af_current;
                var ga_2af_current = obj.ga_2af_current;
                var mobile_confirm = obj.mobile_confirm;
                check_2fa_situation(tow_fa_current, mobile_2af_current, ga_2af_current, tow_fa, mobile_2af, ga_2af, mobile_confirm);
            }
        }
    });
}

function check_2fa_situation(tow_fa_current, mobile_2af_current, ga_2af_current, tow_fa, mobile_2af, ga_2af, mobile_confirm) {
    /*console.log('tow_fa_current: ' + tow_fa_current);
    console.log('mobile_2af_current: ' + mobile_2af_current);
    console.log('ga_2af_current: ' + ga_2af_current);
    console.log('tow_fa: ' + tow_fa);
    console.log('mobile_2af: ' + mobile_2af);
    console.log('ga_2af: ' + ga_2af);
    console.log('mobile_confirm: ' + mobile_confirm);*/

    if (tow_fa_current == "0" && tow_fa == "0") {
        $(".uk-button").removeAttr("disabled");
        $(".spinner-onload").attr("style", "opacity:0");
    } else if (tow_fa_current == "0" && tow_fa == "1") {
        if (mobile_2af_current == "0" && mobile_2af == "1") {
            send_sms_code_and_update(mobile_2af, tow_fa, ga_2af);
        }
        if (ga_2af_current == "0" && ga_2af == "1") {
            create_ga_code_and_update(ga_2af, tow_fa, mobile_2af);
        }

    } else if (tow_fa_current == "1" && tow_fa == "0") {
        if (mobile_2af_current == "1") {
            send_sms_code_and_update(mobile_2af, tow_fa, ga_2af);
        }
        if (ga_2af_current == "1") {
            send_ga_code_and_update(ga_2af, tow_fa, mobile_2af);
        }


    } else if (tow_fa_current == "1" && tow_fa == "1") {
        if (mobile_2af == "1") {
            send_sms_code_and_update(mobile_2af, tow_fa, ga_2af);
        }
        if (ga_2af == "1") {
            create_ga_code_and_update(ga_2af, tow_fa, mobile_2af);
        }

    }
}

function send_sms_code_and_update(mobile_2af, tow_fa, ga_2af) {
    $.ajax({
        type: "post",
        url: "../../include/send_sms_2fa.php",
        data: {
            arguments: [mobile_2af, tow_fa, ga_2af]
        },
        datatype: "json",
        success: function (obj) {
            $(".uk-button").removeAttr("disabled");
            $(".spinner-onload").attr("style", "opacity:0");
            if (obj.ok == 'ok') {
                UIkit.modal("#modal-sms-verify").show();

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
            url: "../../include/check_sms_verify_code.php",
            datatype: "json",
            data: {
                arguments: [v_code]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    if (obj.status == 'on')
                        $("#info_msg").html("ورود دو مرحله ای با موفقیت برای شما فعال گردید.");
                    if (obj.status == 'off')
                        $("#info_msg").html("ورود دو مرحله ای با موفقیت برای شما غیرفعال گردید.");
                    UIkit.modal("#info").show();
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

function check_mobile_confirm_code() {
    var v_code = $("#mobile_confirm_code").val();
    var mobile = $("#mobile").val();
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
            url: "../../include/check_mobile_confirm.php",
            datatype: "json",
            data: {
                arguments: [v_code,mobile]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#mobile_confirm_div").empty();
                    $("#mobile_confirm_footer").remove();
                    var data = '<div class="uk-text-center"><img width="80" height="149" src="../../asset/img/mobile_check2.png" alt="mobile check"><p class="calculate-title">شماره همراه شما با موفقیت تایید گردید.</p></div>';
                    $("#mobile_confirm_div").append(data);

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

function send_sms_again() {
    var mobile = $("#mobile").val();
    $(".uk-button").attr("disabled", "true");
    $(".spinner-onload").attr("style", "opacity:1");
    $.ajax({
        type: "post",
        url: "../../include/send_sms_verify_code_again.php",
        datatype: "json",
        data: {
            arguments: [mobile]
        },
        success: function (obj) {
            if (obj.ok == 'ok') {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                $(".alert-info").attr("style", "display:block;");
            }
        }
    });
}

function create_ga_code_and_update(ga_2af, tow_fa, mobile_2af) {
    $.ajax({
        type: "post",
        url: "../../include/create_ga_2fa.php",
        datatype: "json",
        data: {
            arguments: [ga_2af, tow_fa, mobile_2af]
        },
        success: function (obj) {
            $(".uk-button").removeAttr("disabled");
            $(".spinner-onload").attr("style", "opacity:0");
            if (obj.ok == 'ok') {
                $("#ga_qrcode").attr("src", obj.qrCodeUrl);
                UIkit.modal("#modal-ga-create").show();
            }
        }
    });
}

function submit_first_ga_verify() {
    var v_code = $("#g_code").val();
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
            url: "../../include/check_ga_verify_code.php",
            datatype: "json",
            data: {
                arguments: [v_code]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("ورود دو مرحله ای با موفقیت برای شما فعال گردید.");
                    UIkit.modal("#info").show();
                } else {
                    if (obj.error == 'code error') {
                        $(".alert-danger").attr("style", "display:block;");
                        return false;
                    } else {
                        $("#info_title").html("پیام سیستم");
                        $("#info_msg").html("فعال سازی ورود دو مرحله ای با شکست مواجه گردید.لطفا بعد از مدتی مجدد تلاش نمایید.");
                        UIkit.modal("#info").show();
                    }
                }
            }
        });
    }
}

function send_sms_code_for_2af_off(mobile_2af, tow_fa, ga_2af) {
    $.ajax({
        type: "post",
        url: "../../include/send_sms_2fa.php",
        data: {
            arguments: [mobile_2af, tow_fa, ga_2af]
        },
        datatype: "json",
        success: function (obj) {
            $(".uk-button").removeAttr("disabled");
            $(".spinner-onload").attr("style", "opacity:0");
            if (obj.ok == 'ok') {
                UIkit.modal("#modal-sms-verify").show();
            }
        }
    });

}

function send_ga_code_and_update(ga_2af, tow_fa, mobile_2af) {
    $.ajax({
        type: "post",
        url: "../../include/ga_2fa_on_off.php",
        datatype: "json",
        data: {
            arguments: [ga_2af, tow_fa, mobile_2af]
        },
        success: function (obj) {
            $(".uk-button").removeAttr("disabled");
            $(".spinner-onload").attr("style", "opacity:0");
            if (obj.ok == 'ok') {
                UIkit.modal("#modal-ga-check").show();
            }
        }
    });


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
            url: "../../include/check_ga_verify_on_off.php",
            datatype: "json",
            data: {
                arguments: [v_code]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    if (obj.status == 'on')
                        $("#info_msg").html("ورود دو مرحله ای با موفقیت برای شما فعال گردید.");
                    if (obj.status == 'off')
                        $("#info_msg").html("ورود دو مرحله ای با موفقیت برای شما غیرفعال گردید.");
                    UIkit.modal("#info").show();
                } else {
                    if (obj.error == 'code error') {
                        $(".alert-danger").attr("style", "display:block;");
                        return false;
                    } else {
                        $("#info_title").html("پیام سیستم");
                        if (obj.status == 'on')
                            $("#info_msg").html("فعال سازی ورود دو مرحله ای با شکست مواجه گردید.لطفا بعد از مدتی مجدد تلاش نمایید.");
                        if (obj.status == 'off')
                            $("#info_msg").html("غیرفعال سازی ورود دو مرحله ای با شکست مواجه گردید.لطفا بعد از مدتی مجدد تلاش نمایید.");

                        UIkit.modal("#info").show();
                    }
                }
            }
        });
    }

}

function save_sec_ques() {
    var sec_ques_id = $("#sec_ques_id").val();
    var sec_answ = $("#sec_answ").val();
    if (sec_answ.length < 3) {
        $("#sec_answ_err").html("پاسخ سوال امنیتی باید حداقل 3 کارکتر باشد.");
        return false;
    }
    if (sec_answ.length >= 3) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/update_security_question.php",
            datatype: "json",
            data: {
                arguments: [sec_ques_id, sec_answ]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("ثبت سوال امنیتی با موفقیت انجام گردید.");
                    UIkit.modal("#info").show();
                }
                if (obj.error == 'update') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("ثبت سوال امنیتی با شکست مواجه گردید.");
                    UIkit.modal("#info").show();
                }
            }
        });


    }


}

function profile_save() {
    var valid = true;
    var regExfa = /^[\u0600-\u06FF\s]*$/;
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var fathername = $("#fathername").val();
    var code_meli = $("#code_meli").val();
    var birthday = $("#birthdate").val();

    if (firstname.length < 1) {
        $('#firstname_err').html('نام  ضروری می باشد');
        valid = false;
    } else if (!(regExfa.test(firstname)) || firstname.length < 3) {
        $('#firstname_err').html('لطفا نام را صحیح وارد نمایید');
        valid = false;
    }

    if (lastname.length < 1) {
        $('#lastname_err').html('نام خانوادگی ضروری می باشد');
        valid = false;
    } else if (!(regExfa.test(lastname)) || lastname.length < 3) {
        $('#lastname_err').html('لطفا نام خانوادگی را صحیح وارد نمایید');
        valid = false;
    }

    if (fathername.length < 1) {
        $('#fathername_err').html('نام پدر ضروری می باشد');
        valid = false;
    } else if (!(regExfa.test(fathername)) || fathername.length < 3) {
        $('#fathername_err').html('لطفا نام پدر را صحیح وارد نمایید');
        valid = false;
    }

    if (code_meli.length != 10) {
        $('#code_meli_err').html('لطفا کد ملی را صحیح وارد نمایید');
        valid = false;
    }

    if (birthday.length < 1) {
        $('#birthdate_err').html('تاریخ تولد ضروری می باشد');
        valid = false;
    }
    if (birthday.length > 0) {
        var fa_b = persianJs(birthday).toEnglishNumber().toString();
        var y = fa_b.substr(0, 4);
        var d = new Date();
        var n = d.getFullYear();
        var dif = parseInt(n) - (parseInt(y) + 621);
        if (dif < 18) {
            $('#birthdate_err').html('سن باید بالاتر از ۱۸ سال باشد.');
            valid = false;
        }
    }

    if (valid) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/update_profile.php",
            datatype: "json",
            data: {
                arguments: [firstname, lastname, fathername, code_meli, birthday]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("ثبت مشخصات هویتی با موفقیت انجام گردید.");
                    UIkit.modal("#info").show();
                }
                if (obj.error == 'update') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("ثبت مشخصات هویتی با شکست مواجه گردید.");
                    UIkit.modal("#info").show();
                }
            }
        });
    }
}

function bank_save() {
    var valid = true;
    var card_number = $("#card_number").val();
    var acc_number = $("#acc_number").val();
    var shaba = $("#shaba").val();
    var bank_name = $("#bank_id option:selected").text();


    if (card_number.length < 1) {
        $('#card_number_err').html('شماره کارت ضروری می باشد');
        valid = false;
    } else if (card_number.length != 16) {
        $('#card_number_err').html('لطفا شماره کارت را صحیح وارد نمایید');
        valid = false;
    }

    /* if (acc_number.length < 1) {
        $('#acc_number_err').html('شماره حساب ضروری می باشد');
        valid = false;
    }


    if (shaba.length < 1) {
        $('#shaba_err').html('شماره شبا ضروری می باشد');
        valid = false;
    } else if (shaba.length != 24) {
        $('#shaba_err').html('لطفا شماره شبا را صحیح وارد نمایید');
        valid = false;
    }
*/


    if (valid) {
        $(".uk-button").attr("disabled", "true");
        $(".spinner-onload").attr("style", "opacity:1");
        $.ajax({
            type: "post",
            url: "../../include/update_bank_info.php",
            datatype: "json",
            data: {
                arguments: [card_number, shaba, bank_name, acc_number]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("ثبت اطلاعات حساب بانکی با موفقیت انجام گردید.در صورت صحت اطلاعات وارد شده در اسرع وقت حساب شما تایید می گردد. چنانچه آدرس ایمیل یا شماره همراه شما تایید گردیده باشد، نتیجه آن به اطلاع شما خواهد رسید. ");
                    UIkit.modal("#info").show();
                    UIkit.modal("#info").on("hidden", function () {
                        window.location.reload();
                    });
                }
                if (obj.error == 'update') {
                    $("#info_title").html("پیام سیستم");
                    $("#info_msg").html("ثبت اطلاعات حساب بانکی با شکست مواجه گردید.");
                    UIkit.modal("#info").show();
                }
            }
        });
    }
}

function add_row(card_number, acc_number, shaba, bank_name, status) {
    var t = $('#orders').DataTable();
    t.row.add([
        '<span>' + card_number + '</span>',
        '<span>' + acc_number + '</span>',
        '<span>' + shaba + '</span>',
        '<span>' + bank_name + '</span>',
        '<span><i class="fa fa-spinner fa-spin"></i> ' + status + '</span>'
    ]).draw(false);
}

function send_email_code() {
    $(".uk-button").attr("disabled", "true");
    $(".spinner-onload").attr("style", "opacity:1");
    $.ajax({
        type: "post",
        url: "../../include/send_email_verify_code.php",
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

function check_email_confirm_code() {
    var v_code = $("#email_confirm_code").val();
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
            url: "../../include/check_email_confirm.php",
            datatype: "json",
            data: {
                arguments: [v_code]
            },
            success: function (obj) {
                $(".uk-button").removeAttr("disabled");
                $(".spinner-onload").attr("style", "opacity:0");
                if (obj.ok == 'ok') {
                    $("#email_confirm_div").empty();
                    $("#email_confirm_footer").remove();
                    var data = '<div class="uk-text-center"><img width="150" height="150" src="../../asset/img/email_check2.png" alt="mobile check"><p class="calculate-title">آدرس ایمیل شما با موفقیت تایید گردید.</p></div>';
                    $("#email_confirm_div").append(data);

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
