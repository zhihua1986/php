<?php
return array (
  'DATA_CACHE_TYPE' => 'File',
  'DATA_CACHE_TIME' => '3600',
  'MEMCACHED_HOST' => '127.0.0.1',
  'MEMCACHED_PORT' => '11211',
  'MEMCACHED_SERVER' => array(array('127.0.0.1', 11211, 0)),
  'CREATE_HTML_ON' => 0,
  'URL_MODEL' => 1,
  'URL_HTML_SUFFIX' => 'html',
  'HTML_CACHE_ON'     =>  false, 
  'URL_PATHINFO_DEPR' => '/',
  'URL_ROUTER_ON' => true,
  'DB_FIELDS_CACHE'       =>  true, 
  'TMPL_CACHE_ON'         =>  true,        
  'TMPL_STRIP_SPACE'      =>  true,       
  'APP_SUB_DOMAIN_DEPLOY' => 0, // 开启子域名配置
  'APP_SUB_DOMAIN_RULES' => 
  array (
    'www' => 'Home',
    'admin' => 'Admin',
    'm' => 'M',
  ),
  // 'APP_DOMAIN_SUFFIX'=>'com.cn', //如果你的域名是 com.cn 、net.cn 这种后缀，去掉前面的注释，并且把后面的域名后缀改为对应的即可
);