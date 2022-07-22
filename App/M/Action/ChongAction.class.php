<?php
namespace M\Action;
use Common\Model;
class ChongAction extends BaseAction{
    public function _initialize() {
        parent::_initialize();
    }

    public function index()
    {
		$t = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
		$data = $this->phoneBill($t);
		$data = $data['resource_url_response']['single_url_list'];
		if($data['url']){
			 redirect($data['url']);
		}
		echo('<script>alert("获优惠信息失败，请稍后再试！");history.go(-1)</script>');
        
    }

  


}