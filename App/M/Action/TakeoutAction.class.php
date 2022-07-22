<?php
namespace M\Action;
class TakeoutAction extends BaseAction
{
	
	public function _initialize()
	{
	    parent::_initialize();
	}
	
	
	
	public function index(){
		$index = I('tab');
		$data = $this->MeituanTab();
		if(C('yh_dm_cid_mt') == 1){
		$data = $this->MeituanDmTab();
		}
		$this->assign('Tab',$data);
		$PageInfo = $data[$index?$index:0];
		$this->assign('PageInfo',$PageInfo);
		$uid = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
		if(C('yh_dm_cid_mt') == 1){
		$LinkInfo = $this->DuomaiLink($PageInfo['id'],'https://i.meituan.com',array('euid'=>$uid?$uid:'m001'));
		}else{
		$qrcode = $this->MeituanCode($PageInfo['id'],$uid);
		$linktype = 1;
		if($PageInfo['id'] == 105){ //优选不支持生成h5
			$linktype =8;
		}
		$link = $this->MeituanLink($PageInfo['id'],$uid,$linktype);
		
		$LinkInfo['cps_short'] = $link;
		$LinkInfo['wx_qrcode'] = $qrcode;
		
		}
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