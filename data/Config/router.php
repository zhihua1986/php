<?php
return array(
    'SESSION_AUTO_START' => true, 
    'DATA_CACHE_SUBDIR'		=> false,						// 缓存文件夹
    'DATA_PATH_LEVEL'		=> 3,							// 缓存文件夹层级
    // 设置禁止访问的模块列表
    'DEFAULT_C_LAYER'       =>  'Action', // 默认的控制器层名称
    'MODULE_ALLOW_LIST'     =>   array('Home','M','Admin','Wap','Install'),
    'MODULE_DENY_LIST'      =>   array('Common','Runtime'),
    'DEFAULT_MODULE'        =>   'Home',
    'DEFAULT_FILTER'        =>  'htmlspecialchars,fanXSS',
    'URL_CASE_INSENSITIVE'  =>  false,
	//'URL_MODULE_MAP'    =>    array('supadmin'=>'admin'),

);
