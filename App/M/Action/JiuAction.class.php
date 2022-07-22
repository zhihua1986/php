<?php
namespace M\Action;
use Common\Model;
use Think\Page;
class JiuAction extends BaseAction{
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
$size	= 20;
$cid		= I('cid',0,'number_int');
$sort	= I('sort', 'new', 'trim');
$start = $size * $page;
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = trimall(I("k",'','htmlspecialchars'));
$key    = urldecode($key);
$where['ems'] = 1;
$where['status'] = 'underway';
if($key){
 $where['title'] = array( 'like', '%' . $key . '%' );
 $this->assign('sokey', $key);
}

if($cid){
 $where['cate_id'] = $cid;
}
$today_str=0;
$tomorr_str=9.9;
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

$count =$this->_mod->where($where)->count();
$pagesize=ceil($count/$size);
$pagesize==0?$pagesize=1:$pagesize;
$this -> assign('page_size',$pagesize);
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

$items_list = $this->_mod->where($where)->field('id,pic_url,num_iid,title,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit(0,20)->select();
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
$this->_config_seo(C('yh_seo_config.jiu'), array(
			'cate_name' => $cateinfo['name'],
            'title' => $cateinfo['seo_title']?$cateinfo['seo_title']:$cateinfo['name'],
            'keywords' => $cateinfo['seo_keys'],
            'description' => $cateinfo['seo_desc'],
 ));
 
$this->display();

}


public function catelist(){
$page	= I('p',0,'number_int');
$size	= 10;
$cid		= I('cid',0,'number_int');
$sort	= I('sort', 'new', 'trim');
$start = $size * $page;
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = trimall(I("k",'','htmlspecialchars'));
$key    = urldecode($key);
$where['ems'] = 1;
$where['status'] = 'underway';
if($key){
 $where['title'] = array( 'like', '%' . $key . '%' );
 $this->assign('k', $key);
}

if($cid){
 $where['cate_id'] = $cid;
}
$today_str=0;
$tomorr_str=9.9;
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


$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));

$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[3];

$count=count($items_list);
if(!empty($appkey) && !empty($appsecret) && $key && $count<10 && !empty($AdzoneId)){
vendor('taobao.taobao');
$c = new \TopClient();
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$c->format = 'json';
$req = new \TbkDgItemCouponGetRequest();
$req->setAdzoneId($AdzoneId);
$req->setPlatform("1");
$req->setPageSize("100");
$req->setQ((string)$key);
$req->setPageNo("1");
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$resp=$resp['results']['tbk_coupon'];	
//$item=array();
$patterns = "/\d+/";
foreach($resp as $k=>$v){
preg_match_all($patterns,$v['coupon_info'],$arr);
$quan=$arr[0];
//$coupon_price=$item_1['zk_final_price']-$quan;
$goodslist[$k+$count]['quan']=$arr[0][1];
$goodslist[$k+$count]['coupon_click_url']=$v['coupon_click_url'];
$goodslist[$k+$count]['num_iid']=$v['num_iid'];
$goodslist[$k+$count]['title']=$v['title'];
$goodslist[$k+$count]['coupon_price']=$v['zk_final_price']-$goodslist[$k+$count]['quan'];
if($v['user_type']=="1"){
$goodslist[$k+$count]['shop_type']='B';	
}else{
$goodslist[$k+$count]['shop_type']='C';	
}
$goodslist[$k+$count]['price']=$v['zk_final_price'];
$goodslist[$k]['commission_rate']=$v['commission_rate']*100; //比例
$goodslist[$k+$count]['volume']=$v['volume'];
$goodslist[$k+$count]['pic_url']=$v['pict_url'];
}
 
}
$this->assign('list',$goodslist);
	
	
$this->display('Cate/catelist2');	
}




}