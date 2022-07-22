<?php
namespace M\Action;
use Common\Model;
use Common\TagLib\yhxia_https;
class DetailAction extends BaseAction{
	public function _initialize() {
		parent::_initialize();
		$this->_mod = D('items');
		if($this->getRobot()==false){
			$this->getrobot='no';
		}
	}
	
	
	public function index(){
		$id = I('id', '','trim');
		$item = $this->_mod->where(array('id' => $id))->find(); !$item && $this->_404();
		$this->assign('mdomain',str_replace('/index.php/m','',C('yh_headerm_html')));
		if($this->getrobot=='no')
		{
			$last_time=date('Y-m-d',$item['last_time']);
			$today=date('Y-m-d',time());

			if($last_time!=$today){
				$api_err='no';
				$apiurl=$this->tqkapi.'/gconvert';
				$apidata=array(
				'tqk_uid'=>$this->tqkuid,
				'time'=>time(),
				'good_id'=>''.$item['num_iid'].''
				);
				$token=$this->create_token(trim(C('yh_gongju')),$apidata);
				$apidata['token']=$token;
				$res= $this->_curl($apiurl,$apidata, false);
				$res = json_decode($res, true);
				$me=$res['me'];
				if(strlen($me)>5){
					$quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.'&activityId='.$item['Quan_id'].'&itemId='.$item['num_iid'].'&pid='.trim(C("yh_taobao_pid")).'&af=1';
					$kouling=kouling($item['pic_url'].'_400x400',$item['title'],$quanurl);
					$data=array(
						'last_time'=>time(),
						'quankouling'=>$kouling,
						'quanurl'=>$quanurl,
						'ding'=>0,
						'que'=>1
						);
					$re=$this->_mod->where(array(
						'num_iid' => $item['num_iid']
						))->save($data);


					if($re){

						$item['quankouling']=$kouling;
						$item['quanurl']=$quanurl;
						$item['que']=1;
					}else{

						$api_err='yes';

					}	

				}else{

					$api_err='yes';

				}

			}
		}


		$this->_config_seo(C('yh_seo_config.item'), array(
			'title' => $item['title'],
			'intro' => $item['intro'],
			'price' => $item['price'],
			'quan' => floattostr($item['quan']),
			'coupon_price' => $item['coupon_price'],
			'tags' => $tags,
			'title' => $item['seo_title'],
			'keywords' => $item['seo_keys'],
			'description' => $item['seo_desc'],
			));

		$cid = $item["cate_id"];
		$where=array(
			'cate_id'=>$cid,
			'id'=>array('neq',$id)
			);
		$orlike = $this->_mod->where($where)->field('id,volume,quan,title,pic_url,coupon_price,price,shop_type')->limit('0,6')->order('is_commend desc,id desc')->select();
		$this->assign('orlike', $orlike);
		if(empty($item['quankouling']) || $item['quankouling']=='0' || $item['quankouling']=='undefined'){
			$kouling=kouling($item['pic_url'].'_200x200.jpg',$item['title'],$item['quanurl']);
			$item['quankouling']=$kouling;
			$this->_mod->where(array(
				'num_iid' => $item['num_iid']
				))->setField('quankouling',$kouling);
		}


		if($this->getrobot=='no' && $api_err=='yes')
		{
			$last_time=date('Y-m-d',$item['last_time']);
			$today=date('Y-m-d',time());
//if($last_time!=$today || $item['ding']==1 || ($item['tk']==1 && $item['que']==0) ){
			if($last_time!=$today){	
				if(function_exists('opcache_invalidate')){
					$basedir = $_SERVER['DOCUMENT_ROOT']; 
					$dir=$basedir.'/data/runtime/Data/coupon/disable_num_iids.php';
					$ret=opcache_invalidate($dir,TRUE);
				}
				$disable_num_iids = F('coupon/disable_num_iids');
				if(!$disable_num_iids){
					$disable_num_iids = array();
				}
				$is=strpos(serialize($disable_num_iids),$item['num_iid']);
				if(empty($is)){
					$disable_num_iids[] =array(
						'num_iid'=>$item['num_iid'],
						'rate'=>$item['commission_rate'],
						'zc_id'=>$item['zc_id']
						); 
					
					if(function_exists('opcache_invalidate')){
						$basedir = $_SERVER['DOCUMENT_ROOT']; 
						$dir=$basedir.'/data/runtime/Data/coupon/disable_num_iids.php';
						$ret=opcache_invalidate($dir,TRUE);
					}

					F('coupon/disable_num_iids', $disable_num_iids);
					$data=array(
						'last_time'=>time(),
						'ding'=>0,
						'que'=>1,
						);
					$this->_mod->where(array(
						'num_iid' => $item['num_iid']
						))->save($data);

				}
			}
		}


		if($this->getrobot=='no'){
			$track_val=cookie('trackid');
			if(!empty($track_val)){
				$apiurl=$this->tqkapi.'/gconvert';
				$track='_'.base64_decode($track_val);
				$par_pid=$this->parent_pid();
				$npid=str_replace($par_pid,$track,trim(C('yh_taobao_pid')));
				$apidata=array(
				'tqk_uid'=>$this->tqkuid,
				'good_id'=>$item['num_iid'],
				'time'=>time(),
				'pid'=>$npid
				);
				$token=$this->create_token(trim(C('yh_gongju')),$apidata);
				$apidata['token']=$token;
				$res= $this->_curl($apiurl,$apidata, false);
				$res = json_decode($res, true);
				$me=$res['me'];
				if(strlen($me)>5){
					$quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.'&activityId='.$item['Quan_id'].'&itemId='.$item['num_iid'].'&pid='. $npid .'&af=1';
					$item['quanurl']=$quanurl;
					$item['quankouling']=kouling($item['pic_url'].'_400x400',$item['title'],$quanurl);
				}
				$this->assign('act','yes');
			}

		}
		if($this->getrobot=='no'){
			$Now=time();
			$this->assign('uptime',$Now-$item['up_time']);
		}
//		$agent=strtolower($_SERVER['HTTP_USER_AGENT']);
//		if(strpos($agent,'ucbrowser')>10 || strpos($agent,'mqqbrowser')>10){
//			$item['quankouling']=str_replace("￥","《",$item['quankouling']);	
//		}
		$buyer=$this->buyer(5);
		$this->assign('buyer', $buyer);
		$this->assign('item', $item);

		$this->display();

	}



	public function qrcode(){
		vendor("phpqrcode.phpqrcode");
		$data= I('dataurl', '', 'trim');
		$data=urldecode($data);
		$level = 'H';  
		$size = 4;
		$object = new \QRcode();
		ob_clean();
		$object->png($data, false, $level, $size,0);
	} 

	
	
}

function floattostr( $val ){
	preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o );
	return $o[1].sprintf('%d',$o[2]).($o[3]!='.'?$o[3]:'');
}	