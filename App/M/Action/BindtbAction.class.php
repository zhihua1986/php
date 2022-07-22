<?php
namespace M\Action;
use Think\Page;
use Common\Model\userModel;
class BindtbAction extends BaseAction {
public function _initialize() {
  parent::_initialize();
}



public function index(){
$this->check_tb_token(trim(C('yh_gongju')));
$apidata=array(
'uid'=>$this->params['uid'],
'from'=>$this->params['from'],
);
$token=$this->create_token(trim(C('yh_gongju')),$apidata);
$callback=trim(C('yh_site_url')).'/index.php?m=home&c=api&a=tbclientauth&uid='.$this->params['uid'].'&token='.$token.'&from='.$this->params['from'];
$callback=urlencode($callback);
$oauth='https://oauth.m.taobao.com';	
$url=$oauth.'/authorize?response_type=code&client_id='.trim(C('yh_taobao_appkey')).'&redirect_uri='.$callback.'&state=1&view=wap';
echo('<script>window.location.href="'.$url.'" </script>');		
	
}




}
