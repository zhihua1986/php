<?php
namespace M\Action;
use Common\Model;
class PdditemAction extends BaseAction{
	public function _initialize() {
		parent::_initialize();
		/*
		当前duoId被禁止调用商品详情。封禁原因：系统检测该duoId商品详情请求查询非多多进宝商品占比过高，涉嫌爬虫或本地缓存商品信息。
		拼多多商品禁止搜索引擎访问，防止出现接口被封
		*/
		if ($this->getRobot()!==false) {
		exit;
		}
		$this->_mod = D('pdditems');
		
	}
public function index(){
  $id = I('s');
 $result = $this->PddGoodsDetail($id);
 !$result && $this->_404();
		$this->assign('item', $result);
		$this->_config_seo(C('yh_seo_config.pdditem'), array(
			'title' => $result['goods_name'],
			'intro' => $result['goods_desc'],
			'price' => $result['min_group_price'],
			'quan' => floattostr($result['coupon_discount']),
			'coupon_price' => $result['min_group_price']-$result['coupon_discount'],
			'tags' => implode(',',$this->GetTags($result['goods_name'],4)),
			'title' => $result['goods_name'],
			'keywords' => '',
			'description' => $result['goods_desc'],
			));

       $this->assign('mdomain',str_replace('/index.php/m','',C('yh_headerm_html')));
		
	$orlike = M('items')->field('id,pic_url,num_iid,commission_rate,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->limit('0,12')
	      ->order('id desc')
	      ->select();
	      $this->assign('orlike', $orlike);
		
		$buyer=$this->buyer(5);
		$this->assign('buyer', $buyer);
		$this->display();

	}
	
public function wxjump(){
$data = str_replace(' ','+',I('r'));
$val = explode("|",base64_decode($data));
$id = $val[6];
$goods_sign = I('goods_sign');
if($goods_sign){
$result = $this->PddGoodsDetail($goods_sign);
}else{
$result = $this->PddGoodsDetail($id,false);
}

$result['goods_thumbnail_url']=str_replace("http://","https://",$result['goods_thumbnail_url']);
$Tag= $this->GetTags($result['goods_name'],4);
$this->assign('tag', $Tag);
$this->assign('item', $result);
$orlike = M('items')->field('id,pic_url,num_iid,volume,commission_rate,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->limit('0,12')
	      ->order('id desc')
	      ->select();
	      $this->assign('orlike', $orlike);
$this->assign('mdomain',str_replace('/index.php/m','',C('yh_headerm_html')));
$this->_config_seo(C('yh_seo_config.item'), array(
			'title' => $result['goods_name'],
			'intro' => $result['goods_desc'],
			'price' => $result['min_group_price']/100,
			'quan' => floattostr($result['coupon_discount']/100),
			'coupon_price' => $result['min_group_price']/100-$result['coupon_discount']/100,
			'tags' => implode(',',$Tag),
			'title' => $result['goods_name'],
			'keywords' => '',
			'description' => $result['goods_desc'],
			));

$this->display();

	
}
	
	
	
public function jump(){
$result = $this->PddGoodsDetail(I('id'));
        !$result && $this->_404();
$Tag= $this->GetTags($result['goods_name'],4);
$this->assign('tag', $Tag);
$this->assign('item', $result);
$orlike = M('items')->field('id,pic_url,num_iid,volume,commission_rate,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->limit('0,12')
	      ->order('id desc')
	      ->select();
	      $this->assign('orlike', $orlike);
$this->assign('mdomain',str_replace('/index.php/m','',C('yh_headerm_html')));
$this->_config_seo(C('yh_seo_config.pdditem'), array(
			'title' => $result['goods_name'],
			'intro' => $result['goods_desc'],
			'price' => $result['min_group_price'],
			'quan' => floattostr($result['coupon_discount']),
			'coupon_price' => $result['min_group_price']-$result['coupon_discount'],
			'tags' => implode(',',$Tag),
			'title' => $result['goods_name'],
			'keywords' => '',
			'description' => $result['goods_desc'],
			));

$this->display();
	
	
}	

public function jumpclick(){
$params=I('param.');
$skuId=$params['goods_id'];
if(is_numeric($skuId) && $skuId>0){
$cach_name='jump_pdd_'.$params['goods_id'];
$value = S($cach_name);
if(false === $value){
S($cach_name,$params,86400);
}
$json=array(
'status'=>1,
'urls'=>U('pdditem/jump/'.$skuId)
);

exit(json_encode($json));
}

	
}

	
	
	
	protected function delitem($id){
	$where=array(
	'goods_id'=>$id,
	);
	$this->_mod->where($where)->delete();
	}
	

public function jumpout(){
	
 $id=I('id');
 $t = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
 $data = $this->pddPromotionUrl($id,'',$t);
  if(!$data['url']){
	$data = $this->pddPromotionUrl($id,'',$t,'true');
 }
 if($data['url']){
	 
	//if($t && $t!==null){
	//	$data['url'] = urldecode($data['url']);
	//	$data['url'] = str_replace('&goods_sign','&customParameters='.$t.'&goods_sign',$data['url']);
	//}
    echo('<script>window.location.href="'.$data['url'].'"</script>');	 
	 exit;
 }
 echo('<script>alert("获取领券链接失败，请稍后再试！");history.go(-1)</script>');
	
}
	


	
}

function floattostr( $val ){
	preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o );
	return $o[1].sprintf('%d',$o[2]).($o[3]!='.'?$o[3]:'');
}	