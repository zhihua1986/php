<?php
namespace M\Action;

class RechargeAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if ((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1) {
            $this->assign('isweixin', true);
        }

		$pid = trim(C('yh_jdpid'));
		if ($pid && C('yh_openjd') == 1) {
			$Name = 'recharge';
			$res = S($Name);
			if(!$res){
			$apiurl=$this->tqkapi.'/recharge';
			$apidata=[
				'tqk_uid'=>$this->tqkuid,
				'time'=>time(),
				'pid'=>$pid,
			];
			$token=$this->create_token(trim(C('yh_gongju')), $apidata);
			$apidata['token']=$token;
			$res= $this->_curl($apiurl, $apidata, false);
			$res = json_decode($res, true);
			S($Name,$res);
			}
			
		}
        $this->assign('list', $res['result']['result']['goods']);
			
		$this->_config_seo(array(
					   'title' => '特惠充值电话费 - '. C('yh_site_name'),
			  ));
 

     $this->display();
    }

}
