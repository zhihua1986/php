<?php
namespace Home\Action;
use Think\Page;

class LiveAction extends BaseAction {
	
	public function _initialize() {
        parent::_initialize();
    }
	
	
public function index(){
	
		$page	= I('p',1 ,'number_int');
		$size = 40;
		$data = $this->DmRequest(
		'cps-mesh.cpslink.douyinSelf.liveMaterial.get',[
		"page"=>$page,
		"page_size"=>$size
		]
		);
		$count =$data['pagination']['total'];
		$pager = new Page($count, $size);
		$this->assign('p', $page);
		$this->assign('page', $pager->show());
		$this->assign('total_item', $count);
		$this -> assign('page_size',$size);
		$this->assign('list',$data['data']);
		$this->_config_seo(array(
				'title' => '看抖音直播享特惠好货 - '.C('yh_site_name'),
				'keywords' => '抖音直播优惠,抖音下单返利',
				'description' => '看抖音直播间,抢特惠好货还能得返利。',
			));
 
$this->display();

}

public function getlink(){
	
	$uid = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
	$authorid = I('openid');
	$data = $this->DuomaiLink('14633','',array('euid'=>$uid?$uid:'m001','douyin_openid'=>$authorid));
	if($data['qr_code']){
	$data = [
		'link'=>$data['qr_code'],
		'type'=>3,
	];
	exit(json_encode($data));
	}
	
}
	
	
}