<?php
namespace M\Action;
class WaimaiAction extends BaseAction
{
	
	public function index(){
		$tab = I('tab');
		$this->assign('tab',$tab);
		 $isfanli = C('yh_isfanli');
		 $this->assign('isfanli',$isfanli);
		 if($isfanli == 2){
			 $btn = '立即领红包';
		 }else{
			 $btn = '下单拿返利';
		 }
		 $this->assign('btn',$btn);
		
		$this->_config_seo(array(
            'title' => '本地商超、外卖红包天天领 - '.C('yh_site_name'),
			'keywords' => '美团外卖红包,饿了么外卖红包',
			'description' => '',
        ));
		$this->display();
	}
	
	
	public function getlink(){
		
		$relation = I('type');
		$id = I('id');
		$activityid = $this->ActivityID(I('id'));
		if($relation && $relation == 'elm'){
		$RelationId = $this->memberinfo['webmaster_pid']?$this->memberinfo['webmaster_pid']:$this->GetTrackid('t_pid');	
		$data = $this->TbkActivity($activityid,$RelationId,$this->memberinfo['id']);
		$result = $data['data'];
		$result['img']=$this->imglist($id);
		$result['wxapp']=$result['wx_qrcode_url'];
		$this->assign('data',$result);
		}
		
		if($relation && $relation == 'mt'){
			$RelationId = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');	
			$qrcode = $this->MeituanCode($id,$RelationId);
			$linktype = 1;
			if($id == 105){ //优选不支持生成h5
				$linktype =8;
			}
			$link = $this->MeituanLink($id,$RelationId,$linktype);
			$result = array();
			$result['img']= $this->imglist($id);
			$result['click_url']=$link;
			$result['wxapp']=$qrcode;
			$this->assign('data',$result);
		}
		
		$this->assign('relation',$relation);
		
		$this->_config_seo(array(
		    'title' => '领红包下单 - '.C('yh_site_name'),
			'keywords' => '',
			'description' => '',
		));
		
		$this->display();
		
		
	} 
	
 private function imglist($id){
	 
	 $data = array(
	 '4'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN019W952m2MgYhKlN1TI_!!3175549857.jpg',
	 '33'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN018URa0G2MgYhW3UkrJ_!!3175549857.jpg',
	 '22'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01USVI472MgYhw2dkHG_!!3175549857.png',
	 '8877'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN0145R1MN2MgYhKqj0Gr_!!3175549857.jpg',
	 '4441'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01EHEutE2MgYhTjAKqS_!!3175549857.jpg',
	 '1583739244161'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01yXCRnq2MgYhUabUVU_!!3175549857.jpg'
	 );
	 
	 return $data[$id];
	 
 }
	

}