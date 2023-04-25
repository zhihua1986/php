<?php
namespace Home\Action;
use Common\Model\userModel;
use Common\Model\adModel;
use Common\Model\itemscateModel;
use Common\Model\itemsModel;
use Common\Model\usercashModel;
use Common\Model\articleModel;
use Common\Model\helpModel;
class TqkapiAction extends BaseAction {
private $accessKey = '';
public function _initialize()
    {
        parent::_initialize();
     
  $info= htmlspecialchars_decode(I('data'));
  $info = json_decode($info,true);    
  if($info){
  $this->params=$info;
  }else{
  	$this->params=I('param.');
  }    
  
	    $this->onlytime=I('time','0','number_int');
	    $this->accessKey = trim(C('yh_gongju'));
        $this->pre=C("DB_PREFIX");
        $this->check_token($this->accessKey);

 }
 
public function subcates(){
$ali_id = $this->params['cid'];

$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[3];
if(($key || $ali_id) && !empty($appkey) && !empty($appsecret) && !empty($AdzoneId)){
vendor('taobao.taobao');
$c = new \TopClient();
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$c->format = 'json';
$req = new \TbkDgMaterialOptionalRequest();
$req->setAdzoneId($AdzoneId);
$req->setPlatform("1");
$req->setPageSize("20");
if($ali_id){
$req->setCat($ali_id);
}
if($key){
$req->setQ((string)$key);
}
if($page>0){
$req->setPageNo($page);
}else{
$req->setPageNo(1);	
}

$req->setHasCoupon('true');
$req->setIncludePayRate30("true");
if($sort=='hot'){
$req->setSort("total_sales_des");	
}elseif($sort=='price'){
$req->setSort("price_asc");
}else if($sort=='rate'){
$req->setSort("tk_rate_des");	
}else{
$req->setSort("tk_des");	
}
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$resp=$resp['result_list']['map_data'];	
$patterns = "/\d+/";
foreach($resp as $k=>$v){
preg_match_all($patterns,$v['coupon_info'],$arr);
$quan=$arr[0];

echo('<img src="'.$v['pict_url'].'"/><br/>'.$v['title'].'<br/>');

}
 
}

	
}
 

 
public function findpwd(){
            $code = $this->params['code'];
            $phone = $this->params['username'];
            $password = $this->params['password'];
            if(S($phone) != $code){
            	$this->Exitjson(400,'验证码输入不正确');
            }
            $mo = new userModel();
            $where = array(
                'phone'=>$phone
            );
            $res = $mo->where($where)->setField('password', md5($password));
            if($res !== false){
            		$this->Exitjson(200,'密码修改成功');
            }
            	$this->Exitjson(400,'操作失败');
	
}
 
private function Exitjson($code,$msg,$result=''){
$json=array(
'code'=>$code,
'msg'=>$msg,
'result'=>$result,
'stime'=>NOW_TIME
);
exit(json_encode($json,JSON_UNESCAPED_UNICODE));
}
 
public function readarticle(){
	
$id = $this->params['id'];
$mod = new articleModel();
$result = $mod->field('id,title,info,author,seo_title,seo_keys,seo_desc,add_time,cate_id')->find($id);
if($result){
$result['add_time'] = frienddate($result['add_time']);
$this->Exitjson(200,'成功',$result);	
}
$this->Exitjson(400,'失败');
	
}
 
 
public function register(){
$verify = $this->params['code'];
$phone = $this->params['username'];
$invicode = $this->params['invicode'];
$password = $this->params['password'];
if(!is_numeric($this->params['username'])){
	$this->Exitjson(400,'手机号格式非法');
}
if(0 < C('yh_sms_status') && S($phone) != $verify){
  $this->Exitjson(400,'手机验证码输入不正确');
}  
if($invicode){
        	 $where=array(
		'invocode' => $invicode
        	);
$mod=new userModel();
$exist = $mod->field('id,fuid,guid')->where($where)->find();
        	if($exist){
        	  	$data['fuid']=$exist['id'];
        	  	$data['guid']=$exist['fuid']?$exist['fuid']:0;
        	}else{
        	$this->Exitjson(400,'邀请码不存在');
        	}
}

$res = $this->visitor->register($username, $phone, $email, $password, $data);
 if($res){
   	$this->Exitjson(200,'注册成功');
     
}
 
 $this->Exitjson(400,$this->visitor->error);
	
}
 
 
public function runcode(){
            $phone = $this->params['username'];
            $ac=$this->params['ac'];
            $mod=new userModel();
            $res=$mod->where(array('phone'=>$phone))->find();
            if('reg' == $ac && $res){
              $this->Exitjson(400,'此手机号已经被占用');
            }
             if('findpass' == $ac && !$res){
              $this->Exitjson(400,'此手机号不存在');
            }
           if($ac=='reg'){
           	 $tempid=trim(C('yh_sms_reg_id'));
           	 }else{
           	 	 $tempid=trim(C('yh_sms_fwd_id'));
           	}
            $code = rand(100000, 999999);
            $data=array(
            'phone'=>$phone,
            'code'=>$code,
           // 'webname'=>trim(C('yh_site_name')),
            'temp_id'=>$tempid
            );
           $res= $this->send_sms($data);
           if($res){
           	    S($phone,$code);
			    $this->Exitjson(200,'验证码发送成功');
            }
           $this->Exitjson(400,'验证码发送失败');

}


public function createkouling(){
$kouling=kouling($this->params['img'],$this->params['title'],urldecode($this->params['url']));
if($kouling){
$this->Exitjson(200,'成功',$kouling);	
}
$this->Exitjson(400,'程序异常');	
}

public function convert(){
$apiurl=$this->tqkapi.'/gconvert';
$apidata=array(
'tqk_uid'=>$this->tqkuid,
'time'=>time(),
'good_id'=>''.$this->params['num_iid'].''
);
$token=$this->create_token(trim(C('yh_gongju')),$apidata);
$apidata['token']=$token;
$res= $this->_curl($apiurl,$apidata, false);
$res = json_decode($res, true);
$me=$res['me'];
if(strlen($me)>5 && $this->params['quanid']){
$quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.'&activityId='.$this->params['quanid'].'&pid='.trim(C('yh_taobao_pid')).'&af=1';
$this->Exitjson(200,'成功',$quanurl);

}elseif(strlen($me)>5){
$quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.'&pid='.trim(C('yh_taobao_pid')).'&af=1';	
$this->Exitjson(200,'成功',$quanurl);
}

$this->Exitjson(400,'程序异常');	
	
}

public function item(){
$id=$this->params['id'];
$cach_name='Detail_'.$id;
$sinfo = S($cach_name);
if($sinfo){
$this->Exitjson(200,'缓存数据',$sinfo);
}else{
$info = $this->taobaodetail($id);
if($info){
S($cach_name,$info,86400);
$this->Exitjson(200,'Api数据',$info);
}
$this->Exitjson(400,'程序异常');
}


	
}
 
public function index(){
$mod = new itemscateModel();	
$mod = $mod ->cache(true, 10 * 60);
$catelist=$mod->field('name,id')->where('status = 1 and pid = 0')->order('ordid desc')->select();	
$admod = new adModel();
$admod = $admod ->cache(true, 10 * 60);
$adlist=$admod->field('url,img,status')->where('(status = 5 or status = 6) and add_time=0')->order('ordid desc')->select();	
foreach($adlist as $k=>$v){
if($v['status'] == 5){
$ad[$k]['url']=$v['url'];
$ad[$k]['img']=C('yh_site_url').$v['img'];	
}else{
$adhome[$k]['url']=$v['url'];
$adhome[$k]['img']=C('yh_site_url').$v['img'];	
}
}
$articlemod = new articleModel();
$articlemod = $articlemod ->cache(true, 10 * 60);
$articlelist=$articlemod->where('status=1')->order('ordid desc')->field('title,id')->limit(4)->select();	
$ItemsMod = new itemsModel();
$ItemsMod = $ItemsMod ->cache(true, 10 * 60);
$HotProduct = $ItemsMod ->field('id,num_iid,pic_url,cate_id,ali_id,title,coupon_price,commission_rate,price,quan,shop_type,volume,quanurl,Quan_id,sellerId,quankouling')
->where('coupon_price<20 and volume>10000')
->order('id desc')
->limit(8)
->select();

  $sqlwhere['pass'] = '1';
  $sqlwhere['isshow'] = '1';	
  $sqlwhere['ems'] = '1';
  $sqlwhere['status'] = 'underway';
  if(C('yh_index_shop_type')){
  $sqlwhere['shop_type']=C('yh_index_shop_type');
  }
  $sqlwhere['quan']=array(array(
  'egt',
  C('yh_index_mix_price')
  ),
array(
  'elt',
 C('yh_index_max_price')
  ));
 $sqlwhere['volume']=array(array(
  'egt',
  C('yh_index_mix_volume')
  ),
array(
  'elt',
 C('yh_index_max_volume')
  ));

$items_list = $ItemsMod->where($sqlwhere)->field('id,cate_id,ali_id,num_iid,pic_url,title,coupon_price,commission_rate,price,quan,shop_type,volume,quanurl,Quan_id,sellerId,quankouling')->order(C('yh_index_sort'))->limit(trim(C('yh_index_page_size')))->select();	
if($items_list){
$goodslist=array();
foreach($items_list as $k=>$v){
$goodslist[$k]['id']=$v['id'];
$goodslist[$k]['num_iid']=$v['num_iid'];
$goodslist[$k]['pic_url']=$v['pic_url'];
$goodslist[$k]['title']=$v['title'];
$goodslist[$k]['commission_rate']=$v['commission_rate'];
$goodslist[$k]['rebate']=Rebate1($v['coupon_price']*$v['commission_rate']/10000);
$goodslist[$k]['coupon_price']=round($v['coupon_price'],2);
$goodslist[$k]['price']=round($v['price'],2);
$goodslist[$k]['quan']=intval($v['quan']);
$goodslist[$k]['shop_type']=$v['shop_type'];
$goodslist[$k]['volume']=$v['volume'];	
$goodslist[$k]['quanurl']=$v['quanurl'];	
$goodslist[$k]['Quan_id']=$v['Quan_id'];	
$goodslist[$k]['sellerId']=$v['sellerId'];
$goodslist[$k]['cate_id']=$v['cate_id'];	
$goodslist[$k]['ali_id']=$v['ali_id'];		
$goodslist[$k]['quankouling']=$v['quankouling']?$v['quankouling']:'';	
	
}
}

$Taolijin = $ItemsMod->where('ems=2')->field('id,cate_id,ali_id,num_iid,pic_url,title,coupon_price,commission_rate,price,quan,shop_type,volume,quanurl,Quan_id,sellerId,quankouling')->order('ordid desc')->limit(5)->select();

 if($adlist || $brandlist || $itemslist || $catelist || $articlelist){
	
	$list=array(
   'adlist'=>$ad,
   'adhome'=>$adhome,
   'adcount' =>count($ad),
   'hotproduct' =>$HotProduct,
   'itemslist' => $goodslist,
   'catelist'=>$catelist,
   'taolijin'=>$Taolijin,
   'articlelist'=>$articlelist,
   'seo'=>C('yh_seo_config.ershi'),
   //'webconf'=>$this->getconfig()
   );
   
   $json=array(
	'code'=>200,
	'appstatus'=>C('yh_site_secret'),
	'webname'=>C('yh_site_name'),
	'result'=>$list
	);
	
	
	
}else{

$json=array(
	'code'=>400,
	'msg'=>'error'
	);
	
}

exit(json_encode($json,JSON_UNESCAPED_UNICODE));	


}

public function teamprofit(){
$openid = $this->params['openid'];
$memberinfo = $this->getuid($openid);
$uid=$memberinfo['id'];
$fuid=$memberinfo['fuid'];
$guid=$memberinfo['guid'];
$today_str = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$tomorr_str = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$pre_time = strtotime(date('Y-m-01', strtotime('last day of -1 month')));
$last_str = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
$pre_month=$this->getthemonth($pre_time);
$field = 'id,
( select SUM(money) from tqk_usercash where uid = '.$uid.' and type=3 and create_time>'.$pre_month[1].') as allmoney,
( select SUM(income*leve1/100) from tqk_order where nstatus=1 and (status =1 or status =3) and uid = '.$uid.' and add_time>'.$today_str.') as u_today_money,
( select SUM(income*leve2/100) from tqk_order where nstatus=1 and (status =1 or status =3) and fuid = '.$uid.' and add_time>'.$today_str.') as f_today_money,
( select SUM(income*leve3/100) from tqk_order where nstatus=1 and (status =1 or status =3) and guid = '.$uid.' and add_time>'.$today_str.') as g_today_money,
( select SUM(income*leve1/100) from tqk_order where nstatus=1 and (status =1 or status =3) and uid = '.$uid.' and  add_time>'.$pre_month[1].') as u_month,
( select SUM(income*leve2/100) from tqk_order where nstatus=1 and (status =1 or status =3) and fuid = '.$uid.' and add_time>'.$pre_month[1].') as f_month,
( select SUM(income*leve3/100) from tqk_order where nstatus=1 and (status =1 or status =3) and guid = '.$uid.' and add_time>'.$pre_month[1].') as g_month,
( select count(id) from tqk_order where nstatus=1 and (status =1 or status =3) and (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and add_time>'.$today_str.') as today_count,
( select count(id) from tqk_order where nstatus=1 and (status =1 or status =3) and (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and add_time>'.$pre_month[1].' ) as this_month_count,
( select count(id) from tqk_order where nstatus=1 and (status =1 or status =3) and (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (add_time>'.$pre_month[0].' and add_time<'.$pre_month[1].') ) as last_month_count';
$res = M('user')->field($field)->find();
if($res){
$res['yesterday']=round($res['u_today_money']+$res['f_today_money']+$res['g_today_money'],2);
$res['thismonth']=round($res['u_month']+$res['f_month']+$res['g_month'],2);
$res['today_count']=$res['today_count'];
$res['this_month_count']=$res['this_month_count'];
$res['last_month_count']=$res['last_month_count'];

$this->Exitjson(200,'成功',$res);
}


$this->Exitjson(400,'失败');


	
}

public function myteam(){
$usermod=new userModel();
$openid = $this->params['openid'];
$memberinfo = $this->getuid($openid);
$uid=$memberinfo['id'];
$today_str = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$last_str = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
        $field = 'id,
        ( select count(id) from tqk_user where fuid = '.$uid.' or guid = '.$uid.') as allperson,
        ( select count(id) from tqk_user where (fuid = '.$uid.' or guid = '.$uid.') and reg_time>'.$today_str.' ) as todayperson,
        ( select count(id) from tqk_user where (fuid = '.$uid.' or guid = '.$uid.') and (reg_time>'.$last_str.' and reg_time<'.$today_str.') ) as lastperson,
        ( select count(id) from tqk_user where fuid = '.$uid.') as person1,
        ( select count(id) from tqk_user where guid = '.$uid.') as person2';
        $res = $usermod->field($field)->find();
if($res){
$this->Exitjson(200,'成功',$res);
}
$this->Exitjson(400,'失败');
	
}

public function teamlist(){
$openid = $this->params['openid'];
$page = $this->params['p'];	
$page_size = 10;
$start = $page_size * $page;
$userinfo = $this->getuid($openid);
$ac = $this->params['ac'];

if($ac == 'guid'){
$stay['guid'] = $userinfo['id'];	
}else{
$stay['fuid'] = $userinfo['id'];
}
$usermod = new userModel();
$rows = $usermod->field('reg_time,nickname,oid,avatar')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
$list = array();
foreach($rows as $k=>$v){
$list[$k]['reg_time']=date('Y-m-d H:i',$v['reg_time']);
$list[$k]['nickname']=$v['nickname'];
$list[$k]['oid']=$v['oid'];
if(substr($v['avatar'],0,4)!='http'){
$list[$k]['avatar']=trim(C('yh_site_url')).$v['avatar'];
}else{
$list[$k]['avatar']=$v['avatar'];	
}

}

if($list){
$this->Exitjson(200,'成功',$list);
}
$this->Exitjson(400,'失败');

	
}

public function record() {
$openid = $this->params['openid'];
$userinfo = $this->getuid($openid);
$where = array('id'=>$userinfo['id']);	
$mod = new userModel();
$user = $mod->where($where)->field('money,frozen')->find();
if($user){
$this->Exitjson(200,'成功',$user);
}
$this->Exitjson(400,'失败');

	
}

public function recordlist() {
$openid = $this->params['openid'];
$userinfo = $this->getuid($openid);
$page = $this->params['p'];	
$page_size = 20;
$start = $page_size * $page;
$map['uid'] = $userinfo['id'];
$rows = M('balance')->where($map)->field('money,create_time,status')->limit($start . ',' . $page_size)->order('id desc')->select();
$list=array();
foreach($rows as $k=>$v){

$list[$k]['create_time']=frienddate($v['create_time']);
if($v['status'] == 0){
$list[$k]['status']='处理中';
}else{
$list[$k]['status']='已处理';	
}
$list[$k]['money']=$v['money'];
}

if($list){
$this->Exitjson(200,'成功',$list);
}
$this->Exitjson(400,'失败');

	
}


public function journal(){
$openid = $this->params['openid'];
$page = $this->params['p'];	
$page_size = 10;
$start = $page_size * $page;
$userinfo = $this->getuid($openid);
$where = array('id'=>$userinfo['id']);		
$mod = new userModel();
$user = $mod->where($where)->field('money,id')->find();
$where=array(
	'type'=>6,
	'uid'=>$user['id']
);
$balance = M('usercash')->where($where)->sum('money');
$result = array(
'balance'=>$balance,
'money'=>round($user['money'],2)
);
if($result){
$this->Exitjson(200,'成功',$result);
}
$this->Exitjson(400,'失败');

	
}

public function integral(){
$openid = $this->params['openid'];
$page = $this->params['p'];	
$page_size = 20;
$start = $page_size * $page;
$userinfo = $this->getuid($openid);
$stay['uid'] =$userinfo['id'];
$mod=M('basklistlogo');
$rows = $mod->field('create_time,integray,remark')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
$list=array();
foreach($rows as $k=>$v){
$list[$k]['create_time']=frienddate($v['create_time']);
$list[$k]['remark']=$v['remark'];	
$list[$k]['integray']=$v['integray'];
}

if($list){
$this->Exitjson(200,'成功',$list);
}
$this->Exitjson(400,'失败');

	
}


public function journallist(){
$openid = $this->params['openid'];
$page = $this->params['p'];	
$page_size = 20;
$start = $page_size * $page;
$userinfo = $this->getuid($openid);
$mod = M('usercash');
$stay['uid'] = $userinfo['id'];
$rows =$mod->where($stay)->field('type,money,create_time,status')->order('id desc')->limit($start . ',' . $page_size)->select();
$list=array();
foreach($rows as $k=>$v){
$val=unserialize(user_cash_type($v['type']));
$list[$k]['create_time']=frienddate($v['create_time']);
$list[$k]['type']=$val[0];
$list[$k]['money']=$val[1].round($v['money'],2);
}

if($list){
$this->Exitjson(200,'成功',$list);
}
$this->Exitjson(400,'失败');

	
}


public function bindorder(){
$fromdata = $this->params['fromdata'];
$fromdata = json_decode($fromdata,true);
$orderid = $fromdata['orderid'];
$openid = $this->params['openid'];
$UID = $this->getuid($openid);	
$mod = new userModel();
if(is_numeric($orderid)){
$map=array(
'id'=>$UID['id'],
);
$oid=md5(substr($orderid,-6,6));
$data=array(
'oid'=>$oid
);
$isset=$mod->field('id')->where($data)->find();
if(!$isset){
$res=$mod->where($map)->save($data);
if($res){
$this->Exitjson(200,'认领成功!');
}else{
$this->Exitjson(400,'认领失败!');
}

}else{
$this->Exitjson(400,'此订单已经被其它账号绑定过！!');
}

}else{
$this->Exitjson(400,'提交的订单参数不符合要求');	
}


}



public function exchange(){
$fromdata = $this->params['fromdata'];
$fromdata = json_decode($fromdata,true);
$count = abs($fromdata['count']);
			if ($count<=0) {
				$this->Exitjson(400,'兑换数量与实际数量不符');
			}
$openid = $this->params['openid'];
$userinfo = $this->getuid($openid);	
$usermod = new userModel();
$user=$usermod->where('id='.$userinfo['id'])->field('id,score,money')->find();

			if ($count>$user['score']) {
				$this->Exitjson(400,'兑换数量与实际数量不符');
			}

			$userid=$userinfo['id'];
			$price=(C('yh_fanxian')/100)*$count;

			if($price>0){
				
				$usermod->where('id='.$userid)->save(array(
					'money'=>array('exp','money+'.$price),
					'score'=>array('exp','score-'.$count)
					));

				M('usercash')->add(array(
					'uid'         =>$userid,
					'money'       =>$price,
					'remark'      =>'积分兑换: '.$price.'元',
					'type'        =>10, 
					'create_time' =>time(),
					'status'      =>1,
					));
				$this->Exitjson(200,'兑换成功!');

			}
			$this->Exitjson(400,'兑换失败!');
	
}


public function tixian() {
$mod=new userModel();
$F=M('balance');
$fromdata = $this->params['fromdata'];
$fromdata = json_decode($fromdata,true);
$mymoney = abs($fromdata['money']);
if($mymoney<=0){
$this->Exitjson(400,'提现金额异常！');

}
			if($mymoney<C('yh_Quota')){
			$this->Exitjson(400,'单笔提现金额不能小于'.C('yh_Quota').'元');
		
			}
$openid = $this->params['openid'];
$userinfo = $this->getuid($openid);			
$map['id'] = $userinfo['id'];	
$balance = $mod->field('nickname,avatar,username,money,id,realname,alipay')->where($map)->find();
$alipay=$fromdata['alipay'];
$name=$fromdata['realname'];
if($alipay && $name && !$balance['alipay']){
			$data=array(
			'realname'=>$name,
			'alipay'=>$alipay
			);
			$mod->where($map)->save($data);
}
			
     		if($mymoney>$balance['money']){
     		$this->Exitjson(400,'账户余额不足');
     		}
     			$data=array(
					'uid'=>$balance['id'],
					'money'=>$mymoney,
					'name'=>$name?$name:$balance['realname'],
					'method'=>2,
					'allpay'=>$alipay?$alipay:$balance['alipay'],
					'status'=>0,
					'content'=>$fromdata['content'],
					'create_time'=>time()
				);
			$res = $F->add($data);
			if($res !== false){
			$mod->where(array(
                'id'=>$balance['id']
            ))->save(array(
                'money'=>array('exp','money-'.$mymoney),
                'frozen'=>array('exp','frozen+'.$mymoney),
));

M('usercash')->add(array(
                'uid'=>$balance['id'],
                'money'=>$mymoney,
                'type'=>1,
                'remark'=>'提现冻结资金：'.$mymoney.'元',
                'create_time'=>NOW_TIME,
                'status'=>1,
   ));
   $myphone=trim(C('yh_sms_my_phone'));
   if(0 < C('yh_sms_status') && $myphone){
     $data=array(
            'phone'=>$myphone,
            'code'=>$balance['nickname'],
            'webname'=>floatval($mymoney),
            'temp_id'=>trim(C('yh_sms_tixian_id'))
            );
  			 $res= $this->send_sms($data);
   }
   $this->Exitjson(200,'申请提交成功，请等待处理！');

	 }
				$this->Exitjson(400,'申请提交失败！');
			
		}

public function qudao(){
$openid = $this->params['openid'];	
$userinfo = $this->getuid($openid);	
$map=array(
'uid'=>$userinfo['id'],
'from'=>'wechat'
);
$token=$this->create_token(trim(C('yh_gongju')),$map);
$url ='/index.php?m=m&c=msg&a=bindtb&uid='.$userinfo['id'].'&from=wechat&token='.$token;
$mdomain = str_replace('/index.php/m','',trim(C('yh_headerm_html')));
$url = $mdomain.$url;
$this->Exitjson(200,'成功！',$url);
	
}


public function monthlydetail(){
$openid = $this->params['openid'];
$page = $this->params['p'];	
$page_size = 10;
$start = $page_size * $page;
$userinfo = $this->getuid($openid);
$stay['uid'] = $userinfo['id'];
$stay['type'] = 3;
$modfin=M('finance');
$rows = $modfin->field('income,mark')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
if($rows){
	$this->Exitjson(200,'成功',$rows);
}

$this->Exitjson(400,'失败');
	
}

public function modify(){
$mod = new userModel();
$fromdata = $this->params['fromdata'];
$fromdata = json_decode($fromdata,true);
			$password = $fromdata['password'];
			$password2 = $fromdata['password2'];
			$phone = $fromdata['phone'];
			$data=array(
				'nickname'=>$fromdata['nickname'],
				'qq'=>$fromdata['qq'],
				'wechat'=>$fromdata['wechat'],
				);
			if($fromdata['avatar']){
				$data['avatar'] = $fromdata['avatar'];
			}
			
	if($phone){
	   $result = $mod->where(array('phone'=>$phone))->count('id');
        if ($result) {
        	$this->Exitjson(400,'手机号已存在');
        } else {
          $data['phone'] = $phone;
        }
	}
			
			
			if($password){
				if($password == $password2){
					$data['password'] = md5($password);
				}else{
					$this->Exitjson(400,'两次密码不一致');
				
				}
			}
			$uid = $this->getuid($this->params['openid']);
			$where['id'] = $uid['id'];
			$res = $mod->where($where)->save($data);
			if($res !== false){
				$json = array(
				'avatar'=>$data['avatar'],
				'nickname'=>$data['nickname'],
				);
				$this->Exitjson(200,'修改成功',$json);
			}
			$this->Exitjson(400,'修改失败');
	
	
}


public function uploadfile(){
	
if(!empty($_FILES['file'])){	
$file = $this->_upload($_FILES['file'], 'avatar',$thumb = array('width'=>150,'height'=>150));

if($file['error']) {
$this->Exitjson(400,$file['info']);
} else {
$url = trim(C('yh_site_url')).$file['mini_pic'];
$this->Exitjson(200,'上传成功',$url);
}

}
$this->Exitjson(400,'上传文件为空');	
}

public function userinfo(){
$openid = $this->params['openid'];
$artmod = new articleModel();
$usermod = new userModel();
$notice=$artmod->cache(true, 10 * 60)->where('cate_id=2')->field('id,title')->limit(5)->select();
$userinfo = $usermod->field('id,money,oid,avatar,score,invocode,tbname,webmaster_pid,webmaster,webmaster_rate,phone,qq,wechat,phone,nickname,realname,alipay,tb_open_uid')->where(array('openid'=>$openid))->find();
if(!$userinfo['invocode']){
$codes=$this->invicode($userinfo['id']);
$userinfo['invocode']=$codes;
}
if(substr($userinfo['avatar'],0,4)!='http'){ 
$userinfo['avatar']=trim(C('yh_site_url')).$userinfo['avatar'];
};
if(1 != $userinfo['tbname']){
		$map=array(
		'uid'=>$userinfo['id'],
		//'nstatus'=>1,
		'status'=>3
		);
		$mymoney=M('order')->where($map)->sum('price');
		$agentcondition=trim(C('yh_agentcondition'));
		if(!$mymoney)$mymoney=0;
		if($mymoney>=$agentcondition){
		$usermod->where(array('id'=>$userinfo['id']))->setField('tbname',1);	
		}
}
$start=strtotime(date('Y-m-01', strtotime('last day of -1 month')));	
$end =strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
$uid = $userinfo['id'];
$field ='(select SUM(money) from tqk_usercash where uid = '.$uid.' and type = 3 and (create_time > '.$start.' and create_time < '.$end.')) as pre_money,
(select SUM(money) from tqk_usercash where uid = '.$uid.' and type = 3 and create_time > '.$end.') as this_money';
$res = $usermod->field($field)->find();
$userinfo['pre_money']=$res['pre_money']?$res['pre_money']:0;
$userinfo['this_money']=$res['this_money']?$res['this_money']:0;

$json =array(
'code'=>200,
'userinfo'=>$userinfo,
'notice'=>$notice
);

exit(json_encode($json,JSON_UNESCAPED_UNICODE));	
	
}

public function help(){
$mod = new helpModel();
$result = $mod->field('info')->find(4);
if($result){
$result['add_time'] = frienddate($result['add_time']);
$this->Exitjson(200,'成功',$result);	
}
$this->Exitjson(400,'失败');	
}

public function chaojisou(){
 $url=$this->tqkapi."/so";
 $data=array(
 'time'=>time(),
 'tqk_uid'=>	$this->tqkuid,
 'k'=>$this->params['content'],
 'hash'=>true
 );

$data=$this->_curl($url,$data,true); 
$result=json_decode($data,true);  
if($result && $result['status'] == 1){
$this->Exitjson(200,'成功',$result);	
}

$this->Exitjson(400,'失败');
	
}


public function hotkey(){
$val = F('data/hotkey');
$json=array(
 'code'=>200,
 'endTime'=>NOW_TIME,
 'result'=>$val,
);
exit(json_encode($json,JSON_UNESCAPED_UNICODE));
}


public function jiu(){
$mod = new itemsModel();
$mod = $mod ->cache(true, 10 * 60);
$size	= 20;
$pid = $this->params['pid'];
$key = $this->params['key'];
$block = $this->params['block'];
$sort = $this->params['sort'];
$ali_id = $this->params['ali_id'];
$page = $this->params['page'];
$start = $size * $page;
$key    = urldecode($key);
$where['ems'] = 1;
$where['status'] = 'underway';
if($key){
 $where['title|tags'] = array( 'like', '%' . $key . '%' );
}

if($block =='jiu'){
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
}

if($block =='top100'){
$where['volume'] = array('gt','5000');
}

if($block =='hot'){
$where['volume'] = array('gt','5000');
}


if($pid){
 $where['cate_id'] = $pid;
}
if($ali_id){
 $where['ali_id'] = $ali_id;
}
$order = 'ordid asc';

if($block =='top100'){
$order = '(quan/price)*100 DESC,ordid asc';
}

switch ($sort){
    		case 'new':
				$order.= ',coupon_start_time DESC';
				break;
			case 'price':
				$order.= ',coupon_price ASC';
				break;
			case 'rate':
				$order.= ',quan DESC';
				break;
			case 'hot':
				$order.= ',volume DESC';
				break;
			default:
				$order.= ',id desc';
}

$count =$mod->where($where)->count();
$endpage = ceil($count/$size);
if($start <= $endpage){
$items_list = $mod->where($where)->field('id,cate_id,ali_id,num_iid,pic_url,title,coupon_price,commission_rate,price,quan,shop_type,volume,quanurl,Quan_id,sellerId,quankouling,add_time')->order($order)->limit($start . ',' . $size)->select();
//$pager = new Page($count, $size);
if($items_list){
$goodslist=array();
foreach($items_list as $k=>$v){
$goodslist[$k]['id']=$v['id'];
$goodslist[$k]['num_iid']=$v['num_iid'];
$goodslist[$k]['pic_url']=$v['pic_url'];
$goodslist[$k]['title']=$v['title'];
$goodslist[$k]['rebate']=Rebate1($v['coupon_price']*$v['commission_rate']/10000);
$goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
$goodslist[$k]['coupon_price']=round($v['coupon_price'],2);
$goodslist[$k]['price']=round($v['price'],2);
$goodslist[$k]['quan']=intval($v['quan']);
$goodslist[$k]['shop_type']=$v['shop_type'];
$goodslist[$k]['volume']=$v['volume'];
$goodslist[$k]['quanurl']=$v['quanurl'];	
$goodslist[$k]['Quan_id']=$v['Quan_id'];	
$goodslist[$k]['cate_id']=$v['cate_id'];	
$goodslist[$k]['ali_id']=$v['ali_id'];	
$goodslist[$k]['sellerId']=$v['sellerId'];	
$goodslist[$k]['quankouling']=$v['quankouling']?$v['quankouling']:'';	
		}
	}
}

if($goodslist){
$this->Exitjson(200,$ali,$goodslist);
}else{
$this->Exitjson(400,'没有数据啦！');
}
	
	
}


public function cate(){
$mod = new itemsModel();
$mod = $mod ->cache(true, 10 * 60);
$size	= 20;
$pid = $this->params['pid'];
$key = $this->params['key'];
$sort = $this->params['sort'];
$ali_id = $this->params['ali_id'];
$page = $this->params['page'];
$start = $size * $page;
$key    = urldecode($key);
$where['ems'] = 1;
$where['status'] = 'underway';
if($key){
 $where['title|tags'] = array( 'like', '%' . $key . '%' );
}
if($pid){
 $where['cate_id'] = $pid;
}
if($ali_id){
 $where['ali_id'] = $ali_id;
}
$order = 'ordid asc';
switch ($sort){
    		case 'new':
				$order.= ',coupon_start_time DESC';
				break;
			case 'price':
				$order.= ',coupon_price ASC';
				break;
			case 'rate':
				$order.= ',quan DESC';
				break;
			case 'hot':
				$order.= ',volume DESC';
				break;
			default:
				$order.= ',id desc';
}
$count =$mod->where($where)->count();
$endpage = ceil($count/$size);
if($start <= $endpage){
$items_list = $mod->where($where)->field('id,cate_id,ali_id,num_iid,pic_url,title,coupon_price,commission_rate,price,quan,shop_type,volume,quanurl,Quan_id,sellerId,quankouling,add_time')->order($order)->limit($start . ',' . $size)->select();
//$pager = new Page($count, $size);
if($items_list){
$goodslist=array();
foreach($items_list as $k=>$v){
if($this->FilterWords($v['title'])){
continue;
}
$goodslist[$k]['id']=$v['id'];
$goodslist[$k]['num_iid']=$v['num_iid'];
$goodslist[$k]['pic_url']=$v['pic_url'];
$goodslist[$k]['title']=$v['title'];
$goodslist[$k]['rebate']=Rebate1($v['coupon_price']*$v['commission_rate']/10000);
$goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
$goodslist[$k]['coupon_price']=round($v['coupon_price'],2);
$goodslist[$k]['price']=round($v['price'],2);
$goodslist[$k]['quan']=intval($v['quan']);
$goodslist[$k]['shop_type']=$v['shop_type'];
$goodslist[$k]['volume']=$v['volume'];
$goodslist[$k]['quanurl']=$v['quanurl'];	
$goodslist[$k]['Quan_id']=$v['Quan_id'];	
$goodslist[$k]['cate_id']=$v['cate_id'];	
$goodslist[$k]['ali_id']=$v['ali_id'];	
$goodslist[$k]['sellerId']=$v['sellerId'];	
$goodslist[$k]['quankouling']=$v['quankouling']?$v['quankouling']:'';	
		}
	}
}

$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[3];
$count=count($items_list);
if(C('yh_site_secret')==1 && ($key || $ali_id) && !empty($appkey) && !empty($appsecret) && $count<20 && !empty($AdzoneId)){
$ali = $ali_id.'+'.$key;
vendor('taobao.taobao');
$c = new \TopClient();
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$c->format = 'json';
$req = new \TbkDgMaterialOptionalRequest();
$req->setAdzoneId($AdzoneId);
$req->setPlatform("1");
$req->setPageSize("20");
if($ali_id){
$req->setCat("".$ali_id."");
}
if($key){
$req->setQ((string)$key);
}
if($page>0){
$req->setPageNo("".$page."");
}else{
$req->setPageNo(1);	
}

$req->setHasCoupon('true');
$req->setIncludePayRate30("true");
if($sort=='hot'){
$req->setSort("total_sales_des");	
}elseif($sort=='price'){
$req->setSort("price_asc");
}else if($sort=='rate'){
$req->setSort("tk_rate_des");	
}else{
$req->setSort("tk_des");	
}
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$resp=$resp['result_list']['map_data'];	
$patterns = "/\d+/";
foreach($resp as $k=>$v){
if($this->FilterWords($v['title'])){
continue;
}
$goodslist[$k+$count]['quan']=formatprice($v['coupon_amount']);
$goodslist[$k+$count]['quanurl']=$v['coupon_share_url'];
$goodslist[$k+$count]['num_iid']=$v['item_id'];
$goodslist[$k+$count]['title']=$v['title'];
$goodslist[$k+$count]['coupon_price']=round($v['zk_final_price']-$goodslist[$k+$count]['quan'],2);
$goodslist[$k+$count]['rebate']=Rebate1($goodslist[$k+$count]['coupon_price']*$v['commission_rate']/10000);
if($v['user_type']=="1"){
$goodslist[$k+$count]['shop_type']='B';	
}else{
$goodslist[$k+$count]['shop_type']='C';	
}
$goodslist[$k+$count]['commission_rate']=$v['commission_rate']; //比例
$goodslist[$k+$count]['price']=round($v['zk_final_price'],2);
$goodslist[$k+$count]['volume']=$v['volume'];
$goodslist[$k+$count]['ali_id']=$ali_id;
$goodslist[$k+$count]['pic_url']=$v['pict_url'];
$goodslist[$k+$count]['quankouling']='';
}
 
}
if($goodslist){

if(12 > strlen($key) && strlen($key)>3 && !preg_match('/[a-zA-Z]/',$key)){
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
$this->Exitjson(200,$ali,$goodslist);
}else{
$this->Exitjson(400,'没有数据啦！');
}

	
	
}


protected function getuid($openid){
$uid = S($openid);
if($uid){
return $uid;	
}
$mod = new userModel();
$uid = $mod->field('webmaster_pid,id,fuid,guid,webmaster,webmaster_rate')->where(array('openid'=>$openid))->find();
if($uid){
S($openid,$uid,3600);
return $uid;		
}

return false;
	
}

protected function orderstatic($id){
		switch($id){
			case 0 :
			return '待处理';
			break;
			case 1 :
			return '已付款';
			break;
			case 2 :
			return '无效订单';
			break;
			case 3 :
			return '已收货';
			break;
			default : 
			return '订单失效';
			break;
		}
		
	}
	
	
public function channelorder(){
$openid = $this->params['openid'];
$res = $this->getuid($openid);
$status = $this->params['status']?$this->params['status']:0;
$relationid = $res['webmaster_pid'];
if(!$relationid){
$this->Exitjson(400,'没有数据');
}
$p = $this->params['p'];	
$page_size = 6;
$stay['relation_id'] = $relationid;
$stay['nstatus'] = 2;
if($status){
$stay['status'] = $status;
}
$rows = M('order')->field('goods_title,status,orderid,add_time,price,settle,income,up_time,id,leve1,leve2,leve3')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
$list=array();
		foreach($rows as $k=>$v){
			$list[$k]['status']= $v['settle']==1?'已结算':$this->orderstatic($v['status']);
			$list[$k]['state']=$v['status'];
			$list[$k]['orderid']=$v['orderid'];
			$list[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			$list[$k]['price']=round($v['price'],2);
			$list[$k]['income']= round($v['income']*($v['leve1']/100), 2);
			$list[$k]['goods_title']=$v['goods_title'];
			$list[$k]['up_time']=date('Y-m-d H:i',$v['up_time']);
			$list[$k]['id']=$v['id'];
			$list[$k]['leve1']=$v['leve1'];
			$list[$k]['leve2']=$v['leve2'];
			$list[$k]['leve3']=$v['leve3'];
		}

if($list){
	$this->Exitjson(200,'成功',$list);
}
$this->Exitjson(400,'没有数据了');	


	
	
}

public function teamorder(){
$openid = $this->params['openid'];
$res = $this->getuid($openid);
$uid = $res['id'];
$p = $this->params['p'];
$ac = $this->params['ac'];
$page_size = 6;
$start = abs($page_size * $p);
if($ac == 'fuid'){
$stay['fuid'] = $uid;	
}
if($ac == 'guid'){
$stay['guid'] = $uid;	
}
$stay['nstatus'] = 1;

if(!$ac){
$stay['fuid'] = $uid;	
}

$rows = M('order')->field('goods_title,status,orderid,settle,add_time,price,income,up_time,id,leve1,leve2,leve3')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
$list=array();
		foreach($rows as $k=>$v){
			$list[$k]['status']=$v['settle']==1?'已结算':$this->orderstatic($v['status']);
			$list[$k]['state']=$v['status'];
			$list[$k]['orderid']=$v['orderid'];
			$list[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			$list[$k]['price']=round($v['price'],2);
			if($ac == 'guid'){
			$list[$k]['income']= Orebate3(array('price'=>$v['income'],'leve'=>$v['leve3']));
			}else{
			$list[$k]['income']= Orebate2(array('price'=>$v['income'],'leve'=>$v['leve2']));
			}
			$list[$k]['goods_title']=$v['goods_title'];
			$list[$k]['up_time']=date('Y-m-d H:i',$v['up_time']);
			$list[$k]['id']=$v['id'];
			$list[$k]['leve1']=$v['leve1'];
			$list[$k]['leve2']=$v['leve2'];
			$list[$k]['leve3']=$v['leve3'];
		}

if($list){
	$this->Exitjson(200,'成功',$list);
}
$this->Exitjson(400,'没有数据了');	
	
	
}


public function userorder(){
$p = $this->params['p'];
$status = $this->params['status']?$this->params['status']:0;
$page_size = 6;
$start = abs($page_size * $p);
$openid = $this->params['openid'];
$res = $this->getuid($openid);
$uid = $res['id'];
$stay['uid'] = $uid;
//$stay['nstatus'] = 1;
if($status){
$stay['status'] = $status;
}
$mod = M('order');
$rows =$mod ->field('status,orderid,goods_title,price,up_time,settle,income,leve1,add_time,bask')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
$list=array();

foreach($rows as $k=>$v){
			$list[$k]['status']=$v['settle']==1?'已结算':$this->orderstatic($v['status']);
			$list[$k]['state']=$v['status'];
			$list[$k]['orderid']=$v['orderid'];
			$list[$k]['goods_title']=$v['goods_title'];
			$list[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			$list[$k]['price']=round($v['price'],2);
			$list[$k]['bask']=$v['bask'];
			$list[$k]['income']=Orebate1(array('price'=>$v['income'],'leve'=>$v['leve1']));
			$list[$k]['up_time']=date('Y-m-d H:i',$v['up_time']);
}

if($list){
	$this->Exitjson(200,'成功',$list);
}

$this->Exitjson(400,'没有数据了');	
	
}


public  function subcate(){
$mod = new itemscateModel();	
$mod = $mod ->cache(true, 10 * 60);
$pid = $this->params['pid'];
$catelist=$mod->field('name,remark,ali_id,id,pid')->where('status = 1 and pid ='.$pid.'')->order('ordid asc')->select();	
if($catelist){
$this->Exitjson(200,'',$catelist);
}else{
$this->Exitjson(404,'没有数据了');
}	
	
}
 
 
public function navlist(){
$mod = new itemscateModel();	
$mod = $mod ->cache(true, 10 * 60);
$catelist=$mod->field('name,id')->where('status = 1 and pid = 0')->order('ordid asc')->select();	
if($catelist){
$this->Exitjson(200,'',$catelist);
}else{
$this->Exitjson(404,'没有数据');
}
 	
 }
 
 
public function getconfig(){
	
$json=array(
//'appstatus'=>$this->params['debug']==1?1:C('yh_site_secret'),//小程序审核模式
'appstatus'=>C('yh_site_secret'),//小程序审核模式
'openduoduo'=>C('yh_openduoduo'),//开启多多
'islogin'=>C('yh_islogin'),//登录领券
'isfanli'=>C('yh_isfanli'),//返利显示
'invocode'=>C('yh_invocode'),//邀请码
'fanxian'=>C('yh_fanxian'),//积分兑换比例
'item_hit'=>C('yh_item_hit'),//签到送积分
'Quota'=>C('yh_Quota'),//提现门槛
'agentcondition'=>C('yh_agentcondition'),//代理门槛
'site_name'=>C('yh_site_name'),//网站名称
'qq'=>C('yh_qq'),//QQ客服
'wechat'=>C('yh_zhibo_url'),//QQ客服
'sms_status'=>C('yh_sms_status'),//短信验证
'bili1'=>trim(C('yh_bili1')),
'bili2'=>trim(C('yh_bili2')),
'bingtaobao'=>C('yh_bingtaobao'),//开启淘宝授权
'ComputingTime'=>trim(C('yh_ComputingTime')),
);
$json=array(
'code'=>200,
'result'=>$json
);

exit(json_encode($json,JSON_UNESCAPED_UNICODE));	

}
 
 
 public function getbanner(){
 $mod = new adModel();
 $mod = $mod ->cache(true, 10 * 60);
 $adlist=$mod->field('url,img')->where('(status = 1) and add_time=0')->order('ordid asc,id desc')->select();	
if($adlist){
foreach($adlist as $ak=>$av){
$adlist[$ak]['img']	=C('yh_site_url').$av['img'];
$adlist[$ak]['url']	=$av['url'];
}
	
	
$json=array(
'code'=>200,
'result'=>$adlist,
'num' =>count($adlist),
);
}else{
$json=array(
	'code'=>404,
	'msg'=>'没有数据'
	);	
}
	
exit(json_encode($json,JSON_UNESCAPED_UNICODE));	
 	
 	
 }
 
 
public function brand(){
$Pre=C("DB_PREFIX");
$mod=M('brandcate');
$catelist=$mod->cache(true, 30 * 60)->field('name,id')->where('status = 1')->order('ordid asc,id desc')->select();
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
    }

 }

if($catelist && $list){
$json=array(
'code'=>200,
'result'=>$list,
'cate'=>$catelist
);
}else{
$json=array(
	'code'=>404,
	'msg'=>'没有数据'
	);	
}

exit(json_encode($json,JSON_UNESCAPED_UNICODE));
	
}

public function recommend(){
$cid = $this->params['cid'];
$itemid = $this->params['itemid'];
$ali_id = $this->params['ali_id'];
if($cid){
$where = array('ems'=>1,'cid'=>$cid,'num_iid'=>array('neq',$itemid));
}
if($ali_id){
$where = array('ems'=>1,'ali_id'=>$ali_id,'num_iid'=>array('neq',$itemid));
}
$mod = new itemsModel();
$mod = $mod ->cache(true, 10 * 60);
$list = $mod->where($where)->field('id,cate_id,ali_id,num_iid,pic_url,title,coupon_price,commission_rate,price,quan,shop_type,volume,quanurl,Quan_id,sellerId,quankouling,add_time')->order('id desc')->limit(6)->select();
if($list){
	$this->Exitjson(200,'成功',$list);
}else{
     $this->Exitjson(400,'失败');
}

}

public function login(){
       $username = $this->params['username'];
       $password = $this->params['password'];
       $remember = 1;
       $res = $this->visitor->login($username, $password, $remember);
       if($res){
       	$info = $this->visitor->get();
       	$userinfo = array(
       	 'nickname'=>$info['nickname'],
       	 'openid'=>$info['openid'],
       	 'isagent'=>$info['tbname'],
       	 'oid'=>$info['oid'],
       	 'webmaster'=>$info['webmaster'],
       	 'rate'=>$info['webmaster_rate'],
       	 'relation'=>$info['webmaster_pid'],
       	);
		$json=array(
			'code'=>200,
			'result'=>$userinfo
			);
       }else{
       	$json=array(
		'code'=>400,
		'result'=>$this->visitor->error
		);
       	
       }
exit(json_encode($json,JSON_UNESCAPED_UNICODE));

    }


public function classify(){
$mod = new itemscateModel();	
$mod = $mod ->cache(true, 10 * 60);
$where = array(
'status'=>1
);
$result = $mod->field('id,name,ali_id,pid,remark')->where($where)->order('ordid asc,id desc')->select();
if($result){
$json=array(
'code'=>200,
'result'=>$result
);
}else{
$json=array(
	'code'=>404,
	'msg'=>'没有数据'
	);	
}
exit(json_encode($json,JSON_UNESCAPED_UNICODE));
}
 
 
 

public function checkin(){
$Amount=abs(trim(C('yh_item_hit')));
if($Amount){
$Amount=mt_rand(1,$Amount*100)/100;
$mod_cash=new usercashModel();
$Mod_user=new userModel();
$today=date('Y-m-d',NOW_TIME);
$openid = $this->params['openid'];
$this->uid = $Mod_user->where(array('openid'=>$openid))->getField('id');
if(!$this->uid){
$json=array(
	'code'=>200,
	'msg'=>'用户信息不存在'
	);	
exit(json_encode($json,JSON_UNESCAPED_UNICODE));	
}

$checked=S('checkin_'.$this->uid);
if($this->uid && false ===$checked){
S('checkin_'.$this->uid,$this->uid,2);
$ischeck=$mod_cash->where(array('uid'=>$this->uid,'type'=>12))->order('id desc')->getField('create_time');
if($today==date('Y-m-d',$ischeck)){
$json=array(
	'code'=>200,
	'msg'=>'今日已签到，请明天再来！'
	);
	exit(json_encode($json,JSON_UNESCAPED_UNICODE));
}

$data=array(
'uid'=>$this->uid,
'money'=>$Amount,
'type'=>12,
'remark'=>'签到送现金',
'create_time'=>NOW_TIME,
'status'=>1
);

$res=$mod_cash->add($data);
	if($res){
		$mod=new userModel();
		$mod->where("id='".$this->uid."'")->setInc('money',$Amount);

$json=array(
	'code'=>200,
	'msg'=>'签到成功，恭喜获得'.$Amount.'元'
	);

exit(json_encode($json,JSON_UNESCAPED_UNICODE));

	}


}


$json=array(
	'code'=>200,
	'msg'=>'请不要重复签到'
	);

exit(json_encode($json,JSON_UNESCAPED_UNICODE));

	
}	

$json=array(
'msg'=>'还没有开启签到功能！',
'code'=>200
);

exit(json_encode($json,JSON_UNESCAPED_UNICODE));
	
}





}