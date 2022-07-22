<?php
namespace M\Action;
use Common\Action\FirstendAction;
class MsgAction extends FirstendAction{
	
	
	public function index(){
		
		$this->_config_seo(array(
			'title'=>'绑定淘宝账号'
			));                         
		
	 $this->display('Index/msg');
	 
	}
	
	
public function bindtb(){

$time=NOW_TIME;
$UID=I('uid', '', 'trim');
$this->check_tb_token(trim(C('yh_gongju')));

$map=array(
'uid'=>$UID,
'from'=>'wechat'
);

$token=$this->create_token(trim(C('yh_gongju')),$map);


$url = U('bindtb/index',array('uid'=>$UID,'from'=>'wechat','token'=>$token));
$this->assign('quanurl',$url);
		
$this->display('User/bindtb');
		
}
	
    
}