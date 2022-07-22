<?php
header("Content-Type:text/html;charset=utf-8");

if (is_file('./data/install.lock')) {
    header('Location: ./index.php');
    exit;
}

function reset_session_path()
{
    $root = str_replace("\\", '/', dirname(__FILE__));
    $savePath = $root . "/tmp/";
    session_save_path($savePath);
}

//reset_session_path();  //如果您的服务器无法安装或者无法登陆，又或者后台验证码无限错误，请尝试取消本行起始两条左斜杠，让本行代码生效，以修改session存储的路径

if (version_compare(PHP_VERSION, '5.6.0', '<'))
    die('当前PHP版本'.PHP_VERSION.'，最低要求PHP版本5.6.0 <br/><br/>很遗憾，未能达到最低要求。本系统必须运行在PHP5.6 及以上版本。如果您是虚拟主机，请联系空间商升级PHP版本，如果您是VPS用户，请自行升级php版本或者联系VPS提供商寻求技术支持。');

define('BIND_MODULE','Install');
/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ('APP_DEBUG', false);
define ('DB_DEBUG', false);
define ('APP_PATH', './App/');
define('ROOT_PATH', __DIR__);
define ('RUNTIME_PATH', './data/install/');

require './ThinkPHP/ThinkPHP.php';
