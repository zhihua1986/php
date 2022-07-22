<?php
namespace M\Action;
use Common\Model\itemsModel;
use Common\Model\helpModel;
use Think\Page;
class FirstorderAction extends BaseAction
{

public function help(){
 $help_mod =new helpModel();
$helps = $help_mod->field('id,title')->select();
$this->_config_seo(array(
    'title' => $help['title']
));
$this->assign('helps', $helps);

 $this->_config_seo(array(
        'title' => '首单礼金领取教程-'.C('yh_site_name'),
));

$this->display();	
}	

public function index(){
	$back = $_SERVER["HTTP_REFERER"];
	if($back && stristr($back,trim(C('yh_headerm_html')))){
	$this->assign('back',$back);
	}
$useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT'])); 
if((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1){ 
$this->assign('isweixin',true);
}
$page	= I('p',0,'number_int');
$sort	= I('sort','price');
$ItemMod = new itemsModel();
$where = array(
'status'=>'underway',
'pass'=>1,
'isshow'=>1,
'ems'=>1,
'cate_id'=>11000
);
$key    = I("k");
if($key){
 $where['title'] = array( 'like', '%' . $key . '%' );
 $this->assign('sokey', $key);
}

switch ($sort){
    		case 'new':
				$order = 'id DESC';
				break;
			case 'price':
				$order = 'change_price asc';
				break;
			case 'rate':
				$order = 'quan DESC';
				break;
			case 'hot':
				$order = 'volume DESC';
				break;
			default:
				$order = 'change_price asc';
}
$this->assign('sort',$sort);
$list=$ItemMod->where($where)->field('id,pic_url,change_price,title,num_iid,coupon_price,price,quan,shop_type,volume,add_time,commission_rate')->order($order)->limit(20)->select();
$this->assign('topone',$list);
$count =$ItemMod->where($where)->count();
$pagesize=ceil($count/20);
$pagesize==0?$pagesize=1:$pagesize;
$this->assign('page_size',$pagesize);
	
	$this->_config_seo(array(
            'title' => '天猫品牌首单礼金-'.C('yh_site_name'),
       ));
	$this->display();
	
	
}


public function create()
{
$line=I('line',1,'number_int');
$cachename = 'firstorder-'.$line;
if(IS_POST){
$data = S($cachename);
if(false === $data || !$data){
$apiurl=$this->tqkapi.'/Fristorder';
$apidata=array(
'tqk_uid'=>$this->tqkuid,
'time'=>time(),
'line'=>$line
);
$token=$this->create_token(trim(C('yh_gongju')),$apidata);
$apidata['token']=$token;
$data= $this->_curl($apiurl,$apidata, false);
$data=json_decode($data,true);
S($cachename,$data);
}

}


exit(json_encode($data));
}



public function pagelist(){
$ItemMod = new itemsModel();
$page	= I('page',1,'number_int');
$size	= 20;
$sort	= I('sort','price');
$start = $size * $page;
$where = array(
'status'=>'underway',
'pass'=>1,
'isshow'=>1,
'ems'=>1,
'cate_id'=>11000
);
$key    = I("k");
if($key){
 $where['title'] = array( 'like', '%' . $key . '%' );
 $this->assign('sokey', $key);
}

switch ($sort){
    		case 'new':
				$order = 'id DESC';
				break;
			case 'price':
				$order = 'change_price asc';
				break;
			case 'rate':
				$order = 'quan DESC';
				break;
			case 'hot':
				$order = 'volume DESC';
				break;
			default:
				$order = 'change_price asc';
}

$list=$ItemMod->where($where)->field('id,pic_url,change_price,title,num_iid,coupon_price,price,quan,shop_type,volume,add_time,commission_rate')->order($order)->limit($start . ',' . $size)->select();
$goodslist=array();
foreach($list as $k=>$v){
$goodslist[$k]['id']=$v['id'];
$goodslist[$k]['pic_url']=$v['pic_url'];
$goodslist[$k]['change_price']=$v['change_price'];
$goodslist[$k]['title']=$v['title'];	
$goodslist[$k]['num_iid']=$v['num_iid'];	
$goodslist[$k]['coupon_price']=$v['coupon_price'];
$goodslist[$k]['price']=$v['price'];	
$goodslist[$k]['quan']=$v['quan'];	
$goodslist[$k]['volume']=$v['volume'];	
$goodslist[$k]['add_time']=$v['add_time'];	
$goodslist[$k]['commission_rate']=$v['commission_rate'];	
if(C('APP_SUB_DOMAIN_DEPLOY')){
$goodslist[$k]['linkurl']=U('/item/',array('id'=>$v['num_iid']));
}else{
$goodslist[$k]['linkurl']=U('item/index',array('id'=>$v['num_iid']));
}
}
$this->assign('topone',$list);
$this->display();

	
	
}




}