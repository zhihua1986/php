<?php
namespace Home\Action;
use Common\Action\FirstendAction;
use Common\Model\userModel;
use Common\Model\navModel;
class BaseAction extends FirstendAction{
public function _initialize()
    {
	parent::_initialize();
	// 网站状态
	if (! C('yh_site_status')) {
	    header('Content-Type:text/html; charset=utf-8');
	    exit(C('yh_closed_reason'));
	}
	$this->assign('navlist',$this->nav());
	$this->tqkuid=C('yh_app_kehuduan');
	$this->assign('hotkey',F('data/hotkey'));
	$this->assign('trackurl',$this->trackid());
	$tkapi=trim(C('yh_api_line'));
	if(false ===$tkapi){
		$this->tqkapi = 'http://api.tuiquanke.com';
		}else{
		$this->tqkapi = $tkapi;
	}
	$this->assign('alertwin',$this->_alert_adv());
	
	if($this->visitor->is_login){
        $info = $this->CreateJdPid($this->memberinfo);
        $info = $this->CreateElmPid($this->memberinfo);
    }
	
	// if($this->memberinfo && cookie('setsid') == 1){
	// 		$this->Getspecial();
	// }
	
	
	}

	
 protected function _alert_adv(){
 	$Ad = S('alertadhome');
    if($Ad){
    	$adlist = $Ad;
    }else{
     $adlist = M('ad')->field('id',true)->where('beginTime<'.NOW_TIME.' and endTime>'.NOW_TIME.' and status=3 and add_time=0')->order('id desc')->find();
      S('alertadhome',$adlist,3600);
     }
      return $adlist;
    }	

protected function nav(){
$where=array(
'status'=> 1
);
$navdata = S('navdata');
if($navdata){
$navlist	 = $navdata;
}else{
	
$mod=new navModel();
$navlist=$mod->cache(true, 50 * 60)->field('name,alias,link,target,status,type')->where($where)->order('ordid asc,id desc')->select();
S('navdata',$navlist,86400);
}

return $navlist;

}


protected function CreatePic($data,$uid){
$haibao = 'Public/static/wap/images/haibao.png';

if(!file_exists($haibao)){
return false;	
}

$config = array(
'text'=>array(
    array(
     'text'=>'扫描或者长按识别微信小程序二维码',
            'left'=>212,
            'top'=>-230,
            'fontPath'=>'ThinkPHP/Library/Think/Verify/zhttfs/1.ttf',     
            'fontSize'=>16,            			 
            'fontColor'=>'0,0,0', 				
            'angle'=>0,
    )
  ),
    'image'=>array(
        array(
			'url'=>$data,				
            'left'=>238,
            'top'=>-337,
            'stream'=>0,						
            'right'=>0,
            'bottom'=>0,
            'width'=>300,
            'height'=>300,
            'opacity'=>100
        )
    ),
    'background'=>$haibao,
);
 $filename = 'data/upload/editer/'.$uid.'.jpg';
 $this->createPoster($config,$filename); 
 @unlink('data/upload/editer/'.$uid.'_temp.jpg');
 return $filename;
}

protected function api_notice_increment($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return $ch;
    } else {
        curl_close($ch);
        return $tmpInfo;
    }
}

protected function Authtoken(){
	$http_token = $_SERVER['HTTP_AUTHTOKEN'];
	if($http_token && $http_token == F('data/'.$http_token)){
	return $http_token;
	}
	
	$json=array(
	'code'=>500,
	'msg'=>'登录超时！',
	'stime'=>NOW_TIME
	);
	exit(json_encode($json,JSON_UNESCAPED_UNICODE));
}

    protected function trackid(){
        $track=I('t','0','number_int');
        $isagent=cookie('createagent');
        
        if(($track && $track!=$isagent && is_numeric($track)) || (false === $isagent && $track && is_numeric($track))){
        $usermod=new userModel();
        	$res=$usermod->field('webmaster_pid,pdd_pid,invocode,jd_pid')->where(array('id'=>$track))->find();
        	if($res){
        	$data=array(
        	't_pid'=>$res['webmaster_pid'],
//      	'p_pid'=>$res['pdd_pid'],
        	'recode'=>$res['invocode'],
        	'jd_pid'=>$res['jd_pid'],
        	't'=>$track,
        	);
        	$data=serialize($data);
       }
         cookie("trackid", $data, 86400 * 15);
         cookie('createagent',$track);
        }

    }
	
	
	
}
	