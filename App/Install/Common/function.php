<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

// 检测环境是否支持可写
define('IS_WRITE',APP_MODE !== 'sae');

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env(){
	$items = array(
		'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
		'php'     => array('PHP版本', '5.3', '5.3+', PHP_VERSION, 'success'),
		//'mysql'   => array('MYSQL版本', '5.0', '5.0+', '未知', 'success'), //PHP5.5不支持mysql版本检测
		'upload'  => array('附件上传', '不限制', '2M+', '未知', 'success'),
		'gd'      => array('GD库', '2.0', '2.0+', '未知', 'success'),
		'disk'    => array('磁盘空间', '500M', '不限制', '未知', 'success'),
	);

	//PHP环境检测
	if($items['php'][3] < $items['php'][1]){
		$items['php'][4] = 'error';
		session('error', true);
	}

	//数据库检测
	// if(function_exists('mysql_get_server_info')){
	// 	$items['mysql'][3] = mysql_get_server_info();
	// 	if($items['mysql'][3] < $items['mysql'][1]){
	// 		$items['mysql'][4] = 'error';
	// 		session('error', true);
	// 	}
	// }

	//附件上传检测
	if(@ini_get('file_uploads'))
		$items['upload'][3] = ini_get('upload_max_filesize');

	//GD库检测
	$tmp = function_exists('gd_info') ? gd_info() : array();
	if(empty($tmp['GD Version'])){
		$items['gd'][3] = '未安装';
		$items['gd'][4] = 'error';
		session('error', true);
	} else {
		$items['gd'][3] = $tmp['GD Version'];
	}
	unset($tmp);

	//磁盘空间检测
	if(function_exists('disk_free_space')) {
		$items['disk'][3] = floor(disk_free_space(INSTALL_APP_PATH) / (1024*1024)).'M';
	}

	return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile(){
	$items = array(
		array('dir',  '可写', 'success', './data/upload'),
		array('dir',  '可写', 'success', './data/Runtime'),
		array('dir',  '可写', 'success', './data/Config'),
		array('dir', '可写', 'success', './data/Config/url.php'),
		array('file', '可写', 'success', './data/start.txt'),

	);

	foreach ($items as &$val) {
		if('dir' == $val[0]){
			if(!is_writable(INSTALL_APP_PATH . $val[3])) {
				if(is_dir($items[1])) {
					$val[1] = '可读';
					$val[2] = 'error';
					session('error', true);
				} else {
					$val[1] = '不存在';
					$val[2] = 'error';
					session('error', true);
				}
			}
		} else {
			if(file_exists(INSTALL_APP_PATH . $val[3])) {
				if(!is_writable(INSTALL_APP_PATH . $val[3])) {
					$val[1] = '不可写';
					$val[2] = 'error';
					session('error', true);
				}
			} else {
				if(!is_writable(dirname(INSTALL_APP_PATH . $val[3]))) {
					$val[1] = '不存在';
					$val[2] = 'error';
					session('error', true);
				}
			}
		}
	}

	return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func(){

	$items = array(
		//array('mysql_connect',     			'支持', 'success'),
		array('file_get_contents', 			'支持', 'success'),
		array('mb_strlen',		   			'支持', 'success'),
		array('iconv',     					'支持', 'success'),
		array('mb_convert_encoding', 		'支持', 'success'),
		//array('mysqli_connect',     	'支持', 'success'),
		array('gzclose', 		'支持', 'success'),
		array('gd_info',		   	'支持', 'success'),
		array('curl_getinfo',		   	'支持', 'success'),
		);

	foreach ($items as &$val) {
		if(!function_exists($val[0])){
			$val[1] = '不支持';
			$val[2] = 'error';
			$val[3] = '开启';
			session('error', true);
		}
	}

	return $items;
}

/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($config, $auth){
if(is_array($config)){	
$db_str=<<<php
<?php
return array(

//*************************************数据库设置*************************************
    'DB_TYPE'               =>  '{$config['DB_TYPE']}',                 // 数据库类型
    'DB_HOST'               =>  '{$config['DB_HOST']}',     // 服务器地址
    'DB_NAME'               =>  '{$config['DB_NAME']}',     // 数据库名
    'DB_USER'               =>  '{$config['DB_USER']}',     // 用户名
    'DB_PWD'                =>  '{$config['DB_PWD']}',      // 密码
    'DB_PORT'               =>  '{$config['DB_PORT']}',     // 端口
    'DB_PREFIX'             =>  '{$config['DB_PREFIX']}',   // 数据库表前缀
);
php;
if(!IS_WRITE){
return '由于您的网站目录下data文件夹环境不可写，本次安装失败。';	

}else{
 if(file_put_contents(ROOT_PATH.'/data/Config/db.php', $db_str)){
 // @touch(ROOT_PATH.'/data/install2.lock');
  show_msg('配置文件写入成功');	
 	
 }else{
 	
 	show_msg('配置文件写入失败！', 'error');
	session('error', true);
 	
 }
 
 return '';

}


}	
	

}

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function create_tables($db, $prefix = ''){
	//读取SQL文件
	$sql = file_get_contents(MODULE_PATH . 'Data/install.sql');
	$sql = str_replace("\r", "\n", $sql);
	$sql = explode(";\n", $sql);

	//替换表前缀
	$orginal = C('ORIGINAL_TABLE_PREFIX');
	$sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);

	//开始安装
	show_msg('开始安装数据库...');
	foreach ($sql as $value) {
		$value = trim($value);
		if(empty($value)) continue;
		if(substr($value, 0, 12) == 'CREATE TABLE') {
			$name = preg_replace("/^CREATE TABLE IF NOT EXISTS `(\w+)` .*/s", "\\1", $value);
			$msg  = "创建数据表{$name}";
			if(false !== $db->execute($value)){
				show_msg($msg . '...成功');
			} else {
				show_msg($msg . '...失败！', 'error');
				session('error', true);
			}
		} else {
			$db->execute($value);
		}

	}
}

function register_administrator($db, $prefix, $admin, $auth){
	show_msg('开始注册创始人帐号...');
	  $password = md5($admin['password']);   
      $sqls = "INSERT INTO `" . $prefix . "admin` (`id`,`username`, `password`, `email`, `role_id`,`last_ip`,`last_time`) VALUES " .
                "(1,'" . $admin['username'] . "', '" . $password . "', '" . $admin['email'] . "', 1, '" . NOW_TIME. "','" .get_client_ip(1). "')";
	$db->execute($sqls);
	show_msg('创始人帐号注册完成！');
}

/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = ''){
	echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
	flush();
	ob_flush();
}

/**
 * 生成系统AUTH_KEY
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function build_auth_key(){
	$chars  = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
	$chars  = str_shuffle($chars);
	return substr($chars, 0, 40);
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function user_md5($str, $key = ''){
	return '' === $str ? '' : md5(sha1($str) . $key);
}

function recurse_copy($src,$des) {
    $dir = opendir($src);
    @mkdir($des);
    while(false !== ($file = readdir($dir))) {
         if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file,$des . '/' . $file);
            }else{
				//检查系统
				if (strtoupper(substr(PHP_OS, 0,3)) !== 'WIN') {
				    $refile = iconv('gbk', $charset, $file);
				}
                copy($src . '/' . $file,$des . '/' . $refile);
            }
        }

      }

    closedir($dir);

}