<?php
namespace M\Action;
use Common\Model\helpModel;
class AgentAction extends BaseAction
{
	
	public function index(){
		$this->_config_seo(array(
            'title' => '代理介绍'
        ));
		$this->display('Help/agent');
	}
	

}