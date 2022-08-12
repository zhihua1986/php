<?php
namespace M\Action;
use Common\Model;
use Think\Page;
class CateAction extends BaseAction{
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
$sort	= I('sort', 'new');
$start = $size * $page;
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = I("k");
$key    = urldecode($key);
$where['ems'] = 1;
$where['status'] = 'underway';

if($key){
if($this->FilterWords($key)){
$this->_404();
}
    

if(false !== strpos($key,'https://')) {
	$linkid =$this->_itemid($key);
}

if(!$linkid){
	preg_match('/([a-zA-Z0-9]{11})/',$key,$allhtml1);
	if($allhtml1[1] && !is_numeric($allhtml1[1])){
	$kouling = $allhtml1[1];
	}
}

if(!$linkid && !$kouling){
	if(false !== strpos($key,'https://m.tb.cn') && preg_match('/[\x{4e00}-\x{9fa5}]/u', $key)>0){
		$kouling = true;
	}
}
if(($kouling || $linkid) && strstr($key, 'http') && !$this->hasEmoji($key)){
 $apiurl=$this->tqkapi.'/so';
 $data=array(
 'key'=>$this->_userappkey,
 'time'=>time(),
 'tqk_uid'=>$this->tqkuid,
 'hash'=>true,
 'k'=>urlencode($key),
 );
$token=$this->create_token(trim(C('yh_gongju')),$data);
$data['token']=$token;
$result=$this->_curl($apiurl,$data,true); 
$result=json_decode($result,true);
$newitem = array();
$newitem=	$result['result'][0];
if($result['status'] == 1 && !$newitem['tongkuan']){
$cach_name='jump_'.$newitem['num_iid'];
$newitem['coupon_price']=$newitem['price']-$newitem['quan'];
$newitem['quan']=$newitem['quan'];
$newitem['rate']=$newitem['commission_rate'];
$newitem['pict_url']=$newitem['pic_url'];
$newitem['quanid']=$newitem['Quan_id'];
$newitem['quanurl']=$newitem['url'];
S($cach_name,$newitem,86400);	
$url = U('jumpto/index',array('item'=>$newitem['num_iid']));
echo('<script>window.location.href="'.$url.'"</script>');
exit;
}
 
}

}




if($key){
 $where['title'] = array( 'like', '%' . $key . '%' );
 $this->assign('sokey', $key);
}

if($cid){
 $where['cate_id'] = $cid;
}
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

$items_list = $this->_mod->where($where)->field('id,pic_url,title,num_iid,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit(0,20)->select();
if($items_list){
$today=date('Ymd');
$goodslist=array();
foreach($items_list as $k=>$v){
if($this->FilterWords($v['title'])){
continue;
}
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
$goodslist[$k]['category_id']=$v['category_id'];
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

$this->assign('total_item',$count);

if(!empty($appkey) && !empty($appsecret) && !$this->hasEmoji($key) && $key && $count<10 && !empty($AdzoneId)){
vendor('taobao.taobao');
$c = new \TopClient();
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$c->format = 'json';
$req = new \TbkDgMaterialOptionalRequest();
$req->setAdzoneId($AdzoneId);
$req->setPlatform("1");
$req->setPageSize("100");
$req->setSort("tk_total_sales_des");
//$req->setHasCoupon("true");
$req->setQ((string)$key);
$req->setPageNo("1");
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$resp=$resp['result_list']['map_data'];	
//$item=array();
$patterns = "/\d+/";
foreach($resp as $k=>$v){
if($this->FilterWords($v['title'])){
continue;
}
$goodslist[$k+$count]['quan']=$v['coupon_amount'];
$goodslist[$k+$count]['coupon_click_url']=$v['coupon_share_url']?$v['coupon_share_url']:$v['url'];
$goodslist[$k+$count]['num_iid']=$v['item_id'];
$goodslist[$k+$count]['title']=$v['title'];
$goodslist[$k+$count]['coupon_price']=$v['zk_final_price']-$goodslist[$k+$count]['quan'];
if($v['user_type']=="1"){
$goodslist[$k+$count]['shop_type']='B';	
}else{
$goodslist[$k+$count]['shop_type']='C';	
}
$goodslist[$k+$count]['commission_rate']=$v['commission_rate']; //比例
$goodslist[$k+$count]['price']=$v['zk_final_price'];
$goodslist[$k+$count]['volume']=$v['volume'];
$goodslist[$k+$count]['pic_url']=$v['pict_url'];
$goodslist[$k+$count]['category_id']=$v['category_id'];
}
 
}


$this->assign('list',$goodslist);

if($cid){
$cid = $sid?$sid:$cid;
$cateinfo=$this->_cate_mod->where('ali_id='.$cid)->field('id,name,seo_title,seo_keys,seo_desc')->order('id desc')->find();
}

$seo = C('yh_seo_config.search');
 if($key && $seo['title']){
$this->_config_seo($seo, array(
    'key' => $key,
));
   
}else if($cateinfo){
	 $this->_config_seo(array(
            'cate_name' => $cateinfo['name'],
             'title' => $cateinfo['seo_title']?$cateinfo['seo_title']:'' . $cateinfo['name'] . '淘宝优惠券 - '. C('yh_site_name'),
            'keywords' => $cateinfo['seo_keys'],
            'description' => $cateinfo['seo_desc']
   ));
	
 }else{
 $this->_config_seo(C('yh_seo_config.cate'));
  }
 
$this->display();

if (preg_match('/[a-zA-Z]/',$key))
{
     return false;
}		
if($goodslist && 12 > strlen($key) && strlen($key)>3){
if(function_exists('opcache_invalidate')){
            $basedir = $_SERVER['DOCUMENT_ROOT']; 
            $dir=$basedir.'/data/Runtime/Data/data/hotkey.php';
            $ret=opcache_invalidate($dir,TRUE);
 }
        $disable_num_iids = F('data/hotkey');
        if(!$disable_num_iids){
            $disable_num_iids = array();
}
 if(count($disable_num_iids)>5){
 $disable_num_iids=array_slice($disable_num_iids,1,5); 
 }
 if(!in_array($key, $disable_num_iids)){
          $disable_num_iids[] = $key;
}
F('data/hotkey',$disable_num_iids);
}

}


public function catelist(){
$page	= I('p',0,'number_int');
$size	= 20;
$cid		= I('cid',0,'number_int');
$sort	= I('sort', 'new', 'trim');
//$start = $size * ($page-1);
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

$items_list = $this->_mod->where($where)->field('id,pic_url,title,num_iid,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit($start . ',' . $size)->select();
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
if($this->FilterWords($v['title'])){
continue;
}
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
$goodslist[$k]['category_id']=$v['category_id'];
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
if(!empty($appkey) && !empty($appsecret) && $key && $count<20 && !empty($AdzoneId)){
vendor('taobao.taobao');
$c = new \TopClient();
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$c->format = 'json';
$req = new \TbkDgMaterialOptionalRequest();
$req->setAdzoneId($AdzoneId);
$req->setPlatform("1");
$req->setPageSize("100");
$req->setQ((string)$key);
$req->setPageNo("1");
//$req->setHasCoupon('true');
$req->setIncludePayRate30("true");
$req->setSort("tk_des");
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$resp=$resp['result_list']['map_data'];	
$patterns = "/\d+/";
foreach($resp as $k=>$v){
if($this->FilterWords($v['title'])){
continue;
}
preg_match_all($patterns,$v['coupon_info'],$arr);
$quan=$arr[0];
//$coupon_price=$item_1['zk_final_price']-$quan;
$goodslist[$k+$count]['quan']=$v['coupon_amount'];
$goodslist[$k+$count]['coupon_click_url']=$v['coupon_share_url']?$v['coupon_share_url']:$v['url'];
$goodslist[$k+$count]['num_iid']=$v['num_iid'];
$goodslist[$k+$count]['title']=$v['title'];
$goodslist[$k+$count]['coupon_price']=$v['zk_final_price']-$goodslist[$k+$count]['quan'];
if($v['user_type']=="1"){
$goodslist[$k+$count]['shop_type']='B';	
}else{
$goodslist[$k+$count]['shop_type']='C';	
}
$goodslist[$k+$count]['commission_rate']=$v['commission_rate']; //比例
$goodslist[$k+$count]['price']=$v['zk_final_price'];
$goodslist[$k+$count]['volume']=$v['volume'];
$goodslist[$k+$count]['pic_url']=$v['pict_url'];
$goodslist[$k+$count]['category_id']=$v['category_id'];
}
 
}
$this->assign('list',$goodslist);
	
	
$this->display('catelist2');	
}




}