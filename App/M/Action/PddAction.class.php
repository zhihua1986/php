<?php
namespace M\Action;
use Common\Model;
use Think\Page;
class PddAction extends BaseAction{
	public function _initialize() {
        parent::_initialize();
		
		/*
		当前duoId被禁止调用商品详情。封禁原因：系统检测该duoId商品详情请求查询非多多进宝商品占比过高，涉嫌爬虫或本地缓存商品信息。
		拼多多商品禁止搜索引擎访问，防止出现接口被封
		*/
		if ($this->getRobot()!==false) {
		exit;
		}
		
        $mod = D('pdditems');
        $this->_mod = $mod ->cache(true, 5 * 60);
    }
	
public function index(){
$useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT'])); 
if((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1){ 
$this->assign('isweixin',true);

}	

$cats = $this->PddGoodsCats();
$this->assign('ItemCate',$cats);
$size	= 20;
$cid		= I('cid',0 ,'intval');
$sort	= I('sort', '12', 'intval');
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = I("k");
$key    = urldecode($key);
if($key){
if($this->FilterWords($key)){
$this->_404();
}

 //$this->pddquery($key);
 $this->assign('sokey', $key);
}


$stype    = I("stype");
$hash='false';
if($stype == 1){
	$hash = 'true';
	$this->assign('stype', $stype);
}
$data = $this->PddGoodsSearch($cid,'',$key,$sort,'',$size=20,$hash);

$this->assign('list',$data['goodslist']);


if($cid){
$cateinfo['name'] = $cats[$cid];
}
$seo = C('yh_seo_config.searchduoduo');
 if($key && $seo['title']){
$this->_config_seo($seo, array(
    'key' => $key,
));
	
}else if($cateinfo){
	 $this->_config_seo(array(
            'cate_name' => $cateinfo['name'],
             'title' => '拼多多'.$cateinfo['name'].'优惠券 - '. C('yh_site_name'),
            'keywords' => $cateinfo['name'],
            'description' => $cateinfo['name']
   ));
 }else{
 $this->_config_seo(C('yh_seo_config.pinduoduo'));
  }
 
$this->display();

}


public function catelist(){
$page	= I('p',0,'trim');
$cid		= I('cid',0 ,'intval');
$sort	= I('sort', '8', 'intval');
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = trimall(I("k",'','htmlspecialchars'));
$key    = urldecode($key);
$hash='false';
$stype    = I("stype");
if($stype == 1){
	$hash = 'true';
	$this->assign('stype', $stype);
}
$data = $this->PddGoodsSearch($cid,$page,$key,$sort,'',$size=20,$hash);


$this->assign('list',$data['goodslist']);
	
	
$this->display('catelist2');	
}








}