<?php
namespace M\Action;
use Common\Model\usermodel;
class AuthorAction extends BaseAction
{
	
	public function perfect(){
		$this->_config_seo(array(
            'title' => '完善信息'
        ));
		$this->display();
	}
	

}