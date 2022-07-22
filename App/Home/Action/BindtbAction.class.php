<?php
namespace Home\Action;
use Think\Page;
use Common\Model\userModel;
class BindtbAction extends BaseAction {
public function _initialize() {
  parent::_initialize();
  if($this->visitor->is_login == false){
        	   $url=U('login/index','','');
           redirect($url);
    }
    
}



public function index(){
$apidata=array(
'uid'=>$this->memberinfo['id'],
'from'=>'pc'
);
$token=$this->create_token(trim(C('yh_gongju')),$apidata);
$callback=trim(C('yh_site_url')).'/index.php?c=api&a=tbclientauth&uid='.$this->memberinfo['id'].'&token='.$token.'&from=pc';
$callback=urlencode($callback);
$oauth='https://oauth.taobao.com';	
$url=$oauth.'/authorize?response_type=code&client_id='.trim(C('yh_taobao_appkey')).'&redirect_uri='.$callback.'&state=1';
echo('<script>window.location.href="'.$url.'" </script>');		
	
}




}
