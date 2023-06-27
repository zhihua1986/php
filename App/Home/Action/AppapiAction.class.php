<?php
namespace Home\Action;

use Common\Model\userModel;
use Common\Model\adModel;
use Common\Model\itemscateModel;
use Common\Model\itemsModel;
use Common\Model\usercashModel;
use Common\Model\articleModel;
use Common\Model\helpModel;
use Common\Model\jdorderModel;
use Common\Model\pddorderModel;
use Common\Model\jditemsModel;
use Common\Model\mtorderModel;
use Common\Model\dmorderModel;
use Common\Api\Weixin;
class AppapiAction extends AppletsAction
{
    private $accessKey = '';
	
	public function taolijin(){
		
		$page = $this->params['page'];
		$data = $this->Toplijin($page,30);
		$lang = array(
		'banner'=>'https://img.alicdn.com/imgextra/i4/126947653/O1CN01Bv2LmG26P7kkKEILC_!!126947653.png',
		'name'=>'淘礼金专区',
		'Em'=>'礼金',
		'bgcolor'=>'#6F1DD9',
		'fontcolor'=>'#ffffff',
		);
		$this->Exitjson(200,'',$data,$lang);
		
		
	}
	
	public function help(){
	$mod = new helpModel();
	$where = array(
	'url'=> $this->params['url']
	);
	$result = $mod->field('info')->where($where)->find();
	if($result){
	$result['add_time'] = frienddate($result['add_time']);
	$this->Exitjson(200,'成功',$result);	
	}
	$this->Exitjson(400,'失败');	
	}

    public function subcate()
    {
        $mod = new itemscateModel();
        $mod = $mod ->cache(true, 10 * 60);
        $pid = $this->params['pid'];
        $catelist=$mod->field('name,remark,ali_id,id,pid')->where('status = 1 and pid ='.$pid.'')->order('ordid desc')->select();
        if ($catelist) {
            $this->Exitjson(200, '', $catelist);
        } else {
            $this->Exitjson(200, '没有数据了');
        }
    }
	
	public function bdlogin(){
		
		$mod = new userModel();
		$url = 'https://spapi.baidu.com/oauth/jscode2sessionkey';
		$data = [
		    'client_id'=>trim(C('yh_bd_appid')),
		    'sk'=>trim(C('yh_bd_secret')),
		    'code'=>$this->params['code'],
		];
		$res = $this->_curl($url, $data);
		$json = json_decode($res, true);
	 if ($json && $json['openid']) {
	    $where['bdAppOpenid'] = $json['openid'];
	    $user_info = $mod->where($where)
	    ->field('openid,webmaster_pid,jd_pid,webmaster_rate,id,status,tbname,webmaster')
	    ->find();
	
	    if ($user_info && $user_info['status'] != 1) {
	        $this->Exitjson(400, '账号被禁用', $data);
	    }
	
	    if ($user_info) {//登录成功
	        $map = [
	            'openid'=>$user_info['openid'],
	        ];
	        $data = [
	            'last_time'=>time(),
	            'last_ip'=>get_client_ip(),
	            'login_count'=>['exp', 'login_count+1']
	        ];
	        $mod->where($map)->save($data);
	
	        $map['relationid'] = $user_info['webmaster_pid'];
	        F('data/'.$user_info['openid'], $user_info['openid']);
	        $this->Exitjson(200, '', $user_info);
	    }
	
	    if (C('yh_index_ems') == 0 && C('yh_bd_version')!='1.0.0') { //绑定手机号
	        $data = [
	            'bdAppOpenid'=>$json['openid'],
	            'nickName'=>$this->params['nickName'],
	            'avatarUrl'=>$this->params['avatarUrl'],
	            'sms'=>C('yh_sms_status'),
	        ];
	        $this->Exitjson(300, '', $data);
	    }
	
	    //注册新账号
	    if ($this->params['fromid']) {
	        $exist = $mod->field('id,fuid,guid')->where(['id'=>$this->params['fromid']])->find();
	    }
	
	    $pwd = md5($this->params['code']);
	    $openid=$json['openid'];
	    $nickname=$this->params['nickName'];
	    $nickname=$this->remoji($nickname);
	
	    $agentcondition=trim(C('yh_agentcondition'));
	    if (0 >= $agentcondition) {
	        $tbname = 1;
	    } else {
	        $tbname = 0;
	    }
	
	    $now=NOW_TIME;
	    $info=[
	        'username'=>$nickname ? $nickname : '匿名',
	        'nickname'=>$nickname ? $nickname : '匿名',
	        'password'=>$pwd,
	        'reg_ip'=>get_client_ip(),
	        'avatar'=>$this->params['avatarUrl'],
	        'bdAppOpenid'=>$openid,
	        'state'=>1,
	        'tbname'=>$tbname,
	        'status'=>1,
	        'reg_time'=>$now,
	        'last_time'=>$now,
	        'create_time'=>$now,
	        'openid'=>$openid,
	        'fuid'=>$exist['id'] ? $exist['id'] : 0,
	        'guid'=>$exist['fuid'] ? $exist['fuid'] : 0
	    ];
	    $res=$mod->add($info);
	    if ($res) {
	        $this->reinvi($res);
	
	        $data = [
	            'openid'=>$json['openid'],
	            'id'=>$res,
	        ];
	        F('data/'.$json['openid'], $json['openid']);
	        $this->Exitjson(200, '', $data);
	    }
	}
			 $this->Exitjson(400, $json['error_description']);
		
	}
	
    public function tbdetail()
    {
        $num_iid = $this->params['id'];
        $json = $this->taobaodetail($num_iid);
        $item = $json['small_images']['string'] ? $json['small_images']['string'] : '';
		$lang = [
		    'msg'=>'口令复制成功，现在咑開手机綯宝APP即可下单',
		    'auth'=>'淘宝官方要求“授权备案”后分享和购买商品才可以获得返利',
		];
        $this->Exitjson(200, '', $item,$lang);
    }

    public function record()
    {
        $openid = $this->Authtoken();
        $userinfo = $this->getuid($openid);
        $where = array('id'=>$userinfo['id']);
        $mod = new userModel();
        $user = $mod->where($where)->field('money,frozen')->find();
        if ($user) {
			$user['balance'] = $user['frozen'];
            $this->Exitjson(200, '成功', $user);
        }
        $this->Exitjson(400, '没有数据');
    }

    public function recordlist()
    {
        $openid = $this->Authtoken();
        $userinfo = $this->getuid($openid);
        $page = $this->params['p'];
        $page_size = 15;
        $start = $page_size * $page;
        $map['uid'] = $userinfo['id'];
        $rows = M('balance')->where($map)->field('money,create_time,status')->limit($start . ',' . $page_size)->order('id desc')->select();
        $list=[];
        foreach ($rows as $k=>$v) {
            $list[$k]['create_time']=frienddate($v['create_time']);
            if ($v['status'] == 0) {
                $list[$k]['status']='处理中';
            } else {
                $list[$k]['status']='已处理';
            }
            $list[$k]['money']=$v['money'];
        }

        if ($list) {
            $this->Exitjson(200, '成功', $list);
        }
        $this->Exitjson(200, '没有数据了');
    }

    public function articleView()
    {
        $id = $this->params['id'];
        $mod = new articleModel();
        $result = $mod->field('id,title,info,author,seo_title,seo_keys,seo_desc,add_time,cate_id')->find($id);
        if ($result) {
            $result['add_time'] = frienddate($result['add_time']);
            if ($result['author']) {
                $Gmod = new itemsModel();
                $like = $Gmod->GoodsList(8, ['title'=>['like', '%' . $result['author'] . '%'], 'pass'=>1, 'isshow'=>1, 'ems'=>1], 'id desc');
            }

            $cateMod = D('articlecate')->cache(true, 10 * 60);
            $articleCate= $cateMod->where('status=1')->field('id,name')->order('ordid desc')->select();

            $data = [
                'info'=>$result,
                'item'=>$like,
                'cate'=>$articleCate,
				'seo'=> array(
				'title'=>$result['seo_title']?:$result['title'].'_'.C('yh_site_name'),
				'keywords'=>$result['seo_keys'],
				'description'=>$result['seo_desc']
				),
            ];
            $lang = ['Em'=>'约返'];

            $this->Exitjson(200, '成功', $data, $lang);
        }
        $this->Exitjson(400, '失败');
    }


    public function article()
    {
        $cid = $this->params['cid'];
        $page = $this->params['p'];
        $page_size = 15;
        $start = $page_size * $page;
        $mod = new articleModel();
        $mod = $mod->cache(true, 10 * 60);
        $order = 'ordid asc,id desc';
        $where['ordid'] = ['gt', 0];
        $where['status'] = '1';
        if ($cid) {
            $where['cate_id'] = $cid;
        }
        $rows =$mod->where($where)->field('title,cate_id,add_time,id,pic,info')->order($order)->limit($start . ',' . $page_size)->select();
        $cateMod = D('articlecate')->cache(true, 10 * 60);
        $articleCate= $cateMod->where('status=1')->field('id,name')->order('ordid desc')->select();

        if ($rows) {
            foreach ($rows as $k=>$v) {
				
				if($k == 5 && $this->Isopen() == 0){
					
				 break;
					
				}
				
				
                $goodslist[$k]['id']=$v['id'];
				if(strpos($v['pic'],'http')===false){
				$v['pic']=C('yh_site_url').$v['pic'];
				}
                $goodslist[$k]['pic']=$v['pic'];
                $goodslist[$k]['cateid']=$v['cate_id'];
                $goodslist[$k]['title']=$v['title'];
                $goodslist[$k]['add_time']=date('Y-m-d', $v['add_time']);
            }
        }
		
		if($cid){
		 $cateinfo=$cateMod->where('id='.$cid)->field('id,name,seo_title,seo_keys,seo_desc')->find();
		 $seo =  array(
		            'keywords' => $cateinfo['seo_keys'],
		            'description' => $cateinfo['seo_desc'],
		            'title' => $cateinfo['seo_title']?$cateinfo['seo_title']:$cateinfo['name'].'优惠券头条- '. C('yh_site_name')
		        );
		}else{
			$seo = C('yh_seo_config.toutiao');
			$seo = $this->_config_seo($seo, [
				'site_name'=>C('yh_site_name'),
			],false);
		}
		

        if ($goodslist || $articleCate) {
            $data = [
                'list'=>$goodslist ? $goodslist : '',
                'cate'=>$articleCate ? $articleCate : '',
				'seo'=>$seo
            ];
            $this->Exitjson(200, '成功', $data);
        }
        $this->Exitjson(200, '已经没有数据啦');
    }

    public function contactus()
    {
        $data = [
            'qq'=>C('yh_qq'),
            'wx'=>C('yh_zhibo_url')
        ];
        $this->Exitjson(200, '', $data);
    }

public function topic(){
	
	$id = $this->params['id'];
	$relation = $this->params['relation'];
	
	$openid = $_SERVER['HTTP_AUTHTOKEN'];
	if($openid){
	$res = $this->getuid($openid);
	$uid = $res['id'];
	}
	
	
	$res = $this->TbkActivity($id,$relation,$uid);
	if($id == '20150318019998877'){
	$res['data']['page_name'] = '饿了么外卖';
	}
	
	if($id == '1583739244161'){
	$res['data']['page_name'] = '口碑生活';
	}
	
	if($res['data']['click_url']){
		$data = array(
		'msg'=>'文案复制成功！现在打开手机.淘.宝即可进入活动页面。',
		'banner'=>$this->topicImg($id),
		'name'=>$res['data']['page_name'],
		'content'=> kouling('',$res['data']['page_name'],$res['data']['click_url'])
		);
		
		$lang = [
			'fontcolor'=>'#ffffff',
			'bgcolor'=>$this->topicColor($id),
		];
		
		  $this->Exitjson(200, '', $data,$lang);
	}
	
	$this->Exitjson(400, '获取活动信息失败');
	
}


    public function exchange()
    {
        $fromdata = $this->params['fromdata'];
        $fromdata = json_decode($fromdata, true);
        $count = abs($fromdata['count']);
        if ($count<1 || is_float($count)) {
            $this->Exitjson(400, '兑换数量必须是1的整数倍');
        }
        $openid = $this->Authtoken();
        $userinfo = $this->getuid($openid);
        $usermod = new userModel();
        $user=$usermod->where('id='.$userinfo['id'])->field('id,score,money')->find();

        if ($count>$user['score']) {
            $this->Exitjson(400, '兑换数量与实际数量不符');
        }

        $userid=$userinfo['id'];
        $price=(C('yh_fanxian')/100)*$count;

        if ($price>0) {
            $usermod->where('id='.$userid)->save([
                'money'=>['exp', 'money+'.$price],
                'score'=>['exp', 'score-'.$count]
            ]);

            M('usercash')->add([
                'uid'         =>$userid,
                'money'       =>$price,
                'remark'      =>'积分兑换: '.$price.'元',
                'type'        =>10,
                'create_time' =>time(),
                'status'      =>1,
            ]);
            $this->Exitjson(200, '兑换成功!');
        }
        $this->Exitjson(400, '兑换失败!');
    }

    public function bindorder()
    {
        $orderid = $this->params['fromdata'];
        $openid = $this->Authtoken();
        $UID = $this->getuid($openid);
        $mod = new userModel();
        if (is_numeric($orderid)) {
            $map=[
                'id'=>$UID['id'],
            ];
            $oid=md5(substr($orderid, -6, 6));
            $data=[
                'oid'=>$oid
            ];
            $isset=$mod->field('id')->where($data)->find();
            if (!$isset) {
                $res=$mod->where($map)->save($data);
                if ($res) {
                    $this->Exitjson(200, '找回成功!');
                } else {
                    $this->Exitjson(400, '找回失败!');
                }
            } else {
                $this->Exitjson(400, '此订单已经被其它账号绑定过！!');
            }
        } else {
            $this->Exitjson(400, '提交的订单参数不符合要求');
        }
    }

    public function agent()
    {
        $lang = [
            'name'=>C('yh_site_name'),
            'condition'=>C('yh_agentcondition'),
            'rate1'=>ceil(C('yh_bili2')/C('yh_bili1')*100),
            'rate2'=>ceil(C('yh_bili3')/C('yh_bili1')*100),
            'tb'=>'做淘宝授权或'
        ];
        $this->Exitjson(200, $lang);
    }

    public function integral()
    {
        $openid = $this->Authtoken();
        $page = $this->params['p'];
        $page_size = 15;
        $start = $page_size * $page;
        $userinfo = $this->getuid($openid);
        $stay['uid'] =$userinfo['id'];
        $mod=M('basklistlogo');
        $rows = $mod->field('create_time,integray,remark')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $list=[];
        foreach ($rows as $k=>$v) {
            $list[$k]['create_time']=frienddate($v['create_time']);
            $list[$k]['remark']=$v['remark'];
            $list[$k]['integray']=$v['integray'];
        }

        if ($list) {
            $this->Exitjson(200, '成功', $list);
        }
        $this->Exitjson(200, '已经没有数据啦！');
    }

    public function teamprofit()
    {
        $openid = $this->Authtoken();
        $memberinfo = $this->getuid($openid);
        $uid=$memberinfo['id'];
        $today_str = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $pre_time = strtotime(date('Y-m-01', strtotime('last day of -1 month')));
        $last_str = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
        $pre_month=$this->getthemonth($pre_time);
        $field = 'id,
( select SUM(money) from tqk_usercash where uid = '.$uid.' and type=3 and create_time>'.$pre_month[1].') as allmoney,
( select SUM(income*leve1/100) from tqk_order where nstatus=1 and (status =1 or status =3) and uid = '.$uid.' and add_time>'.$today_str.') as u_today_money,
( select SUM(income*leve2/100) from tqk_order where nstatus=1 and (status =1 or status =3) and fuid = '.$uid.' and add_time>'.$today_str.') as f_today_money,
( select SUM(income*leve3/100) from tqk_order where nstatus=1 and (status =1 or status =3) and guid = '.$uid.' and add_time>'.$today_str.') as g_today_money,
( select SUM(promotion_amount*leve1/100) from tqk_pddorder where status <>4 and uid = '.$uid.' and order_pay_time>'.$today_str.') as udd_today_money,
( select SUM(promotion_amount*leve2/100) from tqk_pddorder where status <>4 and fuid = '.$uid.' and order_pay_time>'.$today_str.') as fdd_today_money,
( select SUM(promotion_amount*leve3/100) from tqk_pddorder where status <>4 and guid = '.$uid.' and order_pay_time>'.$today_str.') as gdd_today_money,
( select SUM(estimateFee*leve1/100) from tqk_jdorder where validCode >1  and uid = '.$uid.' and orderTime>'.$today_str.') as ujd_today_money,
( select SUM(estimateFee*leve2/100) from tqk_jdorder where validCode >1 and fuid = '.$uid.' and orderTime>'.$today_str.') as fjd_today_money,
( select SUM(estimateFee*leve3/100) from tqk_jdorder where validCode >1 and guid = '.$uid.' and orderTime>'.$today_str.') as gjd_today_money,
( select SUM(profit*leve1/100) from tqk_mtorder where status<9 and uid = '.$uid.' and paytime>'.$today_str.') as umt_today_money,
( select SUM(profit*leve2/100) from tqk_mtorder where status<9 and fuid = '.$uid.' and paytime>'.$today_str.') as fmt_today_money,
( select SUM(profit*leve3/100) from tqk_mtorder where status<9 and guid = '.$uid.' and paytime>'.$today_str.') as gmt_today_money,
( select SUM(order_commission*leve1/100) from tqk_dmorder where status>=0 and uid = '.$uid.' and order_time>'.$today_str.') as udm_today_money,
( select SUM(order_commission*leve2/100) from tqk_dmorder where status>=0 and fuid = '.$uid.' and order_time>'.$today_str.') as fdm_today_money,
( select SUM(order_commission*leve3/100) from tqk_dmorder where status>=0 and guid = '.$uid.' and order_time>'.$today_str.') as gdm_today_money,
( select SUM(income*leve1/100) from tqk_order where nstatus=1 and (status =1 or status =3) and uid = '.$uid.' and  add_time>'.$pre_month[1].') as u_month,
( select SUM(income*leve2/100) from tqk_order where nstatus=1 and (status =1 or status =3) and fuid = '.$uid.' and add_time>'.$pre_month[1].') as f_month,
( select SUM(income*leve3/100) from tqk_order where nstatus=1 and (status =1 or status =3) and guid = '.$uid.' and add_time>'.$pre_month[1].') as g_month,
( select SUM(promotion_amount*leve1/100) from tqk_pddorder where status <>4 and uid = '.$uid.' and order_settle_time>'.$pre_month[1].') as udd_month,
( select SUM(promotion_amount*leve2/100) from tqk_pddorder where status <>4 and fuid = '.$uid.' and order_settle_time>'.$pre_month[1].') as fdd_month,
( select SUM(promotion_amount*leve3/100) from tqk_pddorder where status <>4 and guid = '.$uid.' and order_settle_time>'.$pre_month[1].') as gdd_month,
( select SUM(estimateFee*leve1/100) from tqk_jdorder where validCode >1 and uid = '.$uid.' and modifyTime>'.$pre_month[1].') as ujd_month,
( select SUM(estimateFee*leve2/100) from tqk_jdorder where validCode >1 and fuid = '.$uid.' and modifyTime>'.$pre_month[1].') as fjd_month,
( select SUM(estimateFee*leve3/100) from tqk_jdorder where validCode >1 and guid = '.$uid.' and modifyTime>'.$pre_month[1].') as gjd_month,
( select SUM(profit*leve1/100) from tqk_mtorder where status<9 and uid = '.$uid.' and settle_time>'.$pre_month[1].') as umt_month,
( select SUM(profit*leve2/100) from tqk_mtorder where status<9 and fuid = '.$uid.' and settle_time>'.$pre_month[1].') as fmt_month,
( select SUM(profit*leve3/100) from tqk_mtorder where status<9 and guid = '.$uid.' and settle_time>'.$pre_month[1].') as gmt_month,
( select SUM(order_commission*leve1/100) from tqk_dmorder where status>=0 and uid = '.$uid.' and order_time>'.$pre_month[1].') as udm_month,
( select SUM(order_commission*leve2/100) from tqk_dmorder where status>=0 and fuid = '.$uid.' and order_time>'.$pre_month[1].') as fdm_month,
( select SUM(order_commission*leve3/100) from tqk_dmorder where status>=0 and guid = '.$uid.' and order_time>'.$pre_month[1].') as gdm_month,
( select count(*) from tqk_pddorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and order_pay_time>'.$today_str.') as dd_today_count,
( select count(*) from tqk_pddorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and order_pay_time>'.$pre_month[1].' ) as dd_this_month_count,
( select count(*) from tqk_pddorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (order_pay_time>'.$pre_month[0].' and order_pay_time<'.$pre_month[1].') ) as dd_last_month_count,
( select count(*) from tqk_jdorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and orderTime>'.$today_str.') as jd_today_count,
( select count(*) from tqk_jdorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and orderTime>'.$pre_month[1].' ) as jd_this_month_count,
( select count(*) from tqk_jdorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (orderTime>'.$pre_month[0].' and orderTime<'.$pre_month[1].') ) as jd_last_month_count,
( select count(*) from tqk_mtorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and paytime>'.$today_str.') as mt_today_count,
( select count(*) from tqk_mtorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and paytime>'.$pre_month[1].' ) as mt_this_month_count,
( select count(*) from tqk_mtorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (paytime>'.$pre_month[0].' and paytime<'.$pre_month[1].') ) as mt_last_month_count,
( select count(*) from tqk_dmorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and order_time>'.$today_str.') as dm_today_count,
( select count(*) from tqk_dmorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and order_time>'.$pre_month[1].' ) as dm_this_month_count,
( select count(*) from tqk_dmorder where (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (order_time>'.$pre_month[0].' and order_time<'.$pre_month[1].') ) as dm_last_month_count,
( select count(*) from tqk_order where nstatus=1 and (status =1 or status =3) and (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and add_time>'.$today_str.') as today_count,
( select count(*) from tqk_order where nstatus=1 and (status =1 or status =3) and (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and add_time>'.$pre_month[1].' ) as this_month_count,
( select count(*) from tqk_order where nstatus=1 and (status =1 or status =3) and (uid = '.$uid.' or fuid='.$uid.' or guid='.$uid.') and (add_time>'.$pre_month[0].' and add_time<'.$pre_month[1].') ) as last_month_count';
        $res = D('user')->field($field)->order('id desc')->find();
        if ($res) {
            $res['yesterday']=round($res['udm_today_money']+$res['gdm_today_money']+$res['fdm_today_money']+$res['u_today_money']+$res['udd_today_money']+$res['ujd_today_money']+$res['f_today_money']+$res['fdd_today_money']+$res['umt_today_money']+$res['gmt_today_money']+$res['fmt_today_money']+$res['fjd_today_money']+$res['g_today_money']+$res['gdd_today_money']+$res['gjd_today_money'], 2);
            $res['thismonth']=round($res['gdm_month']+$res['fdm_month']+$res['udm_month']+$res['u_month']+$res['udd_month']+$res['ujd_month']+$res['gmt_month']+$res['fmt_month']+$res['umt_month']+$res['f_month']+$res['fdd_month']+$res['fjd_month']+$res['g_month']+$res['gdd_month']+$res['gjd_month'], 2);
            $res['ComputingTime'] = C('yh_ComputingTime');
            $this->Exitjson(200, '成功', $res);
        }

        $this->Exitjson(400, '失败');
    }

    public function teamlist()
    {
        $openid = $this->Authtoken();
        $page = $this->params['p'];
        $page_size = 10;
        $start = $page_size * $page;
        $userinfo = $this->getuid($openid);
        $ac = $this->params['ac'];
        if ($ac == 'guid') {
            $stay['guid'] = $userinfo['id'];
        } else {
            $stay['fuid'] = $userinfo['id'];
        }
        $usermod = new userModel();
        $rows = $usermod->field('reg_time,nickname,oid,avatar')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $list = [];
        foreach ($rows as $k=>$v) {
            $list[$k]['reg_time']=date('Y-m-d H:i', $v['reg_time']);
            $list[$k]['nickname']=$v['nickname'];
            $list[$k]['oid']=$v['oid'];
            if (substr($v['avatar'], 0, 4)!='http') {
                $list[$k]['avatar']=trim(C('yh_site_url')).$v['avatar'];
            } else {
                $list[$k]['avatar']=$v['avatar'];
            }
        }

        if ($list) {
            $this->Exitjson(200, '成功', $list);
        }
        $this->Exitjson(200, '没有数据');
    }

    public function myteam()
    {
        $usermod=new userModel();
        $openid = $this->Authtoken();
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
        if ($res) {
            $this->Exitjson(200, '成功', $res);
        }
        $this->Exitjson(200, '没查询到数据');
    }

    public function journallist()
    {
        $openid = $this->Authtoken();
        $page = $this->params['p'];
        $page_size = 15;
        $start = $page_size * $page;
        $userinfo = $this->getuid($openid);
        $mod = M('usercash');
        $stay['uid'] = $userinfo['id'];
        $rows =$mod->where($stay)->field('type,money,create_time,status')->order('id desc')->limit($start . ',' . $page_size)->select();
        $list=[];
        foreach ($rows as $k=>$v) {
            $val=unserialize(user_cash_type($v['type']));
            $list[$k]['create_time']=frienddate($v['create_time']);
            $list[$k]['type']=$val[0];
            $list[$k]['money']=$val[1].round($v['money'], 2);
        }

        if ($list) {
            $this->Exitjson(200, '成功', $list);
        }
        $this->Exitjson(200, '没查询到数据');
    }

    public function journal()
    {
        $openid = $this->Authtoken();
        $userinfo = $this->getuid($openid);
        $where = ['id'=>$userinfo['id']];
        $mod = new userModel();
        $user = $mod->where($where)->field('money,id')->find();
        $where=[
            'type'=>6,
            'uid'=>$user['id']
        ];
        $balance = M('usercash')->where($where)->sum('money');
        $result = [
            'balance'=>$balance,
            'money'=>round($user['money'], 2)
        ];
        if ($result) {
            $this->Exitjson(200, '成功', $result);
        }
        $this->Exitjson(200, '没查询到数据');
    }

    public function tixian()
    {
        $openid = $this->Authtoken();
        $mod=new userModel();
        $F= M('balance');
        $fromdata = $this->params['fromdata'];
        $fromdata = json_decode($fromdata, true);
        $mymoney = abs($fromdata['money']);
        if ($mymoney<=0) {
            $this->Exitjson(400, '提现金额异常！');
        }
        if ($mymoney<C('yh_Quota')) {
            $this->Exitjson(400, '单笔提现金额不能小于'.C('yh_Quota').'元');
        }
        $userinfo = $this->getuid($openid);
        $map['id'] = $userinfo['id'];
        $balance = $mod->field('nickname,avatar,username,money,id,realname,alipay')->where($map)->find();
        $alipay=$fromdata['alipay'];
        $name=$fromdata['realname'];
        if ($alipay && $name && !$balance['alipay']) {
            $data=[
                'realname'=>$name,
                'alipay'=>$alipay
            ];
            $mod->where($map)->save($data);
        }

        if ($mymoney>$balance['money']) {
            $this->Exitjson(400, '账户余额不足');
        }
        $data=[
            'uid'=>$balance['id'],
            'money'=>$mymoney,
            'name'=>$name ? $name : $balance['realname'],
            'method'=>$fromdata['method']?:2,
            'allpay'=>$alipay ? $alipay : $balance['alipay'],
            'status'=>0,
            'content'=>$fromdata['content'],
            'create_time'=>time()
        ];
        $res = $F->add($data);
        if ($res !== false) {
			$resid = $res;
            $mod->where([
                'id'=>$balance['id']
            ])->save([
                'money'=>['exp', 'money-'.$mymoney],
                'frozen'=>['exp', 'frozen+'.$mymoney],
            ]);

            M('usercash')->add([
                'uid'=>$balance['id'],
                'money'=>$mymoney,
                'type'=>1,
                'remark'=>'提现冻结资金：'.$mymoney.'元',
                'create_time'=>NOW_TIME,
                'status'=>1,
            ]);
            $myphone=trim(C('yh_sms_my_phone'));
            if (0 < C('yh_sms_status') && $myphone) {
                $data=[
                    'phone'=>$myphone,
                    'code'=>$balance['nickname'],
                    'webname'=>(float) $mymoney,
                    'temp_id'=>trim(C('yh_sms_tixian_id'))
                ];
                $res= $this->send_sms($data);
            }
			
			$data = array(
			'name'=>$balance['nickname'],
			'money'=>(float) $mymoney,
			'payment'=> $this->CashMethod($fromdata['method']),
			'content'=>$fromdata['content']
			);
			Weixin::Cashout($data);
			
			if(C('yh_payment') == '2' && $fromdata['method'] == '1'){
			 $res = $this->wechatpay($balance['id'],$mymoney,$resid);
			 if($res['code']==200){
				  $this->Exitjson(200, $res['msg']);
			 }
			 $this->Exitjson(400, $res['msg']);
			
			}
			
			
            $this->Exitjson(200, '申请提交成功，请等待处理！');
        }
        $this->Exitjson(400, '申请提交失败！');
    }

    public function userinfo()
    {
        $openid = $this->Authtoken();
		if($openid){
        $artmod = new articleModel();
        $usermod = new userModel();
        $notice=$artmod->cache(true, 10 * 60)->where('cate_id=2')->field('id,title')->limit(5)->select();
        $userinfo = $usermod->field('special_id,id,money,oid,avatar,score,invocode,tbname,webmaster_pid,webmaster,webmaster_rate,phone,qq,wechat,phone,nickname,realname,alipay,tb_open_uid,jd_pid,elm_pid')->where(['openid'=>$openid])->find();
		
		if($userinfo){
		    $userinfo['jd_pid'] = $userinfo['jd_pid']>0?$userinfo['jd_pid']:$this->CreateJdPid($userinfo);
            $userinfo['elm_pid'] = strlen($userinfo['elm_pid'])>5?$userinfo['elm_pid']:$this->CreateElmPid($userinfo);
		if($userinfo['special_id'] < 2 ){ //会员管理ID
		$this->Getspecial();
		}
		
        if (!$userinfo['invocode'] && $userinfo['id']) {
            $codes=$this->invicode($userinfo['id']);
            $userinfo['invocode']=$codes;
        }
        if (substr($userinfo['avatar'], 0, 4)!='http') {
            $userinfo['avatar']=trim(C('yh_site_url')).$userinfo['avatar'];
        }
        if (1 != $userinfo['tbname']) {
            $map=[
                'uid'=>$userinfo['id'],
                //'nstatus'=>1,
                'status'=>3
            ];
            $mymoney=M('order')->where($map)->sum('price');
            $agentcondition=trim(C('yh_agentcondition'));
            if (!$mymoney) {
                $mymoney=0;
            }
            if ($mymoney>=$agentcondition) {
                $usermod->where(['id'=>$userinfo['id']])->setField('tbname', 1);
            }
        }
        $today_str = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $start=strtotime(date('Y-m-01', strtotime('last day of -1 month')));
        $end =strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
        $uid = $userinfo['id'];
        $field ='(select SUM(money) from tqk_usercash where uid = '.$uid.' and type = 3 and (create_time > '.$start.' and create_time < '.$end.')) as pre_money,
				(select SUM(money) from tqk_usercash where uid = '.$uid.' and type = 3 and create_time > '.$end.') as this_money,
				( select SUM(income*leve1/100) from tqk_order where nstatus=1 and (status =1 or status =3) and uid = '.$uid.' and add_time>'.$today_str.') as u_today_money,
				( select SUM(income*leve2/100) from tqk_order where nstatus=1 and (status =1 or status =3) and fuid = '.$uid.' and add_time>'.$today_str.') as f_today_money,
				( select SUM(income*leve3/100) from tqk_order where nstatus=1 and (status =1 or status =3) and guid = '.$uid.' and add_time>'.$today_str.') as g_today_money,
				( select SUM(promotion_amount*leve1/100) from tqk_pddorder where (status >0 AND status <4) and uid = '.$uid.' and order_pay_time>'.$today_str.') as udd_today_money,
				( select SUM(promotion_amount*leve2/100) from tqk_pddorder where (status >0 AND status <4) and fuid = '.$uid.' and order_pay_time>'.$today_str.') as fdd_today_money,
				( select SUM(promotion_amount*leve3/100) from tqk_pddorder where (status >0 AND status <4) and guid = '.$uid.' and order_pay_time>'.$today_str.') as gdd_today_money,
				( select SUM(estimateFee*leve1/100) from tqk_jdorder where (validCode >15 AND validCode <18) and uid = '.$uid.' and orderTime>'.$today_str.') as ujd_today_money,
				( select SUM(estimateFee*leve2/100) from tqk_jdorder where (validCode >15 AND validCode <18) and fuid = '.$uid.' and orderTime>'.$today_str.') as fjd_today_money,
				( select SUM(estimateFee*leve3/100) from tqk_jdorder where (validCode >15 AND validCode <18) and guid = '.$uid.' and orderTime>'.$today_str.') as gjd_today_money,
				( select SUM(profit*leve1/100) from tqk_mtorder where (status = 1 OR status = 8) and uid = '.$uid.' and paytime>'.$today_str.') as umt_today_money,
				( select SUM(profit*leve2/100) from tqk_mtorder where (status = 1 OR status = 8) and fuid = '.$uid.' and paytime>'.$today_str.') as fmt_today_money,
				( select SUM(profit*leve3/100) from tqk_mtorder where (status = 1 OR status = 8) and guid = '.$uid.' and paytime>'.$today_str.') as gmt_today_money';
        $res = $usermod->field($field)->find();
        $userinfo['pre_money']=$res['pre_money'] ? $res['pre_money'] : 0;
        $userinfo['this_money']=$res['this_money'] ? $res['this_money'] : 0;
        $todayMoney = round($res['fmt_today_money']+$res['gmt_today_money']+$res['umt_today_money']+$res['u_today_money']+$res['udd_today_money']+$res['ujd_today_money']+$res['f_today_money']+$res['fdd_today_money']+$res['fjd_today_money']+$res['g_today_money']+$res['gdd_today_money']+$res['gjd_today_money'], 2);
        $userinfo['today_money']=$todayMoney ? $todayMoney : 0;
        $lang = array(
		'msg'=>'口令复制成功，现在咑開手机綯宝APP即可下单',
		'auth'=>'淘宝官方要求“授权备案”后分享和购买商品才可以获得返利',
		'notice'=>'确定收货'.C('yh_ComputingTime').'天后结算到账，余额满'.C('yh_Quota').'元可提现。'
		);
        $json =[
            'userinfo'=>$userinfo,
            'notice'=>$notice,
            'lang'=>$lang,
            'ratio'=>C('yh_fanxian'),
            'quota'=>C('yh_Quota'),
			'payment_alipay'=>C('yh_payment_alipay'),
			'payment_wechat'=>C('yh_payment_wechat')
        ];

        $this->Exitjson(200, '', $json);
		
		}
		
		}
		
		$this->Exitjson(500, '');
		
    }


    public function elm()
    {
		
		$openid = $this->Authtoken();
		$res = $this->getuid($openid);
		//$uid = $res['id'];
		$data = $this->getElmLink($res);
        if ($data) {
            $this->Exitjson(200, '', $data);
        }

        $this->Exitjson(400, '没有获取到饿了么数据！');
    }


    public function wxposter()
    {
        $openid = $this->Authtoken();
		$res = $this->getuid($openid);
		$uid = $res['id'];
        $Path = 'data/upload/editer/'.$openid.'.jpg';
        if (file_exists($Path)) {
            $data = ['img'=>trim(C('yh_site_url')).'/'.$Path];
            $this->Exitjson(200, '', $data);
        }
        $Name = 'wxAccessToken';
        $FromId = $uid;
        if (S($Name)) {
            $AccessToken = S($Name);
        } else {
            $Url = 'https://api.weixin.qq.com/cgi-bin/token';
            $Param = [
                'grant_type'=>'client_credential',
                'appid'=>trim(C('yh_wechat_appid')),
                'secret'=>trim(C('yh_wechat_secret')),
            ];
            $Result = $this->_curl($Url, $Param);
            $Result = json_decode($Result, true);
            if ($Result['access_token']) {
                S($Name, $Result['access_token'], 7000);
                $AccessToken = $Result['access_token'];
            }
        }

        if ($AccessToken) {
            $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$AccessToken;
            $Param = [
                'scene'=>$FromId ? $FromId : 0,
                //'page'=>'pages/index/index', 临时
            ];

            $result = $this->api_notice_increment($url, json_encode($Param));
            $newFilePath = 'data/upload/editer/'.$openid.'_temp.jpg';
            $newFile = fopen($newFilePath, "w");
            fwrite($newFile, $result);
            fclose($newFile);

            $imgUrl =  $this->CreatePic($newFilePath, $openid);

            $data = ['img'=>trim(C('yh_site_url')).'/'.$imgUrl];
        }

        $this->Exitjson(200, '', $data);
    }

    public function bindPhone()
    {
        $usermod = new userModel();
        $phone = $this->params['phone'];
        $verify = $this->params['smscode'];
        $password = $this->params['password'];
        $openid = $this->params['openid'];
		$unionid = $this->params['unionid'];
        $src = $this->params['src'];
        $nickname = $this->remoji($this->params['nickName']);
        $now = NOW_TIME;

        switch ($src) {
            case 'qq':
            $opeindKey = 'qqAppOpenid';
            break;
            case 'bd':
            $opeindKey = 'bdAppOpenid';
            break;
            default:
            $opeindKey = 'wxAppOpenid';
        }

        if (0 < C('yh_sms_status') && S($phone) != $verify) {
            $this->Exitjson(400, '短信验证码输入不正确');
        }
        $info = $usermod->field('id,password,openid,webmaster_pid,status,jd_pid,reg_time,webmaster_rate,money,phone,score,frozen')->where([
            'phone' => $phone
        ])->find();

        if ($info && $info['status'] != 1) {
            $this->Exitjson(400, '账号被禁用', $data);
        }

        if ($info) {
            if (md5($password)==$info['password']) {
				$map = [
				        $opeindKey => $openid
				    ];
				$wechat = $usermod->field('id,money,openid,wxAppOpenid,bdAppOpenid,webmaster_pid,status,jd_pid,reg_time,opid,unionid,score,frozen')->where($map)->find();
				if($wechat && $wechat['reg_time']>$info['reg_time'] && $info['id']!=$wechat['id']){
					$this->mergeUser($wechat['id'],$info['id']);
					$des = $usermod->where(['id'=>$wechat['id']])->delete();
					if ($des) {
						$data = array(
						$opeindKey=>$openid,
						'money'=>array('exp','money+'.$wechat['money'].''),
						'score'=>array('exp','score+'.$wechat['score'].''),
						'frozen'=>array('exp','frozen+'.$wechat['frozen'].''),
						);
						
						if($wechat['unionid'] && strlen($wechat['unionid']) > 1){
							$data['unionid'] = $wechat['unionid'];
						}
						
					    $res = $usermod->where(['id'=>$info['id']])->save($data);
						F('data/'.$info['openid'], $info['openid']);
						unset($info['password']);
						$this->Exitjson(200, '绑定成功', $info);
					}
					
				}elseif($wechat && $info['id']!=$wechat['id']){
					$this->mergeUser($info['id'],$wechat['id']);
					$des = $usermod->where(['id'=>$info['id']])->delete();
					if ($des) {
						$data = array(
						'phone'=>$info['phone'],
						'password'=>$info['password'],
						'money'=>array('exp','money+'.$info['money'].''),
						'score'=>array('exp','score+'.$info['score'].''),
						'frozen'=>array('exp','frozen+'.$info['frozen'].''),
						);
						
						if($unionid && strlen($unionid) > 1){
							$data['unionid'] = $unionid;
						}
						
					   $res = $usermod->where(['id'=>$wechat['id']])->save($data);
					   
					   F('data/'.$wechat['openid'], $wechat['openid']);
					   unset($wechat['password']);
					   $this->Exitjson(200, '绑定成功', $wechat);
					}
					
					
				}
				
                $map = [
                    $opeindKey => $openid,
                    'nickname'=>$nickname ? $nickname : '匿名',
                    'username'=>$nickname ? $nickname : '匿名',
                    'avatar'=>$this->params['avatarUrl'],
                    'last_time'=>time(),
                    'last_ip'=>get_client_ip(),
                    'login_count'=>['exp', 'login_count+1']
                ];
				
				if($unionid && strlen($unionid) > 1){
					$map['unionid'] = $unionid;
				}

                $res = $usermod->where(['id'=>$info['id']])->save($map);

                if ($res) {
                    F('data/'.$info['openid'], $info['openid']);
                    unset($info['password']);
                    $this->Exitjson(200, '绑定成功', $info);
                }

                $this->Exitjson(400, '关联账号失败');
            } else {
                $this->Exitjson(400, '您输入的手机号和密码不正确！');
            }
        }

        if ($this->params['fromid']) {
            $exist = $usermod->field('id,fuid,guid')->where(['id'=>$this->params['fromid']])->find();
        }

        $agentcondition=trim(C('yh_agentcondition'));
        if (0 >= $agentcondition) {
            $tbname = 1;
        } else {
            $tbname = 0;
        }

        $data = [
            'phone'=>$phone,
            'password' =>md5($password),
            'nickname'=>$nickname ? $nickname : '匿名',
            'username'=>$nickname ? $nickname : '匿名',
            'reg_ip'=>get_client_ip(),
            'avatar'=>$this->params['avatarUrl'],
            $opeindKey => $openid,
            'state'=>1,
            'tbname'=>$tbname,
            'status'=>1,
            'reg_time'=>$now,
            'last_time'=>$now,
            'create_time'=>$now,
            'openid'=>$openid,
            'fuid'=>$exist['id'] ? $exist['id'] : 0,
            'guid'=>$exist['fuid'] ? $exist['fuid'] : 0
        ];
		
		if($unionid && strlen($unionid) > 1){
			$data['unionid'] = $unionid;
		}

        $res=$usermod->add($data);

        if ($res) {
            $this->reinvi($res);
            $data = [
                'openid'=>$openid,
                'id'=>$res,
            ];
            F('data/'.$openid, $openid);
            $this->Exitjson(200, '绑定成功', $data);
        }

        $this->Exitjson(400, 'code:2 绑定失败!');
    }

    public function smscode()
    {
        $phone = $this->params['phone'];
        $tempid=trim(C('yh_sms_reg_id'));
        $code = rand(100000, 999999);
        $data=[
            'phone'=>$phone,
            'code'=>$code,
            'temp_id'=>$tempid
        ];
        $res= $this->send_sms($data);

        $res = json_decode($res, true);

        if ($res) {
            S($phone, $code);
            $this->Exitjson(200, '验证码发送成功');
        }
        $this->Exitjson(400, $res['errmsg']);
    }

    public function miling()
    {
        $kouling=kouling($this->params['img'], $this->params['title'], urldecode($this->params['url']));
        if ($kouling) {
            $this->Exitjson(200, '成功', $kouling);
        }
        $this->Exitjson(400, '程序异常');
    }

    public function convert()
    {
	
	$openid = $_SERVER['HTTP_AUTHTOKEN'];
	if($openid){
	$res = $this->getuid($openid);
	$uid = $res['id'];
	}
	
	$pid = trim(C('yh_taobao_pid'));
	
		// if($uid && $res['webmaster']!=1 && !$res['webmaster_pid']){
		// 	$R = A("Records");
		// 	$Arr = explode('-',$this->params['num_iid']);
		// 	$itemId = $Arr[1]?$Arr[1]:$this->params['num_iid'];
		// 	$data= $R ->content($itemId,$uid); 
		// 	$pid = $data['pid'];
		// }
		$quanurl = $this->Tbconvert($this->params['num_iid'],$res,$this->params['quanid']);
		 $this->Exitjson(200, '成功', $quanurl);
		/*
        $apiurl=$this->tqkapi.'/gconvert';
        $apidata=[
            'tqk_uid'=>$this->tqkuid,
            'time'=>time(),
			'pid'=>$pid,
            'good_id'=>''.$this->params['num_iid'].''
        ];
        $token=$this->create_token(trim(C('yh_gongju')), $apidata);
        $apidata['token']=$token;
        $res= $this->_curl($apiurl, $apidata, false);
        $res = json_decode($res, true);
        $me=$res['me'];
        if (\strlen($me)>5 && $this->params['quanid']) {
			$activityId =$this->params['quanid'] ? '&activityId='.$this->params['quanid'] : '';
            $quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.$activityId.'&itemId='.$this->params['num_iid'].'&pid='.$pid.'&af=1';
            $this->Exitjson(200, '成功', $quanurl);
        } elseif (\strlen($me)>5) {
            $quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.'&itemId='.$this->params['num_iid'].'&pid='.$pid.'&af=1';
            $this->Exitjson(200, '成功', $quanurl);
        }elseif($res['item_url']){
			$quanurl=$res['item_url'];
			$this->Exitjson(200, '成功', $quanurl);
		}*/

        $this->Exitjson(400, '程序异常');
    }
	
	
	public function wxlogin(){
		
		$mod = new userModel();
		$url = 'https://api.weixin.qq.com/sns/jscode2session';
		$data = [
		    'appid'=>trim(C('yh_wechat_appid')),
		    'secret'=>trim(C('yh_wechat_secret')),
		    'js_code'=>$this->params['code'],
		    'grant_type'=>'authorization_code'
		];
		
		$res = $this->_curl($url, $data);
		$json = json_decode($res, true);
		if ($json && $json['openid']) {
			
			$UnionId = C('yh_unionid');
			if($UnionId == 1 && $json['unionid']){
			$where['unionid'] = $json['unionid'];
			}else{
			$where['wxAppOpenid'] = $json['openid'];	
			}
		    $user_info = $mod->where($where)
		    ->field('openid,webmaster_pid,jd_pid,webmaster_rate,id,status,tbname,webmaster')
		    ->find();
		
		    if ($user_info && $user_info['status'] != 1) {
		        $this->Exitjson(400, '账号被禁用', $data);
		    }
		
		    if ($user_info) {//登录成功
		        $map = [
		            'openid'=>$user_info['openid'],
		        ];
		        $data = [
		            'last_time'=>time(),
		            'last_ip'=>get_client_ip(),
		            'login_count'=>['exp', 'login_count+1']
		        ];
		        $mod->where($map)->save($data);
		
		        $map['relationid'] = $user_info['webmaster_pid'];
		        F('data/'.$user_info['openid'], $user_info['openid']);
		        $this->Exitjson(200, '', $user_info);
		    }
			
			}
			 $this->Exitjson(300, '登录失败');
		
	}
	
	public function GetLive(){
		
		$uid = $this->params['uid'];
		$authorid = $this->params['authorid'];
		
		$data = $this->DuomaiLink('14633','',array('euid'=>$uid?$uid:'m001','douyin_openid'=>$authorid));
		
		if($data['url']){
			
		  $this->Exitjson(200, '', $data['url']);	
		}
		 $this->Exitjson(300, '转链接失败！');
		
	}
	
	public function MultiLink(){
		
		$id = $this->params['id'];
		$jumpmode = $this->params['jumpmode'];
		$mod = $this->params['mod'];
		$ext = $this->params['ext'];
		$uid = $this->params['uid'];
		
		
		switch($mod){
			
			case "douyin":
			
			$data = $this->DuomaiLink('14882',$ext,array('euid'=>$uid?$uid:'m001'));
			
			if($data['url']){
			$data = [
				'kouling'=>$data['url'],
				'msg'=>'现在打开抖音APP,即可进入商品页面',
				'jumpmode'=>$jumpmode,
			];
			
			}
			
			break;
			
			default:
			break;
			
		}
		
		
		 $this->Exitjson(200, '', $data);
		 
	}
	
	public function Detail(){
		
		$uid = $this->params['uid'];
		$id = $this->params['id'];
		$mod = $this->params['mod'];
		$cid = $this->params['cid'];
		
		$lang = [
			'Em'=>'约返',
			'sold'=>'销量',
			'share'=>'分享赚',
			'buy'=>'立即领取'
		];
		
		switch($mod){
			
			case 'douyin':
			
			 $where = array(
			 'ids'=>$id
			 );
			 $info = $this->DmRequest('cps-mesh.cpslink.douyin.product.detail.get',$where);
			$info = $info['data'][0];
			$info = [
							'title'=> $info['item_title'],
							'commission_rate' =>floatval($info['commission_rate'])*100, //要统一算法
							'pict'=> $info['item_picture'],
							'coupon_price'=> $info['item_final_price'],
							'price'=> $info['item_price'],
							'quan'=> $info['coupon_price']?$info['coupon_price']:0,
							'sold'=> $this->floatNumber($info['item_volume']),
							'itemid'=> $info['item_id'],
							'jumpmode'=>'miling',
							'ext'=>$info['item_url'],
							'detailImages'=> $info['item_small_pictures'],
			];
			
			$data = [
				    'detail'=>$info,
				    'relation'=>$relation
				];
			
			if($data){
				 $this->Exitjson(200, '', $data,$lang);
			}
			break;
			
			
			case 'jd':
			
			$data = $this->jditem($id,$cid);
			if($data){
				 $this->Exitjson(200, '', $data,$lang);
			}
			break;
			
			case 'pdd':
			
			$info = $this->PddGoodsDetail($id);
			if ($info) {
			 $info = [
				'title'=> $info['goods_name'],
				'commission_rate' =>$info['promotion_rate']/10, //要统一算法
				'pict'=> $info['goods_thumbnail_url'],
				'coupon_price'=> $info['coupon_price'],
				'price'=> $info['min_group_price'],
				'quan'=> $info['coupon_discount'],
				'sold'=> $info['sold_quantity'],
				'itemid'=> $info['goods_sign'],
				'detailImages'=> $info['goods_image_url'],
			 ];
			$mod = new itemsModel();
			$relation = $mod->GoodsList(8, ['pass'=>1, 'isshow'=>1, 'ems'=>1], 'ordid desc');
			$data = [
				    'detail'=>$info,
				    'relation'=>$relation
				];
			    $this->Exitjson(200, '', $data,$lang);
			}
			
			break;
			
			default:
			break;
			
			$this->Exitjson(200, '没查到商品信息');
			
		}
		
		$this->Exitjson(200, '没查到商品信息');
	}
	
	public function douyincate(){
		// $data = array(
		// '20000'=>'3C数码',
		// '20005'=>'服饰内衣',
		// '20026'=>'运动户外',
		// '20006'=>'箱包鞋帽',
		// '20029'=>'美妆护肤',
		// '20070'=>'家居日用',
		// '20028'=>'母婴用品',
		// '20103'=>'生活服务',
		// '20065'=>'生活电器',
		// );
		
		$data = array();
		
		$this->Exitjson(200, '', $data);
	}
	
	public function vphcate(){
		$this->Exitjson(200, '没有分类数据');
	}
	
	
	public function Multi(){
	
	$mod = $this->params['mod'];
	$data = array();
	$size = 20;
	$page	= I('page',1 ,'intval');
	$key    = I("key");
	$key    = urldecode($key);
	$sort	= I('sort');
	$cid	= I('pid',0 ,'intval');
	$pop   = I("stype");
	
	switch($mod){
		
		case 'jd':
		
		$PageParam = [
			'fontcolor'=>'#FFFFFF',
			'bgcolor'=>'#F2480B',
			'banner'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01uh0UK82MgYl6FtUob_!!3175549857.jpg',
			'title'=>'京东专区',
			'Em'=>'约返',
			'mod'=>'jd',
			'iscate'=>1,
			'sort'=>[
				'new'=>'综合',
				'hot'=>'评论数',
				'price'=>'价格',
				'coupon'=>1,
				'ishow'=>1
			],
		];
		
		if($key){
		 $where['title'] = array( 'like', '%' . $key . '%' );
		}
		
		if($pop && $pop!='false'){
		 $where['item_type'] = 4;
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
		if($data['goodslist']){
			 $this->Exitjson(200, '', $data['goodslist'],$PageParam);	
		}
		
		break;
		
		case 'pdd':
		
		$PageParam = [
			'fontcolor'=>'#FFFFFF',
			'bgcolor'=>'#8C34E9',
			'banner'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN017LZJ5O2MgYl1qGgen_!!3175549857.jpg',
			'title'=>'拼多多专区',
			'Em'=>'约返',
			'sales'=>'销量',
			'mod'=>'pdd',
			'iscate'=>1,
			'sort'=>[
				'new'=>'综合',
				'hot'=>'销量',
				'price'=>'价格',
				'coupon'=>1,
				'ishow'=>1
			],
		];
		
		if(!$sort || $sort == 'new'){
			$sort = 12;
		}
		if($sort == 'hot'){ $sort = 6;}
		if($sort == 'price'){ $sort = 9;}
		
		$data = $this->PddGoodsSearch($cid,$page,$key,$sort,'',$size=20,$pop);
		
		if($data['goodslist']){
			 $this->Exitjson(200, '', $data['goodslist'],$PageParam);	
		}
		break;
		
		case 'douyin':
		
		$PageParam = [
			'fontcolor'=>'#FFFFFF',
			'bgcolor'=>'#170B1A',
			'banner'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01JFbGPA2MgYl3YFs6K_!!3175549857.png',
			'title'=>'抖音精选',
			'Em'=>'约返',
			'sales'=>'销量',
			'mod'=>'douyin',
			'iscate'=>2,// 不显示分类
			'sort'=>[
				'new'=>'综合',
				'hot'=>'销量',
				'price'=>'价格',
				//'coupon'=>1,
				'ishow'=>2 //不显示排序
			],
		];
		
		$where = array(
		'channel_id'=>'137',
		'page'=>$page,
		'page_size'=>$size
		);
		
		$data = $this->DmRequest('cps-mesh.open.products.query.get',$where);
		if($data['data']){
			$goodslist=[];
			foreach($data['data'] as $k=>$v){
				
				if(($key && $this->FilterWords($key)) || $this->FilterWords($v['item_title'])){
				continue;
				}
				
				$query = parse_url($v['item_url'], PHP_URL_QUERY);
				parse_str($query, $arr);	
				
				$goodslist[$k]['id']=$arr['id'];
				$goodslist[$k]['goods_id']=$arr['id'];
				$goodslist[$k]['title']=$v['item_title'];
				$goodslist[$k]['pic_url']=$v['item_picture'];
				$goodslist[$k]['coupon_price']=$v['item_final_price'];
				$goodslist[$k]['price']=$v['item_price'];
				$goodslist[$k]['commission_rate']=floatval($v['commission_rate'])*100;
				$goodslist[$k]['quan']=$v['coupon_price'];
				$goodslist[$k]['volume']=$this->floatNumber($v['item_volume']);
				$goodslist[$k]['itemid']=$arr['id'];
				
			}
			
			 $this->Exitjson(200, '', array_values($goodslist),$PageParam);	
		}
		
		break;
		
		default:
		break;
		
	}
		
		
		 $this->Exitjson(200, '没有数据了',array(),$PageParam);	
		
	}
	
	
	public function Live(){
		
		$page = $this->params['page'];
		$data = $this->DmRequest(
		'cps-mesh.cpslink.douyinSelf.liveMaterial.get',[
		"page"=>$page,
		"page_size"=>20
		]
		);
		
		$lang = array(
		// 'tabs'=>array(
		// 	array(
		// 	'name'=>'直播间',
		// 	'path'=>'/pages/other/live',
		// 	),
		// 	array(
		// 	'name'=>'精选商品',
		// 	'path'=>'/pages/cate/multi?mod=douyin',
		// 	)
		// 	),
		    'title'=>'抖音直播间',
			'bgcolor'=>'#FF9B5F',
			'fontcolor'=>'#ffffff',
			'banner'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01neAAQD2MgYl7dQmnf_!!3175549857.jpg'
		);
		
		if ($data['data']) {
		    $this->Exitjson(200, '', $data['data'],$lang);
		} else {
		    $this->Exitjson(200, '没有数据');
		}
		
		
	}
	
	public function localquery(){
		$tab = $this->params['tab'];
		$id = $this->params['id'];
		$uid = $this->params['uid'];
		$relationid = $this->params['relationid'];
		if(!$uid){ 
			$this->Exitjson(500,'请登录后再操作');
		}
		
		switch($tab){
			
			case 'didi':
			$data = $this->DuomaiLink($id,'https://www.didiglobal.com/',array('euid'=>$uid?$uid:'m001'));
			$res = array(
			'data'=>$data['wx_path'],
			'tab'=>'didi',
			'appid'=>$data['wx_appid'],
			'qrcode'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode($data['wx_qrcode']),
			'tabs'=> $this->DidiTab()
			);
			break;
			case 'cz':
			
			$data = $this->phoneBill($this->params['uid']);
			$data = $data['resource_url_response'];
			$res = array(
			'data'=>$data,
			'tab'=>'cz'
			);
			
			break;
			case 'mt':
			$data = $this->MeituanLink($id,$uid,4);
			$appid = 'wxde8ac0a21135c07d';
			if($id == 105){
				$appid = 'wx77af438b3505c00e';
			}
			$res = array(
			'data'=>$data,
			'tab'=>'mt',
			'appid'=>$appid,
			'qrcode'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode($this->MeituanCode($id,$uid)),
			'tabs'=> $this->MeituanTab()
			);
			break;
			case 'dmmt':
			$data = $this->DuomaiLink($id,'https://i.meituan.com',array('euid'=>$uid?$uid:'m001'));
			$res = array(
			'data'=>$data['wx_path'],
			'tab'=>'dmmt',
			'appid'=>$data['wx_appid'],
			'qrcode'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode($data['wx_qrcode']),
			'tabs'=> $this->MeituanDmTab()
			);
			break;
			case 'elm':
			$activityid = $this->ActivityID($id);
			
			$openid = $_SERVER['HTTP_AUTHTOKEN'];
			if($openid){
			$res = $this->getuid($openid);
			//$uid = $res['id'];
			}
            $data = $this->CreateElmLink($activityid,$res);
			//$data = $this->TbkActivity($activityid,$relationid,$uid);
			$res = array(
			'data'=>$data['link']['wx_path'],
			'qrcode'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode($data['link']['mini_qrcode']),
			'tab'=>'elm',
			'appid'=>trim(C('yh_elmappid')),
			'tabs'=>$this->ElmTab()
			);
			
			break;
			
			case 'gaode':
			//联盟不透出高德订单，暂时不能给粉丝返利
			$data = true;
			$res = array(
			'data'=>'shareActivity/basic_activity/page/BasicActivityPop/BasicActivityPop?page_id=4k1Khw5X8wy&gd_from=outside_coupon_&pid='.trim(C('yh_taobao_pid')),
			'tab'=>'gaode',
			'appid'=>'wxbc0cf9b963bd3550'
			);
			break;
			
			case 'taopiaopiao':
			$data = true;
			//联盟不透出淘票票订单，暂时不能给粉丝返利
			$res = array(
			'data'=>'pages/index/index?sqm=dianying.wechat.taobaolianmeng.1.'.trim(C('yh_taobao_pid')).'&url=https%3A%2F%2Ft.taopiaopiao.com%2Fyep%2Fpage%2Fm%2Fstyuc69mu6',
			'tab'=>'taopiaopiao',
			'appid'=>'wx553b058aec244b78'
			);
			break;
			
			default:
			
		}
		
		if($data){
			$this->Exitjson(200,'成功',$res);
		}
		
		$this->Exitjson(200,'转链失败');
		
		
	}
	
	public function locallife(){
		
		$nav = $this->localTab();
		$tab = $this->params['tab'];
		$list = $this->localContent($tab);
		$json = array(
		'nav'=>$nav,
		'list'=>$list
		);
		$this->Exitjson(200, $tab, $json);		
		
	}
	
    public function code2session()
    {
        $mod = new userModel();
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $data = [
            'appid'=>trim(C('yh_wechat_appid')),
            'secret'=>trim(C('yh_wechat_secret')),
            'js_code'=>$this->params['code'],
            'grant_type'=>'authorization_code'
        ];

        $res = $this->_curl($url, $data);
        $json = json_decode($res, true);
        if ($json && $json['openid']) {
			$UnionId = C('yh_unionid');
			if($UnionId == 1 && $json['unionid']){
			$where['unionid'] = $json['unionid'];
			}else{
			$where['wxAppOpenid'] = $json['openid'];	
			}
            $user_info = $mod->where($where)
            ->field('openid,webmaster_pid,jd_pid,webmaster_rate,id,status,tbname,webmaster')
            ->find();

            if ($user_info && $user_info['status'] != 1) {
                $this->Exitjson(400, '账号被禁用', $data);
            }

            if ($user_info) {//登录成功
                $map = [
                    'openid'=>$user_info['openid'],
                ];
                $data = [
                    'last_time'=>time(),
                    'last_ip'=>get_client_ip(),
                    'login_count'=>['exp', 'login_count+1']
                ];
                $mod->where($map)->save($data);

                $map['relationid'] = $user_info['webmaster_pid'];
                F('data/'.$user_info['openid'], $user_info['openid']);
                $this->Exitjson(200, '', $user_info);
            }

            if (C('yh_index_ems') == 0 && C('yh_wx_version')!='1.0.0') { //绑定手机号
                $data = [
                    'wxAppOpenid'=>$json['openid'],
                    'nickName'=>$this->params['nickName'],
                    'avatarUrl'=>$this->params['avatarUrl'],
					'unionid'=>$json['unionid']?$json['unionid']:0,
                    'sms'=>C('yh_sms_status'),
                ];
                $this->Exitjson(300, '', $data);
            }

            //注册新账号
            if ($this->params['fromid']) {
                $exist = $mod->field('id,fuid,guid')->where(['id'=>$this->params['fromid']])->find();
            }
            $pwd = md5($this->params['code']);
            $openid=$json['openid'];
            $nickname=$this->params['nickName'];
            $nickname=$this->remoji($nickname);

            $agentcondition=trim(C('yh_agentcondition'));
            if (0 >= $agentcondition) {
                $tbname = 1;
            } else {
                $tbname = 0;
            }

            $now=NOW_TIME;
            $info=[
                'username'=>$nickname ? $nickname : '匿名',
                'nickname'=>$nickname ? $nickname : '匿名',
                'password'=>$pwd,
                'reg_ip'=>get_client_ip(),
                'avatar'=>$this->params['avatarUrl'],
                'wxAppOpenid'=>$openid,
                'state'=>1,
                'tbname'=>$tbname,
                'status'=>1,
                'reg_time'=>$now,
                'last_time'=>$now,
                'create_time'=>$now,
                'openid'=>$openid,
				'unionid'=>$json['unionid'],
                'fuid'=>$exist['id'] ? $exist['id'] : 0,
                'guid'=>$exist['fuid'] ? $exist['fuid'] : 0
            ];
            $res=$mod->add($info);
            if ($res) {
                $this->reinvi($res);

                $data = [
                    'openid'=>$json['openid'],
                    'id'=>$res,
                ];
                F('data/'.$json['openid'], $json['openid']);
                $this->Exitjson(200, '', $data);
            }
        }

        $this->Exitjson(400, $json['errmsg']);
    }

    public function navlist()
    {
        $mod = new itemscateModel();
        $mod = $mod ->cache(true, 10 * 60);
        $catelist=$mod->field('name,id')->where('status = 1 and pid = 0')->order('ordid desc')->select();
        if ($catelist) {
            $this->Exitjson(200, '', $catelist);
        } else {
            $this->Exitjson(200, '没有数据');
        }
    }
	
	public function jdcate()
	{
		$mod = new jditemsModel();
	    $catelist = $mod->Jdcate();
	    if ($catelist) {
	        $this->Exitjson(200, '', $catelist);
	    } else {
	        $this->Exitjson(200, '没有数据');
	    }
	}
	
	public function pddcate()
	{
	    $catelist = $this->PddGoodsCats();
	    if ($catelist) {
	        $this->Exitjson(200, '', $catelist);
	    } else {
	        $this->Exitjson(200, '没有数据');
	    }
	}

public function getdditem(){
	
	$id = I('num_iid');
	$info = $this->PddGoodsDetail($id);
	 $mod = new itemsModel();
	$relation = $mod->GoodsList(8, ['pass'=>1, 'isshow'=>1, 'ems'=>1], 'ordid desc');
	
	if ($info) {
		$data = [
		    'detail'=>$info,
		    'relation'=>$relation
		];
	    $this->Exitjson(200, '', $data);
	} else {
	    $this->Exitjson(200, '没有数据');
	}
	
	
}	


public function jdlink(){
	$itemurl = $this->params['itemurl'];
	$couponlink = $this->params['couponlink'];
	$jd_pid = $this->params['jd_pid'];
	
		//临时使用
		$openid = $_SERVER['HTTP_AUTHTOKEN'];
		if($openid && !$jd_pid){
		$res = $this->getuid($openid);
		$jd_pid = $res['jd_pid'];	
		}
	
$click =$this->jdpromotion($itemurl,$couponlink,$jd_pid);

if ($click && $click!=1) {
$data = array(
'appid'=>'wx91d27dbf599dff74',
'path'=>'pages/union/proxy/proxy?spreadUrl='.urlencode($click),
);
 $this->Exitjson(200, '', $data);   
            } else {
               
 $this->Exitjson(200, '获取链接失败！');   
            }
	
}

public function getjditem(){
	
	$id = I('num_iid');
	$cid	= I('cate_id',0 ,'intval');
	$mod = new jditemsModel();
	$result=$mod->field('cid2', true)->where(['itemid'=>$id])->find();
	if(!$result){
	$detail = $this->JdGoodsDetail($id);
	$result['commission_rate']=$detail['commissionShare'];
	$result['quan']=$detail['couponAmount']>0?$detail['couponAmount']:0;
	$result['couponlink']=$detail['couponLink'];
	$result['pict']=$detail['picMain'];
	$result['itemurl']=$detail['materialUrl'];
	$result['coupon_price']=$detail['actualPrice'];
	$result['price']=$detail['originPrice'];
	$result['owner']=$detail['isOwner'];
	$result['comments']=$detail['comments'];
	$result['cate_id']=$detail['cid1'];
	$result['itemid']=$detail['skuId'];
	$result['title']=$detail['skuName'];
	$result['detailImages']=$detail['detailImages'];
	}
	
	if(!$result['detailImages']){
		$info = $this->JdGoodsQuery($id);
		$result['detailImages'] = explode(",", $info['data'][0]['detailImages']);
	}
	
	
	foreach($result['detailImages'] as $k=>$v){
		if(strpos($v,'//') === 0){
			$result['detailImages'][$k]='https:'.$v;
		}else{
			$result['detailImages'][$k]=$v;
		}
	}
	
	
	if($cid){
	 $where = [
	     'quan'=>['gt', 0],
		 'cate_id'=>$cid
	 ];
	 $order = 'id desc';
	 $jditem = $mod->Jditems(8, $where, $order);
	 }
	
	if ($result) {
		$data = [
		    'detail'=>$result,
		    'relation'=>$jditem
		];
	    $this->Exitjson(200, '', $data);
	} else {
	    $this->Exitjson(200, '没有数据');
	}
	
	
}

public function jdlist(){
	
	$size = 20;
	$page	= I('page',1 ,'intval');
	$key    = I("key");
	$key    = urldecode($key);
	$sort	= I('sort');
	$cid	= I('pid',0 ,'intval');
	if($key){
	 $where['title'] = array( 'like', '%' . $key . '%' );
	}
	$pop   = I("stype");
	if($pop && $pop!='false'){
	 $where['item_type'] = 4;
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
	
	$lang = array(
	                'Em'=>'约返',
	            );
				
    if($data['goodslist']){
	$this->Exitjson(200, '', $data['goodslist'], $lang);
	}
				
	$this->Exitjson(400, '没有相关商品！');
	
}

	
public function pddlist(){
	$cid	= I('pid',0 ,'intval');
	$page	= I('page',1 ,'intval');
	$key    = I("key");
	$key    = urldecode($key);
	$sort	= I('sort', '12', 'intval');
	$sort	=$sort == 0?12:$sort;
	$stype    = I("stype");
	if($key && $page>1){
	$data = false;
	}else{
	$data = $this->PddGoodsSearch($cid,$page,$key,$sort,'',$size=20,$stype);	
	}
	

  $lang = array(
                'Em'=>'约返',
            );
	
	if($data['goodslist']){
	$this->Exitjson(200, '', $data['goodslist'], $lang);
	}
				
	$this->Exitjson(400, '没有相关商品！');	
		
		
	}

    public function cate()
    {
        $mod = new itemsModel();
        $mod = $mod ->cache(true, 10 * 60);
        $size	= 20;
        $pid = $this->params['pid'];
        $key = $this->params['key'];
        $sort = $this->params['sort'];
        $ali_id = $this->params['ali_id'];
        $page = $this->params['page'];
        $start = abs($size * $page);
        $key    = urldecode($key);
        $where['ems'] = 1;
        $where['status'] = 'underway';
        if ($key) {
            $where['title'] = ['like', '%' . $key . '%'];
        }
        if ($pid) {
            $where['cate_id'] = $pid;
        }
        if ($ali_id) {
            $where['ali_id'] = $ali_id;
        }
		
		$action = $this->params['action'];
		switch($action){
			case "chaohuasuan":
			$where['cate_id'] = 28026;
			$banner = 'https://img.alicdn.com/imgextra/i4/126947653/O1CN01zB6KOQ26P7kZ09xAe_!!126947653.png';
			$bgcolor = '#FF590B';
			$fontcolor = '#ffffff';
			break;
			case "haitao":
			$where['cate_id'] = 32362;
			$banner = 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN018UeWgF2MgYgER10Tx_!!3175549857.jpg';
			$bgcolor = '#DC6493';
			$fontcolor = '#ffffff';
			break;
			case "jiu":
			$where['coupon_price'] = array(
			            array(
			                'egt',
			                0
			            ),
			            array(
			                'elt',
			                9.9
			            )
			);
			$banner = 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01bd9N6q2MgYhhsA238_!!3175549857.jpg';
			$bgcolor = '#F34F35';
			$fontcolor = '#ffffff';
			break;
			case "tuijian":
			$where['volume'] = array('gt','10000');
			$banner = 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01VS4lBd2MgYhiSWCu7_!!3175549857.jpg';
			$bgcolor = '#FF8F61';
			$fontcolor = '#ffffff';
			break;
			default:
			$action = false;
			$bgcolor = '#FF0000';
			$fontcolor = '#ffffff';
			
		}
		

        switch ($sort) {
                    case 'new':
                        $order = 'id DESC';
                        break;
                    case 'price':
                        $order = 'coupon_price ASC';
                        break;
                    case 'rate':
                        $order = 'quan DESC';
                        break;
                    case 'hot':
                        $order = 'volume DESC';
                        break;
                    default:
                        $order = 'ordid desc';
        }
		
            $items_list = $mod->where($where)->field('id,cate_id,ali_id,num_iid,pic_url,title,coupon_price,commission_rate,price,quan,shop_type,volume,quanurl,Quan_id,sellerId,quankouling,add_time')->order($order)->limit($start . ',' . $size)->select();
            $goodslist=[];
			if ($items_list) {
                
                foreach ($items_list as $k=>$v) {
                    if(($key && $this->FilterWords($key)) || $this->FilterWords($v['title'])){
                    continue;
                    }
                    $goodslist[$k]['id']=$v['id'];
                    $goodslist[$k]['num_iid']=$v['num_iid'];
                    $goodslist[$k]['pic_url']=$v['pic_url'];
                    $goodslist[$k]['title']=$v['title'];
                    $goodslist[$k]['rebate']=Rebate1($v['coupon_price']*$v['commission_rate']/10000);
                    $goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
                    $goodslist[$k]['coupon_price']=round($v['coupon_price'], 2);
                    $goodslist[$k]['price']=round($v['price'], 2);
                    $goodslist[$k]['quan']=(int) ($v['quan']);
                    $goodslist[$k]['shop_type']=$v['shop_type'];
                    $goodslist[$k]['volume']=$v['volume'];
                    $goodslist[$k]['quanurl']=$v['quanurl'];
                    $goodslist[$k]['Quan_id']=$v['Quan_id'];
                    $goodslist[$k]['cate_id']=$v['cate_id'];
                    $goodslist[$k]['ali_id']=$v['ali_id'];
                    $goodslist[$k]['sellerId']=$v['sellerId'];
                    $goodslist[$k]['quankouling']=$v['quankouling'] ? $v['quankouling'] : '';
                }
            }
        // }

        $appkey=trim(C('yh_taobao_appkey'));
        $appsecret=trim(C('yh_taobao_appsecret'));
        $apppid=trim(C('yh_taobao_pid'));
        $apppid=explode('_', $apppid);
        $AdzoneId=$apppid[3];
        $count=count($items_list);
        if (!$action &&  ($key || $ali_id) && $appkey && $appsecret && $count<=20 && $AdzoneId) {
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
            if ($ali_id) {
                $req->setCat("".$ali_id."");
            }
            if ($key) {
                $req->setQ((string)$key);
            }
            if ($page>0) {
                $req->setPageNo("".$page."");
            } else {
                $req->setPageNo(1);
            }

            //$req->setHasCoupon('true');
           // $req->setIncludePayRate30("true");
            if ($sort=='hot') {
                $req->setSort("total_sales_des");
            } elseif ($sort=='price') {
                $req->setSort("price_asc");
            } elseif ($sort=='rate') {
                $req->setSort("tk_rate_des");
            } else {
                $req->setSort("tk_des");
            }
            $resp = $c->execute($req);
            $resp = json_decode(json_encode($resp), true);
            $resp = $resp['result_list']['map_data'];
            $patterns = "/\d+/";
            foreach ($resp as $k=>$v) {
               if(($key && $this->FilterWords($key)) || $this->FilterWords($v['title']) || !$v['item_id']){
               continue;
               }
                $goodslist[$k+$count]['quan']=formatprice($v['coupon_amount']);
                $goodslist[$k+$count]['quanurl']=$v['coupon_share_url'];
                $goodslist[$k+$count]['num_iid']=$v['item_id'];
                $goodslist[$k+$count]['title']=$v['title'];
                $goodslist[$k+$count]['coupon_price']=round($v['zk_final_price']-$goodslist[$k+$count]['quan'], 2);
                $goodslist[$k+$count]['rebate']=Rebate1($goodslist[$k+$count]['coupon_price']*$v['commission_rate']/10000);
                if ($v['user_type']=="1") {
                    $goodslist[$k+$count]['shop_type']='B';
                } else {
                    $goodslist[$k+$count]['shop_type']='C';
                }
                $goodslist[$k+$count]['commission_rate']=$v['commission_rate']; //比例
                $goodslist[$k+$count]['price']=round($v['zk_final_price'], 2);
                $goodslist[$k+$count]['volume']=$v['volume'];
                $goodslist[$k+$count]['ali_id']=$ali_id;
                $goodslist[$k+$count]['pic_url']=$v['pict_url'];
                $goodslist[$k+$count]['quankouling']='';
            }
        }
		
		
        if ($goodslist) {
            if (12 > strlen($key) && strlen($key)>3 && !preg_match('/[a-zA-Z]/', $key)) {
                if (function_exists('opcache_invalidate')) {
                    $basedir = $_SERVER['DOCUMENT_ROOT'];
                    $dir=$basedir.'/data/Runtime/Data/data/hotkey.php';
                    $ret=opcache_invalidate($dir, true);
                }
                $disable_num_iids = F('data/hotkey');
                if (!$disable_num_iids) {
                    $disable_num_iids = [];
                }
                if (\count($disable_num_iids)>5) {
                    $disable_num_iids=\array_slice($disable_num_iids, 1, 5);
                }
                if (!\in_array($key, $disable_num_iids)) {
                    $disable_num_iids[] = $key;
                }
                F('data/hotkey', $disable_num_iids);
            }
            $lang = array(
                'Em'=>'约返',
				'banner'=>$banner,
				'fontcolor'=>$fontcolor,
				'bgcolor'=>$bgcolor,
            );
			
			
			
			if ($pid) {
			    $pid = $ali_id ? $ali_id : $pid;
				$catemod = new  itemscateModel();
			    $cateinfo=$catemod->where('ali_id='.$pid)->field('id,name,seo_title,seo_keys,seo_desc')->find();
			
			}
			
			$seo = C('yh_seo_config.search');
			if ($key && $seo['title']) {
			    $seodata = $this->_config_seo($seo, [
			        'key' => $key,
			    ],false);
			} elseif ($cateinfo) {
			    $seodata = array(
			        'title' => $cateinfo['seo_title'] ? $cateinfo['seo_title'] : '' . $cateinfo['name'] . '淘宝优惠券 - '. C('yh_site_name'),
			        'keywords' => $cateinfo['seo_keys'],
			        'description' => $cateinfo['seo_desc']
			    );
			} else {
			    $seo = C('yh_seo_config.cate');
				$seodata = $this->_config_seo($seo, [
				    'cate_name' => $cateinfo['name'],
					'site_name'=>C('yh_site_name'),
					'seo_keywords'=>$cateinfo['seo_keys'],
					'seo_description'=>$cateinfo['seo_desc']
				],false);
			}
			
            $this->Exitjson(200, $seodata,array_values($goodslist), $lang);
        } else {
            $this->Exitjson(200, '没有数据啦！');
        }
    }
	
	public function chalijin(){
	$numiid = $this->params['num_iid'];
	$lijin = $this->params['lijin'];
	
	$lim = (float)C('yh_jingpintie');
	if($lim>1 && $lijin>$lim){
		$lijin = $lim;
	}
	
	if($numiid){
	$apiurl=$this->tqkapi.'/chalijin';
	$TljPid = trim(C('yh_taolijin_pid'));
	$TbPid = trim(C('yh_taobao_pid'));
	$apidata=array(
	'tqk_uid'=>$this->tqkuid,
	'time'=>time(),
	'numiid'=>$numiid,
	'lijin'=>$lijin,
	'pid'=>$TljPid?$TljPid:$TbPid,
	'commission_rate'=>$this->params['commission_rate']
	);
	
	$token=$this->create_token(trim(C('yh_gongju')),$apidata);
	$apidata['token']=$token;
	$data= $this->_curl($apiurl,$apidata, true);
	$result = json_decode($data,true);
	if($result['code']==200 && $result['content']){
		$this->Exitjson(200,'',$result['content']);
	}
	
	$this->Exitjson('200',$result['msg']);
	
	}
		
	$this->Exitjson('200','获取礼金失败');	
		
	}
	

    public function index()
    {
        $ItemCate = new itemscateModel();
        $catelist=$ItemCate->cate_cache()['p'];
		
        $admod = new adModel();
        $admod = $admod ->cache(true, 10 * 60);
        $adlist=$admod->field('url,img,type,name')->where(['status'=>5, 'add_time'=>0, 'beginTime'=>['lt', NOW_TIME], 'endTime'=>['gt', NOW_TIME]])->order('ordid desc')->select();
        $ad = array();
		foreach ($adlist as $k=>$v) {
            $ad[$k]['title']=$v['url'];
            $ad[$k]['type']=$v['type'];
			$ad[$k]['name']=$v['name'];
            $ad[$k]['image']=C('yh_site_url').$v['img'];
        }
		
        $ArticleMod = new articleModel();
        $articlelist = $ArticleMod->articleList(5);
        $ItemMod = new itemsModel();
        $HotProduct = $ItemMod->GoodsList(8, ['volume'=>['gt', 10000], 'coupon_price'=>['lt', 20]], 'id desc');
        $sqlwhere['pass'] = '1';
        $sqlwhere['isshow'] = '1';
        $sqlwhere['ems'] = '1';
        $sqlwhere['status'] = 'underway';
        if (C('yh_index_shop_type')) {
            $sqlwhere['shop_type']=C('yh_index_shop_type');
        }
        $sqlwhere['quan']=[[
            'egt',
            C('yh_index_mix_price')
        ],
        [
            'elt',
            C('yh_index_max_price')
        ]];
        $sqlwhere['volume']=[[
            'egt',
            C('yh_index_mix_volume')
        ],
        [
            'elt',
            C('yh_index_max_volume')
        ]];
        $itemslist = $ItemMod->GoodsList(30, $sqlwhere, C('yh_index_sort'));
        //$Taolijin =  $ItemMod->GoodsList(5, ['ems'=>2], 'id desc');
		
		if (C('yh_taolijin')>0) {
			
			$taoData = S('taolijin');
			if($taoData){
				$Taolijin = $taoData;
			}else{
				$Taolijin = $this->Toplijin(1,12);
				S('taolijin',$Taolijin);
			}
		
		}
		 if (C('yh_openjd')) {
		$Jdmod = new jditemsModel();
		$where = [
		    'comments' => ['gt', 10000],
		    'quan'=>['gt', 0]
		];
		$order = 'id desc';
		$jditem = $Jdmod->Jditems(30, $where, $order);
		}
		
		if (C('yh_openduoduo')) {
			$duoData = S('duodata');
			if($duoData){
				$pdditem = $duoData;
			}else{
				$pdditem = $this->PddGoodsSearch('','','','','24',30,true);
				S('duodata',$pdditem);
			}
			
		}
		
		
        if ($itemslist || $catelist || $articlelist) {
			
				 $recommendlist = [
					 [
					 'name'=>'京东热销爆品',
					 'tab'=>'jd']
				 ];
				$recommendlist[] = [
									 'name'=>'今日超值推荐',
									 'tab'=>'tb'
				];
				 if(C('yh_openduoduo')){
					 $recommendlist[] = [
						 'name'=>'拼多多爆款热销',
						 'tab'=>'pdd'
					 ];
				}
			
			
            $list=[
                'adlist'=>$ad,
                'hotproduct' =>$HotProduct,
                'itemslist' => $itemslist,
                'catelist'=>$catelist,
                'taolijin'=>$Taolijin,
				'jditem'=>$jditem,
				'pdditem'=>$pdditem['goodslist'],
                'articlelist'=>$articlelist,
                'seo'=>C('yh_seo_config.ershi'),
                'submenu'=>$this->submenu(),
				'seo'=>C('yh_seo_config.ershi'),
                'recommendlist'=>$recommendlist,
            ];
			
			$lang=[
				'NewTitle'=>'淘礼金专区',
			    'New'=>'券后折上折 • 最低0元购',
				'HotTitle'=>'今日爆款',
			    'Hot'=>'爆品荟萃 • 实时更新',
			    'Em'=>'约返',
				'tab'=>$recommendlist[0]['tab'],
			    'Bt'=>'口令复制成功!现在打开啕宝APP即可进入活动页面',
			];
			
            $json=[
                'code'=>200,
                'appstatus'=>C('yh_site_secret'),
                'webname'=>C('yh_site_name'),
                'result'=>$list
            ];
        } else {
            $json=[
                'code'=>400,
                'msg'=>'error'
            ];
        }
		
		$this->Exitjson(200,'',$json,$lang);

    }

    public function brand()
    {
        $Pre=C("DB_PREFIX");
        $mod=M('brandcate');
        $catelist=$mod->cache(true, 30 * 60)->field('name,id')->where('status = 1')->order('ordid asc,id desc')->select();
		if ($catelist) {
            $sql='';
            $si=0;
            $k2=1;
            $field='id,logo,brand,remark,cate_id';
            foreach ($catelist as $k=>$v) {
                if ($si==0) {
                    $sql='(SELECT '.$field.' from '.C("DB_PREFIX").'brand where cate_id='. $v['id'] .' and status=1 order by ordid asc)';
                } else {
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
            foreach ($list as $ik=>$p) {
                $list[$ik]['logo']=C('yh_site_url').$p['logo'];
            }
        }

        if ($catelist && $list) {
            $json=[
                'result'=>$list,
                'cate'=>$catelist
            ];
			$this->Exitjson(200,'',$json);
        }
		$this->Exitjson(200,'没有数据了');

    }

    public function RelationCode()
    {
        $user['id'] = $this->params['id'];

        $inviterCode = $this->RelationInviterCode($user);

        if ($user['id'] && $inviterCode) {
            $this->Exitjson(200, '', $inviterCode);
        }

        $this->Exitjson(400, '获取链接失败');
    }
	
	public function dmorder(){
	
	$p = $this->params['p'];
	$status = $this->params['status']?$this->params['status']:0;
	$page_size = 6;
	$start = abs($page_size * $p);
	$openid = $this->Authtoken();
	$res = $this->getuid($openid);
	$uid = $res['id'];
	$stay['uid'] = $uid;
	if($status){
	$stay['status'] = $status == 3?0:$status;
	}
	 $this->Mod= new dmorderModel();	
			$rows = $this->Mod->field('leve2,leve3,uid',true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
			$list=array();
			foreach($rows as $k=>$v){
			$list[$k]['status']=$v['order_status'];
			$list[$k]['order_sn']=$v['order_sn'];
			$list[$k]['order_amount']=$v['orders_price'];
			$list[$k]['add_time']=date('Y-m-d H:i',$v['order_time']);
			$list[$k]['promotion_amount']=$v['order_commission'];
			//$list[$k]['order_pay_time']=date('Y-m-d H:i',$v['order_time']);
			$list[$k]['order_settle_time']=$v['charge_time']>0?date('Y-m-d H:i',$v['charge_time']):'';
			$list[$k]['settle']=$v['settle'];
			$list[$k]['income']=Orebate1(['price'=>$v['order_commission'], 'leve'=>$v['leve1']]);
			$list[$k]['leve1']=$v['leve1'];
			$list[$k]['id']=$v['id'];
			$list[$k]['goods_name']=$v['goods_name'];
			}
			
		$Tab = $this->OrderTab();
		$data = [
			'Tab'=>$Tab,
			'List'=>$list
		];
		$this->Exitjson(200,'成功',$data);
		
	}
	
public function mtorder(){

$p = $this->params['p'];
$status = $this->params['status']?$this->params['status']:0;
$page_size = 6;
$start = abs($page_size * $p);
$openid = $this->Authtoken();
$res = $this->getuid($openid);
$uid = $res['id'];
$stay['uid'] = $uid;
if($status){
$stay['status'] = $status;
}
 $this->Mod= new mtorderModel();	
		$rows = $this->Mod->field('leve2,leve3,uid',true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
		$list=array();
		foreach($rows as $k=>$v){
			
		$list[$k]['status']=$this->MettuanStatus($v['status']);
		$list[$k]['order_sn']=$v['orderid'];
		$list[$k]['order_amount']=$v['payprice'];
		$list[$k]['add_time']=date('Y-m-d H:i',$v['paytime']);
		$list[$k]['promotion_amount']=$v['estimateFee'];
		//$list[$k]['order_pay_time']=date('Y-m-d H:i',$v['paytime']);
		$list[$k]['order_settle_time']=$v['settle_time']?date('Y-m-d H:i',$v['settle_time']):'';
		$list[$k]['settle']=$v['settle'];
		$list[$k]['income']=Orebate1(['price'=>$v['profit'], 'leve'=>$v['leve1']]);
		$list[$k]['leve1']=$v['leve1'];
		$list[$k]['id']=$v['id'];
		$list[$k]['goods_name']=$v['smstitle'];
		}
		
if($list){
	$Tab = $this->OrderTab();
	$data = [
		'Tab'=>$Tab,
		'List'=>$list
	];
	$this->Exitjson(200,'成功',$data);
}

$this->Exitjson(200,'没有数据了');	

	
}
	

public function jdorder(){

$p = $this->params['p'];
$status = $this->params['status']?$this->params['status']:0;
$page_size = 6;
$start = abs($page_size * $p);
$openid = $this->Authtoken();
$res = $this->getuid($openid);
$uid = $res['id'];
$stay['uid'] = $uid;
if($status){
$stay['validCode'] = $status;
}
 $this->Mod= new jdorderModel();	
		$rows = $this->Mod->field('leve2,leve3,uid',true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
		$list=array();
		foreach($rows as $k=>$v){
			$list[$k]['status']=$this->jdstatus($v['validCode']);
			$list[$k]['order_sn']=$v['orderId'];
			$list[$k]['order_amount']=$v['estimateCosPrice'];
			$list[$k]['add_time']=$v['orderTime'];
			$list[$k]['promotion_amount']=$v['estimateFee'];
			$list[$k]['order_pay_time']=date('Y-m-d H:i',$v['orderTime']);
			$list[$k]['order_settle_time']=$v['payMonth']?date('Y-m-d H:i',$v['payMonth']):'';
			$list[$k]['settle']=$v['settle'];
			$list[$k]['income']=Orebate1(array('price'=>$v['estimateFee'],'leve'=>$v['leve1']));
			$list[$k]['leve1']=$v['leve1'];
			$list[$k]['id']=$v['id'];
			$list[$k]['goods_name']=$v['skuName'];
		}
	$Tab = $this->OrderTab();
		$data = [
			'Tab'=>$Tab,
			'List'=>$list
		];	
	$this->Exitjson(200,'成功',$data);
	
}

public function userorder(){
$p = $this->params['p'];
$status = $this->params['status']?$this->params['status']:0;
$page_size = 6;
$start = abs($page_size * $p);
$openid = $this->Authtoken();
$res = $this->getuid($openid);
$uid = $res['id'];
$stay['uid'] = $uid;
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
			$list[$k]['up_time']=$v['up_time']?date('Y-m-d H:i',$v['up_time']):'';
}


$Tab = $this->OrderTab();
$data = [
	'Tab'=>$Tab,
	'List'=>$list
];

if($list){
	$this->Exitjson(200,'成功',$data);
}

$this->Exitjson(200,'没有数据了',$data);	
	
}

public function pddteam(){
$openid = $this->Authtoken();
$res = $this->getuid($openid);
$uid = $res['id'];
$p = $this->params['p'];
$grade = $this->params['grade'];
$page_size = 6;
$start = abs($page_size * $p);
if($grade == 'fuid'){
$stay['fuid'] = $uid;	
}
if($grade == 'guid'){
$stay['guid'] = $uid;	
}
if(!$grade){
$stay['fuid'] = $uid;	
}
$this->Mod = new pddorderModel();
$rows = $this->Mod->field('p_id,uid',true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
$list=array();
		foreach($rows as $k=>$v){
			
			$list[$k]['order_sn']=$v['order_sn'];
			$list[$k]['order_amount']=$v['order_amount'];
			$list[$k]['add_time']=$v['add_time'];
			$list[$k]['promotion_amount']=$v['promotion_amount'];
			$list[$k]['order_pay_time']=date('Y-m-d H:i',$v['order_pay_time']);
			$list[$k]['order_settle_time']=$v['order_settle_time']?date('Y-m-d H:i',$v['order_settle_time']):'';
			$list[$k]['settle']=$v['settle'];
			if($grade == 'guid'){
			$list[$k]['income']=Orebate3(array('price'=>$v['promotion_amount'],'leve'=>$v['leve3']));
			}else{
			$list[$k]['income']=Orebate2(array('price'=>$v['promotion_amount'],'leve'=>$v['leve2']));
			}
			$list[$k]['leve1']=$v['leve1'];
			$list[$k]['leve2']=$v['leve2'];
			$list[$k]['leve3']=$v['leve3'];
			$list[$k]['id']=$v['id'];
			$list[$k]['goods_name']=$v['goods_name'];
		}
		

	$Tab = $this->OrderTab();
	$data = [
		'Tab'=>$Tab,
		'List'=>$list
	];
	$this->Exitjson(200,'成功',$data);
		
}

	public function dmteam(){
	$openid = $this->Authtoken();
	$res = $this->getuid($openid);
	$uid = $res['id'];
	$p = $this->params['p'];
	$grade = $this->params['grade'];
	$page_size = 6;
	$start = abs($page_size * $p);
	if($grade == 'fuid'){
	$stay['fuid'] = $uid;	
	}
	if($grade == 'guid'){
	$stay['guid'] = $uid;	
	}
	if(!$grade){
	$stay['fuid'] = $uid;	
	}
	$this->Mod = new dmorderModel();	
	$rows = $this->Mod->field('uid',true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
	$list=array();
			foreach($rows as $k=>$v){
				
				$list[$k]['status']=$v['order_status'];
				$list[$k]['order_sn']=$v['order_sn'];
				$list[$k]['order_amount']=$v['orders_price'];
				$list[$k]['add_time']=date('Y-m-d H:i',$v['order_time']);;
				$list[$k]['promotion_amount']=$v['order_commission'];
				//$list[$k]['order_pay_time']=date('Y-m-d H:i',$v['order_time']);
				$list[$k]['order_settle_time']=$v['charge_time']>0?date('Y-m-d H:i',$v['charge_time']):'';
				$list[$k]['settle']=$v['settle'];
				if($grade == 'guid'){
				$list[$k]['income']=Orebate3(array('price'=>$v['order_commission'],'leve'=>$v['leve3']));
				}else{
				$list[$k]['income']=Orebate2(array('price'=>$v['order_commission'],'leve'=>$v['leve2']));
				}
				$list[$k]['leve1']=$v['leve1'];
				$list[$k]['leve2']=$v['leve2'];
				$list[$k]['leve3']=$v['leve3'];
				$list[$k]['id']=$v['id'];
				$list[$k]['goods_name']=$v['goods_name'];
			}
		
			
		$Tab = $this->OrderTab();
		$data = [
			'Tab'=>$Tab,
			'List'=>$list
		];
		$this->Exitjson(200,'成功',$data);

		
		
	}

public function mtteam(){
$openid = $this->Authtoken();
$res = $this->getuid($openid);
$uid = $res['id'];
$p = $this->params['p'];
$grade = $this->params['grade'];
$page_size = 6;
$start = abs($page_size * $p);
if($grade == 'fuid'){
$stay['fuid'] = $uid;	
}
if($grade == 'guid'){
$stay['guid'] = $uid;	
}
if(!$grade){
$stay['fuid'] = $uid;	
}
$this->Mod = new mtorderModel();	
$rows = $this->Mod->field('uid',true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
$list=array();
		foreach($rows as $k=>$v){
			
			$list[$k]['status']=$this->MettuanStatus($v['status']);
			$list[$k]['order_sn']=$v['orderid'];
			$list[$k]['order_amount']=$v['payprice'];
			$list[$k]['add_time']=date('Y-m-d H:i',$v['paytime']);
			$list[$k]['promotion_amount']=$v['profit'];
			//$list[$k]['order_pay_time']=date('Y-m-d H:i',$v['paytime']);
			$list[$k]['order_settle_time']=$v['payMonth']?date('Y-m-d H:i',$v['payMonth']):'';
			$list[$k]['settle']=$v['settle']?date('Y-m-d H:i',$v['settle']):'';
			if($grade == 'guid'){
			$list[$k]['income']=Orebate3(array('price'=>$v['profit'],'leve'=>$v['leve3']));
			}else{
			$list[$k]['income']=Orebate2(array('price'=>$v['profit'],'leve'=>$v['leve2']));
			}
			$list[$k]['leve1']=$v['leve1'];
			$list[$k]['leve2']=$v['leve2'];
			$list[$k]['leve3']=$v['leve3'];
			$list[$k]['id']=$v['id'];
			$list[$k]['goods_name']=$v['smstitle'];
		}
	
		

	$Tab = $this->OrderTab();
	$data = [
		'Tab'=>$Tab,
		'List'=>$list
	];
	$this->Exitjson(200,'成功',$data);

	
	
}


public function jdteam(){
$openid = $this->Authtoken();
$res = $this->getuid($openid);
$uid = $res['id'];
$p = $this->params['p'];
$grade = $this->params['grade'];
$page_size = 6;
$start = abs($page_size * $p);
if($grade == 'fuid'){
$stay['fuid'] = $uid;	
}
if($grade == 'guid'){
$stay['guid'] = $uid;	
}
if(!$grade){
$stay['fuid'] = $uid;	
}
$this->Mod = new jdorderModel();	
$rows = $this->Mod->field('uid',true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
$list=array();
		foreach($rows as $k=>$v){
			$list[$k]['status']=$this->jdstatus($v['validCode']);
			$list[$k]['order_sn']=$v['orderId'];
			$list[$k]['order_amount']=$v['estimateCosPrice'];
			$list[$k]['add_time']=$v['orderTime'];
			$list[$k]['promotion_amount']=$v['estimateFee'];
			$list[$k]['order_pay_time']=date('Y-m-d H:i',$v['orderTime']);
			$list[$k]['order_settle_time']=$v['payMonth']?date('Y-m-d H:i',$v['payMonth']):'';
			$list[$k]['settle']=$v['settle'];
			if($grade == 'guid'){
			$list[$k]['income']=Orebate3(array('price'=>$v['estimateFee'],'leve'=>$v['leve3']));
			}else{
			$list[$k]['income']=Orebate2(array('price'=>$v['estimateFee'],'leve'=>$v['leve2']));
			}
			$list[$k]['leve1']=$v['leve1'];
			$list[$k]['leve2']=$v['leve2'];
			$list[$k]['leve3']=$v['leve3'];
			$list[$k]['id']=$v['id'];
			$list[$k]['goods_name']=$v['skuName'];
		}
	
		

	$Tab = $this->OrderTab();
	$data = [
		'Tab'=>$Tab,
		'List'=>$list
	];
	$this->Exitjson(200,'成功',$data);

	
	
}


public function teamorder(){
$openid = $this->Authtoken();
$res = $this->getuid($openid);
$uid = $res['id'];
$p = $this->params['p'];
$grade = $this->params['grade'];
$page_size = 6;
$start = abs($page_size * $p);
if($grade == 'fuid'){
$stay['fuid'] = $uid;	
}
if($grade == 'guid'){
$stay['guid'] = $uid;	
}
if(!$grade){
$stay['fuid'] = $uid;	
}
$mod = M('order');
$rows = $mod->field('status,orderid,add_time,price,goods_title,settle,income,up_time,id,leve1,leve2,leve3')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
$list=array();
		foreach($rows as $k=>$v){
			$list[$k]['status']=$this->orderstatic($v['status']);
			$list[$k]['state']=$v['status'];
			$list[$k]['orderid']=$v['orderid'];
			$list[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			$list[$k]['price']=$v['price'];
			if($grade == 'guid'){
			$list[$k]['income']=Orebate3(array('price'=>$v['income'],'leve'=>$v['leve3']));
			}else{
			$list[$k]['income']=Orebate2(array('price'=>$v['income'],'leve'=>$v['leve2']));
			}
			$list[$k]['up_time']=$v['up_time']?date('Y-m-d H:i',$v['up_time']):'';
			$list[$k]['id']=$v['id'];
			$list[$k]['settle']=$v['settle'];
			$list[$k]['goods_title']=$v['goods_title'];
			$list[$k]['leve1']=$v['leve1'];
			$list[$k]['leve2']=$v['leve2'];
			$list[$k]['leve3']=$v['leve3'];
		}
		

	$Tab = $this->OrderTab();
	$data = [
		'Tab'=>$Tab,
		'List'=>$list
	];
	$this->Exitjson(200,'成功',$data);
			
}

public function pddlink(){
	$id = $this->params['num_iid'];
	$search_id = $this->params['search_id'];
	
	$data = $this->pddPromotionUrl($id,$search_id,$this->params['uid']);
	if(!$data['we_app_info']['page_path']){
	$data = $this->pddPromotionUrl($id,$search_id,$this->params['uid'],'true');
	}
	//if($data['we_app_info']['page_path']){
		 
		//if($this->params['uid']){
			//$data['we_app_info']['page_path'] = urldecode($data['we_app_info']['page_path']);
			//$data['we_app_info']['page_path'] = str_replace('&cpsSign','&customParameters='.$this->params['uid'].'&cpsSign',$data['we_app_info']['page_path']);
		//}
		 
	//}
	
	$res = array(
	'data'=>$data
	);
	
	if($data){
		$this->Exitjson(200,'成功',$res);
	}
	$this->Exitjson(200,'转链失败');
	
}

public function pddorder(){
	$p = $this->params['p'];
	$status = $this->params['status']?$this->params['status']:0;
	$page_size = 6;
	$start = abs($page_size * $p);
	$openid = $this->Authtoken();
	$res = $this->getuid($openid);
	$uid = $res['id'];
	    if($status){
		$stay['status'] = $status==10?0:$status;
		}
		$stay['uid'] = $uid;
	    $this->Mod= new pddorderModel();
		$rows = $this->Mod->field('p_id,leve2,leve3,uid',true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
		$list=array();
		foreach($rows as $k=>$v){
			$list[$k]['status']=$this->pddstatus($v['status']);
			$list[$k]['order_sn']=$v['order_sn'];
			$list[$k]['order_amount']=$v['order_amount'];
			$list[$k]['add_time']=$v['add_time'];
			$list[$k]['promotion_amount']=$v['promotion_amount'];
			$list[$k]['order_pay_time']=date('Y-m-d H:i',$v['order_pay_time']);
			$list[$k]['order_settle_time']=$v['order_settle_time']?date('Y-m-d H:i',$v['order_settle_time']):'';
			$list[$k]['settle']=$v['settle'];
			$list[$k]['income']=Orebate1(array('price'=>$v['promotion_amount'],'leve'=>$v['leve1']));
			$list[$k]['leve1']=$v['leve1'];
			$list[$k]['id']=$v['id'];
			$list[$k]['goods_name']=$v['goods_name'];
		}

	$Tab = $this->OrderTab();
		$data = [
			'Tab'=>$Tab,
			'List'=>$list
		];
	$this->Exitjson(200,'成功',$data);

	
}

    public function Getrelation()
    {
        $url=$this->tqkapi."/getrelationid";
        $data=[
            'key'=>$this->_userappkey,
            'time'=>time(),
            'tqk_uid'=>	$this->tqkuid,
        ];
        $token=$this->create_token(trim(C('yh_gongju')), $data);
        $data['token']=$token;
        $data=$this->_curl($url, $data, true);
        $result=json_decode($data, true);
        if ($result['code'] == 200) {
            $Data = [];
            $Mod = new UserModel();
            $id = $this->params['id'];
            if ($result['result']['rtag'] == $id) {
                $relation = $result['result']['relation_id'];
                $Data = [
                    'webmaster_pid' =>$result['result']['relation_id'],
                    'webmaster'=>1,
                ];
            } else {
                foreach ($result['result'] as $k=>$v) {
                    if ($v['rtag'] == $id) {
                        $relation = $v['relation_id'];

                        $Data = [
                            'webmaster_pid' =>$v['relation_id'],
                            'webmaster'=>1,
                        ];
                        break;
                    }
                }
            }

            if ($Data) {
                $res=$Mod->where(['id'=>$id])->save($Data);
                if ($res) {
                    $json = [
                        'status'=>1,
                        'relation'=>$relation
                    ];
                }
            } else {
                $json= [
                    'status'=>2
                ];
            }
        } else {
            $json= [
                'status'=>2
            ];
        }

        $this->Exitjson(200, '', $json);
    }

    public function getvideo()
    {
        $type = $this->params['type'];

        if ($type == 'bd') {
            $src = '';
        } else {
            $src = 'https://cloud.video.taobao.com/play/u/3175549857/p/1/e/6/t/1/295667840700.mp4';
        }

        $data = [
            'video' => $src
        ];

        $this->Exitjson(200, '', $data);
    }

    public function getitem()
    {
        $where = [
            'num_iid'=>$this->params['num_iid']
        ];
        $mod = new itemsModel();
        $item = $mod->where($where)->field('id,qq,ordid,ali_id,zc_id,orig_id,tag', true)->find();
		if($this->params['ali_id']){
        $relation = $mod->GoodsList(8, ['ali_id'=>$this->params['ali_id'], 'pass'=>1, 'isshow'=>1, 'ems'=>1], 'ordid desc');
  }
        $lang = [
            'msg'=>'口令复制成功，现在咑開手机綯宝APP即可下单',
            'auth'=>'淘宝官方要求“授权备案”后分享和购买商品才可以获得返利',
        ];
		
		
		$Tag= $this->GetTags($item['title'], 4);
			 $seo = $this->_config_seo(C('yh_seo_config.item'), [
						'title' => $item['title'],
						'intro' => $item['intro'],
						'price' => $item['price'],
						'shop_type' => $item['shop_type']=='B'?'天猫':'淘宝',
						'quan' => $item['quan'],
						'coupon_price' => $item['coupon_price'],
						'tags' => implode(',', $Tag),
		],false);
		

        if ($item) {
            $data = [
                'detail'=>$item,
                'relation'=>$relation
            ];

            $this->Exitjson(200, $seo, $data, $lang);
        }

        $item = $this->GetTbDetail($this->params['num_iid']);

        if ($item) {
            $item['sellerId']=$item['seller_id'];
            $item['pic_url']=$item['pict_url'];
            $item['price']=$item['zk_final_price'];
            $item['quan']=$item['coupon_amount']?$item['coupon_amount']:0;
            $item['link']=$item['item_url'];
            $item['commission_rate']=$item['commission_rate'];
            $item['tk_commission_rate']=$item['commission_rate'];
            $item['click_url']='https:'.$item['url'];
            $item['volume']=$item['volume'];
            $item['coupon_price']=round($item['zk_final_price']-$item['coupon_amount'], 2);
            $item['coupon_end_time']=$item['coupon_end_time'];
            $item['ems']=2;
            $quanurl = $item['coupon_share_url'];
            $item['quanurl']=$quanurl ? 'https:'.$quanurl : 'https:'.$item['url'];
            $item['Quan_id']=$item['coupon_id'];

            $data = [
                'detail'=>$item,
                'relation'=>$relation
            ];

            $this->Exitjson(200, $seo, $data, $lang);
        }

        $this->Exitjson(400, '没有查询到此商品');
    }

    public function classify()
    {
        $mod = new itemscateModel();
        $mod = $mod ->cache(true, 10 * 60);
        $where = [
            'status'=>1
        ];
        $result = $mod->field('id,name,ali_id,pid,remark')->where($where)->order('ordid desc')->select();
        if ($result) {
			
			$data = [
			    'catelist'=>$result
			];
			
			$this->Exitjson(200,'',$data);
        } 
         
		 $this->Exitjson(200,'没数据了');
        
		
       
    }

    public function hotkey()
    {
        $val = F('data/hotkey');
        $json=[
            'code'=>200,
            'endTime'=>NOW_TIME,
            'result'=>$val,
        ];
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }


    public function modify()
    {
        $openid = $this->Authtoken();
        $mod = new userModel();
        $fromdata = $this->params['fromdata'];
        $fromdata = json_decode($fromdata, true);
        $password = $fromdata['password'];
        $password2 = $fromdata['password2'];
        $phone = $fromdata['phone'];
        $data=[
            'nickname'=>$fromdata['nickname'],
            'qq'=>$fromdata['qq'],
            'wechat'=>$fromdata['wechat'],
        ];
        if ($fromdata['avatar']) {
            $data['avatar'] = $fromdata['avatar'];
        }

        if ($phone) {
            $result = $mod->where(['phone'=>$phone])->find();
            if ($result) {
                $this->Exitjson(400, '手机号已存在');
            } else {
                $data['phone'] = $phone;
            }
        }

        if ($password) {
            if ($password == $password2) {
                $data['password'] = md5($password);
            } else {
                $this->Exitjson(400, '两次密码不一致');
            }
        }
        $uid = $this->getuid($openid);
        $where['id'] = $uid['id'];
        $res = $mod->where($where)->save($data);
        if ($res !== false) {
            $json = [
                'avatar'=>$data['avatar'],
                'nickname'=>$data['nickname'],
            ];
            $this->Exitjson(200, '修改成功', $json);
        }
        $this->Exitjson(400, '修改失败');
    }

    public function uploadfile()
    {
        //$this->Authtoken();
        if (!empty($_FILES['file'])) {
            $file = $this->_upload($_FILES['file'], 'avatar', $thumb = ['width'=>150, 'height'=>150]);

            if ($file['error']) {
                $this->Exitjson(400, $file['info']);
            } else {
                $url = trim(C('yh_site_url')).$file['mini_pic'];
                $this->Exitjson(200, '上传成功', $url);
            }
        }
        $this->Exitjson(400, '上传文件为空');
    }

 

public function checkin(){
$Amount=abs(trim(C('yh_item_hit')));
if($Amount){
$Amount=mt_rand(1,$Amount*100)/100;
$mod_cash=new usercashModel();
$Mod_user=new userModel();
$today=date('Y-m-d',NOW_TIME);
$openid = $this->Authtoken();
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


	
	public function bindlang(){
		
		$data = array(
		'search'=>'请输入淘宝订单号',
		'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01hDlv6P2MgYg99eUIT_!!3175549857.jpg'
		);
		
		$this->Exitjson(200,'',$data);
	}
	
	public function queryMessage(){
		
		$content = $this->params['data'];
		if($this->cli=='bd' && C('yh_wx_version') == '1.0.0'){
			$this->Exitjson(403,'');
		}
		if($content){
			if(strstr($content, 'jd.com') || strstr($content, 'jingxi.com') || strstr($content, 'jd.hk')){
				
				$skuid = $this->jditemid($content);
				if($skuid){
					$pageurl = '/pages/cate/jditem?mod=jd&id='.$skuid;
					$this->Exitjson(200,'',$pageurl);
					
					
				  }
			}
			
			if(strstr($content, 'yangkeduo.com')){
				
				$skuid = $this->pdditemid($content);
				if($skuid){
					
					$info = $this->PddGoodsSearch('','',$skuid);
					if($info['goodslist'][0]['goods_sign']){
						$pageurl = '/pages/cate/jditem?&mod=pdd&id='.$info['goodslist'][0]['goods_sign'];
						$this->Exitjson(200,'',$pageurl);
					}
					
				  }
			}
			
			
			$linkid =$this->_itemid($content);
			
			if($linkid){
				
				$pageurl = '/pages/cate/item?num_iid='.$linkid;
				$this->Exitjson(200,'',$pageurl);
				
			}
			
			
			preg_match('/([a-zA-Z0-9]{11})/',$content,$allhtml1);
			if($allhtml1[1] && !is_numeric($allhtml1[1])){
			 $kouling = $allhtml1[1];
			}	
			
			if(!$kouling){
			
				if(false !== strpos($content,'https://m.tb.cn') && preg_match('/[\x{4e00}-\x{9fa5}]/u', $content)>0){
					$kouling = true;
				}elseif(strlen($content)>24 && preg_match('/[\x{4e00}-\x{9fa5}]/u', $content)>0){
					$kouling = true;
				}	
				
			}
			
			if($kouling){
			 $apiurl=$this->tqkapi.'/so';
			 $data=array(
			 'key'=>$this->_userappkey,
			 'time'=>time(),
			 'tqk_uid'=>$this->tqkuid,
			 'hash'=>true,
			 'k'=>$content,
			 );
			$token=$this->create_token(trim(C('yh_gongju')),$data);
			$data['token']=$token;
			$result=$this->_curl($apiurl,$data,true); 
			$result=json_decode($result,true);
			$newitem = array();
			$newitem=	$result['result'][0];
			if($result['status'] == 1 && !$newitem['tongkuan'] && $newitem['num_iid']){
			
			$pageurl = '/pages/cate/item?num_iid='.$newitem['num_iid'];
			$this->Exitjson(200,'',$pageurl);
				
			}
			 
			}
			
			
			
			
			
		}
		
		
		 $this->Exitjson(200,'没查到此商品的优惠信息');
		
	}
	
	
	public function jumpminapp(){
		$action = $this->params['action'];
		switch($action){
			case 'elm':
				$openid = $this->Authtoken();
				$res = $this->getuid($openid);
				//$uid = $res['id'];
				$data = $this->getElmLink($res);
				if ($data) {
					
					$result = array(
					'appid'=>'wxece3a9a4c82f58c9',
					'path'=>$data
					);
					
					$this->Exitjson(200, '', $result);
				}
			break;
			
			
			
			default :
			$this->Exitjson(200,'');
			break;
			
		}
		
		
		
	}
	
}
