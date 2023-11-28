<?php
namespace Common\Action;
use Think\Action;
use Common\Api\Weixin;
class FuncAction extends Action
{


 private function Tohttps($url){
if(substr($url,0,2)=='//'){ 
$url='https:'.$url;
};	
return $url;	
}

	
protected function receipt(){
$ComputingTime = abs(C('yh_ComputingTime'))*86400;
$now=NOW_TIME;
$usermod = M('user');
$ordermod = M('order');
$cashmod = M('usercash');
$where['_string']='('.($now-$ComputingTime).'>up_time and status = 3 and settle = 0) and (uid>0 or fuid is not null or guid is not null)';
$field ='id,uid,goods_title,fuid,guid,income,leve1,leve2,leve3,up_time,orderid,relation_id';
$list=$ordermod->field($field)->where($where)->limit(100)->select();
if($list){
$count =0;
foreach($list as $k=>$v){
	//if($v['relation_id']){
	// $this->SettleRelation($v,$usermod,$ordermod,$cashmod);
	//}else{
	 $this->SettleUser($v,$usermod,$ordermod,$cashmod);
	//}
$count ++;
		
}

return $count;
		
}

return false;

}

protected function FilterWords($string){
 $keywords = C('yh_basklist');
 if($keywords && preg_match("/$keywords/i",$string)){
  return true;
 }
 return false;
}


protected function SettleJd($item,$usermod,$ordermod,$cashmod){
  if($item['uid'] && $item['uid']>0){
	    $money = Orebate1(array('price'=>$item['actualFee'],'leve'=>$item['leve1']));
		$datacash=array(
		'create_time'=>NOW_TIME,
		'money'=>$money,
		'amount'=>$item['estimateCosPrice'],
		'remark'=>'京东订单'.$item['orderId'],
		'status'=>1,
		'uid'=>$item['uid'],
		'type'=>3
		);
	$ret=$cashmod->add($datacash);
		if($money>0){
		$usermod->where(array('id'=>$item['uid']))->setInc('money',$money);
		$data = array(
				'first'=>'【京东】'.$item['skuName'],
				'keyword2'=>$money,
				'uid'=>$item['uid']
				);
				
				Weixin::settlement($data);
		}
	}

  if($item['fuid'] && $item['fuid']>0){
	    $money2 = Orebate2(array('price'=>$item['actualFee'],'leve'=>$item['leve2']));
		$datacash2=array(
		'create_time'=>NOW_TIME,
		'money'=>$money2,
		'amount'=>$item['estimateCosPrice'],
		'remark'=>'京东订单'.$item['orderId'],
		'status'=>1,
		'uid'=>$item['fuid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash2);
		if($money2>0){
		$usermod->where(array('id'=>$item['fuid']))->setInc('money',$money2);
		$data = array(
				'first'=>'【京东】'.$item['skuName'],
				'keyword2'=>$money2,
				'uid'=>$item['fuid']
				);
				
				Weixin::settlement($data);
		}
	}
	
	
  if($item['guid'] && $item['guid']>0){
	    $money3 = Orebate2(array('price'=>$item['actualFee'],'leve'=>$item['leve3']));
		$datacash3=array(
		'create_time'=>NOW_TIME,
		'money'=>$money3,
		'amount'=>$item['order_amount'],
		'remark'=>'京东订单'.$item['orderId'],
		'status'=>1,
		'uid'=>$item['guid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash3);
		if($money2>0){
		$usermod->where(array('id'=>$item['guid']))->setInc('money',$money3);
		$data = array(
				'first'=>'【京东】'.$item['skuName'],
				'keyword2'=>$money3,
				'uid'=>$item['guid']
				);
				
				Weixin::settlement($data);
		}
	}
	
	
	
$ordermod->where(array('id'=>$item['id']))->setField('settle',1);	
	
}


protected function SettlePdd($item,$usermod,$ordermod,$cashmod){
  if($item['uid'] && $item['uid']>0){
	    $money = Orebate1(array('price'=>$item['promotion_amount'],'leve'=>$item['leve1']));
		$datacash=array(
		'create_time'=>NOW_TIME,
		'money'=>$money,
		'amount'=>$item['order_amount'],
		'remark'=>'多多订单'.$item['order_sn'],
		'status'=>1,
		'uid'=>$item['uid'],
		'type'=>3
		);
	$ret=$cashmod->add($datacash);
		if($money>0){
		$usermod->where(array('id'=>$item['uid']))->setInc('money',$money);
		$data = array(
				'first'=>'【拼多多】'.$item['goods_name'],
				'keyword2'=>$money,
				'uid'=>$item['uid']
				);
				
				Weixin::settlement($data);
		}
	}

  if($item['fuid'] && $item['fuid']>0){
	    $money2 = Orebate2(array('price'=>$item['promotion_amount'],'leve'=>$item['leve2']));
		$datacash2=array(
		'create_time'=>NOW_TIME,
		'money'=>$money2,
		'amount'=>$item['order_amount'],
		'remark'=>'多多订单'.$item['order_sn'],
		'status'=>1,
		'uid'=>$item['fuid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash2);
		if($money2>0){
		$usermod->where(array('id'=>$item['fuid']))->setInc('money',$money2);
		$data = array(
				'first'=>'【拼多多】'.$item['goods_name'],
				'keyword2'=>$money2,
				'uid'=>$item['fuid']
				);
				
				Weixin::settlement($data);
		}
	}
	
	
  if($item['guid'] && $item['guid']>0){
	    $money3 = Orebate2(array('price'=>$item['promotion_amount'],'leve'=>$item['leve3']));
		$datacash3=array(
		'create_time'=>NOW_TIME,
		'money'=>$money3,
		'amount'=>$item['order_amount'],
		'remark'=>'多多订单'.$item['order_sn'],
		'status'=>1,
		'uid'=>$item['guid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash3);
		if($money2>0){
		$usermod->where(array('id'=>$item['guid']))->setInc('money',$money3);
		$data = array(
				'first'=>'【拼多多】'.$item['goods_name'],
				'keyword2'=>$money3,
				'uid'=>$item['guid']
				);
				
				Weixin::settlement($data);
		}
	}
	
	
	
$ordermod->where(array('id'=>$item['id']))->setField('settle',1);	
	
}

protected function RelationInviterCode($userinfo){
$url = 'https://mos.m.taobao.com/inviter/register?inviterCode='.trim(C('yh_invitecode')).'&src=pub&app=common&rtag='.$userinfo['id'];
$inviterCode=kouling('https://gw.alicdn.com/tfs/TB1lOScxDtYBeNjy1XdXXXXyVXa-540-260.png','渠道备案完成后，可为您进行商品/店铺和更多物料的推广',$url);
return $inviterCode;	
}


protected function SettleDuomai($item,$usermod,$ordermod,$cashmod){
	
  if($item['uid'] && $item['uid']>0){
	    $money = Orebate1(array('price'=>$item['order_commission'],'leve'=>$item['leve1']));
		$datacash=array(
		'create_time'=>NOW_TIME,
		'money'=>$money,
		'amount'=>$item['orders_price'],
		'remark'=>'其它订单'.$item['order_sn'],
		'status'=>1,
		'uid'=>$item['uid'],
		'type'=>3
		);
	$ret=$cashmod->add($datacash);
		if($money>0){
		$usermod->where(array('id'=>$item['uid']))->setInc('money',$money);
		
		$data = array(
		'first'=>'【其它】'.$item['goods_name'],
		'keyword2'=>$money,
		'uid'=>$item['uid']
		);
		
		Weixin::settlement($data);
		
		}
	}

  if($item['fuid'] && $item['fuid']>0){
	    $money2 = Orebate2(array('price'=>$item['order_commission'],'leve'=>$item['leve2']));
		$datacash2=array(
		'create_time'=>NOW_TIME,
		'money'=>$money2,
		'amount'=>$item['orders_price'],
		'remark'=>'其它订单'.$item['order_sn'],
		'status'=>1,
		'uid'=>$item['fuid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash2);
		if($money2>0){
		$usermod->where(array('id'=>$item['fuid']))->setInc('money',$money2);
		
		$data = array(
		'first'=>'【其它】'.$item['goods_name'],
		'keyword2'=>$money2,
		'uid'=>$item['fuid']
		);
		
		Weixin::settlement($data);
		
		}
	}
	
	
  if($item['guid'] && $item['guid']>0){
	    $money3 = Orebate2(array('price'=>$item['order_commission'],'leve'=>$item['leve3']));
		$datacash3=array(
		'create_time'=>NOW_TIME,
		'money'=>$money3,
		'amount'=>$item['orders_price'],
		'remark'=>'其它订单'.$item['order_sn'],
		'status'=>1,
		'uid'=>$item['guid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash3);
		if($money2>0){
		$usermod->where(array('id'=>$item['guid']))->setInc('money',$money3);
		
		$data = array(
		'first'=>'【其它】'.$item['goods_name'],
		'keyword2'=>$money3,
		'uid'=>$item['guid']
		);
		
		Weixin::settlement($data);
		
		}
	}
	
	
	
$ordermod->where(array('id'=>$item['id']))->setField('settle',1);
	
}

protected function SettleMeituan($item,$usermod,$ordermod,$cashmod){
	
  if($item['uid'] && $item['uid']>0){
	    $money = Orebate1(array('price'=>$item['profit'],'leve'=>$item['leve1']));
		$datacash=array(
		'create_time'=>NOW_TIME,
		'money'=>$money,
		'amount'=>$item['payprice'],
		'remark'=>'美团订单'.$item['orderid'],
		'status'=>1,
		'uid'=>$item['uid'],
		'type'=>3
		);
	$ret=$cashmod->add($datacash);
		if($money>0){
		$usermod->where(array('id'=>$item['uid']))->setInc('money',$money);
		
		$data = array(
		'first'=>'【美团】'.$item['smstitle'],
		'keyword2'=>$money,
		'uid'=>$item['uid']
		);
		
		Weixin::settlement($data);
		
		}
	}

  if($item['fuid'] && $item['fuid']>0){
	    $money2 = Orebate2(array('price'=>$item['profit'],'leve'=>$item['leve2']));
		$datacash2=array(
		'create_time'=>NOW_TIME,
		'money'=>$money2,
		'amount'=>$item['payprice'],
		'remark'=>'美团订单'.$item['orderid'],
		'status'=>1,
		'uid'=>$item['fuid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash2);
		if($money2>0){
		$usermod->where(array('id'=>$item['fuid']))->setInc('money',$money2);
		
		$data = array(
		'first'=>'【美团】'.$item['smstitle'],
		'keyword2'=>$money2,
		'uid'=>$item['fuid']
		);
		
		Weixin::settlement($data);
		
		}
	}
	
	
  if($item['guid'] && $item['guid']>0){
	    $money3 = Orebate2(array('price'=>$item['profit'],'leve'=>$item['leve3']));
		$datacash3=array(
		'create_time'=>NOW_TIME,
		'money'=>$money3,
		'amount'=>$item['payprice'],
		'remark'=>'美团订单'.$item['orderid'],
		'status'=>1,
		'uid'=>$item['guid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash3);
		if($money2>0){
		$usermod->where(array('id'=>$item['guid']))->setInc('money',$money3);
		
		$data = array(
		'first'=>'【美团】'.$item['smstitle'],
		'keyword2'=>$money3,
		'uid'=>$item['guid']
		);
		
		Weixin::settlement($data);
		
		}
	}
	
	
	
$ordermod->where(array('id'=>$item['id']))->setField('settle',1);
	
}


protected function SettleUser($item,$usermod,$ordermod,$cashmod){
	
  if($item['uid'] && $item['uid']>0){
	    $money = Orebate1(array('price'=>$item['income'],'leve'=>$item['leve1']));
		$datacash=array(
		'create_time'=>NOW_TIME,
		'money'=>$money,
		'amount'=>$item['price'],
		'remark'=>'淘宝订单'.$item['orderid'],
		'status'=>1,
		'uid'=>$item['uid'],
		'type'=>3
		);
		$ret=$cashmod->add($datacash);
		if($money>0){
		$usermod->where(array('id'=>$item['uid']))->setInc('money',$money);
		
		
		$data = array(
		'first'=>'【淘宝】'.$item['goods_title'],
		'keyword2'=>$money,
		'uid'=>$item['uid']
		);
		
		Weixin::settlement($data);
		
		}
		
	}

  if($item['fuid'] && $item['fuid']>0){
	    $money2 = Orebate2(array('price'=>$item['income'],'leve'=>$item['leve2']));
		$datacash2=array(
		'create_time'=>NOW_TIME,
		'money'=>$money2,
		'amount'=>$item['price'],
		'remark'=>'淘宝订单'.$item['orderid'],
		'status'=>1,
		'uid'=>$item['fuid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash2);
		if($money2>0){
		$usermod->where(array('id'=>$item['fuid']))->setInc('money',$money2);
	
		
		$data = array(
		'first'=>'【淘宝】'.$item['goods_title'],
		'keyword2'=>$money2,
		'uid'=>$item['fuid']
		);
		
		Weixin::settlement($data);
		
			}
		
	}
	
	
  if($item['guid'] && $item['guid']>0){
	    $money3 = Orebate2(array('price'=>$item['income'],'leve'=>$item['leve3']));
		$datacash3=array(
		'create_time'=>NOW_TIME,
		'money'=>$money3,
		'amount'=>$item['price'],
		'remark'=>'淘宝订单'.$item['orderid'],
		'status'=>1,
		'uid'=>$item['guid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash3);
		if($money2>0){
		$usermod->where(array('id'=>$item['guid']))->setInc('money',$money3);
		
		$data = array(
		'first'=>'【淘宝】'.$item['goods_title'],
		'keyword2'=>$money3,
		'uid'=>$item['guid']
		);
		
		Weixin::settlement($data);
		
		}
		
	}
	
	
	
$ordermod->where(array('id'=>$item['id']))->setField('settle',1);
	
}

protected function GetTags($title, $num=5){
        vendor('pscws4.pscws4', '', '.class.php');
        $pscws = new \PSCWS4();
        $pscws->set_dict(TQK_DATA_PATH . 'scws/dict.utf8.xdb');
        $pscws->set_rule(TQK_DATA_PATH . 'scws/rules.utf8.ini');
        $pscws->set_ignore(true);
        $pscws->send_text($title);
        $words = $pscws->get_tops($num);
        $pscws->close();
        $tags = array();
        foreach ($words as $val) {
            $tags[] = $val['word'];
        }
        return $tags;
    }

protected function SettleRelation($item,$usermod,$ordermod,$cashmod){
$money = round($item['income']*($item['leve1']/100),2);
$datacash=array(
		'create_time'=>NOW_TIME,
		'money'=>$money,
		'remark'=>'订单'.$item['orderid'],
		'status'=>1,
		'uid'=>$item['userid_f'],
		'type'=>3
		);

	$ret=$cashmod->add($datacash);
	if($money>0){
	$usermod->where(array('id'=>$item['userid_f']))->setInc('money',$money);
	}
	
  if($item['userid_fuid'] && $item['userid_fuid']>0){
	    $money2 = Orebate2(array('price'=>$item['income'],'leve'=>$item['leve2']));
		$datacash2=array(
		'create_time'=>NOW_TIME,
		'money'=>$money2,
		'remark'=>'订单'.$item['orderid'],
		'status'=>1,
		'uid'=>$item['userid_fuid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash2);
		if($money2>0){
		$usermod->where(array('id'=>$item['userid_fuid']))->setInc('money',$money2);
		}
	}
	
	
	  if($item['userid_guid'] && $item['userid_guid']>0){
	    $money3 = Orebate2(array('price'=>$item['income'],'leve'=>$item['leve3']));
		$datacash3=array(
		'create_time'=>NOW_TIME,
		'money'=>$money3,
		'remark'=>'订单'.$item['orderid'],
		'status'=>1,
		'uid'=>$item['userid_guid'],
		'type'=>3
		);
	   $ret=$cashmod->add($datacash3);
		if($money3>0){
		$usermod->where(array('id'=>$item['userid_guid']))->setInc('money',$money3);
		}
	}
	
	
	
	$ordermod->where(array('id'=>$item['id']))->setField('settle',1);
}
	
	/**
     * 添加邮件到队列
     */
    protected function _mail_queue($to, $subject, $body, $priority = 1) {
        $to_emails = is_array($to) ? $to : array($to);
        $mails = array();
        $time = time();
        foreach ($to_emails as $_email) {
            $mails[] = array(
                'mail_to' => $_email,
                'mail_subject' => $subject,
                'mail_body' => $body,
                'priority' => $priority,
                'add_time' => $time,
                'lock_expiry' => $time,
            );
        }
        M('mail_queue')->addAll($mails);
        //异步发送邮件
        $this->send_mail(true);
    }


protected function send_sms($data=array()){
$appid=trim(C('yh_sms_appid'));
$appkey=trim(C('yh_sms_appkey'));
$smsSign=trim(C('yh_sms_sign'));
$phoneNumbers = [$data['phone']];
$phone = $data['phone'];
$code = $data['code'];
$tpl = $data['temp_id'];
if(C('yh_sms_status') == 2){
	
	if($data['webname']){
	$code = $data['webname'];
	}else{
	$code = $data['code'];
	}
	
	
	try {
       date_default_timezone_set('Asia/Shanghai');
        //这个是你下面实例化的类
        Vendor('Alidayu.Aliyunsms','','.class.php');
        $demo = new \SmsDemo(
                "$appid", // AccessKeyId
                "$appkey" // AccessKeySecret
        );
        $response = $demo->sendSms(
                "$smsSign", // 短信签名
                "$tpl", // 短信模板编号
                "$phone", // 短信接收者
                Array(  // 短信模板中字段的值
                        "code"=>"$code",
                )
        );
        return true;
	} catch(\Exception $e) {
	    return false;
	}
		
}else{

try {
    $ssender = new \Common\Tqklib\sms\SmsSingleSender($appid, $appkey);
    if($data['webname']){
    $params = [$data['code'],$data['webname']];
    }else{
    	$params = [$data['code']];
    }
    $result = $ssender->sendWithParam("86", $phoneNumbers[0], $data['temp_id'],
        $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
    $rsp = json_decode($result);
    return $result;
} catch(\Exception $e) {
    return false;
}
	
	
}	
	
}


    public function send_mail($is_sync = true) {
        if (!$is_sync) {
            //异步
            session('async_sendmail', true);
            return true;
        } else {
            //同步
            session('async_sendmail', null);
            return D('mail_queue')->send();
        }
    }

    protected function _upload_init($upload) {
        $allow_max = C('yh_attr_allow_size'); //读取配置
        $allow_exts = explode(',', C('yh_attr_allow_exts')); //读取配置
        $allow_max && $upload->maxSize = $allow_max * 1024;   //文件大小限制
        $allow_exts && $upload->allowExts = $allow_exts;  //文件类型限制
        $upload->saveRule = 'uniqid';
        return $upload;
    }

    /**
     * 上传文件
     */
    protected function _upload_old($file, $dir = '', $thumb = array(), $save_rule='uniqid') {
      
        $upload = new UploadFile();
        if ($dir) {
            $upload_path = C('yh_attach_path') . $dir . '/';
            $upload->savePath = $upload_path;
        }
        if ($thumb) {
            $upload->thumb = true;
            $upload->thumbMaxWidth = $thumb['width'];
            $upload->thumbMaxHeight = $thumb['height'];
            $upload->thumbPrefix = '';
            $upload->thumbSuffix = isset($thumb['suffix']) ? $thumb['suffix'] : '_thumb';
            $upload->thumbExt = isset($thumb['ext']) ? $thumb['ext'] : '';
            $upload->thumbRemoveOrigin = isset($thumb['remove_origin']) ? true : false;
        }

        //自定义上传规则
        $upload = $this->_upload_init($upload);
        if( $save_rule!='uniqid' ){
            $upload->saveRule = $save_rule;
        }

        if ($result = $upload->uploadOne($file)) {
            return array('error'=>0, 'info'=>$result);
        } else {
            return array('error'=>1, 'info'=>$upload->getErrorMsg());
        }
    }

	protected function str_mid_replace($string) {
		if (! $string || !isset($string[1])) return $string;
		$len = strlen($string);
		$starNum = floor($len / 2); 
		$noStarNum = $len - $starNum;
		$leftNum = ceil($noStarNum / 2); 
		$starPos = $leftNum;
		for($i=0; $i<$starNum; $i++) $string[$starPos+$i] = '*';

		return $string;
	}

protected function api_yh_publish_update($item)
    {
        $result = $this->api_yh_update_order($item);
		
        return $result;
    }
    
 protected function api_yh_publish_fail($item)
    {
        $result = $this->api_yh_fail_order($item);
		
        return $result;
    }

protected function api_yh_fail_order($item) {
	$prefix = C(DB_PREFIX);
	$table=$prefix.'order';
	$sql='select id,uid,integral,nstatus from '.$table.' where (status=1 or status=3) and orderid ="'.$item['orderid'].'"';
	$res=M()->query($sql);

 if ($item['status']==2 && $res){
	$map=array(
	'id'=>$res[0]['id']
	);
    	
	$data=array(
	'up_time'=>$item['up_time'],
	'status' =>2
	);
$result=M('order')->where($map)->save($data);
if($result){
	
if($res[0]['integral']>0 && $res[0]['nstatus']==0){
M('user')->where('id='.$res[0]['uid'])->save(array(
 'score'=>array('exp','score-'.$res[0]['integral'])
));
}
	
	return 1;
	}else{
	return 0;
	}
		
		
	}else{
			
		return 0;

        }
     
    }



protected function api_yh_update_order($item) {
	$prefix = C(DB_PREFIX);
	$table=$prefix.'order';
	//$sql='select id,uid,integral,nstatus from '.$table.' where (status=1 or status=2) and orderid ="'.$item['orderid'].'" and goods_iid="'.$item['goods_iid'].'" and format('.$table.'.price,2) = format('.$item['price'].',2)';
	$sql='select id,uid,integral,nstatus from '.$table.' where (status=1 or status=2) and orderid ="'.$item['orderid'].'"';
	$res=M()->query($sql);

 if ($item['status']==3 && $res){
	$map=array(
	'id'=>$res[0]['id']
	);
    	
	$data=array(
	'up_time'=>$item['up_time'],
	'income'=>$item['income'],
	'price'=>$item['price'],
	'status' =>3
	);
$result=M('order')->where($map)->save($data);
if($result){
$map=array(
'id'=>$res[0]['uid'],
'tbname'=>array('neq',1),
'webmaster'=>array('neq',1)
);
if($res[0]['integral']>0 && $res[0]['nstatus']==0){
M('user')->where($map)->save(array(
 'score'=>array('exp','score+'.$res[0]['integral'])
));
}
	return 1;
	}else{
	return 0;
	}
		
		
	}else{
			
		return 0;

        }
     
    }
    
    
  protected function _api_yh_publish_insert($item)
    {
        $result =$this->api_yh_publish_stat($item);
		
        return $result;
    }
    
    
     protected function _api_Scene_publish_insert($item)
    {
        $result =$this->api_Scene_publish_stat($item);
		
        return $result;
    }
    
 protected function api_Scene_publish_stat($item){
	$prefix = C(DB_PREFIX);
	 $table=$prefix.'order';
	 
	$field='id,fuid,guid,webmaster_rate,(select id from '.$table.' where  tqk_order.orderid ="'.$item['orderid'].'" limit 1) as nid';
	$ret=M('user')->field($field)->where(array('webmaster_pid'=>$item['relation_id']))->limit(1)->find();
	
	// $field='id,fuid,guid,webmaster_rate,(select id from '.$table.' where  tqk_order.orderid ="'.$item['orderid'].'" limit 1) as nid';
	// $ret=M('user')->field($field)->where(array('special_id'=>$item['special_id']))->limit(1)->find();
	$mod=M('order');
	
if($ret){

	if($item['status'] == 12 || $item['status'] == 14){
	    $item['status']=1;
	   }elseif($item['status'] == 3){
	    $item['status']=3;
	   }else{
	    $item['status']=2;
	   }
	

if($ret['nid']<=0){
	
	 // $item['status']=1;
	  $item['nstatus']=1;
	  $item['fuid']=$ret['fuid'];
	  $item['uid']=$ret['id'];
      $item['guid']=$ret['guid'];
	  $item['leve1']=$ret['webmaster_rate']?$ret['webmaster_rate']:trim(C('yh_bili1'));
	  $item['oid']=md5($item['oid']);
	 $mod->create($item);
      $item_id = $mod->add();
      if ($item_id) {
		  
		  if ($item['uid'] > 0) { //发送模板消息
		      $wdata = array('url' => 'c=user&a=myorder', 'uid' => $item['uid'], 'keyword1' => $item['orderid'], 'keyword2' => $item['goods_title'], 'keyword3' => $item['price'], 'keyword4' => $item['income'] * ($item['leve1'] / 100));
			  Weixin::orderTaking($wdata);
		  }
		  
            return 1;
        } else {
            return 0;
        }
	
}else{
$data['relation_id']=$item['relation_id'];
$data['special_id']	=$item['special_id'];
$data['leve1']=$ret['webmaster_rate']?$ret['webmaster_rate']:trim(C('yh_bili1'));
$data['nstatus']=1;
$data['price']=$item['price'];
$data['income']=$item['income'];
$data['uid']=$ret['id'];
$data['fuid']=$ret['fuid'];
$data['guid']=$ret['guid'];
$sqlwhere['orderid'] = $item['orderid'];
$res=$mod->where($sqlwhere)->save($data);
if ($res) {
            return 1;
        } else {
            return 0;
        }	
	
}

}

 return 0;

     
 }
    
    
 protected function api_yh_publish_stat($item){
         $mod = D('order');
		  $item['oid']=md5($item['oid']);
		  $item['uid'] = 0;
		 
		 if($item['status'] == 12 || $item['status'] == 14){
			 $item['status']=1;
		 }elseif($item['status'] == 3){
			 $item['status']=3;
		 }else{
			 $item['status']=2;
		 }
		  
		 if($item['relation_id'] && is_numeric($item['relation_id'])) {
			 $res = M('user')->field('id,fuid,guid,webmaster_rate')->where(array('webmaster_pid'=>$item['relation_id']))->find();
			 $item['fuid'] = $res['fuid']?:0;
			 $item['guid'] = $res['guid']?:0;
			 $item['leve1'] = $res['webmaster_rate'] ? $res['webmaster_rate'] : $item['leve1'];
			 $item['uid'] = $res['id']?:0;
		 }elseif($item['special_id'] && is_numeric($item['special_id'])){
			 $res = M('user')->field('id,fuid,guid,webmaster_rate')->where(array('special_id'=>$item['special_id']))->find();
			 $item['fuid'] = $res['fuid']?:0;
			 $item['guid'] = $res['guid']?:0;
			 $item['leve1'] = $res['webmaster_rate'] ? $res['webmaster_rate'] : $item['leve1'];
			 $item['uid'] = $res['id']?:0;
		 
		 }else{

			 $res = M('user')->field('id,fuid,guid,webmaster_rate')->where(array('oid'=>$item['oid']))->find();
			 $item['fuid'] = $res['fuid']?:0;
			 $item['guid'] = $res['guid']?:0;
			 $item['leve1'] = $res['webmaster_rate'] ? $res['webmaster_rate'] : $item['leve1'];
			 $item['uid'] = $res['id']?:0;


		 }


         $TljPid = trim(C('yh_taolijin_pid'));
         $apid = explode('_', $TljPid);
         $AdId = $apid[3];

         if($item['ad_id'] == $AdId){
             $item['uid'] = 0;
         }
		 
		 
		 if (!$mod->create($item)){
			 
			  $mod->setError(); //解决遇到错误无法循环，注意位置
			 
			 $data = array(
			 'status'=>$item['status'],
			 'up_time'=>$item['up_time'],
			 'price' =>$item['price'],
			 'income'=>$item['income'],
			 );
			 $res = $mod->where(array('orderid'=>$item['orderid']))->save($data);
			 if ($res) {
			     return 1;
			 }
			
			 
		 }else{
			$item_id = $mod->add();
			if ($item_id) {
				
				if($item['uid']>0){
				$wdata = array(
						'url'=>'c=user&a=myorder',
						'uid'=>$item['uid'],
						'keyword1'=>$item['orderid'],
						'keyword2'=>$item['goods_title'],
						'keyword3'=>$item['price'],
						'keyword4'=>$item['income']*($item['leve1']/100)
						);
						Weixin::orderTaking($wdata);
				}
				
			     return 1;
			 }
			 
			 
		 }
		
		 return 0;

     
    }

    /**
     * 饿了么订单状态映射到淘宝
     * @param $Num
     * @return int
     */
    protected  function ElmToTb($Num){

        $Data = array(
            '0'=>2,
            '1'=>0,
            '2'=>1,
            '4'=>3
        );

        return $Data[$Num];
    }
 
    protected function ajaxReturn($status=1, $msg='', $data='', $dialog='') {
        parent::ajaxReturn(array(
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
            'dialog' => $dialog,
        ));
    }

	protected function jsonReturn($data,$type='JSON'){
    	header('Content-Type:application/json; charset=utf-8');
    	exit(json_encode($data));
    }
	
	protected function MettuanStatus($id){
		$data = array(
		'1'=>'已付款',
		'8'=>'已完成',
		'9'=>'已退款'
		);
		
		return $data[$id];
		
	}
	
	protected function DuomaiStatus($id){
		$data = array(
		'-1'=>'无效',
		'0'=>'未确认',
		'1'=>'已确认',
		'2'=>'已结算'
		);
		
		return $data[$id];
		
	}
	
	protected function MeituanorderList($startTime,$endTime,$type=4){
		
		$url ='https://openapi.meituan.com/api/orderList';
		$data = array(
		'ts'=>time(),
		'appkey'=> trim(C('yh_mtappkey')),
		'type'=>$type,
		'startTime'=>$startTime,
		'page'=>'1',
		'limit'=>'100',
		'businessLine'=>2,
		'queryTimeType'=>'2',
		'endTime'=>$endTime,
		);
		$sign = $this->Meituansign($data);
		$data['sign']=$sign;
		$res = $this->_curl($url,$data);
		$res = json_decode($res,true);
		if($res['dataList']){
			return $res['dataList'];
		}
		return false;
		
		
		
	}
	
	
	protected function MeituanLink($actid,$sid='m001',$linkType=1){
		
		$cachename = 'mtlink_'.$sid.$linkType.$actid;
		$content = S($cachename);
		if($content){
		return $content;
		}
		$url ='https://openapi.meituan.com/api/generateLink';
		
		if($actid == 105){
			
			$linkType =8;
			
		}
		
		$data = array(
		'actId'=>$actid,
		'appkey'=>trim(C('yh_mtappkey')),
		'sid'=>$sid?:'m001',
		'linkType'=>$linkType,
		);
		$sign = $this->Meituansign($data);
		$data['sign']=$sign;
		$res = $this->_curl($url,$data);
		$res = json_decode($res,true);
		if($res['data']){
			S($cachename,$res['data'],86400);
			return $res['data'];
		}
		return false;
	}
	
	
	protected function MeituanCode($actid,$sid='m001'){
		
		$cachename = 'mtcode_'.$sid.$actid;
		$content = S($cachename);
		if($content){
		return $content;
		}
		$url ='https://openapi.meituan.com/api/miniCode';
		$data = array(
		'actId'=>$actid,
		'appkey'=>trim(C('yh_mtappkey')),
		'sid'=>$sid?:'m001',
		'linkType'=>$actid==105?8:4
		);
		$sign = $this->Meituansign($data);
		$data['sign']=$sign;
		$res = $this->_curl($url,$data);
		$res = json_decode($res,true);
		if($res['data']){
			S($cachename,$res['data'],86400);
			return $res['data'];
		}
		return false;
	}
	
	
	
	protected function Meituansign($params)
	{
		
	if(trim(C('yh_mtappkey')) && trim(C('yh_mtsecret'))){
		
		unset($params["sign"]);
		ksort($params);
		$secret = trim(C('yh_mtsecret'));
		$str = $secret; 
		foreach($params as $key => $value) {
			$str .= $key . $value;
		}
		$str .= $secret;
		$sign = md5($str);
		return $sign;
		}else{
		
		return '美团appkey和密钥没有设置';
		}	
		
	}
	
	/**
	 * 随机字符串
	 * @param int $length
	 * @return string
	 */
	protected function getNonceStr($length = 32)
	{
	    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
	    $str = "";
	    for ($i = 0; $i < $length; $i++) {
	        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	    }
	    return $str;
	}
	 
	protected function wechatmakeSign($data)
	{
	    $key=trim(C('yh_apikey'));
	    // 关联排序
	    ksort($data);
	    // 字典排序
	    $str = http_build_query($data);
	    // 添加商户密钥
	    $str .= '&key=' . $key;
	    // 清理空格
	    $str = urldecode($str);
	    $str = md5($str);
	    // 转换大写
	    $result = strtoupper($str);
	    return $result;
	}
	 
	protected function wechatarrToXml($data)
	{
	    $xml = "<xml>";
	    //  遍历组合
	    foreach ($data as $k=>$v){
	        $xml.='<'.$k.'>'.$v.'</'.$k.'>';
	    }
	    $xml .= '</xml>';
	    return $xml;
	}
	 
	/**
	 * XML转数组
	 * @param string
	 * return $data
	 * */
	function wechatxmlToArray($xml)
	{
	    //禁止引用外部xml实体
	    libxml_disable_entity_loader(true);
	    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	    return $values;
	}
	
	protected function curl_wechat($url, $xmldata, $aHeader = array(),$second = 30){
	    $ch = curl_init();//初始化curl
	    curl_setopt($ch, CURLOPT_TIMEOUT, $second);//设置执行最长秒数
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	    curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 终止从服务端进行验证
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//
	    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');//证书类型
	    curl_setopt($ch, CURLOPT_SSLCERT, $_SERVER['DOCUMENT_ROOT'].C('yh_cert_pem'));
	    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
	    curl_setopt($ch, CURLOPT_SSLKEY, $_SERVER['DOCUMENT_ROOT'].C('yh_key_pem'));
	    curl_setopt($ch, CURLOPT_CAINFO, 'PEM');
	    curl_setopt($ch, CURLOPT_CAINFO, $_SERVER['DOCUMENT_ROOT'].'/data/upload/site/rootca.pem');
		curl_setopt($ch, Authorization, 'PEM');
	    if (count($aHeader) >= 1) {
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
	    }
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);
	    $data = curl_exec($ch);//执行回话
	    if ($data) {
	        curl_close($ch);
	        return $this->wechatxmlToArray($data);
	    } else {
	        $error = curl_errno($ch);
	        echo "call faild, errorCode:$error\n";
	        curl_close($ch);
	        return false;
	    }
	}
	
	
	protected function wechatpay($uid,$money,$id)
		{
		$trade_no = date('YmdHis',time()).$id;
		
		if(C('yh_site_secret') == 2){
			$openid = M('user')->where(array('id'=>$uid))->getField('wxAppOpenid');
		}else{
			$openid = M('user')->where(array('id'=>$uid))->getField('opid');
		}
		
		if($openid){
			
			$apitype = C('yh_apitype');
			
		    if($apitype == 2){
			$data = array(
		        'mch_appid' =>trim(C('yh_mch_appid')),
		        'mchid' => trim(C('yh_mchid')),//微信支付商户号
		        'nonce_str' => $this->getNonceStr(), //随机字符串
		        'partner_trade_no' => $trade_no, //商户订单号，需要唯一
		        'openid' => $openid,
		        'check_name' => 'NO_CHECK', //OPTION_CHECK不强制校验真实姓名, FORCE_CHECK：强制 NO_CHECK：
		        'amount' => intval($money * 100), //付款金额单位为分
		        'desc' => '余额提现',
		        'spbill_create_ip' => get_client_ip(),
		    );
			//请求url
			$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
			//生成签名
			$data['sign']=$this->wechatmakeSign($data);
			$xmldata = $this->wechatarrToXml($data);
			$res = $this->curl_wechat($url,$xmldata);
			}else{
			$pars = [];
			$out_batch_no = 'obn'.$trade_no;
			$out_detail_no = 'odn'.$trade_no;
			$pars['appid'] = trim(C('yh_mch_appid'));
			$pars['out_batch_no'] = $out_batch_no;
			$pars['batch_name']   = '余额提现';
			$pars['batch_remark'] = '余额提现';
			$pars['total_amount'] = intval($money * 100);
			$pars['total_num']    = 1;
			$pars['transfer_detail_list'][0]  = [
													'out_detail_no'=>$out_detail_no,
													'transfer_amount'=>$pars['total_amount'],
													'transfer_remark'=>'余额提现',
													'openid'=>$openid
											];  
			$token  = $this->getWchatToken($pars);
			$url = 'https://api.mch.weixin.qq.com/v3/transfer/batches';	
			$res    = $this->wechat_https_request($url,json_encode($pars),$token);
			$res = json_decode($res,true);
			}
		   
			
			if(($apitype == 2 && $res['return_code'] == 'SUCCESS') || ($apitype == 1 && $res['out_batch_no'] == $out_batch_no)){
				
				if(($apitype == 2 && $res['result_code'] == 'SUCCESS') || ($apitype == 1 && $res['out_batch_no'] == $out_batch_no)){
					
					$row = M('balance')->where(['id'=>$id])->save(array('status'=>1,'method'=>1));
					if ($row) {
					    $map['id'] = $uid;
					    $data = [
					        'frozen'=>['exp', 'frozen-'.$money],
					    ];
					    $row1 = M('user')->where($map)->save($data);
					    $row2 = M('usercash')->add([
					        'uid'         =>$uid,
					        'money'       =>$money,
					        'remark'      =>'余额提现 : '.$money.'元',
					        'type'        =>6,
					        'create_time' =>time(),
					        'status'      =>1,
					    ]);
					    if ($row2 && $row1) {
							$data = array(
							'code'=>200,
							'msg'=>'微信转账成功!'
							);
					        return $data;
					    }
					}
					
					
				}else{
					
					$data = array(
					'code'=>400,
					'msg'=>$res['err_code_des']
					);
					return $data;
					
				}
				
				
			}else{
				
				$data = array(
				'code'=>400,
				'msg'=>$res['message']?$res['message']:$res['return_msg']
				);
				
				return $data;
				
			}
			
			}
			
			$data = array(
			'code'=>400,
			'msg'=>'此用户没有关注公众号！'
			);
			return $data;
			
		}
	
	
	protected function topicImg($id){
		
		$data = array(
            '20150318020014180'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01FAQTzx2MgYqI3YPaZ_!!3175549857.jpg',
		'20150318020003022'=>'https://gw.alicdn.com/imgextra/i3/O1CN01OVBveb1v0t14Adcxe_!!6000000006111-2-tps-800-450.png',
		'20150318020000462'=>'https://gw.alicdn.com/tfs/TB1iwoaSBr0gK0jSZFnXXbRRXXa-800-450.png',
		'chaohuasuan'=>'https://img.alicdn.com/imgextra/i4/126947653/O1CN01zB6KOQ26P7kZ09xAe_!!126947653.png',
		'20150318020003158'=>'https://gw.alicdn.com/imgextra/i4/O1CN01jFyrCP1J0TGA5p2JU_!!6000000000966-0-tps-750-476.jpg',
		'20150318020008177'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01viPBwk2MgYgu6pECr_!!3175549857.png',
		'20150318019998877'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01FqKuRF2MgYhb1lT6G_!!3175549857.png',
		'1583739244161'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01Gn9UEn2MgYhjwBPUM_!!3175549857.jpg',
		'20150318020007201'=>'https://img.alicdn.com/imgextra/i4/126947653/O1CN01v0tP5O26P7nu67JQF_!!126947653.jpg',
		);
		
		return $data[$id];
		
	}
	
	protected function topicColor($id){
		
		$data = array(
            '20150318020014180'=>'#FEA1C',
		'20150318020003022'=>'#BE0724',
		'20150318020000462'=>'#E70012',
		'chaohuasuan'=>'#FF5207',
		'20150318020003158'=>'#FB3596',
		'20150318020008177'=>'#F51613',
		'20150318020002192'=>'#33C4FF',
		'1583739244161'=>'#6532A1',
		'20150318020007201'=>'#F71723',
		);
		
		return $data[$id];
		
	}
	
	
	protected function ActivityID($id){
		$data = array(
		'3022'=>'20150318020003022', //天猫超市
		'4620'=>'20150318020000462',//百亿补贴
		'3158'=>'20150318020003158',//一元购
		'4956'=>'20150318020008177',//1分购
		//'8877'=>'20150318020002597',//饿了么外卖
//		'4441'=>'20150318020004284',//饿了么商超
//		'2192'=>'20150318020002192',// 饿了么小程序
        '4441'=>'10247',//饿了么商超
        '2192'=>'10144',// 饿了么小程序
		'8877'=>'20150318019998877',//饿了么外卖
            '4187'=> '20150318020014187',//U选特惠
            '4185'=> '20150318020014185',//U选快抢
            '4180'=> '20150318020014180',//超级U选
		);
		
		return $data[$id];
		
	}
	
	protected function wechat_https_request($url,$data = null,$token){
	        $curl = curl_init();
	        curl_setopt($curl, CURLOPT_URL, (string)$url);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	        if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	        }
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	        //添加请求头
	        $headers = [
	            'Authorization:WECHATPAY2-SHA256-RSA2048 '.$token,
	            'Accept: application/json',
	            'Content-Type: application/json; charset=utf-8',
	            'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
	        ];
	        if(!empty($headers)){
	            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	        }
	        $output = curl_exec($curl);
	        curl_close($curl);
	        return $output;
	    }
		
	 protected function getWchatToken($pars)
	    {   
	        $url = 'https://api.mch.weixin.qq.com/v3/transfer/batches';
	        $http_method = 'POST';
	        $timestamp   = time();
	        $url_parts   = parse_url($url);
	        $nonce       = $timestamp.rand('10000','99999');
	        $body        = json_encode((object)$pars);
	        $stream_opts = [
	            "ssl" => [
	                "verify_peer"=>false,
	                "verify_peer_name"=>false,
	            ]
	        ];
	        $apiclient_cert_path = $_SERVER['DOCUMENT_ROOT'].C('yh_cert_pem');
	        $apiclient_key_path  = $_SERVER['DOCUMENT_ROOT'].C('yh_key_pem');
	        $apiclient_cert_arr = openssl_x509_parse(file_get_contents($apiclient_cert_path,false, stream_context_create($stream_opts)));
	        $serial_no          = $apiclient_cert_arr['serialNumberHex'];//证书序列号
	        $mch_private_key    = file_get_contents($apiclient_key_path,false, stream_context_create($stream_opts));//密钥
	        $merchant_id = trim(C('yh_mchid'));
	        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
	        $message = $http_method."\n".
	        $canonical_url."\n".
	        $timestamp."\n".
	        $nonce."\n".
	        $body."\n";
	        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
	        $sign = base64_encode($raw_sign);//签名
	        $schema = 'WECHATPAY2-SHA256-RSA2048';
	        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
	            $merchant_id, $nonce, $timestamp, $serial_no, $sign);
	        return $token;
	    }
	
}