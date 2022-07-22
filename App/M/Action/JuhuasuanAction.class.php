<?php
namespace M\Action;
use Common\Model;
use Think\Page;
class JuhuasuanAction extends BaseAction{
	public function _initialize() {
        parent::_initialize();
    }
    
    
public function index(){
$useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT'])); 
if((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1){ 
$this->assign('isweixin',true);
}		
	
$page	= 0;
$size	= 20;
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[3];
if(!empty($appkey) && !empty($appsecret) && !empty($AdzoneId)){
vendor('taobao.taobao');
$c = new \TopClient();
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$c->format = 'json';
$req = new \TbkDgMaterialOptionalRequest();
$req->setPageSize("100");
$req->setPageNo($page);
$req->setSort("total_sales_des");
$req->setCat("16,18");
$req->setPlatform("2");
$req->setAdzoneId($AdzoneId);
$req->setMaterialId('4092');
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$resp=$resp['result_list']['map_data'];	
$patterns = "/\d+/";
$count=count($resp);
if($count%2==0){
$count=$count;	
}else{
$count=$count-1;		
}

foreach($resp as $k=>$v){
if($k>=$count) break;
$quan = $v['coupon_amount'];
$goodslist[$k+$count]['coupon_price']=$v['zk_final_price']-$quan;
$goodslist[$k+$count]['volume']=$v['volume'];
$goodslist[$k+$count]['zk_final_price']=$v['zk_final_price'];
$goodslist[$k+$count]['quan']=$quan;
$goodslist[$k+$count]['coupon_click_url']=$v['coupon_share_url'];
$goodslist[$k+$count]['num_iid']=$v['item_id'];
$goodslist[$k+$count]['title']=$v['title'];
if($v['user_type']=="1"){
$goodslist[$k+$count]['shop_type']='B';	
}else{
$goodslist[$k+$count]['shop_type']='C';	
}
$goodslist[$k+$count]['commission_rate']=$v['commission_rate']; //比例
$goodslist[$k+$count]['volume']=$v['volume'];
$goodslist[$k+$count]['pic_url']=$v['white_image']?$v['white_image']:$v['pict_url'];
}
 
}

$this->assign('list',$goodslist);	
 
 $this->_config_seo(array(
            'title' => '好货精选-淘宝受欢迎的品质好货 - '. C('yh_site_name'),
   ));
 
$this->display();


}


public function catelist(){
$page	= I('p',0,'number_int');
$size	= 20;
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[3];
if(!empty($appkey) && !empty($appsecret) && !empty($AdzoneId)){
vendor('taobao.taobao');
$c = new \TopClient();
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$c->format = 'json';
$req = new \TbkDgMaterialOptionalRequest();
$req->setPageSize("40");
$req->setPageNo($page+1);
$req->setSort("total_sales_des");
$req->setCat("16,18");
$req->setPlatform("2");
$req->setAdzoneId($AdzoneId);
$req->setMaterialId('4092');
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$resp=$resp['result_list']['map_data'];	
$patterns = "/\d+/";
$count=count($resp);
if($count%2==0){
$count=$count;	
}else{
$count=$count-1;		
}
foreach($resp as $k=>$v){
if($k>=$count) break;
$quan = $v['coupon_amount'];
$goodslist[$k+$count]['coupon_price']=$v['zk_final_price']-$quan;
$goodslist[$k+$count]['volume']=$v['volume'];
$goodslist[$k+$count]['zk_final_price']=$v['zk_final_price'];
$goodslist[$k+$count]['quan']=$quan;
$goodslist[$k+$count]['coupon_click_url']=$v['coupon_share_url'];
$goodslist[$k+$count]['num_iid']=$v['item_id'];
$goodslist[$k+$count]['title']=$v['title'];
if($v['user_type']=="1"){
$goodslist[$k+$count]['shop_type']='B';	
}else{
$goodslist[$k+$count]['shop_type']='C';	
}
$goodslist[$k+$count]['commission_rate']=$v['commission_rate']; //比例
$goodslist[$k+$count]['volume']=$v['volume'];
$goodslist[$k+$count]['pic_url']=$v['white_image']?$v['white_image']:$v['pict_url'];
}
 
}

$this->assign('list',$goodslist);	
	
$this->display('catelist');	
	
}





}