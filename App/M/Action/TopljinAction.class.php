<?php
namespace M\Action;

class TopljinAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
    }


public function index(){
	
	$back = $_SERVER["HTTP_REFERER"];
	if($back && stristr($back,trim(C('yh_headerm_html')))){
	$this->assign('back',$back);
	}

     $page	= I('page', 0, 'number_int');
	 $data = $this->Toplijin($page,30);
	
	$this->assign('list', $data);
	
	$this->_config_seo(array(
	           'title' => '淘礼金专区 - '. C('yh_site_name'),
	  ));
	
	$this->display('Chaohuasuan/topljin');
	
}


public function pagelist(){
	
     $page	= I('page', 0, 'number_int');
	 $data = $this->Toplijin($page,30);
	$this->assign('topone', $data);
	
	$this->display('Chaohuasuan/topljinlist');
	
}




}
