<?php
C(require ROOT_PATH . '/data/Config/db.php');
C(require ROOT_PATH . '/data/Config/url.php');
C(require ROOT_PATH . '/data/Config/router.php');
C(require ROOT_PATH . '/data/Config/version.php');
return array(
     'SHOW_PAGE_TRACE'=>false, //输出调试内容
    'SESSION_AUTO_START' => true,
    'SESSION_PREFIX' => '_dg_view',
    'COOKIE_EXPIRE' => 7*24*60*60,
    'COOKIE_PREFIX' => 'tqk',
    'COOKIE_PATH' => '/',     // Cookie路径
    'COOKIE_DOMAIN' => '', // Cookie有效域名 //SITE_DOMAIN
    'SUPER_MANAGER_ID' => '1',
    // '配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__SKIN2__' => __ROOT__ . '/Public/skin2',
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__IMG__' => __ROOT__ . '/Public/static/images',
        '__CSS__' => __ROOT__ . '/Public/static/css',
        '__JS__' => __ROOT__ . '/Public/static/js'
    ),
    
    'LANG_SWITCH_ON' => true, // 开启语言包功能
    'LANG_AUTO_DETECT' => false, // 自动侦测语言 开启多语言功能后有效
    'LANG_LIST' => 'zh-cn', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE' => 'l', // 默认语言切换变量
	
	'TOKEN_ON' => false, // 是否开启令牌验证 默认关闭
	'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称，默认为__hash__
	'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
	'TOKEN_RESET' => true, //令牌验证出错后是否重置令牌 默认为true在这里插入代码片

    
   // 'API_TAOBAO_SEARCH'=>SITE_URL . '/tbk.php',
    //'API_TAOBAO_INFO'=>SITE_URL . '/taobao.php',
    //'API_TAOBAO_RECOMMEND'=>SITE_URL . '/recommend.php',
);