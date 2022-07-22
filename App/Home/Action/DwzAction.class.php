<?php
namespace Home\Action;
class DwzAction extends BaseAction
{
	public function _initialize()
    {
	parent::_initialize();
	
	}

public function create()
{
$url=I('url');
$apiurl=$this->tqkapi.'/Shortlink';
$apidata=array(
'tqk_uid'=>$this->tqkuid,
'time'=>time(),
'url'=>urlencode($url)
);
$token=$this->create_token(trim(C('yh_gongju')),$apidata);
$apidata['token']=$token;
$res= $this->_curl($apiurl,$apidata, false);
exit($res);
}


}