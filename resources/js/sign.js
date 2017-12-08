$(document).ready(function() {
    'use strict';
    /**
     * ===================================================================================
     * 用户注册跟登录共用js
     * @type {Number}
     * ====================================================================================
     */

    $('.QapTcha').QapTcha({ disabledSubmit: true, autoSubmit: false, autoRevert: true, txtLock: "请按住滑块，拖动到最右侧", txtUnlock: '验证通过', PHPfile: 'Verification', htmlError: '32424' });


    var account, passwd, isChecked = 1,
        confirmPasswd, email, phone, type = window.location.href.split('\/').pop().toString() == 'register' ? 'agreement' : 'remember-passwd';
    //window.location.href.split('\/').pop().toString()=='register' ? 'agreement':
    console.log(type);
    $('#account').on('blur', function() {
        if ($(this).val() == '') {
            $(this).css({
                'border': '1px solid #d22a0e',
                'box-shadow': '0 0 3px #d22a0e'
            });
            $('.account-tip').css('display', 'block');
        } else {
            account = $(this).val();
            $('.account-tip').css('display', 'none');
            $(this).css({
                'border': '1px solid #8a8a8a',
                'box-shadow': 'none'
            });
        }
    });

    $('#passwd').on('blur', function() {
        if ($(this).val() == '') {
            $(this).css({
                'border': '1px solid #d22a0e',
                'box-shadow': '0 0 3px #d22a0e'
            });
            $('.passwd-tip').css('display', 'block');
        } else {
            passwd = str_md5($(this).val());
            passwd = $(this).val();
            $('.passwd-tip').css('display', 'none');
            $(this).css({
                'border': '1px solid #8a8a8a',
                'box-shadow': 'none'
            });
        }
    });

    $('#confirm-passwd').on('blur', function() {
        if ($(this).val() == '') {
            $(this).css({
                'border': '1px solid #d22a0e',
                'box-shadow': '0 0 3px #d22a0e'
            });
            $('.confirm-passwd-tip').css('display', 'block');
        } else if ($(this).val() !== $('#passwd').val()) {
            $(this).css({
                'border': '1px solid #d22a0e',
                'box-shadow': '0 0 3px #d22a0e'
            });
            $('.confirm-passwd-tip').css('display', 'block');
            $('.confirm-passwd-tip').html('两次输入密码不同；');
        } else {
            confirmPasswd = $(this).val();
            $(this).css({
                'border': '1px solid #8a8a8a',
                'box-shadow': 'none'
            });
            $('.confirm-passwd-tip').css('display', 'none');
            $('.confirm-passwd-tip').html('密码不能为空；');
        }
    });

    $('#email').on('blur', function() {
        if ($(this).val() == '') {
            $(this).css({
                'border': '1px solid #d22a0e',
                'box-shadow': '0 0 3px #d22a0e'
            });
            $('.email-tip').css('display', 'block');
        } else if (!(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test($(this).val()))) {
            $(this).css({
                'border': '1px solid #d22a0e',
                'box-shadow': '0 0 3px #d22a0e'
            });
            $('.email-tip').css('display', 'block');
            $('.email-tip').html('邮箱格式不正确；');
        } else {
            email = $(this).val();
            $(this).css({
                'border': '1px solid #8a8a8a',
                'box-shadow': 'none'
            });
            $('.email-tip').css('display', 'none');
            $('.email-tip').html('邮箱不能为空；');
        }
    });

    $('#phone').on('blur', function() {
        if ($(this).val() == '') {
            $(this).css({
                'border': '1px solid #d22a0e',
                'box-shadow': '0 0 3px #d22a0e'
            });
            $('.phone-tip').css('display', 'block');
        } else if (!(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/.test($(this).val()))) {
            $(this).css({
                'border': '1px solid #d22a0e',
                'box-shadow': '0 0 3px #d22a0e'
            });
            $('.phone-tip').css('display', 'block');
            $('.phone-tip').html('手机号码格式不正确；');
        } else {
            phone = $(this).val();
            $(this).css({
                'border': '1px solid #8a8a8a',
                'box-shadow': 'none'
            });
            $('.phone-tip').css('display', 'none');
            $('.phone-tip').html('手机号码不能为空；');
        }
    });

    $('.simulation-ckeckbox-but').on('click', function() {
        if ($(this).attr('data-checked') == 'true') {
            $(this).attr('data-checked', false);
            $(this).css('background', 'url(/resources/images/check_box_icon.png) no-repeat 5px 11px');
            isChecked = 0;
        } else {
            $(this).attr('data-checked', true);
            $(this).css('background', 'url(/resources/images/check_box_checked_icon.png) no-repeat 5px 11px');
            isChecked = 1;
            type = $(this).attr('data-type');
        }
    });



    $('.simulation-submit-buts').on('click', function() {

        if ($('.QapTcha input').val() !== '') {
            ADLINKX.alert('请按住滑块，拖动到最右侧！');
            return false;
        }

        if ($(this).attr('data-submit-type') == 'login') { //登录
            var url = encodeURI(ADLINKX.domain + '/user/sign_in');
            var data = { 'account': account, 'passwd': passwd, 'isChecked': isChecked };
            postData(url, data);
            // if (isChecked) {
            //     ADLINKX.confirm('您确定要记住密码吗?', {
            //         'confirm': {
            //             'title': '确定',
            //             'callback': function() {
            //                 postData(url, data);
            //             }
            //         },
            //         'cancel': {
            //             'title': '取消',
            //             'callback': function() {
            //                 return false;
            //             }
            //         }
            //     });
            // }

        } else { //注册
            if (!account) {
                ADLINKX.alert('用户名不能为空！');
                return false;
            }
            if (!passwd) {
                ADLINKX.alert('密码不能为空！');
                return false;
            }
            if (!confirmPasswd) {
                ADLINKX.alert('请再次输入密码！');
                return false;
            } else if (confirmPasswd !== passwd) {
                ADLINKX.alert('两次输入密码不同！');
                return false;
            }
            if (!email) {
                ADLINKX.alert('邮箱不能为空！');
                return false;
            }
            if (!phone) {
                ADLINKX.alert('手机号码不能为空！');
                return false;
            }
            if (isChecked == 0 || !isChecked) {
                ADLINKX.alert('您必须同意服务和隐私政策的条款');
                return false;
            }
            var url = encodeURI(ADLINKX.domain + '/user/sigin_up');
            var data = { 'account': account, 'passwd': passwd, 'email': email, 'phone': phone, 'isChecked': isChecked }
            postData(url, data);
        }
    });


    /**
     * 提交数据
     * @param  {[type]} url  [description]
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    function postData(url, data) {
        ADLINKX.alert('正在提交数据。。。。。', 0.2, false);
        $.ajax({
            url: url,
            type: 'POST',
            async: false,
            data: data,
            success: function(res) {
                console.log(typeof res);
                res = typeof res == 'string' ? (JSON.parse ? JSON.parse(res) : jQuery.parseJSON(res)) : res;
                if (res.code == 1 && res.msg == 'success') {
                    ADLINKX.alert((type == 'agreement' ? '注册成功' : '登录成功'), 2, function() {
                        if (type == 'agreement') { //注册成功，跳转至登录
                            window.location.href = ADLINKX.domain + '/user/login';
                        } else { //登录成功跳转至首页
                            window.location.href = ADLINKX.domain;
                        }
                    });
                } else {
                    ADLINKX.alert((type == 'agreement' ? '注册失败' : '登录失败'));
                    return false;
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
});
