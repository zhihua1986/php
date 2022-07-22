<?php
namespace Home\Action;
class WaimaiAction extends BaseAction
{
	
	public function index(){
		
		$this->_config_seo(array(
            'title' => '本地商超、肯德基、电影票、滴滴打车、滴滴加油、外卖红包天天领 - '.C('yh_site_name'),
			'keywords' => '美团外卖红包,饿了么外卖红包,滴滴打车券,滴滴加油券',
			'description' => '本地商超、肯德基、电影票、滴滴打车、滴滴加油、外卖红包天天领',
        ));
		
		 $this->assign('takeout',$this->Takeout());
		
		$this->display();
	}
	
	

}