<?php
namespace Home\Action;
use Think\Page;
class ErshiAction extends BaseAction {
	public function _initialize() {
        parent::_initialize();
        $this->_mod = D('items')->cache(true, 5 * 60);
    }
	
	
public function index(){
	
$page	= I('p',1 ,'intval');
$size	= 60;
$cid		= I('cid','','trim');
$sort	= I('sort', 'new', 'trim');
$start = $size * ($page - 1);
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = I("k",'','trim');
$where['ems'] = 1;
$where['status'] = 'underway';
if($key){
 $where['title|tags'] = array( 'like', '%' . $key . '%' );
 $this->assign('k', $key);
}
if($cid){
 $where['cate_id'] = $cid;
}
$today_str=0;
$tomorr_str=20;
$where['coupon_price'] = array(
            array(
                'egt',
                $today_str
            ),
            array(
                'elt',
                $tomorr_str
            )
);
$order = 'ordid asc';
switch ($sort){
    		case 'new':
				$order.= ', coupon_start_time DESC';
				break;
			case 'price':
				$order.= ', price DESC';
				break;
			case 'rate':
				$order.= ', coupon_rate ASC';
				break;
			case 'hot':
				$order.= ', volume DESC';
				break;
			case 'default':
				$order.= ', '.C('yh_index_sort');
}

$items_list = $this->_mod->where($where)->field('id,pic_url,title,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit($start . ',' . $size)->select();	
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

$this->_config_seo(C('yh_seo_config.ershi'));
$this->display();

}

	

	
	
}