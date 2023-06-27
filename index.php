<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用入口文件

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    die('PHP版本不能低于5.6.0');
}

if (!is_file('./data/install.lock')) {
    $url=$_SERVER['HTTP_HOST'].trim($_SERVER['SCRIPT_NAME'], 'index.php').'install.php';
    header("Location:http://$url");
    exit;
}

$websoft=strtolower($_SERVER["SERVER_SOFTWARE"]);
$iis=strpos($websoft, 'iis');
if (isset($_SERVER['PATH_INFO']) && $iis>0) {
    if (($encode = mb_detect_encoding($_SERVER['PATH_INFO'], ['ASCII', 'GB2312', 'GBK', 'UTF-8'])) != 'UTF-8') {
        $_SERVER['PATH_INFO'] = mb_convert_encoding($_SERVER['PATH_INFO'], 'UTF-8', $encode);
    }
}

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',false);
define('ROOT_PATH', __DIR__);
define('HTML_PATH', './data/html/');
// 定义应用目录
define('DIR_SECURE_FILENAME', 'index.html');
define('DIR_SECURE_CONTENT', 'deny Access!');
define('APP_PATH', './App/');
define('TQK_IMG_DATA_PATH', './');
define('RUNTIME_PATH', './data/Runtime/');
define('TQK_DATA_PATH', './data/');
define('TQK_VERSION', '4.2.8.1');
define('TQK_RELEASE', '2023-06-26');
require './ThinkPHP/ThinkPHP.php';
