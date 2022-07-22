<?php
namespace Admin\Action;
use Common\Model;
class ZhiboAction extends BaseAction
{
	
public function _initialize()
    {
     parent::_initialize();
	 $this->_mod = D('setting');
  }
	
public function task(){
	
	
$this->display();
	
}

	
public function setting(){
	
$this->display();
	
}





		
		
}
	