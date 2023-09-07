<?php
namespace Common\Api;
vendor('wechat.Wechat');
class Weixin
{
	
static private function weobj()
{
    $options = array(
	'token'=>trim(C('yh_wxtoken')),
	'encodingaeskey'=>trim(C('yh_wxaeskey')), 
	'appid'=>trim(C('yh_wxappid')),
	'appsecret'=>trim(C('yh_wxappsecret')),
	'debug'=>false, //调试开关
	'_logcallback'=>'logdebug', 
	);
   $weobj = new \Wechat($options);
   return $weobj;
 }


public static function settlement($data){
	
	$tempid = trim(C('yh_tempid_2'));
	if(C('yh_site_tiaozhuan') == 1 && C('yh_notice') == 1 && $tempid && $data['uid']){
		
		$info = D('user')->where(array('id'=>$data['uid']))->field('money,opid')->find();
		
		if(!$info['opid']){
			
			return false;
			
		}
		$domain = str_replace('/index.php/m','',C('yh_headerm_html'));
		$post_data = array(
		        "touser"=>$info['opid'],
		        "template_id"=>$tempid,
		        "url"=>$domain.'/index.php?m=m&c=user&a=journal',
		        "data"=> array(
		                "time2"=>array(
		                        "value"=>date('Y-m-d H:i',time())
		                ),
		                "amount3"=>array(
		                        "value"=>number_format($data['keyword2'],'2').'元'
		                ),
		                "thing10"=> array(
		                        "value"=>$data['first']
		                ),
		        )
		);
		self::weobj()->sendTemplateMessage($post_data);
		
	}
	
	
	
}



public static function TimePush($data){
	
	$tempid = trim(C('yh_tempid_4'));
	$time = trim(C('yh_tempid_4_time'));
	if(C('yh_site_tiaozhuan') == 1 && C('yh_notice') == 1 && $tempid && $time){
	
	if(!$data['openid']){
		
		return false;
		
	}
	$domain = str_replace('/index.php/m','',C('yh_headerm_html'));
	$post_data = array(
	        "touser"=>$data['openid'],
	        "template_id"=>$tempid,
	        "url"=>$domain."/index.php?m=m&c=waimai&a=index",
	        "data"=> array(
	                "first" => array(
	                        "value"=>"再忙也要记得吃饭哟！您的点餐时间到啦！",
	                        "color"=>"#666666"
	                ),
	                "keyword1"=>array(
	                        "value"=>'饿了么、美团',
	                        "color"=>"#666666"
	                ),
					"keyword2"=>array(
					        "value"=>'@各位小主',
					        "color"=>"#666666"
					),
					"keyword3"=>array(
					        "value"=>'先领红包 再点外卖,快戳我！',
					        "color"=>"#ff0000"
					),
	                "keyword4"=>array(
	                        "value"=>date('H:i',time()),
	                        "color"=>"#666666"
	                ),
	                "remark"=> array(
	                        "value"=>'回复【td】取消点餐提醒',
	                        "color"=>"#666666"
	                )
	        )
	);
	
	self::weobj()->sendTemplateMessage($post_data);
	
	}
	
	return false;
	
}



//发起提现通知管理员
public static function Cashout($data){
	
	$tempid = trim(C('yh_tempid_1'));
	$uid = trim(C('yh_tempid_1_uid'));
	if(C('yh_site_tiaozhuan') == 1 && C('yh_notice') == 1 && $tempid && $uid){
	
	$openid = D('user')->where(array('id'=>$uid))->getField('opid');
	
	if(!$openid){
		
		return false;
		
	}
	
	$post_data = array(
	        "touser"=>$openid,
	        "template_id"=>$tempid,
	        "url"=>"",
	        "data"=> array(
	                "thing3"=>array(
	                        "value"=>$data['name']
	                ),
	                "time8"=>array(
	                        "value"=>date('Y-m-d H:i',time())
	                ),
	                "amount6"=>array(
	                        "value"=>number_format($data['money'],'2').'元'
	                ),
	                "thing4"=>array(
	                        "value"=>$data['payment']
	                ),

	        )
	);
	self::weobj()->sendTemplateMessage($post_data);
	
	}
	
	return false;
	
}
 
  //订单受理通知
    public static function orderTaking($data){
		
		$tempid = trim(C('yh_tempid_3'));
		if(C('yh_site_tiaozhuan') == 1 && C('yh_notice') == 1 && $tempid && $data['uid']){
		
		$openid = D('user')->where(array('id'=>$data['uid']))->getField('opid');
		
		if(!$openid){
			
			return false;
			
		}
		
		$domain = str_replace('/index.php/m','',C('yh_headerm_html'));
		$keyword3 = sprintf("%.2f",$data['keyword3']);
    	$post_data = array(
                "touser"=>$openid,
                "template_id"=>$tempid,
                "url"=>$domain.'/index.php?m=m&'.$data['url'],
                "data"=> array(
                        "time4"=>array(
                                "value"=>date('Y-m-d H:i',time())
                        ),
                        "thing7"=>array(
                                "value"=>utf_substr($data['keyword2'],28).'...'
                        ),
                        "amount8"=>array(
                                "value"=>$keyword3?:'0'.'元'
                        ),
						"character_string9"=>array(
						        "value"=>$data['keyword1']
						),
                )
        );
    	self::weobj()->sendTemplateMessage($post_data);
    }  
 
    

}

}