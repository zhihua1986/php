<?php
namespace Home\Action;
use Think\Page;
class Top100Action extends BaseAction {
	public function _initialize() {
		parent::_initialize();
		$this->_mod = D('items')->cache(true, 5 * 60);
	}
	

	public function index(){
		$page	= I('p',1 ,'number_int');
		$size	= 40;
		$cid		= I('cid',0,'number_int');
		$sort	= I('sort', 'new', 'trim');
		$start = $size * ($page - 1);
		$this->assign('txt_sort', $sort);
		$this->assign('cid', $cid);
		$key    = I("k",'','trim');
		$key    = urldecode($key);
		$where['ems'] = 1;
		$where['status'] = 'underway';
		$where['volume'] = array('gt','10000');
		if($key){
			$where['title|tags'] = array( 'like', '%' . $key . '%' );
			$this->assign('k', $key);
		}
		if($cid){
			$where['cate_id'] = $cid;
		}
		$stype    = I("stype");
		if($stype){
		 $where['shop_type'] = 'B';
		 $this->assign('stype', $stype);
		}
switch ($sort){
    		case 'new':
				$order = 'id DESC';
				break;
			case 'price':
				$order = 'coupon_price ASC';
				break;
			case 'rate':
				$order = 'quan DESC';
				break;
			case 'hot':
				$order = 'volume DESC';
				break;
			default:
				$order = 'ordid desc';
}

		$items_list = $this->_mod->where($where)->field('id,pic_url,num_iid,title,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit($start . ',' . $size)->select();	
		$count =$this->_mod->where($where)->count();
		$pager = new Page($count, $size);
		$this->assign('p', $page);
		$this->assign('page', $pager->show());
		$this->assign('total_item', $count);
		$this -> assign('page_size',$size);
		if($items_list){
			$today=date('Ymd');
			$goodslist=array();
			foreach($items_list as $k=>$v){
				$goodslist[$k]['id']=$v['id'];
				$goodslist[$k]['num_iid']=$v['num_iid'];
				$goodslist[$k]['pic_url']=$v['pic_url'];
				$goodslist[$k]['title']=$v['title'];
				$goodslist[$k]['coupon_price']=$v['coupon_price'];
				$goodslist[$k]['commission_rate']=$v['commission_rate'];
				$goodslist[$k]['price']=$v['price'];
				$goodslist[$k]['quan']=$v['quan'];
				$goodslist[$k]['shop_type']=$v['shop_type'];
				$goodslist[$k]['volume']=$v['volume'];	
				if($today==date('Ymd',$v['add_time'])){
					$goodslist[$k]['is_new']=1;	
				}else{
					$goodslist[$k]['is_new']=0;		
				}
				if(C('APP_SUB_DOMAIN_DEPLOY')){
					$goodslist[$k]['linkurl']=U('/item/',array('id'=>$v['id']));
				}else{
					$goodslist[$k]['linkurl']=U('item/index',array('id'=>$v['id']));
				}
				
			}
		}

		$this->assign('list',$goodslist);

		$this->_config_seo(C('yh_seo_config.top100'));
		
		$this->display();

	}


	
	
}