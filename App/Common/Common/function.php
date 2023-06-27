<?php
function p($arr){
	dump($arr);
    exit;
}

function table_auto_format($query)
{
    $prefix = C('DB_PREFIX');
    return preg_replace_callback("/__([A-Z_-]+)__/sU", function ($match) use($prefix)
    {
        return $prefix . strtolower($match[1]);
    }, $query);
}

function formatprice($price){
	return floatval($price);
}

function Rebate1($price,$rate=0){
$rate = $rate>0?$rate:C('yh_bili1');
return round($price*($rate/100), 2);	
}

function Rebate2($price){
return round($price*(C('yh_bili2')/100), 2);	
}

function Rebate3($price){
return round($price*(C('yh_bili3')/100), 2);	
}


function Orebate1($res=array()){
return round($res['price']*($res['leve']/100), 2);	
}

function Orebate2($res=array()){
return round($res['price']*($res['leve']/100), 2);	
}

function Orebate3($res=array()){
return round($res['price']*($res['leve']/100), 2);	
}


//防止xss攻击的特殊方法
function fanXSS($string) {
     require_once './Fanxss/HTMLPurifier.auto.php';  //根据实际目录路径进行修改
    // 生成配置对象
    $cfg = HTMLPurifier_Config::createDefault();
    // 以下就是配置：
    $cfg->set('Core.Encoding', 'UTF-8');
    // 设置允许使用的HTML标签
    $cfg->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,li,br,span[style],img[width|height|alt|src]');
    // 设置允许出现的CSS样式属性
    $cfg->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
    // 设置a标签上是否允许使用target="_blank"
    $cfg->set('HTML.TargetBlank', TRUE);
    // 使用配置生成过滤用的对象
    $obj = new HTMLPurifier($cfg);
    // 过滤字符串
    return $obj->purify($string);
}

function content_strip($content){

$str = preg_replace_callback('/<img[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/', function($matches){
   return wap_img($matches[1]);
}, $content); 
		
return $str;
}
function wap_img($url){
	$img='<img src="'.$url.'">';
	return $img;
}

function Fanli_leve1($price,$fuid,$guid)
{
	if($fuid>0 && $guid>0){
		
	//return round($price*(C('yh_bili1')/100)-($price*(C('yh_bili1')/100)*((C('yh_bili2')+C('yh_bili3'))/100)), 2);
	
	return round($price*(C('yh_bili1')/100)+$price*(C('yh_bili2')/100)+$price*(C('yh_bili3')/100), 2);		
		
	}elseif($fuid>0){
	return round($price*(C('yh_bili1')/100)+$price*(C('yh_bili2')/100), 2);		
	//return round($price*(C('yh_bili1')/100)-($price*(C('yh_bili1')/100)*(C('yh_bili2')/100)), 2);	
	}else{
	return round($price*(C('yh_bili1')/100), 2);		
	}
    
}



function Income_leve1($res=array())
{
   if($res['fuid']>0 && $res['guid']>0){
	return round($price*($res['leve1']/100)+$price*($res['leve2']/100)+$price*($res['leve3']/100), 2);		
	//return round($res['price']*($res['leve1']/100)-($res['price']*($res['leve1']/100)*(($res['leve2']+$res['leve3'])/100)), 2);	
		
	}elseif($res['fuid']>0){
	return round($price*($res['leve1']/100)+$price*($res['leve2']/100), 2);	
	//return round($res['price']*($res['leve1']/100)-($res['price']*($res['leve1']/100)*($res['leve2']/100)), 2);	
	
	}else{
	return round($res['price']*($res['leve1']/100), 2);	
	//return round($res['price']*($res['leve1']/100), 2);		
	}
}



function Fanli_leve2($price)
{
   return round($price*(C('yh_bili1')/100)+$price*(C('yh_bili2')/100), 2);	
    //return round(($price*(C('yh_bili1')/100))*(C('yh_bili2')/100), 2);
}

function Income_leve2($res=array())
{
	return round($price*($res['leve1']/100)+$price*($res['leve2']/100), 2);	
   // return round(($res['price']*($res['leve1']/100))*($res['leve2']/100), 2);
}

function Income_leve3($res=array())
{
	return round($price*($res['leve1']/100)+$price*($res['leve3']/100), 2);
   // return round(($res['price']*($res['leve1']/100))*($res['leve3']/100), 2);
}

function Fanli_leve3($price)
{
    return round($price*(C('yh_bili1')/100)+$price*(C('yh_bili3')/100), 2);
   //return round(($price*(C('yh_bili1')/100))*(C('yh_bili3')/100), 2);
}

function pdd_cate($key=''){
$data=array(
'12'=>'优惠精选',
'1'=>'美食',
'4'=>'母婴',
'13'=>'水果',
'14'=>'服饰',
'15'=>'百货',
'16'=>'美妆',
'18'=>'电器',
'743'=>'男装',
'818'=>'家纺',
'1281'=>'鞋包',
'1451'=>'运动',
'1543'=>'手机',
'1917'=>'家装',
'2048'=>'汽车',
'1282'=>'内衣'
);
if($key){
return $data[$key];	
}else{
return $data;
}

}

function profit($rate,$price,$user){
if($user['webmaster']==1 && $user['webmaster_rate']>0){
$profit=round($price*$rate/10000,2);
return round($profit*$user['webmaster_rate']/100,2);
}
return false;
}

function profits($rate,$price,$user){
if($user['webmaster']==1 && $user['webmaster_rate']>0){
$profit=round($price*($rate/1000),2);
return round($profit*$user['webmaster_rate']/100,2);
}
return false;
}


function trimall($str){  
    $qian=array(" ","　","\t","\n","\r");  
    return str_replace($qian, '', $str);    
} 

function string_remove_xss($html) {
    preg_match_all("/\<([^\<]+)\>/is", $html, $ms);
    $searchs[] = '<';
    $replaces[] = '&lt;';
    $searchs[] = '>';
    $replaces[] = '&gt;';
    if ($ms[1]) {
        $allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote';
        $ms[1] = array_unique($ms[1]);
        foreach ($ms[1] as $value) {
            $searchs[] = "&lt;".$value."&gt;";
            $value = str_replace('&amp;', '_uch_tmp_str_', $value);
            $value = string_htmlspecialchars($value);
            $value = str_replace('_uch_tmp_str_', '&amp;', $value);
            $value = str_replace(array('\\', '/*'), array('.', '/.'), $value);
            $skipkeys = array('onabort','onactivate','onafterprint','onafterupdate','onbeforeactivate','onbeforecopy','onbeforecut','onbeforedeactivate',
                    'onbeforeeditfocus','onbeforepaste','onbeforeprint','onbeforeunload','onbeforeupdate','onblur','onbounce','oncellchange','onchange',
                    'onclick','oncontextmenu','oncontrolselect','oncopy','oncut','ondataavailable','ondatasetchanged','ondatasetcomplete','ondblclick',
                    'ondeactivate','ondrag','ondragend','ondragenter','ondragleave','ondragover','ondragstart','ondrop','onerror','onerrorupdate',
                    'onfilterchange','onfinish','onfocus','onfocusin','onfocusout','onhelp','onkeydown','onkeypress','onkeyup','onlayoutcomplete',
                    'onload','onlosecapture','onmousedown','onmouseenter','onmouseleave','onmousemove','onmouseout','onmouseover','onmouseup','onmousewheel',
                    'onmove','onmoveend','onmovestart','onpaste','onpropertychange','onreadystatechange','onreset','onresize','onresizeend','onresizestart',
                    'onrowenter','onrowexit','onrowsdelete','onrowsinserted','onscroll','onselect','onselectionchange','onselectstart','onstart','onstop',
                    'onsubmit','onunload','javascript','script','eval','behaviour','expression','style','class');
            $skipstr = implode('|', $skipkeys);
            $value = preg_replace(array("/($skipstr)/i"), '.', $value);
            if (!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
                $value = '';
            }
            $replaces[] = empty($value) ? '' : "<" . str_replace('&quot;', '"', $value) . ">";
        }
    }
    
    $html = str_replace($searchs, $replaces, $html);

    return $html;
}
 
function string_htmlspecialchars($string, $flags = null) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = string_htmlspecialchars($val, $flags);
        }
    } else {
        if ($flags === null) {
            $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
            if (strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if (PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                if (!defined('CHARSET') || (strtolower(CHARSET) == 'utf-8')) {
                    $charset = 'UTF-8';
                } else {
                    $charset = 'ISO-8859-1';
                }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
    }
 
    return $string;
}
 
function objtoarr($obj){
	$ret = array();
	foreach($obj as $key =>$value){
		if(gettype($value) == 'array' || gettype($value) == 'object'){
			$ret[$key] = objtoarr($value);
		}
		else{
			$ret[$key] = $value;
		}
	}
	return $ret;
}

function lefttime($second){
	$times = '';
    $day = floor($second/(3600*24));
    $second = $second%(3600*24);//除去整天之后剩余的时间
    $hour = floor($second/3600);
    $second = $second-$hour*3600;//除去整小时之后剩余的时间
    $minute = floor($second/60);
    $second = fmod(floatval($second),60);//除去整分钟之后剩余的时间
	if($day){
		$times = $day.'天';
	}
	if($hour){
		$times.=$hour.'小时';
	}
	if($minute){
		$times.=$minute.'分';
	}
	if($second){
		$times.=$second.'秒';
	}
    //返回字符串
    return $times;
}

function fftime($time){
	$tomorrow = strtotime(date('Y-m-d',strtotime("+1 day")));
	if($tomorrow > $time){
		return '今日<i>'.date('H时i分',$time).'</i>开始';
	}else{
		$lefttime = $time - $tomorrow;
		if($lefttime < 86400){
			return '明日<i>'.date('H时i分',$time).'</i>开始';
		}else{
			return '<i>'.date('m月d日 H点i分',$time).'</i>开始';
		}
	}
}

//秒数转换时间
function changeTimeType($seconds){
	if ($seconds>3600){
		$hours = intval($seconds/3600);
		$minutes = $seconds600;
		$time = $hours."时".gmstrftime('%M分%S秒', $minutes);
	}else{
		$time = gmstrftime('%H时%M分%S秒', $seconds);
	}
	return $time;
}



function addslashes_deep($value) {
    $value = is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
    return $value;
}


function stripslashes_deep($value) {
    if (is_array($value)) {
        $value = array_map('stripslashes_deep', $value);
    } elseif (is_object($value)) {
        $vars = get_object_vars($value);
        foreach ($vars as $key => $data) {
            $value->{$key} = stripslashes_deep($data);
        }
    } else {
        $value = stripslashes($value);
    }

    return $value;
}

function filter_default(&$value){
    $value = htmlspecialchars($value);
}

function Newiconv($_input_charset="GBK",$_output_charset="UTF-8",$input ) {
	$output = "";
	if(!isset($_output_charset) )$_output_charset = $this->parameter['_input_charset '];
	if($_input_charset == $_output_charset || $input ==null) { $output = $input;
	}
	elseif (function_exists("m\x62_\x63\x6fn\x76\145\x72\164_\145\x6e\x63\x6f\x64\x69\x6e\147")){
		$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
	} elseif(function_exists("\x69\x63o\156\x76")) {
		$output = iconv($_input_charset,$_output_charset,$input);
		}
		else die("对不起，你的服务器系统无法进行字符转码.请联系空间商。");
		return $output;
}

function newicon($time){
	$date = '';
	if (date('Y-m-d') == date('Y-m-d',$time)){
		$date = '<span class="today-wrapper"><span>今日</span><span>新品</span></span>';
	}
	return $date;
}

function todaytime() {
    return mktime(0, 0, 0, date('m'), date('d'), date('Y'));
}

function cut_html_str($str, $lenth, $replace='', $anchor='<!-- break -->'){
	$str = strip_tags($str);  
    $str = trim($str);
	$str = preg_replace( "@<script(.*?)</script>@is", "", $str ); 
	$str = preg_replace( "@<iframe(.*?)</iframe>@is", "", $str ); 
	$str = preg_replace( "@<style(.*?)</style>@is", "", $str ); 
	$str = preg_replace( "@<(.*?)>@is", "", $str ); 
//  $str = ereg_replace("\t","",$str);  
//  $str = ereg_replace("\r\n","",$str);  
//  $str = ereg_replace("\r","",$str);  
//  $str = ereg_replace("\n","",$str);  
//  $str = ereg_replace(" ","",$str); 
    $_lenth = mb_strlen($str, "utf-8"); // 统计字符串长度（中、英文都算一个字符）
    if($_lenth <= $lenth){
        return $str;    // 传入的字符串长度小于截取长度，原样返回
    }
    $strlen_var = strlen($str);     // 统计字符串长度（UTF8编码下-中文算3个字符，英文算一个字符）
    if(strpos($str, '<') === false){
     $str = mb_substr($str, 0, $lenth,'utf-8');  // 不包含 html 标签 ，直接截取
     return $str;
    } 
    if($e = strpos($str, $anchor)){ 
        return mb_substr($str, 0, $e,'utf-8');  // 包含截断标志，优先
    } 

    $html_tag = 0;  // html 代码标记 
    $result = '';   // 摘要字符串
    $html_array = array('left' => array(), 'right' => array()); //记录截取后字符串内出现的 html 标签，开始=>left,结束=>right
    for($i = 0; $i < $strlen_var; ++$i) { 
        if(!$lenth) break;  // 遍历完之后跳出
        $current_var = substr($str, $i, 1); // 当前字符
        if($current_var == '<'){ // html 代码开始 
            $html_tag = 1; 
            $html_array_str = ''; 
        }else if($html_tag == 1){ // 一段 html 代码结束 
            if($current_var == '>'){ 
                $html_array_str = trim($html_array_str); //去除首尾空格，如 <br / > < img src="" / > 等可能出现首尾空格
                if(substr($html_array_str, -1) != '/'){ //判断最后一个字符是否为 /，若是，则标签已闭合，不记录
                    // 判断第一个字符是否 /，若是，则放在 right 单元 
                    $f = substr($html_array_str, 0, 1); 
                    if($f == '/'){ 
                        $html_array['right'][] = str_replace('/', '', $html_array_str); // 去掉 '/' 
                    }else if($f != '?'){ // 若是?，则为 PHP 代码，跳过
                        // 若有半角空格，以空格分割，第一个单元为 html 标签。如：<h2 class="a"> <p class="a"> 
                        if(strpos($html_array_str, ' ') !== false){ 
                        // 分割成2个单元，可能有多个空格，如：<h2 class="" id=""> 
                        $html_array['left'][] = strtolower(current(explode(' ', $html_array_str, 2))); 
                        }else{ 
                        //若没有空格，整个字符串为 html 标签，如：<b> <p> 等，统一转换为小写
                        $html_array['left'][] = strtolower($html_array_str); 
                        } 
                    } 
                } 
                $html_array_str = ''; // 字符串重置
                $html_tag = 0; 
            }else{ 
                $html_array_str .= $current_var; //将< >之间的字符组成一个字符串,用于提取 html 标签
            } 
        }else{ 
            $lenth; // 非 html 代码才记数
        } 
        $ord_var_c = ord($str[$i]);
        switch (true) { 
            case (($ord_var_c & 0xE0) == 0xC0): // 2 字节 
                $result .= substr($str, $i, 2); 
                $i += 1; break; 
            case (($ord_var_c & 0xF0) == 0xE0): // 3 字节
                $result .= substr($str, $i, 3); 
                $i += 2; break; 
            case (($ord_var_c & 0xF8) == 0xF0): // 4 字节
                $result .= substr($str, $i, 4); 
                $i += 3; break; 
            case (($ord_var_c & 0xFC) == 0xF8): // 5 字节 
                $result .= substr($str, $i, 5); 
                $i += 4; break; 
            case (($ord_var_c & 0xFE) == 0xFC): // 6 字节
                $result .= substr($str, $i, 6); 
                $i += 5; break; 
            default: // 1 字节 
                $result .= $current_var; 
        } 
    } 
    if($html_array['left']){ //比对左右 html 标签，不足则补全
        $html_array['left'] = array_reverse($html_array['left']); //翻转left数组，补充的顺序应与 html 出现的顺序相反
        foreach($html_array['left'] as $index => $tag){ 
            $key = array_search($tag, $html_array['right']); // 判断该标签是否出现在 right 中
            if($key !== false){ // 出现，从 right 中删除该单元
                unset($html_array['right'][$key]); 
            }else{ // 没有出现，需要补全 
                $result .= '</'.$tag.'>'; 
            } 
        } 
    } 
	
    return $result.$replace; 
}


function clearHtml($content){
$content=preg_replace("/<a[^>]*>/i","",$content);
$content=preg_replace("/<\/a>/i","",$content);
$content=preg_replace("/<div[^>]*>/i","",$content);
$content=preg_replace("/<\/div>/i","",$content);
$content=preg_replace("/<!--[^>]*-->/i","",$content);//注释内容    
$content=preg_replace("/style=.+?['|\"]/i",'',$content);//去除样式    
$content=preg_replace("/class=.+?['|\"]/i",'',$content);//去除样式    
$content=preg_replace("/id=.+?['|\"]/i",'',$content);//去除样式       
$content=preg_replace("/lang=.+?['|\"]/i",'',$content);//去除样式        
$content=preg_replace("/width=.+?['|\"]/i",'',$content);//去除样式     
$content=preg_replace("/height=.+?['|\"]/i",'',$content);//去除样式     
$content=preg_replace("/border=.+?['|\"]/i",'',$content);//去除样式     
$content=preg_replace("/face=.+?['|\"]/i",'',$content);//去除样式     
 $content=preg_replace("/face=.+?['|\"]/",'',$content);//去除样式 只允许小写 正则匹配没有带 i 参数  
return $content;
}

function get_word($html,$star,$end){
	$pat = '/'.$star.'(.*?)'.$end.'/s';
	if(!preg_match_all($pat, $html, $mat)) {
	}else{
		$wd= $mat[1][0];
	}
	return $wd;
}

/**
 * 友好时间
 */
function fdate($time) {
    $rtime = date("Y-m-d H:i",$time);
    $htime = date("H:i",$time);
    $timetime = time() - $time;

    if ($timetime < 60) {
       $str = '刚刚';
    }
    else if ($timetime < 60 * 60) {
       $min = floor($timetime/60);
       $str = $min.'分钟前';
    }
    else if ($timetime < 60 * 60 * 24) {
       $h = floor($timetime/(60*60));
       $str = $h.'小时前 ';
    }
    else if ($timetime < 60 * 60 * 24 * 3) {
       $d = floor($timetime/(60*60*24));
       if($d==1)
       $str = '昨天 '.$htime;
    else
       $str = '前天 '.$htime;
    }
    else {
       $str = $rtime;
    }
    return $str;
}


function frienddate($sTime,$type = 'normal',$alt = 'false') {
if (!$sTime) return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime      =   time();
    $dTime      =   $cTime - $sTime;
    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if($type=='normal'){
        if( $dTime < 60 ){
            if($dTime < 10){
                return '刚刚';    //by yangjs
            }else{
                return intval(floor($dTime / 10) * 10)."秒前";
            }
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        //今天的数据.年份相同.日期相同.
        }elseif( $dYear==0 && $dDay == 0  ){
            //return intval($dTime/3600)."小时前";
            return '今天'.date('H:i',$sTime);
        }elseif($dYear==0){
            return date("m-d H:i",$sTime);
        }else{
            return date("Y-m-d H:i",$sTime);
        }
    }elseif($type=='mohu'){
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif( $dDay > 0 && $dDay<=7 ){
            return intval($dDay)."天前";
        }elseif( $dDay > 7 &&  $dDay <= 30 ){
            return intval($dDay/7) . '周前';
        }elseif( $dDay > 30 ){
            return intval($dDay/30) . '个月前';
        }
    //full: Y-m-d , H:i:s
    }elseif($type=='full'){
        return date("Y-m-d , H:i:s",$sTime);
    }elseif($type=='ymd'){
        return date("Y-m-d",$sTime);
    }else{
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif($dYear==0){
            return date("Y-m-d H:i:s",$sTime);
        }else{
            return date("Y-m-d H:i:s",$sTime);
        }
    }
}


function utf_substr($str, $len) {
	for ($i = 0; $i < $len; $i++) {
		$temp_str = substr($str, 0, 1);
		if (ord($temp_str) > 127) {
			$i++;
			if ($i < $len) {
				$new_str[] = substr($str, 0, 3);
				$str = substr($str, 3);
			}
		} else {
			$new_str[] = substr($str, 0, 1);
			$str = substr($str, 1);
		}
	}
	return join($new_str);
}
 
/**
 * 获取用户头像
 */
function avatar($uid, $size) {
    $avatar_size = explode(',', C('yh_avatar_size'));
    $size = in_array($size, $avatar_size) ? $size : '100';
    $avatar_dir = avatar_dir($uid);
    $avatar_file = $avatar_dir . md5($uid) . "_{$size}.jpg";
    if (!is_file(C('yh_attach_path') . 'avatar/' . $avatar_file)) {
        $avatar_file = "default_{$size}.jpg";
    }
    return __ROOT__ . '/' . C('yh_attach_path') . 'avatar/' . $avatar_file;
}

function avatar_dir($uid) {
    $uid = abs(intval($uid));
    $suid = sprintf("%09d", $uid);
    $dir1 = substr($suid, 0, 3);
    $dir2 = substr($suid, 3, 2);
    $dir3 = substr($suid, 5, 2);
    return $dir1 . '/' . $dir2 . '/' . $dir3 . '/';
}


function http( $url, $ua = "" ){
	$opts = array(
		"http" => array(
			"header" => "USER-AGENT: ".$ua)
	);
	$context = stream_context_create( $opts );
    $html = @file_get_contents( $url, FALSE, $context );
	if(!$html){
		$html = @file_get_contents( $url, FALSE, $context );
		if(!$html){
			$html = @file_get_contents( $url, FALSE, $context);
		}
	}
	for($i=0; $i < 10; $i++ ){
		if(!($html=== FALSE )){
			break;
		}
		$html = @file_get_contents( $url, FALSE, $context );
	}
	return $html;
}

function utf8( $string, $code = "" ){
	$code = @mb_detect_encoding($string,array("UTF-8", "GBK"));
	return mb_convert_encoding( $string, "UTF-8", $code );
}

function attach($attach, $type) {
    if (false === strpos($attach, 'http://') && false === strpos($attach, 'https://')) {
        //本地附件
        return __ROOT__ . '/' . C('yh_attach_path') . $type . '/' . $attach;
        //远程附件
        //todo...
    } else {
        //URL链接
        return $attach;
    }
}

function yh($yh){yhtbk();}
function get_id($url) {
        $id = 0;
        $parse = parse_url($url);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $params);
            if (isset($params['id'])) {
                $id = $params['id'];
            } elseif (isset($params['item_id'])) {
                $id = $params['item_id'];
            } elseif (isset($params['default_item_id'])) {
                $id = $params['default_item_id'];
            } elseif (isset($params['amp;id'])) {
                $id = $params['amp;id'];
            } elseif (isset($params['amp;item_id'])) {
                $id = $params['amp;item_id'];
            } elseif (isset($params['amp;default_item_id'])) {
                $id = $params['amp;default_item_id'];
            }
        }
        return $id;
    }
/*
 * 获取缩略图
 */
function get_thumb($img, $suffix = '_thumb') {
    if (false === strpos($img, 'http://') && false === strpos($img, 'https://')) {
        $ext = array_pop(explode('.', $img));
        $thumb = str_replace('.' . $ext, $suffix . '.' . $ext, $img);
    } else {
        if (false !== strpos($img, 'taobaocdn.com') || false !== strpos($img, 'taobao.com') || false !== strpos($img, 'alicdn.com')) {
            //淘宝图片 _s _m _b
            switch ($suffix) {
                case '_s':
                    $thumb = $img . '_100x100.jpg';
                    break;
                case '_m':
                    $thumb = $img . '';
                    break;
                case '_b':
                    $thumb = $img . '';
                    break;
				case '_t':
                    $thumb = $img . '';
                    break;
            }
        }else{
			$thumb = $img;
		}
    }
    return $thumb;
}


/**
 * 将数组键名变成大写或小写
 * @param array $arr 数组
 * @param int $type 转换方式 1大写   0小写
 * @return array
 */
function array_change_key_case_d($arr, $type = 0)
{
    $function = $type ? 'strtoupper' : 'strtolower';
    $newArr = array(); //格式化后的数组
    if (!is_array($arr) || empty($arr))
        return $newArr;
    foreach ($arr as $k => $v) {
        $k = $function($k);
        if (is_array($v)) {
            $newArr[$k] = array_change_key_case_d($v, $type);
        } else {
            $newArr[$k] = $v;
        }
    }
    return $newArr;
}
/**
 * 对象转换成数组
 */
function object_to_array($obj) {
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    foreach ($_arr as $key => $val) {
        $val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
        $arr[$key] = $val;
    }
    return $arr;
}

function yhtbk(){
		$host = $_SERVER['HTTP_HOST'];
		preg_match('/[\w][\w-]*\.(?:com\.cn|com|cn|net|co|org|top|cc|name|info|me|pw|la|hk|dk|xin|so|wang|asia|biz|mobi|ren|club|site|space|online|tech|xyz|cn\.com|com\.cn|net\.cn|org\.cn|gov\.cn|com\.hk|tm|tv|tel|us|tw|website|host|vip|link|press|click|com\.tw)(\/|$)/isU', $host, $host_array);
		$domain = rtrim($host_array[0], '/');
		if (empty($domain)) {
			$strurl = str_replace("http://", "", $host);
			$strurldomain = explode("/", $strurl);
			$domain = $strurldomain[0];
		}
		$domain = trim($domain);
		
}

function is_email($user_email){
	$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
	if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false){
		if (preg_match($chars, $user_email)){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}


/**
 * ID 字母 转换
 */
function id_num($in,$to_num = false,$pad_up = false,$passKey = null)  {
    if(!function_exists('bcpow')) {
            return $in;
    }
        $index = 'abcdefghijklmnopqrstuvwxyz0123456789';
        if ($passKey !== null) {
            for ($n = 0;$n<strlen($index);$n++) {
                $i[] = substr( $index,$n ,1);
            }
            $passhash = hash('sha256',$passKey);
            $passhash = (strlen($passhash) <strlen($index))?hash('sha512',$passKey) : $passhash;
            for ($n=0;$n <strlen($index);$n++) {
                $p[] =  substr($passhash,$n ,1);
            }
            array_multisort($p,SORT_DESC,$i);
            $index = implode($i);
        }
        $base  = strlen($index);
        if ($to_num) {
            $in  = strrev($in);
            $out = 0;
            $len = strlen($in) -1;
            for ($t = 0;$t <= $len;$t++) {
                $bcpow = bcpow($base,$len -$t);
                $out   = $out +strpos($index,substr($in,$t,1)) * $bcpow;
            }
            if (is_numeric($pad_up)) {
                $pad_up--;
                if ($pad_up >0) {
                    $out -= pow($base,$pad_up);
                }
            }
            $out = sprintf('%F',$out);
            $out = substr($out,0,strpos($out,'.'));
        }else {
            if (is_numeric($pad_up)) {
                $pad_up--;
                if ($pad_up >0) {
                    $in += pow($base,$pad_up);
                }
            }
            $out = '';
            for ($t = floor(log($in,$base));$t >= 0;$t--) {
                $bcp = bcpow($base,$t);
                $a   = floor($in / $bcp) %$base;
                $out = $out .substr($index,$a,1);
                $in  = $in -($a * $bcp);
            }
            $out = strrev($out);
        }
        return $out;
}

function Tohttps($url){
if(substr($url,0,2)=='//'){ 
$url='https:'.$url;
};	
return $url;	
}

function kouling($logo, $text, $url,$simple=false)
{
$logo = $logo?$logo:'https://img.alicdn.com/imgextra/i4/126947653/O1CN01zB6KOQ26P7kZ09xAe_!!126947653.png';	
if(substr($url,0,2)=='//'){ 
$url='https:'.$url;
};
$text=str_replace("@","",$text);
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
if(!empty($appkey) && !empty($appsecret)){
vendor("taobao.taobao");
$c = new \TopClient();
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$req = new \TbkTpwdCreateRequest();
//$req->setUserId("123");
//$req->setText($text);
$req->setUrl($url);
//$req->setLogo($logo);
//$req->setExt("{}");
$resp = $c->execute($req);
$resparr = xmlToArray($resp);
if($simple){
$k=$resparr['data']['password_simple'];	
}else{
$k=$resparr['data']['model'];	
}
if($k){
$kouling=str_replace(['£','₤','₳','¢','《','》','€','$','₴','(',')'],'￥',$k);
$symbol=trim(C('yh_dingdan'));
if($symbol){
$ext_kouling=explode('|',$symbol);
$kouling = substr_replace($kouling,$ext_kouling[0],strpos($kouling,'￥'),strlen('￥'));
$kouling = substr_replace($kouling,$ext_kouling[1],strpos($kouling,'￥'),strlen('￥'));
}
	
return $kouling;
	
}



}

}

function xmlToArray($xml){
    $val = json_decode(json_encode($xml),true);
    return $val;
}

/*
 * 根据获取用户信息
 */
function getUserInfo($openid,$uid,$field)
{
    $user        = M('user');
    if($openid){
    	$map['openid'] =  $openid;
    }
    
    if($uid){
    	$map['id'] =  $uid;
    }
    
    $rows = $user->where($map)->getField($field);
    return $rows;
}


  function user_cash_type($key){
		$data=array(
		'6'=>array('提现','-￥'),
		'10'=>array('积分兑换','+￥'),
		'11'=>array('抢红包','+￥'),
		'1'=>array('提现冻结资金','￥'),
		'3'=>array('收益结算','+￥'),
		'4'=>array('余额充值','+￥'),
		'5'=>array('余额扣除','-￥'),
		'12'=>array('签到充值','+￥')
		);
	   //return $data;
	return serialize($data[$key]);
	}
 
	 function floatNumber($number){
		 $length = strlen($number);  //数字长度
		 if($length > 8){ //亿单位
			 $str = substr_replace(strstr($number,substr($number,-7),' '),'.',-1,0)."亿";
		 }elseif($length >4){ //万单位
			 //截取前俩为
			 $str = substr_replace(strstr($number,substr($number,-3),' '),'.',-1,0)."万";
		 }else{
			 return $number;
		 }
		 return $str;
	 }



