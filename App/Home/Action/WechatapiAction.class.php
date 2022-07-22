<?php
namespace Home\Action;
date_default_timezone_set('Asia/Shanghai');
vendor('wechat.Wechat');
use Common\Model\userModel;
class WechatapiAction extends BaseAction
{
public function _initialize()
{
 parent::_initialize();
$this->_wx_appkey=trim(C('yh_wxappid'));
$this->_wx_appsecret=trim(C('yh_wxappsecret'));
$options = array(
	'token'=>trim(C('yh_wxtoken')),
	'encodingaeskey'=>trim(C('yh_wxaeskey')), 
	'appid'=>$this->_wx_appkey, 
	'appsecret'=>$this->_wx_appsecret,
	'debug'=>false, //改为true 表示开启调试，false表示关闭调试
	'_logcallback'=>'logdebug', 
	);
	

$this->weObj = new \Wechat($options);

}


protected function checkUnionId($openid,$unId=''){
	$UnionId = C('yh_unionid');
	$mod = D('user');
	$field = 'tb_open_uid,special_id,id,username,openid,nickname,phone,email,fuid,guid,invocode,avatar,password,score,tbname,money,webmaster,webmaster_rate,oid,webmaster_pid,jd_pid';
	if($UnionId == 1 && $unId){
		$where = array(
		'unionid'=>$unId
		);
		$result = $mod->field($field)->where($where)->find();
		if($result){
			return $result;
		}else{
			$where = array(
				'openid'=>$openid
				);
			$result = $mod->field($field)->where($where)->find();
			if($result){
				$data = array(
				'unionid'=>$unId
				);
			 $res = $mod->where(array('id'=>$result['id']))->save($data);
			 return $result;
			}
			
		}
	
	}else{
		
		$where = array(
			'openid'=>$openid
			);
		$result = $mod->field($field)->where($where)->find();
		if($result){
		 return $result;
		}
		
	}
	
	
	return false;
	
}

protected function putUserInfo($userInfo,$fuid=''){
	$mod = D('user');
	$field = 'tb_open_uid,special_id,id,username,openid,nickname,phone,email,fuid,guid,invocode,avatar,password,score,tbname,money,webmaster,webmaster_rate,oid,webmaster_pid,jd_pid';
	if ($fuid>0){
	    $where=[
	        'id'=>$fuid
	    ];
		$exist = $mod->field($field)->where($where)->find();
	}
	$now=NOW_TIME;
	$phone = $now;
	$pwd = md5($userInfo['openid']);
	$openid=$userInfo['openid'];
	$nickname=$userInfo['nickname'];
	$unionid=$userInfo['unionid'];
	$nickname=$this->remoji($nickname);
	$agentcondition=trim(C('yh_agentcondition'));
	if (0 >= $agentcondition) {
	    $tbname = 1;
	} else {
	    $tbname = 0;
	}
	
	$info=[
	    'username'=>$nickname ? $nickname : '匿名',
	    'nickname'=>$nickname ? $nickname : '匿名',
	    'password'=>$pwd,
	    'reg_ip'=>get_client_ip(),
	    'avatar'=>$userInfo['headimgurl'],
	    'state'=>1,
	    'tbname'=>$tbname,
	    'status'=>1,
	    'reg_time'=>$now,
	    'last_time'=>$now,
	    'create_time'=>$now,
	    'openid'=>$openid,
		'opid'=>$openid,
		'unionid'=>$unionid,
	    'fuid'=>$exist['id'] ? $exist['id'] : 0,
	    'guid'=>$exist['fuid'] ? $exist['fuid'] : 0
	];
	$res=$mod->add($info);
	if($res){
		$this->reinvi($res);
		return true;
	}
	
	return false;
	
}


protected function sendtpl($openid,$userInfo,$fuid=''){
	
	$result = $this->checkUnionId($openid,$userInfo['unionid']);
	if($result){
	$nickname = $result['nickname'];
	$Tiopenid = $result['openid'];
	}else{
	$res = $this->putUserInfo($userInfo,$fuid);	
	$nickname = $userInfo['nickname'];
	}
	
	$user = $this->weObj->getRevData();
	$Ticket = md5($user['Ticket']);
	S($Ticket,$Tiopenid?$Tiopenid:$openid,300);
	
	$tempid = trim(C('yh_tempid_5'));
	if(C('yh_site_tiaozhuan') == 1 && C('yh_notice') == 1 && $tempid && $openid){
		$post_data = array(
		        "touser"=>$openid,
		        "template_id"=>$tempid,
		        "data"=> array(
		                "first" => array(
		                        "value"=>'您已在电脑端成功登录！',
		                        "color"=>"#666666"
		                ),
		                "keyword1"=>array(
		                        "value"=>$nickname,
		                        "color"=>"#666666"
		                ),
		                "keyword2"=>array(
		                        "value"=>date('Y-m-d H:i',time()),
		                        "color"=>"#666666"
		                ),
		                "remark"=> array(
		                        "value"=>'感谢使用，请注意账号安全。',
		                        "color"=>"#666666"
		                ),
		        )
		);
		$this->weObj->sendTemplateMessage($post_data);
		
	}
	
	
}

public function index(){
$this->weObj->valid();
$event = $this->weObj->getRev()->getRevEvent();
if($event){
	
	$openid = $this->weObj->getRevFrom();
	
	switch($event['event']){
		case \Wechat::EVENT_SUBSCRIBE: 
		preg_match('/\d+/',$event['key'],$arr);
		$login = substr($arr[0],0,6);
		$fuid = substr($arr[0],6);
		if($login == 666888 && isset($event['key']) && strpos($event['key'], 'qrscene_') !== false){
			$userInfo = $this->weObj->getUserInfo($openid);
			$this->sendtpl($openid,$userInfo,$fuid);
			
		}
		
		exit;
		
		break;
		case \Wechat::EVENT_SCAN: 
		$login = substr($event['key'],0,6);
		$fuid = substr($event['key'],6);
		if($login == 666888 && isset($event['key'])){
		 $userInfo = $this->weObj->getUserInfo($openid);
		 //F('scan',$userInfo);
			$this->sendtpl($openid,$userInfo,$fuid);
			
		}
		
		
		exit;
		
		break;
		
		
		default:
		
		
		exit;
		break;
		
		
	}
	
	
	
	
}

	exit;
	
}

	



	
	
	
	
}



?>
	
