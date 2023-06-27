<?php
namespace M\Action;
use Common\Model;
use Think\Page;
use Common\Model\jditemsModel;
class JdAction extends BaseAction{
	public function _initialize() {
        parent::_initialize();
        $mod = new jditemsModel();
        $this->_mod = $mod ->cache(true, 5 * 60);
    }
 
protected function jdquery($key){
	
  $skuid = $this->jditemid($key);
  if($skuid){
	 $apiurl=$this->tqkapi.'/sojingdong';
	 $data=array(
	 'key'=>$this->_userappkey,
	 'time'=>time(),
	 'tqk_uid'=>	$this->tqkuid,
	 'skuid'=>$skuid,
	 );
	$token=$this->create_token(trim(C('yh_gongju')),$data);
	$data['token']=$token;
	$result=$this->_curl($apiurl,$data,true);
	$result=json_decode($result,true);
	if($result['status'] == 1){
	$jd_pid = $this->memberinfo['jd_pid'] ? $this->memberinfo['jd_pid'] : $this->GetTrackid('jd_pid');
	$cach_name='jump_jd_'.$skuid.$jd_pid;
	S($cach_name,$result['result'],86400);
	$url = U('jditem/jump',array('i'=>$skuid));
	echo('<script>window.location.href="'.$url.'"</script>');
	exit;
   		
	}
	  	
	  }
	
	
	
}   
    
public function index(){
$useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT'])); 
if((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1){ 
$this->assign('isweixin',true);

}
$this->assign('ItemCate', $this->_mod->Jdcate());
	
$size	= 10;
$cid	= I('cid',0,'number_int');
$sort	= I('sort', 'new', 'trim');
$page	= I('p',1 ,'number_int');
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$start = $size * ($page - 1);
$key    = I("k");
$key    = urldecode($key);
if($key){
if($this->FilterWords($key)){
$this->_404();
}
//$this->jdquery($key);
 $where['title'] = array( 'like', '%' . $key . '%' );
 $this->assign('k', $key);
}
$stype    = I("stype");
if($stype){
 $where['item_type'] = $stype;
 $this->assign('stype', $stype);
}

$pop   = I("pop");
if($pop == 1){
 $where['owner'] = 'g';
 $this->assign('pop', $pop);
}

if($cid){
 $where['cate_id'] = $cid;
}
switch ($sort){
    		case 'new':
				$order = 'id DESC';
				break;
			case 'price':
				$order = 'coupon_price asc';
				break;
			case 'rate':
				$order = 'quan desc';
	       $where['quan'] = array( 'gt', 0);
				break;
			case 'hot':
				$order = 'comments DESC';
				break;
			default:
				$order = 'id desc';
}

$categoryid = I('gid');
if(is_numeric($categoryid)){
$categoryid	= $categoryid;
$this->assign('gid', $categoryid);
}

$data = $this->JdGoodsList($size,$where,$order,$page,false,$key,$categoryid);
//$this->assign('total_item', count($data['goodslist']));
$this->assign('list',$data['goodslist']);

if($cid){
$cateinfo=$this->_mod->Jdcate($cid);
}

$seo = C('yh_seo_config.searchjd');
 if($key && $seo['title']){
$this->_config_seo($seo, array(
    'key' => $key,
));
	
}else if($cateinfo){
	 $this->_config_seo(array(
            'cate_name' => $cateinfo,
             'title' => '京东'.$cateinfo.'优惠券 - '. C('yh_site_name'),
            'keywords' => $cateinfo,
            'description' => $cateinfo
   ));
 }else{
 $this->_config_seo(C('yh_seo_config.jindong'));
  }
 
$this->display();

}


public function catelist(){
$page	= I('page',0,'intval');
$size	= 10;
$cid	= I('cid','','intval');
$sort	= I('sort', 'new', 'trim');
$start = $size * $page;
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = I("k");
$key    = urldecode($key);
if($key){
 $where['title'] = array( 'like', '%' . $key . '%' );
 $this->assign('k', $key);
}
$stype    = I("stype");
if($stype){
 $where['item_type'] = $stype;
 $this->assign('stype', $stype);
}

$pop   = I("pop");
if($pop == 1){
 $where['owner'] = 'g';
 $this->assign('pop', $pop);
}

if($cid){
 $where['cate_id'] = $cid;
}
switch ($sort){
    		case 'new':
				$order = 'id DESC';
				break;
			case 'price':
				$order = 'coupon_price asc';
				break;
			case 'rate':
				$order = 'quan desc';
	       $where['quan'] = array( 'gt', 0);
				break;
			case 'hot':
				$order = 'comments DESC';
				break;
			default:
				$order = 'id desc';
}
$data = $this->JdGoodsList($size,$where,$order,$page,false,$key);
$this->assign('list',$data['goodslist']);
	
$this->display();	
}



}