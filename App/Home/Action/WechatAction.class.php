<?php
namespace Home\Action;
use Common\Model\userModel;
use Common\Model\usercashModel;
class WechatAction extends BaseAction {
private $accessKey = '';
public function _initialize()
    {
 parent::_initialize();
$this->accessKey = trim(C('yh_gongju'));
$this->openid=I('openid','','trim');
$this->memberInfo = S('wechat_'.$this->openid);
$this->uid=$this->memberInfo['id'];
$this->mdomain=str_replace('/index.php/m','',trim(C('yh_headerm_html')));

	if($this->openid && false === $this->uid){
	$mod = new userModel();
	
		if(C('yh_site_tiaozhuan') == 1){
			
			 $this->memberInfo=$mod->field('id,special_id,webmaster_rate')->where(array('opid'=>$this->openid))->find();
			 $this->uid = $this->memberInfo['id'];
			if(!$this->memberInfo){
				$json=array(
				'state'=>2
				);
				exit(json_encode($json));
			}
	
		}elseif(C('yh_qrcode') == 2){ //关闭登录提醒
		
		$this->memberInfo=$mod->field('id,special_id,webmaster_rate')->where(array('openid'=>$this->openid))->find();
		$this->uid = $this->memberInfo['id'];
		}else{
		$this->memberInfo=$mod->field('id,special_id,webmaster_rate')->where(array('opid'=>$this->openid))->find();
		$this->uid = $this->memberInfo['id'];
		if(!$this->uid){
			$json=array(
			'state'=>2
			);
			exit(json_encode($json));
		}
			
		}
	S('wechat_'.$this->openid,$this->memberInfo,3600);
	}

 }
 
public function haibao(){
$this->check_key();	
$map=array(
//'openid'=>$this->openid
'id'=>$this->uid
);
$Mod = new userModel();
$AgentId=$Mod->field('id,invocode,tbname')->where($map)->find();
if($AgentId && $AgentId['tbname']==1){
$Json=array(
'state'=>1,
'invocode'=>$AgentId['invocode'],
'isopen'=>C('yh_invocode'),
'msg'=>$AgentId['id']
);
}elseif($AgentId){

$Json=array(
'state'=>4,
'msg'=>'您还不是代理，无法生成海报
👉<a href="'.$this->mdomain.'/index.php?m=m&c=agent&a=index">点我了解代理政策</a>'
);	

}else{
$Json=array(
'state'=>2
);	
}

exit(json_encode($Json));
	
}
 
public function tuiding(){
$this->check_key();	
$map=array(
//'openid'=>$this->openid
'id'=>$this->uid
);
$Mod = new userModel();
$action = I('action');
$data = array('special_id'=>'2');
if($action == 'td'){
	$data = array('special_id'=>1);
}
$result=$Mod->where($map)->save($data);

if($result!== false && $action == 'td'){
$Json=array(
'state'=>1,
'msg'=>'◇◇◇退订成功◇◇◇

您将不再收到此类外卖订餐提醒

回复【dy】可以重新订阅哟！
'
);	

exit(json_encode($Json));

}elseif($result!== false && $action == 'dy'){
	
	$Json=array(
	'state'=>1,
	'msg'=>'◇◇◇订阅成功◇◇◇
	
感谢您的支持，每天都可以收到我们的点餐提醒哟！
	'
	);	
	
	exit(json_encode($Json));
	
}else{
$json=array(
	'state'=>2,
	'msg'=>'没有查询到您的账户信息'
	);
}
exit(json_encode($json));	
	
}
 
 
public function balance(){
$this->check_key();	
$map=array(
//'openid'=>$this->openid
//'opid'=>$this->openid
'id'=>$this->uid
);
$Mod = new userModel();
$result=$Mod->field('money,frozen,score')->where($map)->find();
if($result){
$Json=array(
'state'=>1,
'msg'=>'◇◇◇账户信息◇◇◇
当前积分：'.$result['score'].'
账户余额：'.$result['money'].'元
冻结资金：'.$result['frozen'].'元
当余额达到'.C('yh_Quota').'元就可以提现啦！
👉<a href="'.$this->mdomain.'/index.php?m=m&c=user&a=tixian">点我申请提现</a>'
);	


exit(json_encode($Json));
	
}else{
$json=array(
	'state'=>2,
	'msg'=>'没有查询到您的账户信息'
	);
}
exit(json_encode($json));	
	
}


public function soJindong(){
$this->check_key();		
$skuid = I('goods_id');
	if ($skuid) {
	    $apiurl=$this->tqkapi.'/sojingdong';
	    $data=[
	        'key'=>$this->_userappkey,
	        'time'=>time(),
	        'tqk_uid'=>	$this->tqkuid,
	        'skuid'=>$skuid,
	    ];
	    $token=$this->create_token(trim(C('yh_gongju')), $data);
	    $data['token']=$token;
	    $result=$this->_curl($apiurl, $data, true);
	    $result=json_decode($result, true);
	    if ($result['status'] == 1) {
			$Mod = new userModel();
			$res=$Mod->where(array('id'=>$this->uid))->Field('jd_pid,webmaster_rate')->find();
			$jd_pid=$res['jd_pid'];
			$rate = $res['webmaster_rate'];
	        $cach_name='jump_jd_'.$skuid.$jd_pid;
			$result = $result['result'];
	        S($cach_name, $result, 86400);
			$itemUrl = 'https://item.jd.com/'.$result['skuId'].'.html';
			$json = array(
			'state'=>1,
			'result'=>$result,
			'rate'=>$rate,
			'link'=>$this->jdpromotion($itemUrl, $result['link'],$jd_pid)
			);
			
	        exit(json_encode($json));
	    }
	}
	
	
}


public function soduoduo(){
$this->check_key();	
$goodsid = I('goods_id');
$result = $this->PddGoodsSearch('', '', $goodsid, 0,'',40,'false');
		if($result){
		$Mod = new userModel();
		$rate=$Mod->where(array('id'=>$this->uid))->getField('webmaster_rate');
		$result['goodslist'][0]['rate']= $rate;
		$result['goodslist'][0]['uid']= $this->uid;
		exit(json_encode($result));	
			
		}
		exit;	
	
}


public function checkin(){
$this->check_key();
$Amount=abs(trim(C('yh_item_hit')));
if($Amount){
$Amount=mt_rand(1,$Amount*100)/100;
$mod_cash=new usercashModel();
$today=date('Y-m-d',NOW_TIME);
$checked=S('checkin_'.$this->uid);
if($this->uid && false ===$checked){
S('checkin_'.$this->uid,$this->uid,2);
$ischeck=$mod_cash->where(array('uid'=>$this->uid,'type'=>12))->order('id desc')->getField('create_time');
	if($today==date('Y-m-d',$ischeck)){
	$data=array(
	'msg'=>'今日已签到，请明天再来！

[玫瑰]置顶公众号 找券更方便！[玫瑰]

👉<a href="'.$this->mdomain.'/index.php?m=m&c=help&a=index&id=wap">《新手领券指南》</a>',
	'state'=>1
	);
	exit(json_encode($data));
	}

$data=array(
'uid'=>$this->uid,
'money'=>$Amount,
'type'=>12,
'remark'=>'签到送现金',
'create_time'=>NOW_TIME,
'status'=>1
);

$res=$mod_cash->add($data);
	if($res){
		$mod=new userModel();
		$mod->where("id='".$this->uid."'")->setInc('money',$Amount);
		$json=array(
		'msg'=>'签到成功，恭喜获得'.$Amount.'元
--------------------------
[玫瑰]置顶公众号 找券更方便！[玫瑰]

👉<a href="'.$this->mdomain.'/index.php?m=m&c=help&a=index&id=wap">《新手领券指南》</a>',
		'state'=>1
		);
		exit(json_encode($json));
	}


}


$data=array(
'msg'=>'请不要重复签到。
--------------------------
[玫瑰]置顶公众号 找券更方便！[玫瑰]

👉<a href="'.$this->mdomain.'/index.php?m=m&c=help&a=index&id=wap">《新手领券指南》</a>',
'state'=>1
);

exit(json_encode($data));

	
}	

$data=array(
'msg'=>'还没有开启签到功能！',
'state'=>1
);

exit(json_encode($data));
	
}


	public function GetUserInfo(){
		
		if($this->memberInfo){
		$data=array(
		'uid'=>$this->memberInfo['id'],
		'rate'=>$this->memberInfo['webmaster_rate'],
		'sid'=>$this->memberInfo['special_id'],
		'state'=>1
		);
		exit(json_encode($data));
		}
		
	}

	public function getUserPid(){
		$num_iid = I('numiid');
		$Arr = explode('-',$num_iid);
		$num_iid = $Arr[1]?$Arr[1]:$num_iid;
		if($this->uid && $num_iid){
		$R = A("Records");
		$res= $R ->content($num_iid,$this->uid);
		$Repid = $res['pid'];
			if($Repid){
			$data=array(
			'pid'=>$Repid,
			'rate'=>$res['rate'],
			'state'=>1
			);
			
			exit(json_encode($data));
			}
			
		}
		
		if(C('yh_qrcode') != 2){
			
		$data=array(
		'msg'=>'没有获取PID',
		'state'=>2
		);
		
		}
		
		exit(json_encode($data));
		
	}



protected  function check_key(){
 $json = array(
         'state'=>'no',
          'msg'=>'通行密钥不正确'
         );

$key = I('key', '', 'trim');
if(!$key || $key!=$this->accessKey){
exit(json_encode($json));
}	
}


}