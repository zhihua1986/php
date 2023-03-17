<?php
namespace M\Action;
class ElmAction extends BaseAction
{
	
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
	if($data['cps_short']){
	redirect($data['cps_short']);
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
	
	public function gaode(){
		
		$URL = 'https://cache.amap.com/activity/2020TaxiGetNew/index.html?gd_from=4k1Su5Z6RjQ&pid='.trim(C('yh_taobao_pid'));
		redirect($URL);
	}

    public function index(){
        $back = $_SERVER["HTTP_REFERER"];
        if ($back && stristr($back, trim(C('yh_headerm_html')))) {
            $this->assign('back', $back);
        }
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
