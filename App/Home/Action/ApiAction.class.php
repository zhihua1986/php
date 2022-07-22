<?php
namespace Home\Action;
use Common\Model\userModel;
use Common\Model\orderModel;
class ApiAction extends BaseAction {
private $accessKey = '';
public function _initialize()
    {
        parent::_initialize();
        $this->accessKey = trim(C('yh_gongju'));

 }
 

 
public function ItemPoster(){
	$coupon_price = I('q');
	$price = I('p');
	$img = urldecode(I('i'));
	$key = I('key', '', 'trim');
	        if(!$key || $key != $this->accessKey){
	            $json = array(
	                'data'=>array(),
	                'result'=>array(),
	                'state'=>'no',
	                'msg'=>'通信密钥错误'
	            );
	            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
	 }
	
	if($coupon_price && $price && $img){
		$config = array(
		'text'=>array(
		    array(
		     'text'=>'券后价:'.$coupon_price.'元',
		            'left'=>252,
		            'top'=>100,
		            'fontPath'=>'ThinkPHP/Library/Think/Verify/zhttfs/1.ttf',     
		            'fontSize'=>20,            			 
		            'fontColor'=>'212,33,31', 				
		            'angle'=>0,
		    ),
			array(
			 'text'=>'原售价:'.$price.'元',
			        'left'=>252,
			        'top'=>155,
			        'fontPath'=>'ThinkPHP/Library/Think/Verify/zhttfs/1.ttf',     
			        'fontSize'=>20,            			 
			        'fontColor'=>'171,162,162', 				
			        'angle'=>0,
			)
		  ),
		    'image'=>array(
		        array(
					'url'=>$img,				
		            'left'=>40,
		            'top'=>40,
		            'stream'=>0,						
		            'right'=>0,
		            'bottom'=>0,
		            'width'=>200,
		            'height'=>200,
		            'opacity'=>100
		        )
		    ),
		    'background'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01Ph3wu92MgYhl209LK_!!3175549857.jpg',
		);
		 $this->createPoster($config);  
		
			}
		 
	 }
	 
public function pddorder(){
$key = I('key', '', 'trim');
        if(!$key || $key != $this->accessKey){
            $json = array(
                'data'=>array(),
                'result'=>array(),
                'state'=>'no',
                'msg'=>'通信密钥错误'
            );
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
 }	

  $file = TQK_DATA_PATH.'pddorder.txt';
        if(!file_exists($file)){
            return false;
 }
        
      $startId = file_get_contents($file);
       if(!$startId){
            $startId = strtotime(date('Y-m-d',strtotime("-1 day"))); 
      }
       $startId = 1597852800;
        $time=NOW_TIME;
		$map=array(
		'start'=>$startId,
		'pagesize'=>50,
		'page'=>1,
		'time'=>$time,
		'tqk_uid'=>$this->tqkuid
		);
	  $token=$this->create_token(trim(C('yh_gongju')),$map);
        $map['token']=$token;
        $url = $this->tqkapi.'/pddgetorder';
        $content = $this->_curl($url,$map);
        $json = json_decode($content, true);
		 $json = $json['result']['order_list_get_response']['order_list'];
		 $count=count($json);
        if($count>0){
        		$n=0;
         foreach ($json as $key => $val){
         if($val['order_status']>=0){
                $raw = array(
                    'goods_id'=>$val['goods_id'],
                    'order_sn'=>$val['order_sn'],
                    'order_status'=>$val['order_status_desc'],
                    'order_amount'=>$val['order_amount']/100,
                    'promotion_amount'=>$val['promotion_amount']/100,
                    'p_id'=>$val['p_id'],
                    'order_pay_time'=>$val['order_pay_time'],
                    'order_settle_time'=>$val['order_settle_time']
                );
                if($this->_ajax_pdd_order_insert($raw)){
			    $n++;
			    }
               
            }   
              
            }
          
		  file_put_contents($file, $time);
		   $json = array(
                'data'=>array(),
                'total'=>$n,
                'state'=>'yes',
                'msg'=>'成功入库'.$n.'个拼多多订单'
            );
			
        }else{
        file_put_contents($file, $time); 	
         $json = array(
                'data'=>array(),
                'total'=>'0',
                'state'=>'no',
                'msg'=>'成功入库0个拼多多订单'
        );
		
        }

       exit(json_encode($json,JSON_UNESCAPED_UNICODE));

	
	
	
}


public function userlist(){
//$this->check_key();
$openid = I('openid', $_SESSION['user']['openid'], 'trim');
$callback=I('callback', '', 'trim');
$uid=I('uid', '', 'trim');
$key=md5($this->accessKey);
if($openid==null || $openid=='' || $uid!=$key){
$json=array(
'status'=>'out',
'msg'=>'登录超时'
);
exit($callback.'('.json_encode($json,JSON_UNESCAPED_UNICODE).')');
}

$U=M('user');
$where=array(
'state'=>0,
);
$userlist_a=$U->where($where)->field('id,avatar')->select();
shuffle($userlist_a);
$where=array(
'openid'=>$openid,
);
$data=array(
'last_time'=>time(),
);
$res=$U->where($where)->save($data);
$time=time();
$userlist_b=$U->where('('.$time.'-last_time) < 3600 and state = 1')->field('id,avatar')->select();

$cout=count($userlist_b);
foreach($userlist_b as $ki=>$vi){
$userlist_b[$ki]['id']=$vi['id'];
$userlist_b[$ki]['avatar']=C('yh_site_url').$vi['avatar'];
}

foreach($userlist_a as $k=>$v){
if($k<25){
$userlist_b[$k+$cout]['id']=$v['id'];
$userlist_b[$k+$cout]['avatar']=C('yh_site_url').$v['avatar'];
}else{
 break;
}

}

M('hongbao')->where('push_time<'.time())->save(array(
'status'=>1,
));

if($userlist_b){
 $json = array(
                'result'=>$userlist_b,
                'count'=>count($userlist_b)+C('yh_person_num')+1,
                'status'=>'ok',
                'money'=>$U->where($where)->getField('money'),
                'cls'=>'person',
                'hongbao_time'=>M('hongbao')->where('status=0')->order('push_time asc')->getField('push_time'),
                'msg'=>'获取用户数据成功'
 );	
exit($callback.'('.json_encode($json,JSON_UNESCAPED_UNICODE).')');
}else{
$data=array(
'status'=>'no',
'msg'=>'没有用户数据！'
);
exit($callback.'('.json_encode($data).')');
}

}	


public function zhibo_save_hid(){
$callback=I('callback', '', 'trim');
$uid = I('openid', $_SESSION['user']['openid'], 'trim');
$ke=I('uid', '', 'trim');
$key=md5($this->accessKey);
if($uid==null || $uid=='' || $ke!=$key){
$json=array(
'status'=>'out',
'msg'=>'登录超时'
);
exit($callback.'('.json_encode($json,JSON_UNESCAPED_UNICODE).')');
}
$hid = I('id', '', 'trim');
$user=M('user')->where("openid='".$uid."'")->field('id,money')->find();
$userid=$user['id'];
$mod=M('hongbao_detail');
$ucount=$mod->where('uid='.$userid.' and hid='.$hid)->count();
if($ucount>0){
$json=array(
'status'=>'no',
'money'=>$user['money'],
//'msg'=>$ucount
'msg'=>'你已经抢过了<br/>把机会留给别人吧！'
);
exit($callback.'('.json_encode($json,JSON_UNESCAPED_UNICODE).')');	
}else{

$where=array(
'hid'=>$hid,
'status'=>0,
);
$res=$mod->where($where)->find();
if($res){
$data=array(
'status'=>1,
'uid'=>$userid,
'get_time'=>time()
);
$re=$mod->where('id='.$res['id'])->save($data);
if($re){
M('user')->where('id='.$userid)->save(array(
 'money'=>array('exp','money+'.$res['price'])
));

 M('user_cash')->add(array(
	    		'uid'         =>$userid,
	    		'money'       =>$res['price'],
	    		'remark'      =>'抢红包: '.$res['price'].'元',
	    		'type'        =>11, 
	    		'create_time' =>time(),
	    		'status'      =>1,
	    	));


$json=array(
'status'=>'ok',
'money'=>$user['money']+$res['price'],
'price'=>$res['price'],
'msg'=>'领取红包成功！'
);
}	
}else{
$json=array(
'status'=>'no',
'money'=>$user['money'],
'msg'=>'手慢了，红包派完了'
);

}
	
exit($callback.'('.json_encode($json,JSON_UNESCAPED_UNICODE).')');
}
	
}

public function push_hongbao(){
$json = array(
         'state'=>'no',
          'msg'=>'通行密钥不正确'
 );
$key = I('ikey', '', 'trim');
if(!$key || $key!=md5($this->accessKey)){
exit($callback.'('.json_encode($json,JSON_UNESCAPED_UNICODE).')');	
}	
$callback=I('callback', '', 'trim');
$token=I('token','','trim');
if(empty($sign) || $sign=='' && $sign==null ){
$sign=$token;
$mod=M('hongbao');
$mod_detail=M('hongbao_detail');
$task=$mod->where('push_time < now() and status=0')->order('push_time asc')->field('id,push_time')->find();
$now=time();
if($task && ($now-$task['push_time'])>0){
$hb_list=$mod_detail->where('hid='.$task['id'])->count();
if($hb_list){
	$json=array(
	'count'=>$hb_list,
	'hid'=>$task['id'],
	'cls'=>'hongbao',
	'status'=>'ok',
	'msg'=>'获取红包成功'
	);
exit($callback.'('.json_encode($json,JSON_UNESCAPED_UNICODE).')');	

}

}

}


$json=array(
'status'=>'no',
'msg'=>'暂时没有要推送的信息'
);
exit($callback.'('.json_encode($json,JSON_UNESCAPED_UNICODE).')');	

}


public function tbclientauth(){
$this->check_tb_token(trim(C('yh_gongju')));
$code = $this->params['code'];
$callback=trim(C('yh_site_url')).'/index.php?c=bindtb&a=authorize';
$oauth='https://oauth.taobao.com';
$url=$oauth.'/token?grant_type=authorization_code&response_type=code&client_id='.trim(C('yh_taobao_appkey')).'&client_secret='.trim(C('yh_taobao_appsecret')).'&redirect_uri='.$callback.'&code='.$code;
$res=$this->_curl($url,array(),true);
$res=json_decode($res,true);
if($res && $res['taobao_user_id']){
$oid=$res['taobao_user_id'];
$o1=substr($oid,-6,2);
$o2=substr($oid,-4,2);
$o3=substr($oid,-2); 
$oid=$o1.$o3.$o2;
$Mod = new UserModel();
$Where=array(
'id'=>$this->params['uid']
);

switch($this->params['from']){
	case 'wechat':
	$iswechat = true;
	break;
	case 'wap':
	$mdomain = str_replace('/index.php/m','',trim(C('yh_headerm_html')));
	$back = $mdomain.'/index.php?m=m&c=user&a=ucenter';
	break;
	default :
	$back = U('user/ucenter');
	break;
}


$result = $this->tbkpublisher($this->params['uid'],$res['access_token']);
if($result['code']==200){
//$bili = trim(C('yh_bili1'));
$Data=array(
'tb_refresh_token'=>$res['refresh_token'],
'tb_access_token'=>$res['access_token'],
'tb_expire_time'=>$res['expire_time'],
'tb_open_uid'=>$res['taobao_open_uid'],
'oid'=>md5($oid),
'webmaster_pid'=>$result['relation_id'],
'webmaster'=>1,
//'webmaster_rate'=>$bili?$bili:'30'
);

$is=$Mod->where(array('tb_open_uid'=>$res['taobao_open_uid']))->find();
if($is){
$this->error('请不要重复授权！',$back);	
}

$res=$Mod->where($Where)->save($Data);
if($iswechat){
$this->display('User/wechat');
exit;
}
if($res){
$this->visitor->wechatlogin($this->memberinfo['openid']); //更新用户信息
$this->Success('授权成功！',$back);

}else{
$this->error('授权失败！',$back);	
}

}else{

$this->error($result['msg'],$back);	
	
}



}else{
$this->error('授权失败！',U('user/ucenter'));
	
}

	
}


protected function tbkpublisher($uid='',$sessionKey){
vendor("taobao.taobao");
$c = new \TopClient();
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$invicode = trim(C('yh_invitecode'));

if($invicode){
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$req = new \TbkScPublisherInfoSaveRequest();
$req->setRelationFrom("1");
$req->setOfflineScene("1");
$req->setOnlineScene("1");
$req->setInviterCode($invicode);
$req->setInfoType("1");
$req->setNote($uid);
$resp = $c->execute($req, $sessionKey);
$resparr = xmlToArray($resp);
$res=$resparr['data'];
if($res['relation_id']){
		$res=array(
		'code'=>200,
		'relation_id'=>$res['relation_id'],
		'msg'=>'绑定成功'
		);
	return $res;
}

$res=array(
		'code'=>400,
		'msg'=>$resparr['sub_msg']
		);
return $res;


}

$res=array(
'code'=>400,
'msg'=>'渠道邀请码还没有设置'
);

return $res;


}


public function bindtaobao(){
$this->check_key();
$nick=I('nick','','trim');
$oid=I('oid','','trim');
$uid=I('uid','','trim');
if($uid && $oid){
$mod=M('user');
$is=$mod->where(array('oid'=>$oid))->find();
if($is){
return false;
}
	
$where=array(
'id'=>$uid
);
$data=array(
'oid'=>$oid
);

$res=$mod->where($where)->save($data);
if($res){
$json=array(
'status'=>1
);
exit(json_encode($json,JSON_UNESCAPED_UNICODE));
}
	
}

return false;
	
}


public function getrelation(){
$this->check_key();	
$openid=I('openid');
$json = S('Relation_'.$openid);
if($openid && false === $json){
$mod = new userModel();
$where=array(
'openid'=>$openid,
'webmaster'=>1
);
$res = $mod->where($where)->getField('webmaster_pid');
if($res){
	$json=array(
	'code'=>200,
	'result'=>'&relationId='.$res
	);
}else{
	$json=array(
	'code'=>400,
	'result'=>''
	);
 S('Relation_'.$openid,$json,3600);
}


}


exit(json_encode($json));
	
	
}



public function recom(){
	$this->check_key();
	$uid=I('uid', '', 'trim');
	$openid=I('openid', '', 'trim');
	$nickname = I('nickname');
	$nickname=$this->remoji($nickname);//过滤enjoy表情
	$headimgurl = I('headimgurl');
	$param = array();
	$param['wx_openid'] = $openid;
	$param['wx_unionid'] = I('unionid');
	$res=$this->_wechat_login($param);
	if(!$res){
		$mod=new userModel();
		if($uid>0){
			$where=array(
				'id'=>$uid
				);	
		$exist = $mod->field('id,username,nickname,phone,email,fuid,guid,invocode,avatar,password,score,tbname,money,webmaster,webmaster_rate,webmaster_pid,oid')->where($where)->find();	
		}
		$agentcondition=trim(C('yh_agentcondition'));
			if(0 >= $agentcondition){
			 $tbname = 1;
			}else{
			 $tbname = 0;	
			}
				$now=NOW_TIME;
				$info=array(
				'username'=>$nickname?$nickname:'匿名',
				'nickname'=>$nickname?$nickname:'匿名',
				'password'=>md5($nickname),
				'reg_ip'=>get_client_ip(),
				'avatar'=>$headimgurl?$headimgurl:'/static/tqkpc/images/noimg.png',
				'state'=>1,
				'tbname'=>$tbname,
				'status'=>1,
				'reg_time'=>$now,
				'last_time'=>$now,
				'create_time'=>$now,
				'openid'=>$openid,
				'unionid'=>I('unionid'),
				'opid'=>$openid,
				'fuid'=>$exist['id']?$exist['id']:0,
				'guid'=>$exist['fuid']?$exist['fuid']:0
				);
				$res=$mod->add($info);
				$this->reinvi($res);
				$this->invicode($res);
				if($res){
					$user = array(
					'id'=>$res,
					'openid'=>$openid
					);
					$this->CreateJdPid($user);
					
				}
				
				 exit('ok');
		
	 
		
		
	}else{
	exit('ok');
	}



return false;
	
}




public function save_order(){
$this->check_key();
$content = I('content', '', 'trim');
if(!empty($content)){
$orderlist=explode(',', $content);
if(count($orderlist)>0){
$paymentlist=array();	
foreach($orderlist as $k=>$v){
$child=explode(':', $v);
$iis=strpos(serialize($paymentlist),$child[0]);
if($iis!==false && $child[1]==12){
		
$paymentlist[$child[0]]=array(
'payStatus'=>$child[1],
'totalAlipayFeeString'=>$child[2]+$paymentlist[$child[0]]['totalAlipayFeeString'],
'feeString'=>ceil($child[3]+$paymentlist[$child[0]]['feeString']),
);
	
}else{
		
$paymentlist[$child[0]]=array(
'payStatus'=>$child[1],
'totalAlipayFeeString'=>$child[2],
'feeString'=>ceil($child[3]),
);
	
}
}


$order=M('order')->where('status=0')->select();
if($order){
foreach($order as $kk=>$vv){
$is=strpos(serialize($paymentlist),$vv['orderid']);
if($is!==false){
$child=$paymentlist[$vv['orderid']];
if($child['payStatus']==12){
$data['status']=1;
$data['integral']=$child['feeString'];
$data['price']=$child['totalAlipayFeeString'];
}else{
$data['status']=2;
$data['integral']=0;	
}


$data['up_time']=time();
$res=M('order')->where('id='.$vv['id'])->save($data);

}else{
$data['status']=2;
$data['integral']=0;	
$data['up_time']=time();
$res=M('order')->where('id='.$vv['id'])->save($data);
	
}




	
}
	
	
}

	
}

}
	
	
}


public function taobao_order_scene(){
$this->check_key();
$starttime=time()-1200;
vendor("taobao.taobao");
$c = new \TopClient();
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$req = new \TbkOrderGetRequest();
$req->setFields("tb_trade_parent_id,tb_trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk3rd_pub_id,tk3rd_site_id,tk3rd_adzone_id,relation_id,tb_trade_parent_id,tb_trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk3rd_pub_id,tk3rd_site_id,tk3rd_adzone_id,special_id,click_time");
$req->setStartTime(date('Y-m-d H:i:s',$starttime));
$req->setSpan("1200");
$req->setPageSize("100");
$req->setTkStatus(12);
$req->setOrderQueryType("create_time");	
$req->setOrderScene("2");
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$datares=$resp['results']['n_tbk_order'];
$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[2];
$val=$datares;
if($val['alipay_total_price']>0 && $val['site_id'] == $AdzoneId && $datares){
$item = array();
$count=0;
$item['orderid'] = $val['trade_id'];
$item['add_time'] = strtotime($val['create_time']);
$item['status'] = 1;
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['num_iid'];
$item['goods_title'] = $val['item_title'];
$item['goods_num'] = $val['item_num'];
$item['ad_id'] = $val['adzone_id'];
$item['income'] = $val['pub_share_pre_fee'];
$item['ad_name'] = $val['adzone_name']?$val['adzone_name']:'渠道订单';
$item['goods_rate'] = $val['total_commission_rate']*100;
$item['oid'] = substr($val['trade_id'],-6,6);
$item['leve1'] = trim(C('yh_bili1'));
$item['leve2'] = trim(C('yh_bili2'));
$item['leve3'] = trim(C('yh_bili3'));
$item['relation_id'] = $val['relation_id'];
$item['special_id'] = $val['special_id'];
if($this->_api_Scene_publish_insert($item)){
	$count++;
}

}elseif($datares){
$item = array();
$count=0;
foreach($datares as $val){
if($val['site_id'] == $AdzoneId && $val['alipay_total_price']>0){
$item['orderid'] = $val['trade_id'];
$item['add_time'] = strtotime($val['create_time']);
$item['status'] = 1;
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['num_iid'];
$item['goods_title'] = $val['item_title'];
$item['goods_num'] = $val['item_num'];
$item['ad_id'] = $val['adzone_id'];
$item['income'] = $val['pub_share_pre_fee'];
$item['ad_name'] = $val['adzone_name']?$val['adzone_name']:'渠道订单';
$item['goods_rate'] = $val['total_commission_rate']*100;
$item['oid'] = substr($val['trade_id'],-6,6);
$item['leve1'] = trim(C('yh_bili1'));
$item['leve2'] = trim(C('yh_bili2'));
$item['leve3'] = trim(C('yh_bili3'));
$item['relation_id'] = $val['relation_id'];
$item['special_id'] = $val['special_id'];

if($this->_api_Scene_publish_insert($item)){
	$count++;
}
}	
}	

}else{
$count=0;		
}

$json = array(
                'state'=>'yes',
                'msg'=>$count
 );	
exit(json_encode($json,JSON_UNESCAPED_UNICODE));	
}




public function taobao_order_pay(){
$this->check_key();
vendor("taobao.taobao");
$c = new \TopClient();
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$starttime=time()-1200;
vendor("taobao.taobao");
$c = new \TopClient();
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$req = new \TbkOrderGetRequest();
$req->setFields("tb_trade_parent_id,tb_trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk3rd_pub_id,tk3rd_site_id,tk3rd_adzone_id,relation_id,tb_trade_parent_id,tb_trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk3rd_pub_id,tk3rd_site_id,tk3rd_adzone_id,special_id,click_time");
$req->setStartTime(date('Y-m-d H:i:s',$starttime));
$req->setSpan("1200");
$req->setPageSize("100");
$req->setTkStatus(12);
$req->setOrderQueryType("create_time");	
$req->setOrderScene("1");
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$datares=$resp['results']['n_tbk_order'];
$apppid=trim(C('yh_taobao_pid'));
$apppid=explode('_', $apppid);
$AdzoneId=$apppid[2];
$val=$datares;
if($val['alipay_total_price']>0 && $val['site_id'] == $AdzoneId && $datares){
$item = array();
$count=0;
$item['orderid'] = $val['trade_id'];
$item['add_time'] = strtotime($val['create_time']);
$item['status'] = 1;
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['num_iid'];
$item['goods_title'] = $val['item_title'];
$item['goods_num'] = $val['item_num'];
$item['ad_id'] = $val['adzone_id'];
$item['income'] = $val['pub_share_pre_fee'];
$item['ad_name'] = $val['adzone_name'];
$item['tb_deposit_time'] = $val['tb_deposit_time'];
$item['goods_rate'] = $val['total_commission_rate']*100;
$item['oid'] = substr($val['trade_id'],-6,6);
$item['leve1'] = trim(C('yh_bili1'));
$item['leve2'] = trim(C('yh_bili2'));
$item['leve3'] = trim(C('yh_bili3'));
$item['relation_id'] = $val['relation_id'];
$item['special_id'] = $val['special_id'];
if($this->_api_yh_publish_insert($item)){
	$count++;
}

}elseif($datares){
$item = array();
$count=0;
foreach($datares as $val){
if($val['site_id'] == $AdzoneId && $val['alipay_total_price']>0){
$item['orderid'] = $val['trade_id'];
$item['add_time'] = strtotime($val['create_time']);
$item['status'] = 1;
$item['price'] = $val['alipay_total_price'];
$item['goods_iid'] = $val['num_iid'];
$item['goods_title'] = $val['item_title'];
$item['goods_num'] = $val['item_num'];
$item['ad_id'] = $val['adzone_id'];
$item['income'] = $val['pub_share_pre_fee'];
$item['ad_name'] = $val['adzone_name'];
$item['goods_rate'] = $val['total_commission_rate']*100;
$item['oid'] = substr($val['trade_id'],-6,6);
$item['leve1'] = trim(C('yh_bili1'));
$item['tb_deposit_time'] = $val['tb_deposit_time'];
$item['leve2'] = trim(C('yh_bili2'));
$item['leve3'] = trim(C('yh_bili3'));
$item['relation_id'] = $val['relation_id'];
$item['special_id'] = $val['special_id'];

if($this->_api_yh_publish_insert($item)){
	$count++;
}
}
}	

}else{
$count=0;		
}

$json = array(
                'state'=>'yes',
                'msg'=>$count
 );	
exit(json_encode($json,JSON_UNESCAPED_UNICODE));	
}





public function tb_order_settle(){
$this->check_key();
$starttime=time()-1200;
vendor("taobao.taobao");
$c = new \TopClient();
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$c->appkey = $appkey;
$c->secretKey = $appsecret;	
$req = new \TbkOrderDetailsGetRequest();
$req->setQueryType("3");
$req->setPageSize("100");
$req->setTkStatus("3");
$req->setStartTime(date('Y-m-d H:i:s',$starttime));
$req->setEndTime(date('Y-m-d H:i:s',time()));
$req->setOrderScene("1");
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$datares=$resp['data']['results']['publisher_order_dto'];
$val=$datares;
if($datares && $val['trade_id'] && $val['alipay_total_price']>0){
$item = array();
$count=0;
$item['orderid'] = $val['trade_id'];
$item['status'] = 3;
$item['price'] = $val['alipay_total_price'];
$item['income'] = $val['pub_share_fee']; //新增
$item['goods_iid'] = $val['item_id'];
$item['up_time'] = strtotime($val['tk_earning_time']);
if($this->api_yh_publish_update($item)){
	 $count++;
 }

}elseif($datares){
$item = array();
$count=0;
foreach($datares as $val){	
$item['orderid'] = $val['trade_id'];
$item['status'] = 3;
$item['price'] = $val['alipay_total_price'];
$item['income'] = $val['pub_share_fee']; //新增
$item['goods_iid'] = $val['item_id'];
$item['up_time'] = strtotime($val['tk_earning_time']);
if($this->api_yh_publish_update($item)){
	 $count++;
 }		
 
}

}else{
$count=0;		
}

$json = array(
                'state'=>'yes',
                'msg'=>$count
 );	
exit(json_encode($json,JSON_UNESCAPED_UNICODE));
	
}





public function taobao_order_settle(){
$this->check_key();
$starttime=time()-1200;
vendor("taobao.taobao");
$c = new \TopClient();
$appkey=trim(C('yh_taobao_appkey'));
$appsecret=trim(C('yh_taobao_appsecret'));
$c->appkey = $appkey;
$c->secretKey = $appsecret;
$req = new \TbkOrderGetRequest();
$req->setFields("tb_trade_parent_id,tb_trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk3rd_pub_id,tk3rd_site_id,tk3rd_adzone_id,relation_id,tb_trade_parent_id,tb_trade_id,num_iid,item_title,item_num,price,pay_price,seller_nick,seller_shop_title,commission,commission_rate,unid,create_time,earning_time,tk3rd_pub_id,tk3rd_site_id,tk3rd_adzone_id,special_id,click_time");
$req->setStartTime(date('Y-m-d H:i:s',$starttime));
$req->setSpan("1200");
$req->setPageSize("100");
$req->setTkStatus(3);
$req->setOrderQueryType("settle_time");	
$resp = $c->execute($req);
$resp = json_decode(json_encode($resp), true);
$datares=$resp['results']['n_tbk_order'];
$val=$datares;
if($datares && $val['trade_id'] && $val['alipay_total_price']>0){
$item = array();
$count=0;
$item['orderid'] = $val['trade_id'];
$item['status'] = 3;
$item['price'] = $val['alipay_total_price'];
$item['income'] = $val['pub_share_fee']; //新增
$item['goods_iid'] = $val['num_iid'];
$item['up_time'] = strtotime($val['earning_time']);
if($this->api_yh_publish_update($item)){
	 $count++;
 }

}elseif($datares){
$item = array();
$count=0;

foreach($datares as $val){	
$item['orderid'] = $val['trade_id'];
$item['status'] = 3;
$item['price'] = $val['alipay_total_price'];
$item['income'] = $val['pub_share_fee']; //新增
$item['goods_iid'] = $val['num_iid'];
$item['up_time'] = strtotime($val['earning_time']);
if($this->api_yh_publish_update($item)){
	 $count++;
 }		
 
}

}else{
$count=0;		
}

$json = array(
                'state'=>'yes',
                'msg'=>$count
 );	
exit(json_encode($json,JSON_UNESCAPED_UNICODE));	
}



public function save_order_jiesuan(){
$this->check_key();
$content = I('content', '', 'trim');
if(!empty($content)){

$orderlist=explode(',', $content);
if(count($orderlist)>0){
$paymentlist=array();	
foreach($orderlist as $k=>$v){
$part=explode(':', $v);
$paymentlist[$part[0]]=array(
'payStatus'=>$part[1]
);
	
}

$order=M('order')->where('status=1')->select();
if($order){
foreach($order as $kk=>$vv){
$is=strpos(serialize($paymentlist),$vv['orderid']);
if($is!==false){
$child=$paymentlist[$vv['orderid']];
if($child['payStatus']==3){
$data['status']=3;
$data['up_time']=time();
$res=M('order')->where('id='.$vv['id'])->save($data);

if($vv['integral']>0){

M('user')->where('id='.$vv['uid'])->save(array(
 'score'=>array('exp','score+'.$vv['integral'])
));

}




}
}

$now=time();
if(($now-$vv['add_time'])>2592000 && $vv['status']==1){
$data['status']=2;
$data['up_time']=time();
$res=M('order')->where('id='.$vv['id'])->save($data);	
}
	
}
	
	
}

	
}

}
	
}



	
public function reg(){
$this->check_key();
$U=M('user');
$openid = I('openid', '', 'trim');
if(!empty($openid) && strlen($openid)>20){
$where=array(
'openid'=>$openid,
);
$exit_openid=$U->where($where)->count();
if($exit_openid<=0){
$data=array(
'username'=>'wx_'.substr($openid,20,6),
'nickname'=>'wx_'.substr($openid,20,6),
'password'=>md5(substr($openid,20,6)),
'reg_ip'=>get_client_ip(),
'avatar'=>'/Public/static/tuiquanke/images/noimg.png',
'state'=>1,
'status'=>1,
'reg_time'=>time(),
'last_time'=>time(),
'create_time'=>time(),
'openid'=>$openid,
);
$res=$U->add($data);
}

if($res){
$json = array(
                'state'=>'yes',
                'msg'=>'注册成功'
 );	
}else{
$json = array(
                'state'=>'no',
                'msg'=>'注册失败'
 );	
}


exit(json_encode($json,JSON_UNESCAPED_UNICODE));
	
}
	
}

public function zhibo_push(){
$num = I('num', 1);
        $key = I('key', '', 'trim');
        if(!$key || $key != $this->accessKey){
            $json = array(
                'data'=>array(),
                'result'=>array(),
                'state'=>'no',
                'msg'=>'通信密钥错误'
            );
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
        }
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
$where['quan']=array('gt',30);
$list=$mod->where($where)->field('num_iid,add_time,title,pic_url,id,price,coupon_price,quan')->limit(30)->select();

$count=count($list);
  if($count>0){
            foreach ($list as $key => $val) {
             $raw[] = array(
			 'num_iid'=>$val['num_iid'],
			 'price'=>$val['price'],
			 'coupon_price'=>$val['coupon_price'],
			 'coupon'=>$val['quan'],
			 'title'=>$val['title'],
			  'id'=>$val['id'],
			  'pic_url'=>$val['pic_url']
			 ); 	
			}
		 $startId = $val['id'];
	     file_put_contents($file, $startId);
			$json = array(
                'total'=>$count,
                'data'=>$raw,
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
 
 exit(json_encode($json,JSON_UNESCAPED_UNICODE));	
 
 
 	
}
	


    
    private function _ajax_yh_publish_insert($item)
    {
        $result = D('items')->ajax_yh_publish($item);
        return $result;
    }
	
	 private function _ajax_pdd_publish_insert($item)
    {
        $result = D('pdditems')->ajax_yh_publish($item);
        return $result;
    }
    
    public function get_items()
    {
        $num = I('num', 20);
        
        $key = I('key', '', 'trim');
        
        if(!$key || $key != $this->accessKey){
            $json = array(
                'data'=>array(),
                'result'=>array(),
                'state'=>'no',
                'msg'=>'通信密钥错误'
            );
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
        }
        
        //$this->items_caiji($num);
        
        $model = M('items');
    
        $where = array(
            'status'=>'underway',
            'tuisong'=>array('neq', '1'),
            'pass'=>1,
            'ems'=>1
        );
        
        $list = $model->field('num_iid,commission_rate,tk_commission_rate,Quan_id')->where($where)->order('rand()')->limit($num)->select();
    
        if(count($list) > 0){
    
            $result = array();
    
            foreach ($list as $k=>$item)
            {
                $result[$k]['num_iid'] = $item['num_iid'];
                $result[$k]['event_rate'] = intval($item['commission_rate']/100);
                $result[$k]['tk_rate'] = intval($item['tk_commission_rate']/100);
                $result[$k]['quan_id'] = $item['Quan_id'];
            }
    
            $json = array(
                'data'=>array(),
                'total'=>count($list),
                'result'=>$result,
                'state'=>'yes',
                'msg'=>'正常'
            );
            
            $json['taobao_appkey'] = C('yh_taobao_appkey');
            $json['taobao_appsecret'] = C('yh_taobao_appsecret');
        }
        else{
            $json = array(
                'data'=>array(),
                'total'=>0,
                'result'=>array(),
                'state'=>'no',
                'msg'=>'商品数量不足'
            );
        }
        
        exit(json_encode($json,JSON_UNESCAPED_UNICODE));
    }
    
    public function save_items()
    {
        $content = stripslashes(I('data', '', 'trim'));
		$key=I('key', '', 'trim');
	    $content= trim($content,chr(239).chr(187).chr(191));
		F('data',$content);
        $json = json_decode($content,true);
        if(!$key || $key != $this->accessKey){
            $json = array(
                'data'=>array(),
                'result'=>array(),
                'state'=>'key',
                'msg'=>'通信密钥错误'
            );
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
        }
        $result = $json['datalist'];
		
        $model = M('items');
        
        $error = '';
    
        foreach ($result as $item){
            $where = array(
                'num_iid'=>$item['num_id']
            );
            if($item['state'] == 'yes'){
                $data = array(
                    'quanshorturl'=>$item['shorturl'],
                    'quanurl'=>$item['quanurl'],
                    'quankouling'=>$item['kouling'],
                    'click_url'=>$item['shorturl'],
                    'tuisong'=>1
                );
				

                $model->where($where)->save($data);
				//Log::write('调试的SQL：'.$model->getLastSql(), Log::SQL);
				
                if($model->getError()){
                    $error = $model->getError();
                }
                elseif($model->getDbError()){
                    $error = $model->getDbError();
                }
            }
            else{
                $model->where($where)->delete();
            }
        }
        
        $json = array(
            'data'=>array(),
            'result'=>array(),
            'state'=>'yes',
            'msg'=>$error
        );
        
        exit(json_encode($json,JSON_UNESCAPED_UNICODE));
    }

public function serverline(){
$this->check_key();
$data=I('data','','trim');
$openid=I('openid','','trim');
if(!empty($openid)){
$mod=M('setting');
$where=array(
'name'=>'app_kehuduan'
);
$datat=array(
'data'=>$openid
);
$mod->where($where)->save($datat);
$file=RUNTIME_PATH.'/Data/setting.php';
@unlink($file);
}

if(!empty($data)){
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/Runtime/Data/tqkapi/api.php';
$ret=opcache_invalidate($dir,TRUE);	
}
F('tqkapi/api', $data);
}
}

public function zhibo_save_list(){
$this->check_key();
$num_iid=I('id','','trim');
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/runtime/Data/zhibo/disable_num_iids.php';
$ret=opcache_invalidate($dir,TRUE);
}
$disable_num_iids = F('zhibo/disable_num_iids');
if($num_iid){
$item=M('items')->where('id='.$num_iid)->field('num_iid,title,id,pic_url,price,coupon_price,quan')->find();

if($item){
		 $data=array(
		 'id'=>$item['id'],
		 'action'=>'zhibo',
		 'title'=>$item['title'],
		 'pic_url'=>$item['pic_url'],
		 'price'=>$item['price'],
		 'coupon'=>$item['quan'],
		 'coupon_price'=>$item['coupon_price'],
		 'key'=>$this->accessKey
		 );
		 $url=$this->tqkapi.'/push';
         $rs= $this->_curl($url,$data, true);

if(!$disable_num_iids){
    $disable_num_iids = array();
 }
$is=strpos(serialize($disable_num_iids),$item['num_iid']);
if(empty($is)){
                    $disable_num_iids[] =array(
                    //'num_iid'=>$item['num_iid'],
                    'title'=>$item['title'],
                    'id'=>$item['id'],
                    'price'=>$item['price'],
                    'coupon_price'=>$item['coupon_price'],
                    'coupon'=>$item['quan'],
                    'push_time'=>time(),
                   // 'domain'=>str_replace('/index.php/m','',C('yh_headerm_html')),
                    'pic_url'=>$item['pic_url']
					);
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/runtime/Data/zhibo/disable_num_iids.php';
$ret=opcache_invalidate($dir,TRUE);
}
 F('zhibo/disable_num_iids', $disable_num_iids);
}	
	
}

}

if(count($disable_num_iids)>100){
F('zhibo/disable_num_iids',NULL);
}

	
}


public function zhibo_list(){
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/runtime/Data/zhibo/disable_num_iids.php';
$ret=opcache_invalidate($dir,TRUE);
}
$disable_num_iids = F('zhibo/disable_num_iids');
if(!$disable_num_iids){
            $disable_num_iids = array();
}
$this->_api($disable_num_iids, 'yes');	
}

public function save_yxhj(){
$this->check_key();
$coupon_url=I('coupon','','trim');
$num_iid=I('num_iid','','trim');
if(!empty($num_iid) && !empty($coupon_url)){
$M=M('items');
$item=$M->where('num_iid='.$num_iid)->field('Quan_id,pic_url,title')->find();
if(!empty($item['Quan_id'])){
//$preg = '/e=(.+?)&pid/is';
//preg_match($preg, $coupon_url, $allhtml);
$quanurl='https://uland.taobao.com/coupon/edetail?e='.urlencode($coupon_url).'&activityId='.$item['Quan_id'].'&itemId='.$num_iid.'&pid='.C('yh_taobao_pid').'&af=1';
$kouling=kouling($item['pic_url'].'_400x400',$item['title'],$quanurl);
$data=array(
'tk'=>1,
'quankouling'=>$kouling,
'quanurl'=>$quanurl,
'que'=>1
);
$res=$M->where('num_iid='.$num_iid)->save($data);
}
}
exit();
}

public function disabled()
 {
 	    $key=I('key', '', 'trim');
        if(!$key || $key != $this->accessKey){
            $json = array(
                'state'=>'key',
                'msg'=>'通信密钥错误'
            );
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
        }
if(function_exists('opcache_invalidate')){
$basedir = $_SERVER['DOCUMENT_ROOT']; 
$dir=$basedir.'/data/runtime/Data/coupon/disable_num_iids.php';
$ret=opcache_invalidate($dir,TRUE);
}
 $disable_num_iids = F('coupon/disable_num_iids');
		
        if(!$disable_num_iids){
            $disable_num_iids = array();
        }
		 F('coupon/disable_num_iids',null);
		 $this->_api($disable_num_iids, 'yes'); 
      
}
    
    public function del_items()
    {
        $key=I('key', '', 'trim');
        
        if(!$key || $key != $this->accessKey){
            $json = array(
                'data'=>array(),
                'result'=>array(),
                'state'=>'key',
                'msg'=>'通信密钥错误'
            );
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
        } 
		
        $itemId = I('itemId', '', 'trim');
        
        if(!is_array($itemId)){
            $itemId = array_filter(explode(',', $itemId));
        }
        
        if(count($itemId) == 0){
            $json = array(
                'data'=>array(),
                'result'=>array(),
                'state'=>'no',
                'msg'=>'商品ID不能为空'
            );
            
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
        }
        
        $model = M('items');
        
        $where = array(
            'num_iid'=>array('in', $itemId)
        );
        
        $model->where($where)->delete();
        
        $json = array(
            'data'=>array(),
            'result'=>array(),
            'state'=>'yes',
            'msg'=>''
        );
        
        exit(json_encode($json,JSON_UNESCAPED_UNICODE));
    }


public function change_items()
    {
        $key=I('key', '', 'trim');
        if(!$key || $key != $this->accessKey){
            $json = array(
                'data'=>array(),
                'result'=>array(),
                'state'=>'key',
                'msg'=>'通信密钥错误'
            );
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
        }
        $itemId = I('itemId', '', 'trim');
        if(!is_array($itemId)){
            $itemId = array_filter(explode(',', $itemId));
        }

        $itemId=implode(',',$itemId);

        if(count($itemId) == 0){
            $json = array(
                'data'=>array(),
                'result'=>array(),
                'state'=>'no',
                'msg'=>'商品ID不能为空'
            );
            
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
        }
        
        $model = M('items');
//      $where = array(
//          'num_iid'=>array('in', $itemId)
//      );
		$data=array(
		'ding'=>1,
		'last_time'=>time()
		);
        //$model->where('to_days(last_time) <> to_days(now()) and num_iid in('.$itemId.')')->save($data);
        $json = array(
            'data'=>array(),
            'result'=>array(),
            'state'=>'yes',
            'msg'=>''
         );
        exit(json_encode($json,JSON_UNESCAPED_UNICODE));
    }

  protected function _api($data = array(), $state = 'yes', $msg = '')
    {
        $result = array(
            'data'=>$data,
            'state'=>$state,
            'msg'=>$msg
        );
        
        exit(json_encode($result));
    }
protected  function check_key(){
 $json = array(
         'state'=>'no',
          'msg'=>'通行密钥不正确'
         );

$key = I('key', '', 'trim');
if(!$key || $key!=$this->accessKey){
exit(json_encode($json,JSON_UNESCAPED_UNICODE));
}	
 }


}


