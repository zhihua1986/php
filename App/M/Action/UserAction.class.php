<?php
namespace M\Action;

use Think\Page;
use Common\Model\userModel;
use Common\Api\Weixin;
class UserAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->modorder=D('order');
        $user=$this->visitor->get();
        if ($this->visitor->is_login == false) {
            $url=U('login/index', '', '');
            redirect($url);
        }
        $this->artmod=D('article')->cache(true, 10 * 60);
        $this->assign('user', $user);
		setcookie("nickname", $user['nickname'],time()+3600*24,'/');
        if (!$user['phone'] && C('yh_index_ems')!=1) {
            $url=U('login/fillphone', '', '');
            redirect($url);
        }
        if ($user['tbname'] == 1) {
            $this->assign('isagent', true);
        }
    }

    public function myorder()
    {
        $ComputingTime = abs(C('yh_ComputingTime'))*86400;
        $this->assign('ComputingTime', $ComputingTime);
        $p = I('p', 1, 'intval');
        $status = I('status', 0, 'intval');
        $page_size = 6;
        $start = $page_size * ($p - 1);
        if ($status) {
            $stay['status'] = $status;
            $this->assign('status', $status);
        }

        $stay['uid'] = $this->memberinfo['id'];
        //$stay['nstatus'] = 1;
        $rows = $this->modorder->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $this->modorder->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        $fuid=$this->memberinfo['fuid'];
        $guid=$this->memberinfo['guid'];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->orderstatic($v['status']);
            $list[$k]['state']=$v['status'];
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['goods_title']=$v['goods_title'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['price']=$v['price'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['bask']=$v['bask'];
            $list[$k]['income']=Orebate1(['price'=>$v['income'], 'leve'=>$v['leve1']]);
            $list[$k]['up_time']=$v['up_time'];
            $list[$k]['id']=$v['id'];
        }

        $this->assign('list', $list);

        $this->_config_seo([
            'title'=>'淘宝订单'
        ]);
        $this->display();
    }

    public function channelorder()
    {
        $p = I('p', 1, 'intval');
        $status = I('status', 0, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        if ($status) {
            $stay['status'] = $status;
            $this->assign('status', $status);
        }
        $stay['relation_id'] = $this->memberinfo['webmaster_pid'];
        $stay['nstatus'] = 2;
        $rows = $this->modorder->field('goods_title,goods_num', true)->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $this->modorder->where($stay)->count('1');
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        $fuid=$this->memberinfo['fuid'];
        $guid=$this->memberinfo['guid'];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->orderstatic($v['status']);
            $list[$k]['state']=$v['status'];
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['goods_title']=$v['goods_title'];
            $list[$k]['price']=$v['price'];
            $list[$k]['bask']=$v['bask'];
            $list[$k]['income']= round($v['income']*($this->memberinfo['webmaster_rate']/100), 2);
            $list[$k]['up_time']=$v['up_time'];
            $list[$k]['id']=$v['id'];
        }

        $this->assign('list', $list);

        $this->_config_seo([
            'title'=>'渠道订单'
        ]);
        $this->display();
    }

    public function vieworder()
    {
        $this->display();
    }

    public function fillphone()
    {
        //废弃
        exit;
        if (0 < C('yh_sms_status')) {
            $this->assign('sms_status', true);
        }
        if (IS_POST) {
            $usermod = new userModel();
            $phone = I('phone');
            $exist = $usermod->where([
                'phone' => $phone,
                'password' =>md5(I('pwd')),
            ])->getField('id');
            if ($exist) {
                $map = [
                    'openid' =>$this->memberinfo['openid']
                ];
                $des = $usermod->where($map)->delete();
                if ($des) {
                    $res = $usermod->where(['id'=>$exist])->save($map);
                }
                $this->visitor->wechatlogin($this->memberinfo['openid']);
                return $this->ajaxReturn(1, '合并账号成功');
            }

            $data = [
                'phone'=>$phone,
                'password' =>md5(I('pwd')),
            ];
            $ret=$usermod->where('openid='.$this->memberinfo['openid'])->save($data);
            if ($ret) {
                return $this->ajaxReturn(1, '绑定成功！');
                $this->visitor->wechatlogin($this->memberinfo['openid']);
            } else {
                return $this->ajaxReturn(0, '绑定失败！');
            }
        }

        $this->_config_seo([
            'title'=>'绑定手机号'
        ]);

        $this->display();
    }

    public function qrcode()
    {
        if (C('APP_SUB_DOMAIN_DEPLOY')) {
            $headerm_html = str_replace('/index.php/m', '', trim(C('yh_headerm_html')));
            $data = $headerm_html.'/?t='.$this->visitor->get('id');
        } else {
            $data = trim(C('yh_headerm_html')).'?t='.$this->visitor->get('id');
        }

        $level = 'L';
        $size = 4;
        vendor("phpqrcode.phpqrcode");
        $object = new \QRcode();
        ob_clean();
        $object->png($data, false, $level, $size, 0);
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
            if ($result['result']['rtag'] == $this->memberinfo['id']) {
                $Data = [
                    'webmaster_pid' =>$result['result']['relation_id'],
                    'webmaster'=>1,
                ];
            } else {
                foreach ($result['result'] as $k=>$v) {
                    if ($v['rtag'] == $this->memberinfo['id']) {
                        $Data = [
                            'webmaster_pid' =>$v['relation_id'],
                            'webmaster'=>1,
                        ];
                        break;
                    }
                }
            }

            if ($Data) {
                $res=$Mod->where(['id'=>$this->memberinfo['id']])->save($Data);
                if ($res) {
                    $this->visitor->wechatlogin($this->memberinfo['openid']); //更新用户信息
                    $json= ['status'=>1];
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
        exit(json_encode($json));
    }

    public function ucenter()
    {
        if (C('yh_bingtaobao') > 0 &&  !$this->memberinfo['webmaster_pid']) {
            $inviterCode = $this->RelationInviterCode($this->memberinfo);
            $this->assign('inviterCode', $inviterCode);
        }
		$uid=$this->visitor->get('id');
		if (!$uid) {
		    $url=U('login/index', '', '');
		    redirect($url);
		}

        $NowTime=NOW_TIME;
        $moduser= new userModel();
		$opid = I('opid');
		if($opid){
		  $res = $moduser->where(array('id'=>$uid))->save(array('opid'=>$opid));
		  S('wechat_'.$opid,NULL);
		}

        if ($uid>0) {
            $code=$this->visitor->get('invocode');
            if (!$code) {
                $codes=$this->invicode($uid);
                $this->assign('invicode', $codes);
            }
        }

        $tbname=$this->visitor->get('tbname');
        if (1 != $tbname) {
            $map=[
                'uid'=>$uid,
                'type'=>3
            ];
            $mymoney=M('usercash')->where($map)->sum('amount');
            $agentcondition=trim(C('yh_agentcondition'));
            if (!$mymoney) {
                $mymoney=0;
            }
            if ($mymoney>=$agentcondition) {
                $moduser->where(['id'=>$uid])->setField('tbname', 1);
            }
        }
        if ($tbname == 1 && C('yh_site_tiaozhuan') == 1) {
            $qrname='qrcode_'.$uid;
            $image = F($qrname);
            if (!$image) {
                $apiurl='http://app.tuiquanke.cn/follow?uid='.$this->tqkuid.'&tuid='.$uid.'&time='.NOW_TIME;
                $qrcodeinfo=$this->_curl($apiurl);
                $images = $this->getImage($qrcodeinfo);
                F($qrname, $images['save_path']);
                $image=$images['save_path'];
            }
            $this->assign('qrcode', $image);
        }
        $article=$this->artmod->where('cate_id=2')->field('id,title')->limit(5)->select();
        $this->assign('article', $article);
        $where=[
            'oid'=>$this->visitor->get('oid'),
            'uid'=>$uid,
            'status'=>1
        ];
        $start=strtotime(date('Y-m-01', strtotime('last day of -1 month')));
        $end =strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
        $sql ='(select SUM(money) from tqk_usercash where uid = '.$uid.' and type = 3 and (create_time > '.$start.' and create_time < '.$end.')) as pre_money';
        $info=$moduser->field($sql)->find();
		$data = $this->Stat();
		if ($data) {
			$current_money=$data['yesterday_money'];
			$this_money = $data['this_month'];
		}
		$pre_money = $info['pre_money'];
		$this->assign('current_money', $current_money ? $current_money : 0);
        $this->assign('pre_money', $pre_money ? $pre_money : 0);
        $this->assign('this_money', $this_money ? $this_money : 0);

        $this->assign('r', $this->memberinfo['id']);
        $this->_config_seo([
            'title'=>'用户中心'
        ]);
        $this->display('ucenter');
    }

    protected function orderstatic($id)
    {
        switch ($id) {
            case 0:
            return '待处理';
            break;
            case 1:
            return '已付款';
            break;
            case 2:
            return '无效订单';
            break;
            case 3:
            return '已收货';
            break;
            default:
            return '订单失效';
            break;
        }
    }
	
	
	protected function Stat(){
		
		$uid=$this->memberinfo['id'];
		$fuid=$this->memberinfo['fuid'];
		$guid=$this->memberinfo['guid'];
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
			$yesterday_money=round($res['udm_today_money']+$res['gdm_today_money']+$res['fdm_today_money']+$res['u_today_money']+$res['udd_today_money']+$res['ujd_today_money']+$res['f_today_money']+$res['fdd_today_money']+$res['umt_today_money']+$res['gmt_today_money']+$res['fmt_today_money']+$res['fjd_today_money']+$res['g_today_money']+$res['gdd_today_money']+$res['gjd_today_money'], 2);
			$this_month=round($res['gdm_month']+$res['fdm_month']+$res['udm_month']+$res['u_month']+$res['udd_month']+$res['ujd_month']+$res['gmt_month']+$res['fmt_month']+$res['umt_month']+$res['f_month']+$res['fdd_month']+$res['fjd_month']+$res['g_month']+$res['gdd_month']+$res['gjd_month'], 2);
			$data = array(
			'allmoney'=>$res['allmoney'],
			'yesterday_money'=>$yesterday_money,
			'this_month'=>$this_month,
			'res'=>$res,
			);
			return $data;
		}
		
		return false;
		
		
	}
	

    public function teamprofit()
    {
        $modfin=M('balance');
        $uid=$this->memberinfo['id'];
        $fuid=$this->memberinfo['fuid'];
        $guid=$this->memberinfo['guid'];
		$data = $this->Stat();

        if ($data) {
			$allmoney=$data['allmoney'];
            $this->assign('allmoney', $allmoney);
            $this->assign('yesterday_money', $data['yesterday_money']);
            $this->assign('this_month', $data['this_month']);
			$this->assign('stat', $data['res']);
        }
      
        $p = I('p', 1, 'intval');
        $page_size = 6;
        $start = $page_size * ($p - 1);
        $stay['uid'] = $uid;
        $stay['status'] = 1;
        $rows = $modfin->field('id,money,create_time')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $modfin->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $this->assign('list', $rows);
        $this->_config_seo([
            'title'=>'我的收益'
        ]);
        $this->display();
    }

    public function teamorder()
    {
        $ComputingTime = abs(C('yh_ComputingTime'))*86400;
        $this->assign('ComputingTime', $ComputingTime);
        $p = I('p', 1, 'intval');
        $uid=$this->memberinfo['id'];
        $fuid = I('fuid', 0, 'intval');
        $guid = I('guid', 0, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);

        if ($fuid) {
            $stay['fuid'] = $uid;
            $this->assign('fuid', $fuid);
        }

        if ($guid) {
            $stay['guid'] = $uid;
            $this->assign('guid', $guid);
        }

        if (!$guid && !$fuid) {
            $stay['fuid'] = $uid;
            $this->assign('fuid', 1);
        }
        //$stay['nstatus'] = 1;
        $rows = $this->modorder->field('status,orderid,add_time,price,goods_title,settle,income,up_time,id,leve1,leve2,leve3')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
//
        $count = $this->modorder->where($stay)->count();

        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);

        $list=[];
        foreach ($rows as $k=>$v) {
            $list[$k]['status']=$this->orderstatic($v['status']);
            $list[$k]['state']=$v['status'];
            $list[$k]['orderid']=$v['orderid'];
            $list[$k]['add_time']=$v['add_time'];
            $list[$k]['price']=$v['price'];
            $list[$k]['income']=$v['income'];
            $list[$k]['up_time']=$v['up_time'];
            $list[$k]['id']=$v['id'];
            $list[$k]['settle']=$v['settle'];
            $list[$k]['goods_title']=$v['goods_title'];
            $list[$k]['leve1']=$v['leve1'];
            $list[$k]['leve2']=$v['leve2'];
            $list[$k]['leve3']=$v['leve3'];
        }

        $this->assign('list', $list);

        $this->_config_seo([
            'title'=>'团队订单'
        ]);

        $this->display();
    }

    public function myteam()
    {
        $usermod=M('user');
        $uid=$this->memberinfo['id'];
        $today_str = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $last_str = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
        $field = 'id,
        ( select count(id) from tqk_user where fuid = '.$uid.' or guid = '.$uid.') as allperson,
        ( select count(id) from tqk_user where (fuid = '.$uid.' or guid = '.$uid.') and reg_time>'.$today_str.' ) as todayperson,
        ( select count(id) from tqk_user where (fuid = '.$uid.' or guid = '.$uid.') and (reg_time>'.$last_str.' and reg_time<'.$today_str.') ) as lastperson,
        ( select count(id) from tqk_user where fuid = '.$uid.') as person1,
        ( select count(id) from tqk_user where guid = '.$uid.') as person2';
        $res = $usermod->field($field)->find();
        $this->assign('stat', $res);

        $p = I('p', 1, 'intval');
        $fuid = I('fuid', 0, 'intval');
        $guid = I('guid', 0, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);

        if ($fuid) {
            $stay['fuid'] = $uid;
            $this->assign('fuid', $fuid);
        }

        if ($guid) {
            $stay['guid'] = $uid;
            $this->assign('guid', $guid);
        }

        if (!$guid && !$fuid) {
            $stay['fuid'] = $uid;
            $this->assign('fuid', 1);
        }

        $rows = $usermod->field('reg_time,nickname,oid')->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $usermod->where($stay)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this->assign('info', $rows);
        $this -> assign('page_size', $page_size);

        $this->_config_seo([
            'title'=>'我的团队'
        ]);

        $this->display();
    }

    public function order()
    {
        $p = I('p', 1, 'intval');
        $page_size = 10;
        $mod=M('basklistlogo');
        $start = $page_size * ($p - 1);
        $stay['uid'] = $this->memberinfo['id'];
        $rows = $mod->where($stay)->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = $mod->where($stay)->count('1');
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $this->assign('list', $rows);

        $where=[
            'oid'=>$this->visitor->get('oid'),
            'uid'=>$this->memberinfo['id'],
            'status'=>1
        ];

        $integral=$this->modorder->where($where)->sum('integral');

        $this->assign('integral', $integral ? $integral : 0);

        $this->_config_seo([
            'title'=>'积分兑换'
        ]);

        $this->display('invite');
    }

    public function jifen()
    {
        if (IS_POST) {
            $count = abs(I('count'));
            if ($count<1 || is_float($count)) {
                $this->ajaxReturn(0, '兑换数量必须是1的整数倍');
            }

            $user=M('user')->where('id='.$this->visitor->get('id'))->field('id,score,money')->find();

            if ($count>$user['score']) {
                $this->ajaxReturn(0, '兑换数量与实际数量不符');
            }

            $userid=$this->visitor->get('id');
            $price=(C('yh_fanxian')/100)*$count;

            if ($price>0) {
                M('user')->where('id='.$userid)->save([
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
                $this->ajaxReturn(1, '兑换成功！');
            } else {
                $this->ajaxReturn(0, '兑换失败！');
            }
        }

        $this->_config_seo([
            'title'=>'积分兑换'
        ]);

        $this->display();
    }

    public function suborder()
    {
        $mod=M('user');
        $this->_config_seo([
            'title'=>'找回淘宝订单'
        ]);

        if (IS_POST) {
            $orderid = I('orderid');
            if (is_numeric($orderid)) {
                $UID=$this->visitor->get('id');

                $map=[
                    'id'=>$UID,
                ];
                $oid=md5(substr($orderid, -6, 6));
                $data=[
                    'oid'=>$oid
                ];

                $isset=$mod->field('id')->where($data)->find();

                if (!$isset) {
                    $res=M('user')->where($map)->save($data);
                    if ($res) {
                        return $this->ajaxReturn(1, '提交成功！');
                    }
                    $this->ajaxReturn(0, '提交失败，请检查后重试');
                } else {
                    $this->ajaxReturn(0, '此订单已经被其它账号绑定过！');
                }
            } else {
                $this->ajaxReturn(0, '提交的订单参数不符合要求');
            }
        }

        $oid=$this->visitor->get('oid');
        if ($oid) {
            $this->assign('oid', true);
        }

        $this->display();
    }

    public function bindtb()
    {
        $time=NOW_TIME;
        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if (strpos($useragent, 'micromessenger') > 1) {
            $wechat=1;
        }
//
        $UID=$this->visitor->get('id');

        $map=[
            'uid'=>$UID,
            'from'=>'wap'
        ];

        $token=$this->create_token(trim(C('yh_gongju')), $map);

        $url = U('bindtb/index', ['uid'=>$UID, 'from'=>'wap', 'token'=>$token], '');

        $this->assign('quanurl', $url);

        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if (strpos($useragent, 'micromessenger') > 1) {
            $map=[
                'uid'=>$UID,
                'from'=>'wechat'
            ];
            $token=$this->create_token(trim(C('yh_gongju')), $map);
            $url=U('msg/bindtb', ['uid'=>$UID, 'from'=>'wechat', 'token'=>$token], '');
            redirect($url);
        } else {
            redirect($url);
        }

        //$this->display();
    }

    public function index()
    {
        $callback=I('callback', '', 'trim');
        $where['openid'] = $this->openid;
        $user = M('user')->where($where)->field('nickname,avatar,money,score,openid')->find();
        if ($user) {
            $this->assign('user', $user);
        } else {
            exit('您的账号不存在！');
        }
        $this->_config_seo([
            'title'=>'用户中心'
        ]);
        $this->display();
    }

    public function modify()
    {
        $F=M('user');

        if (IS_POST) {
            $password = I('password');
            $password2 = I('password2');
            $phone = I('phone');
            $data=[
                'nickname'=>I('nickname'),
                //				'username'=>I('username','trim'),
                'qq'=>I('qq'),
                'wechat'=>I('wechat'),
            ];
            if (I('avatar')) {
                $data['avatar'] = I('avatar');
            }

            if ($phone) {
                $result = $F->where(['phone'=>$phone])->count('id');
                if ($result) {
                    $this->ajaxReturn(0, '手机号已存在');
                } else {
                    $data['phone'] = $phone;
                }
            }

            if ($password) {
                if ($password == $password2) {
                    $data['password'] = md5($password);
                } else {
                    $this->ajaxReturn(0, '两次密码不一致');
                }
            }
            if ($_FILES['avatar']) {
                $file = $this->_upload($_FILES['avatar'], 'avatar', $thumb = ['width'=>150, 'height'=>150]);
                if ($file['error']) {
                    $this->ajaxReturn(0, $file['info']);
                } else {
                    $data['avatar']=$file['mini_pic'];
                }
            }

            $where['id'] = $this->visitor->get('id');
            $res = $F->where($where)->save($data);

            if ($res !== false) {
                return $this->ajaxReturn(1, '修改成功');
            }
            $this->ajaxReturn(0, '修改失败');
        }
        $where['id'] = $this->visitor->get('id');
        $info = $F->where($where)->field('nickname,avatar,username,qq,wechat,phone')->find();
        $this->assign('info', $info);
        $this->_config_seo([
            'title'=>'修改资料'
        ]);
        $this->display();
    }

    public function tixian()
    {
        $mod=new userModel();
		
		if(!$this->visitor->get('phone')){
			//$url=U('login/fillphone', '', '');
			//$this->error('绑定手机号后才可以提现哟！',$url);
			// redirect($url);
		}
		
		C('TOKEN_ON',true);//开启令牌验证
        if (IS_POST) {
            $F=M('balance');
			
			if(!$this->tqkCheckToken($_POST)){
			    $this->ajaxReturn(0, '请不要多次重复提交！');
			    exit();
			  }
			
            $mymoney = abs(I('money'));
            if ($mymoney<=0) {
                $this->ajaxReturn(0, '提现金额异常！');
                exit();
            }
            if ($mymoney<C('yh_Quota')) {
                $this->ajaxReturn(0, '单笔提现金额不能小于'.C('yh_Quota').'元');
                exit();
            }
            $map['id'] = $this->visitor->get('id');
            $balance = $mod->field('nickname,avatar,username,money,id,realname,alipay')->where($map)->find();
            $alipay=I('allpay');
            $name=I('name');
            if ($alipay && $name && !$balance['alipay']) {
                $data=[
                    'realname'=>$name,
                    'alipay'=>$alipay
                ];
                $mod->where($map)->save($data);
            }

            if ($mymoney>$balance['money']) {
                $this->ajaxReturn(0, '账户余额不足！');
                exit();
            }
            $data=[
                'uid'=>$balance['id'],
                'money'=>$mymoney,
                'name'=>$name ? $name : $balance['realname'],
                'method'=>I('method')?:2,
                'allpay'=>$alipay ? $alipay : $balance['alipay'],
                'status'=>0,
                'content'=>I('content'),
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
                        'code'=>$this->visitor->get('nickname'),
                        'webname'=>(float) $mymoney,
                        'temp_id'=>trim(C('yh_sms_tixian_id'))
                    ];
                    $res= $this->send_sms($data);
                }
				
				$data = array(
				'name'=>$this->visitor->get('nickname'),
				'money'=>(float) $mymoney,
				'payment'=> $this->CashMethod(I('method')),
				'content'=>I('content')
				);
				
				Weixin::Cashout($data);
				
				if(C('yh_payment') == '2' && I('method') == '1'){
				
				 $res = $this->wechatpay($balance['id'],$mymoney,$resid);
				 if($res['code']==200){
					 $this->ajaxReturn(1, $res['msg']); 
				 }
				 $this->ajaxReturn(0, $res['msg']);
				
				}
				
				
                return $this->ajaxReturn(1, '申请提交成功，请等待处理！');
            }
            $this->ajaxReturn(0, '申请提交失败！');
        }

        $where['id'] = $this->visitor->get('id');
        $user = $mod->where($where)->field('nickname,avatar,username,money,realname,alipay')->find();
        $this->assign('user', $user);
        $this->_config_seo([
            'title'=>'我要提现'
        ]);
        $this->display();
    }

    public function journal()
    {
        $p = I('p', 1, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        $where['id'] = $this->visitor->get('id');
        $user = M('user')->where($where)->field('money,frozen,id')->find();
        if ($user) {
            $this->assign('user', $user);
        } else {
            exit('您的账号不存在！');
        }

        $where=[
            'type'=>6,
            'uid'=>$user['id']
        ];
        $balance = M('usercash')->where($where)->sum('money');
        if ($balance) {
            $this->assign('balance', $balance);
        } else {
            $this->assign('balance', 0);
        }
        $stay['uid'] = $user['id'];
        $rows = M('usercash')->where($stay)->field('type,money,create_time,status,remark')->order('id desc')->limit($start . ',' . $page_size)->select();
        $count = M('usercash')->where($stay)->count();
        $pager = new Page($count, $page_size);

        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $list=[];
        foreach ($rows as $k=>$v) {
            $val=unserialize(user_cash_type($v['type']));
            $list[$k]['create_time']=$v['create_time'];
            $list[$k]['type']=$val[0];
            $list[$k]['remark']=$v['remark'];
            $list[$k]['money']=$val[1].$v['money'];
        }
        $this->assign('info', $list);
        $this->_config_seo([
            'title'=>'财务日志'
        ]);
        $this->display();
    }

    public function record()
    {
        $p = I('p', 1, 'intval');
        $page_size = 10;
        $start = $page_size * ($p - 1);
        $where['id'] = $this->visitor->get('id');
        $user = M('user')->where($where)->field('money,frozen')->find();
        if ($user) {
            $this->assign('user', $user);
        } else {
            exit('您的账号不存在！');
        }
        $map['uid'] = $this->visitor->get('id');
        $rows = M('balance')->where($map)->field('money,create_time,status')->limit($start . ',' . $page_size)->order('id desc')->select();
        $count = M('balance')->where($map)->count();
        $pager = new Page($count, $page_size);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $page_size);
        $this->assign('info', $rows);
        $this->_config_seo([
            'title'=>'我的钱包'
        ]);
        $this->display();
    }
}
