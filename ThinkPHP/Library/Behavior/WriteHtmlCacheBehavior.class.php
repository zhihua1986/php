<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Behavior;

use Think\Storage;

/**
 * 系统行为扩展：静态缓存写入
 */
class WriteHtmlCacheBehavior
{

    // 行为扩展的执行入口必须是run
    public function run(&$content)
    {
        if (C('HTML_CACHE_ON')=='true' && defined('HTML_FILE_NAME')
            && !preg_match('/Status.*[345]{1}\d{2}/i', implode(' ', headers_list()))
            && !preg_match('/(-[a-z0-9]{2}){3,}/i', HTML_FILE_NAME)) {
            	
           $Url =  HTML_FILE_NAME;
           if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $Url, $match)) { //如果有中文
            
            $Url = explode('_k',$Url);
            
            $Url = $Url[0].md5($Url[1]);
   
          }
            	
            //静态文件写入
            Storage::put($Url, $content, 'html');
        }
    }
}
