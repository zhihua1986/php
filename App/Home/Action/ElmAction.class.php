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
	
	$domain = str_replace('/index.php/m','',C('yh_headerm_html'));
	$url = $domain.'/index.php?m=m&c=recharge&a=index';
	if($url){
		
		$data = array(
		'link'=>base64_encode($url),
		'type'=>1
		);
		
		exit(json_encode($data));
	}
	
	
}


    public  function ElmLink(){
        $activityId = I('id');
       $res = $this->CreateElmLink($activityId,$this->memberinfo);
       if($res['link']['mini_qrcode']){

           $data = array(
               'link'=>$res['link']['mini_qrcode'],
               'type'=>3
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
	  $back = $_SERVER["HTTP_REFERER"];
	  if ($back && stristr($back, trim(C('yh_headerm_html')))) {
	      $this->assign('back', $back);
	  }
	  // 是否需要做渠道备案
//	  if ($this->memberinfo && C('yh_bingtaobao') == 2 && !$this->visitor->get('webmaster_pid')){
//	  	$this->assign('callback',$this->fullurl());
//	      $this->assign('Tbauth', true);
//	  }
	  $index = I('tab')?I('tab'):0;
	  $data = $this->ElmTab();
	  $this->assign('Tab',$data);
	  $PageInfo = $data[$index?$index:0];
	  $this->assign('PageInfo',$PageInfo);
     $Id = $this->ActivityID($PageInfo['id']);
     $data = $this->CreateElmLink($Id,$this->memberinfo);
	  $this->assign('item',$data);
	  $this->_config_seo([
	      'title' => '饿了么外卖红包每天领-'.C('yh_site_name'),
	  ]);
	  $this->display();
	
}
	

}

