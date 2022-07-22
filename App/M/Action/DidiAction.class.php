<?php
namespace M\Action;
class DidiAction extends BaseAction
{
	
	public function _initialize()
	{
	    parent::_initialize();
	}
	
	
	
	public function index(){
		$index = I('tab');
		$data = $this->DidiTab();
		$this->assign('Tab',$data);
		$PageInfo = $data[$index?$index:0];
		$this->assign('PageInfo',$PageInfo);
		$uid = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
		$LinkInfo = $this->DuomaiLink($PageInfo['id'],'https://www.didiglobal.com/',array('euid'=>$uid?$uid:'m001'));
		$this->assign('info',$LinkInfo);
		$this->_config_seo([
		    'title' => $PageInfo['title'].'每天免费领取-'.C('yh_site_name'),
			'keywords'=>$PageInfo['title'],
			'description'=>C('yh_site_name').'提供'.$PageInfo['title'].'免费领取'
		]);
		
		$back = $_SERVER["HTTP_REFERER"];
		if ($back && stristr($back, trim(C('yh_headerm_html')))) {
		    $this->assign('back', $back);
		}
		
		$this->display();
	}
	
	
}