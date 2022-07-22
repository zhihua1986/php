<?php
namespace Home\Action;
class KoubeiAction extends BaseAction
{
	
	
public function index(){

$RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
$data = $this->TbkActivity('1583739244161', $RelationId,$this->memberinfo['id']);
if ($data['data']['click_url']) {
$kouling = kouling('https://img.alicdn.com/bao/uploaded/i1/2219509495/O1CN01yA0cYi2K0lETuFHN8_!!0-item_pic.jpg', '口碑生活', $data['data']['click_url']);
}


$this->assign('kouling',$kouling);
	$this->_config_seo(array(
            'title' => '领口碑红包好生活更优惠-'.C('yh_site_name'),
       ));
	$this->display();
	
}
	

}

