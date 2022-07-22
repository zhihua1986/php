<?php
namespace Home\Action;
class ElmAction extends BaseAction
{
	
	
public function meituan(){
	
	$relation = I('type');
	$id = I('id');
	$RelationId = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
	$qrcode = $this->MeituanCode($id,$RelationId);
	if($qrcode){
	$data = array(
	'link'=>$qrcode,
	'type'=>3
	);
	exit(json_encode($data));
	
	}
	
}

public function other(){
	
	$ac = I('ac');
	$type = I('type');
	$id = I('id');
	$uid = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
	
	switch($id){
		case '5933':
		$url ='https://qz-m.oaqhsgl.cn/kfc/?platformId=10130';
		break;
		
		case '6680':
		$url ='https://qz-m.oaqhsgl.cn/starbucks/?platformId=10130';
		break;
		
		default:
		$url = 'https://www.didiglobal.com/';
		break;
	}
	if($id==10124 || $id==10127 || $id == 10130){
		$url= "https://i.meituan.com";
	}
    $data = $this->DuomaiLink($id,$url,array('euid'=>$uid?$uid:'m001'));
	if($data['url']){
	$link = base64_encode($data['url']);
	if($type == 3){
	$link = $data['wx_qrcode'];
	}
	
	$data = array(
	'link'=>$link,
	'type'=>$type
	);
	
	exit(json_encode($data));
	
	}
	
	
}


public function chong(){
	
	$t = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
	$data = $this->phoneBill($t);
	$data = $data['resource_url_response']['single_url_list'];
	if($data['url']){
		
		$data = array(
		'link'=>base64_encode($data['url']),
		'type'=>1
		);
		
		exit(json_encode($data));
	}
	
	
}


public function minapp(){
	
	if(I('ac') == 'wm'){
		$acid = $this->ActivityID(2192);
	}
	if(I('ac') == 'sc'){
		$acid = $this->ActivityID(4441);
	}
	$RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
	$data = $this->TbkActivity($acid, $RelationId,$this->memberinfo['id']);
	if($data['data']['wx_qrcode_url']){
		
		$data = array(
		'link'=>$data['data']['wx_qrcode_url'],
		'type'=>3
		);
		
		exit(json_encode($data));
	}
	
	
}

public function gaode(){
	
	$data = array(
	'link'=>base64_encode('https://cache.amap.com/activity/2020TaxiGetNew/index.html?gd_from=4k1Su5Z6RjQ&pid='.trim(C('yh_taobao_pid'))),
	'type'=>1
	);
	exit(json_encode($data));
}
	
public function index(){
	  $RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
	$data = $this->TbkActivity('20150318019998877', $RelationId,$this->memberinfo['id']);
	if ($data['data']['click_url']) {
		$kouling = kouling('https://img.alicdn.com/bao/uploaded/i1/2219509495/O1CN01yA0cYi2K0lETuFHN8_!!0-item_pic.jpg', '饿了么外卖红包', $data['data']['click_url']);
	}

$this->assign('kouling',$kouling);
$this->assign('qrcode',$data['data']['wx_qrcode_url']);
	$this->_config_seo(array(
            'title' => '饿了么外卖红包每天领-'.C('yh_site_name'),
       ));
	$this->display();
	
}
	

}

