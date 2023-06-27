<?php
namespace M\Action;
use Common\Model;
use Think\Page;
class Top100Action extends BaseAction{
	public function _initialize() {
        parent::_initialize();
        $this->_mod = D('items')->cache(true, 5 * 60);
    }
    
    
public function index(){
	$useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT'])); 
if((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1){ 
$this->assign('isweixin',true);

}	
$page	= I('p',0,'number_int');
$size	= 10;
$cid		= I('cid',0,'number_int');
$sort	= I('sort');
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = trimall(I("k",'','htmlspecialchars'));
$key    = urldecode($key);
$where['ems'] = 1;
$where['status'] = 'underway';
$where['volume'] = array('gt','1000');
if($key){
 $where['title|tags'] = array( 'like', '%' . $key . '%' );
 $this->assign('sokey', $key);
}

if($cid){
 $where['cate_id'] = $cid;
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
$items_list = $this->_mod->where($where)->field('id,pic_url,title,num_iid,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit(0,20)->select();
if($items_list){
$today=date('Ymd');
$goodslist=array();
foreach($items_list as $k=>$v){
$goodslist[$k]['id']=$v['id'];
$goodslist[$k]['num_iid']=$v['num_iid'];
$goodslist[$k]['pic_url']=$v['pic_url'];
$goodslist[$k]['title']=$v['title'];
$goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
$goodslist[$k]['coupon_price']=$v['coupon_price'];
$goodslist[$k]['price']=$v['price'];
$goodslist[$k]['quan']=intval($v['quan']);
$goodslist[$k]['shop_type']=$v['shop_type'];
$goodslist[$k]['volume']=$v['volume'];	
if($today==date('Ymd',$v['add_time'])){
$goodslist[$k]['is_new']=1;	
}else{
$goodslist[$k]['is_new']=0;		
}
if(C('APP_SUB_DOMAIN_DEPLOY')){
$goodslist[$k]['linkurl']=U('/item/',array('id'=>$v['num_iid']));
}else{
$goodslist[$k]['linkurl']=U('item/index',array('id'=>$v['num_iid']));
}
	
}
}
$this->assign('list',$goodslist);
if($cid){
$cateinfo=$this->_cate_mod->where('id='.$cid)->field('id,name,seo_title,seo_keys,seo_desc')->find();
}
$this->_config_seo(C('yh_seo_config.top100'), array(
			'cate_name' => $cateinfo['name'],
            'title' => $cateinfo['seo_title']?$cateinfo['seo_title']:$cateinfo['name'],
            'keywords' => $cateinfo['seo_keys'],
            'description' => $cateinfo['seo_desc'],
 ));
 
$this->display();

}


public function catelist(){
$page	= I('page',0,'number_int');
$size	= 20;
$cid		= I('cid','','trim');
$sort	= I('sort', 'new', 'trim');
$start = $size * $page;
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = trimall(I("k",'','htmlspecialchars'));
$key    = urldecode($key);
$where['ems'] = 1;
$where['status'] = 'underway';
$where['volume'] = array('gt','1000');
if($key){
 $where['title|tags'] = array( 'like', '%' . $key . '%' );
 $this->assign('k', $key);
}

if($cid){
 $where['cate_id'] = $cid;
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

$items_list = $this->_mod->where($where)->field('id,pic_url,title,num_iid,coupon_price,commission_rate,price,quan,shop_type,volume,add_time')->order($order)->limit($start . ',' . $size)->select();
if($items_list){
$today=date('Ymd');
$goodslist=array();
foreach($items_list as $k=>$v){
$goodslist[$k]['id']=$v['id'];
$goodslist[$k]['num_iid']=$v['num_iid'];
$goodslist[$k]['pic_url']=$v['pic_url'];
$goodslist[$k]['title']=$v['title'];
$goodslist[$k]['coupon_price']=$v['coupon_price'];
$goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
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
$goodslist[$k]['linkurl']=U('/item/',array('id'=>$v['num_iid']));
}else{
$goodslist[$k]['linkurl']=U('item/index',array('id'=>$v['num_iid']));
}
	
}
}


$this->assign('list',$goodslist);
	
	
$this->display('Cate/catelist2');	
}




}