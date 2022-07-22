<?php
namespace M\Action;
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
class ZhiboAction extends BaseAction {
	
public function _initialize()
{
parent::_initialize();

// if($this->visitor->is_login == false){
//      	   $url=U('login/index');
//         redirect($url);
// }
 
$this->_userdomain=str_replace('/index.php/m','',C('yh_headerm_html'));
$this->_userappkey=trim(C('yh_gongju'));
$this->_pcdomain=C('yh_site_url');
$this->_avatar=C('yh_site_zhibo');

}


public function index(){
	
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/runtime/Data/zhibo/disable_num_iids.php';
$ret=opcache_invalidate($dir,TRUE);
}
$disable_num_iids = F('zhibo/disable_num_iids');

if(count($disable_num_iids)>40){
F('zhibo/disable_num_iids', NULL);
}



if(!$disable_num_iids){
$disable_num_iids = array();
}


if($disable_num_iids){

$this->assign('list',$disable_num_iids);
$this->assign('mdomain',$this->_userdomain);

}
$this->assign('domain',$this->_pcdomain);
$this->assign('openid',$this->_openid);
$this->assign('uid',$this->_uid);
$this->assign('keyuid',md5(md5($this->_userappkey)));
if($this->_avatar){
$this->assign('avatar',$this->_avatar);	
}else{
$this->assign('avatar','/Public/static/images/default_photo.gif');	
}

$this->display();
}

public function zhibo_push(){
$num = I('num', 1);
$file = TQK_DATA_PATH.'push.txt';
if(!file_exists($file)){
            return false;
}
 $startId = file_get_contents($file);
 $model=C('yh_zhibo_model');
 if($model==0){
 
 if(!$startId){
            $startId = 0;
 }
 
$mod=M('items');
$shop_type=C('yh_zhibo_shop_type');
if($shop_type!=0){
$where['shop_type']=$shop_type;	
}
$mix_price=C('yh_zhibo_mix_price');
$max_price=C('yh_zhibo_max_price');
$mix_volume=C('yh_zhibo_mix_volume');
if($mix_price>0){
$where['coupon_price']=array('egt',$mix_price);	
}
if($mix_volume>0){
$where['volume']=array('egt',$mix_volume);	
}
if($max_price>0){
$where['coupon_price']=array('elt',$max_price);	
}

 if ($mix_price > 0 && $max_price > 0) {
            $where['coupon_price'] = array(
                array(
                    'egt',
                    $mix_price
                ),
                array(
                    'elt',
                    $max_price
                ),
                'and'
            );
        }
$where['id']=array('gt',$startId);
$where['quan']=array('gt',20);
$list=$mod->where($where)->field('num_iid,add_time,title,pic_url,id,price,coupon_price,quan')->limit(1)->select();
$count=count($list);
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/runtime/Data/zhibo/disable_num_iids.php';
$ret=opcache_invalidate($dir,TRUE);
}
$disable_num_iids = F('zhibo/disable_num_iids');
if($count>0){
            foreach ($list as $key => $val) {
             $raw[] = array(
			 'num_iid'=>$val['num_iid'],
			 'price'=>$val['price'],
			 'coupon_price'=>$val['coupon_price'],
			 'coupon'=>$val['quan'],
			 'title'=>$val['title'],
			 'push_time'=>date('m-d H:i:s',time()),
			  'id'=>$val['id'],
			  'pic_url'=>$val['pic_url']
			 ); 	
			}
		 $startId = $val['id'];
	     file_put_contents($file, $startId);
if(!$disable_num_iids){
    $disable_num_iids = array();
 }
$is=strpos(serialize($disable_num_iids),$val['num_iid']);
if(empty($is)){
$disable_num_iids[] =array(
                    //'num_iid'=>$item['num_iid'],
                    'title'=>$val['title'],
                    'id'=>$val['id'],
                    'price'=>$val['price'],
                    'coupon_price'=>$val['coupon_price'],
                    'coupon'=>$val['quan'],
                    'push_time'=>date('m-d H:i:s',time()),
                    'pic_url'=>$val['pic_url']
					);

if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/runtime/Data/zhibo/disable_num_iids.php';
$ret=opcache_invalidate($dir,TRUE);
}
F('zhibo/disable_num_iids', $disable_num_iids);
}	 
		 
		 
			$json = array(
                'total'=>$count,
                'result'=>$raw,
                'state'=>'yes',
                'msg'=>'成功获取数据'
            );
		 
  }else{
  	
	$json = array(
                'data'=>'0',
                'state'=>'no',
                'msg'=>'暂时没有数据'
            );
	
  }
	

 }else{
 $json = array(
                'state'=>'no',
                'data'=>'0',
                'msg'=>'当前开启了手动模式'
 );		

 }
 
 exit(json_encode($json));	
 
 
 	
}



}




?>