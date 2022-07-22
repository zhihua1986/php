<?php
namespace Home\Action;
class WxappAction extends BaseAction {
public function _initialize()
    {
        parent::_initialize();
		$this->params=I('param.');
	    $this->onlytime=I('time','0','number_int');
	   // $this->check_time($this->onlytime);
	    $this->accessKey = trim(C('yh_gongju'));
       $this->pre=C("DB_PREFIX");
    }
	
public function create_kouling($num_iid,$item=array()){
$apiurl=$this->tqkapi.'/gconvert';
$apidata=array(
'tqk_uid'=>$this->tqkuid,
'time'=>time(),
'good_id'=>''.$item['num_iid'].''
);
$token=$this->create_token(trim(C('yh_gongju')),$apidata);
$apidata['token']=$token;
$res= $this->_curl($apiurl,$apidata, false);
if($res){
$res = json_decode($res, true);
$me=$res['me'];

if(strlen($me)>5){
$quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.'&activityId='.$item['Quan_id'].'&itemId='.$item['num_iid'].'&pid='.trim(C('yh_taobao_pid')).'&af=1';
$kouling=kouling($item['pic_url'],$item['title'],$quanurl);
return $kouling;
}

}
	
}

public function articleinfo(){
$this->check_token($this->accessKey);
$id=I('id');	
$mod=M('article');
$info=$mod->field('title,info,add_time')->where('id='.$id)->find();
if($info){
$json=array(
'status'=>1,
'result'=>$info
);
}else{
$json=array(
	'status'=>'0',
	'msg'=>'没有数据'
	);	
}
	
exit(json_encode($json));		
}	
	
public function Itemdetail(){
$this->check_token($this->accessKey);
$id=I('id');	
$this->_mod=M('items');
if(is_numeric($id)){
$detail=$this->_mod->field('title,id,num_iid,Quan_id,cate_id,sellerId,pic_url,price,volume,coupon_price,shop_type,quan,quanurl,quankouling,Quan_id,up_time')->find($id);
if($detail){
if(empty($detail['quankouling'])){
$detail['quankouling']=$this->create_kouling($detail['num_iid'],$detail);
}
$orlike = $this->_mod->where(array(
                    'cate_id' => $detail['cate_id']
  ))
  ->field('title,num_iid,id,pic_url,price,volume,coupon_price,shop_type,quan')
                    ->limit('0,6')
                    ->order('is_commend desc,id desc')
                    ->select();
if($orlike){
$detail['orlike']=$orlike;
}
$json=array(
'status'=>1,
'qrcode'=>C('yh_site_url').C('yh_site_background'),
'result'=>$detail
);
}else{
$json=array(
	'status'=>'0',
	'msg'=>'没有数据'
	);	
}

}
exit(json_encode($json));	
}

	
public function Brandcate(){
//$this->check_token($this->accessKey);
$Pre=C("DB_PREFIX");
$mod=M('brandcate');
$catelist=$mod->cache(true, 30 * 60)->field('name,id')->where('status = 1')->order('ordid asc')->select();
if($catelist){
$sql='';
$si=0;
$k2=1;
$field='id,logo,brand,remark,cate_id';
foreach($catelist as $k=>$v){
	if($si==0){
         $sql='(SELECT '.$field.' from '.C("DB_PREFIX").'brand where cate_id='. $v['id'] .' and status=1 order by ordid asc)';
        }else{
            $sql=$sql.' union all (SELECT '.$field.' from '.C("DB_PREFIX").'brand where cate_id='. $v['id'] .' and status=1 order by ordid asc)';		
        }
        $si++;	
       $cate[$v['id']]['id']=$v['id'];
       $cate[$v['id']]['name']=$v['name'];
}

    $Model = M();
    $list=$Model->cache(true, 10 * 60)->query($sql);
	$cateid=0;
    $pi=0;
    foreach($list as $ik=>$p){
    	    $list[$ik]['logo']=C('yh_site_url').$p['logo'];
        $newsale[$p['cate_id']][$pi]['logo']=C('yh_site_url').$p['logo'];
        $newsale[$p['cate_id']][$pi]['brand']=$p['brand'];
        $newsale	[$p['cate_id']][$pi]['id']=$p['id'];
        $pi++;
    }
	
    foreach($catelist as $i=>$c){
        $product[$k2]['itemlist']=$newsale[$c['id']];
        $product[$k2]['name']=$cate[$c['id']]['name'];
        $product[$k2]['cid']=$k2;
		//$product[$k2]['cid']=$cate[$c['id']]['id'];
        $k2++;
    }
	
	
	$product['brandlist']=$list;
	
 
 }


if($product){
$json=array(
'status'=>1,
'result'=>$product
);
}else{
	$json=array(
	'status'=>0,
	'msg'=>'error'
	);
}
exit(json_encode($json));
}

public function Getitem(){
$this->check_token($this->accessKey);
$size=I('size','40','int');
$start=I('start','0','int');
$cateid=I('cid','0','int');
$ismore=I('ismore','','trim');
$sort=I('sort','new','trim');//排序
$model=I('model','','trim'); //模块
$common=I('common','','trim'); //模块
if($common==1){
$order = 'is_commend DESC,ordid asc';
}else{
$order = 'ordid asc';
}
$where=array(
      'pass'=>1,
      'isshow'=>1,
      'ems'=>1,
      'status'=>'underway'
);
switch ($model){
case 'jiu':
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
break;
case 'ershi':
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
break;
case 'top100':
$sort="hot";
break;
case 'xinpin':
$today_str = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$tomorr_str = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
$where['add_time'] = array(
            array(
                'egt',
                $today_str
            ),
            array(
                'elt',
                $tomorr_str
            )
 );	
 break;
case 'jingxuan':
$order.=', is_commend DESC,(quan/price)*100 DESC,ordid asc';
break;
default:
break;
}


if($cateid>0){
 $where['cate_id'] = $cateid;
}

$key    = I("k",'','htmlspecialchars');
$key    = urldecode($key);
if($key){
 $where['title|tags'] = array( 'like', '%' . $key . '%' );
}
switch ($sort){
    		case 'new':
				$order.= ', coupon_start_time DESC';
				break;
			case 'price':
				$order.= ', price ASC';
				break;
			case 'rate':
				$order.= ', coupon_rate ASC';
				break;
			case 'hot':
				$order.= ', volume DESC';
				break;
			case 'default':
				$order.= ', coupon_start_time DESC';
}
$mod=M('items');

$goodslist = $mod->where($where)->field('id,pic_url,title,coupon_price,price,quan,shop_type,volume,add_time,quankouling')->order($order)->limit($start . ',' . $size)->select();	

$count=count($goodslist);

$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$apppid=trim(C('yh_taobao_pid'));
$pid=I('pid','','trim');
$apppid=$pid?$pid:$apppid;
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[3];
$haoquan=true; // 初始化

if(!empty($appkey) && !empty($appsecret) && $key && $count<10 && $ismore!='ismore' && !empty($AdzoneId)){
vendor("taobao.taobao");
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
$req->setHasCoupon("true");
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$resp=$resp['result_list']['map_data'];	
//$item=array();
$patterns = "/\d+/";
foreach($resp as $k=>$v){
//preg_match_all($patterns,$v['coupon_info'],$arr);
//$quan=$arr[0];
//$coupon_price=$item_1['zk_final_price']-$quan;
$goodslist[$k+$count]['quan']=$v['coupon_amount'];
//$goodslist[$k+$count]['coupon_click_url']=$v['coupon_click_url'];
$goodslist[$k+$count]['coupon_click_url']=true;
$goodslist[$k+$count]['num_iid']=$v['item_id'];
$goodslist[$k+$count]['title']=$v['title'];
$goodslist[$k+$count]['coupon_price']=$v['zk_final_price']-$goodslist[$k+$count]['quan'];
if($v['user_type']=="1"){
$goodslist[$k+$count]['shop_type']='B';	
}else{
$goodslist[$k+$count]['shop_type']='C';	
}
$goodslist[$k+$count]['price']=$v['zk_final_price'];
$goodslist[$k+$count]['volume']=$v['volume'];
$goodslist[$k+$count]['pic_url']=$v['pict_url'];
}


$haoquan=false; 
	
}


if($goodslist){
$json=array(
'status'=>1,
'result'=>$goodslist,
'haoquan'=>$haoquan,
);	
}else{
$json=array(
'status'=>0,
'msg'=>'没有数据了'
);		
}

exit(json_encode($json));
	
}


	
 Public function index(){
  $this->check_token($this->accessKey); 
  $adlist=M('ad')->field('id,ordid,add_time',true)->where('status = 5 and add_time = 0')->order('id desc')->select();
 foreach($adlist as $ak=>$av){
$adlist[$ak]['img']	=C('yh_site_url').$av['img'];
 }
  $where['status']=1;
  $brandlist=M('brand')->field('id,logo,brand,remark')->where($where)->order('ordid asc')->limit(0,3)->select();	
  $where=array(
      'pass'=>1,
      'isshow'=>1,
      'ems'=>1,
      'status'=>'underway'
  );
  
  foreach($brandlist as $k=>$v){
  	$brandlist[$k]['logo']=C('yh_site_url').$v['logo'];
  }
  
  $itemslist = M('items')->where($where)->field('id,pic_url,title,coupon_price,price,quan,shop_type,volume,add_time')->order('id desc')->limit(0,40)->select();	
  $catelist=M('itemscate')->field('name,id')->where('status = 1')->order('ordid asc')->select();
 
 if($adlist || $brandlist || $itemslist || $catelist){
	
	$list=array(
   'adlist'=>$adlist,
   'brandlist'=>$brandlist,
   'itemslist'=>$itemslist,
   'catelist'=>$catelist,
   'qrcode'=>C('yh_site_url').C('yh_site_background'),
   );
   
   $json=array(
	'status'=>1,
	'appname'=>C('yh_site_name'),
	'result'=>$list
	);
	
}else{
	
$json=array(
	'status'=>0,
	'msg'=>'error'
	);
	
}
	
exit(json_encode($json));
}
	


		
}
	