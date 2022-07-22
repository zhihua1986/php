<?php
namespace M\Action;

class SpecialAction extends BaseAction
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
	
	$id = $this->ActivityID(I('id'));
	$RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
	$res = $this->TbkActivity($id,$RelationId,$this->memberinfo['id']);
	if($res['data']['click_url']){
		
		$data = array(
		'banner'=>$this->topicImg($id),
		'content'=> kouling('',$res['data']['page_name'],$res['data']['click_url'])
		);
		
		$this->assign('data',$data);
		
		$this->_config_seo(array(
		           'title' => $res['data']['page_name'].'-'. C('yh_site_name'),
		 ));
		
		
	}
	
	
	
	$this->display('Topic/special');
	
}


}
