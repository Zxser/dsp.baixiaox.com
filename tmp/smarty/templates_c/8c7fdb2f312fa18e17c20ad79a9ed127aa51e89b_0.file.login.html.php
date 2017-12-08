<?php
/* Smarty version 3.1.30, created on 2017-12-08 09:33:59
  from "C:\web\www\dsp.baixiaox.com\application\views\user\login.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5a29ec070d17a9_83795353',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8c7fdb2f312fa18e17c20ad79a9ed127aa51e89b' => 
    array (
      0 => 'C:\\web\\www\\dsp.baixiaox.com\\application\\views\\user\\login.html',
      1 => 1512696742,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:common/header.html' => 1,
  ),
),false)) {
function content_5a29ec070d17a9_83795353 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html>
<head>
    <?php $_smarty_tpl->_subTemplateRender("file:common/header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <link rel="stylesheet" type="text/css" href="/resources/css/login.css" />
</head>
<body>
    <div class="page-bg-block h480">
        <div class="log-block">
            <div class="log-img-block"><img src="/resources/images/login-logo.png"></div>
            <div class="log-text-block">百晓Dsp|登录</div>
        </div>
        <div class="form-list">
            <label for="account"></label>
            <input type="text" name="account" id="account" placeholder="输入用户名/邮箱/手机号" value="" />
        </div>
        <div class="account-tip">用户名不能为空;</div>
        <div class="form-list">
            <label for="passwd"></label>
            <input type="password" name="passwd" id="passwd" placeholder="" value="" />
        </div>
        <div class="passwd-tip">密码不能为空;</div>
        <div class="form-list"><div id="QapTcha" class="QapTcha"></div></div>
        <div class="form-list"><a href='javascript:void(0);' class="simulation-submit-buts" data-submit-type="login">登录</a></div>
        <div class="form-list"><a href='javascript:void(0);' class="simulation-ckeckbox-but" data-checked="true" data-type="remember-passwd">记住密码</a><span class="link-block">还没有帐号？<a href="/user/register" class="a-link">注册</a></span></div>
    </div>
</body><?php echo '<script'; ?>
 type="text/javascript" src="/resources/js/sign.js"><?php echo '</script'; ?>
>
</html>
<?php }
}
