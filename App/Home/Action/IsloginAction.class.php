<?php
namespace Home\Action;
class IsloginAction extends BaseAction{
  public function _initialize()
  {
    parent::_initialize();
    $reurl=$_SERVER['REQUEST_URI'];
    $reurl=str_replace('index.php/','',$reurl);
  }

public function index(){
	
	$this->display('Index/islogin');
	
}  



}