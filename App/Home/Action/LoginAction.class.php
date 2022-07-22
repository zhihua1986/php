<?php
namespace Home\Action;

use Common\Model\userModel;

class LoginAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
    }


	public function fillphone(){
		if ($this->visitor->is_login == false) {
		    $url=U('login/index', '', '');
		    redirect($url);
		}
		if($this->visitor->get('phone')){
			$url=U('user/ucenter', '', '');
			redirect($url);
		}
		
		if (0 < C('yh_sms_status')) {
		    $this->assign('sms_status', true);
		}
		if (IS_POST) {
		    $usermod = new userModel();
		    $phone = I('phone');
		    $verify = I('code');
		    $password = md5(I('password'));
		    $openid = $this->visitor->get('openid');
		    if (0 < C('yh_sms_status') && (session('sms_code') != $verify || session('sms_phone') != $phone)) {
		        $this->ajaxReturn(0, '手机验证码输入不正确');
		    }
		    $exist = $usermod->field('id,password,money,wxAppOpenid,bdAppOpenid,reg_time,phone,score,frozen')->where([
		        'phone' => $phone,
		    ])->find();
		    if ($exist){
		        if ($password==$exist['password']) {
		            $map = [
		                'openid' => $openid
		            ];
				$wechat = $usermod->field('id,money,wxAppOpenid,bdAppOpenid,reg_time,opid,unionid,score,frozen')->where($map)->find();
				if($wechat['reg_time']>$exist['reg_time'] && $exist['id']!=$wechat['id']){
					$this->mergeUser($wechat['id'],$exist['id']);
					$des = $usermod->where(['id'=>$wechat['id']])->delete();
					if ($des) {
						$data = array(
						'openid'=>$openid,
						'opid'=>$openid,
						'unionid'=>$wechat['unionid'],
						'money'=>array('exp','money+'.$wechat['money'].''),
						'score'=>array('exp','score+'.$wechat['score'].''),
						'frozen'=>array('exp','frozen+'.$wechat['frozen'].''),
						);
					    $res = $usermod->where(['id'=>$exist['id']])->save($data);
					}
					
				}elseif($exist['id']!=$wechat['id']){
					$this->mergeUser($exist['id'],$wechat['id']);
					$des = $usermod->where(['id'=>$exist['id']])->delete();
					if ($des) {
						
						$data = array(
						'wxAppOpenid'=>$exist['wxAppOpenid'],
						'bdAppOpenid'=>$exist['wxAppOpenid'],
						'phone'=>$exist['phone'],
						'password'=>$exist['password'],
						'money'=>array('exp','money+'.$exist['money'].''),
						'score'=>array('exp','score+'.$exist['score'].''),
						'frozen'=>array('exp','frozen+'.$exist['frozen'].''),
						);
						
					   $res = $usermod->where(['id'=>$wechat['id']])->save($data);
					}
					
					
				}
		
		            $this->visitor->logout();
		            return $this->ajaxReturn(1, '合并账号成功请重新登录',U('login/index'));
		        }
		        return $this->ajaxReturn(0, '您输入的手机号和密码不正确！');
		    }
		
		    $data = [
		        'phone'=>$phone,
		        'password' =>md5(I('pwd')),
		    ];
		    $sqlwhere = [
		        'openid'=>$openid
		    ];
		    $ret=$usermod->where($sqlwhere)->save($data);
		    if ($ret) {
		        return $this->ajaxReturn(1, '绑定成功！');
		        $this->visitor->wechatlogin($openid);
		    } else {
		        return $this->ajaxReturn(0, '绑定失败！');
		    }
		}
		
		C('TOKEN_ON',true);//开启令牌验证
		$this->_config_seo([
		    'title'=>'绑定手机号'
		]);
		$this->display();
		
	}


    public function index()
    {
        if ($this->visitor->is_login) {
            $this->redirect(U('user/ucenter', '', ''));
        }
        if (0 < C('yh_sms_status')) {
            $this->assign('sms_status', true);
        }
        $this->_config_seo([
            'title'=>'用户登录'
        ]);
		
		$wechatType = C('yh_site_tiaozhuan');
		$scan = C('yh_scan');
		$notice = C('yh_notice');
		$tempid = C('yh_tempid_5');
		if($this->getRobot()==false && $tempid && 1==$wechatType && 1==$scan && 1==$notice){
			$this->assign('scan',true);
		}
		
        $this->display();
    }
	
	
	public function getqrcode(){
		
		$track_val=cookie('trackid');
		if ($track_val){
		    $track=unserialize($track_val);
		    $fuid=$track['t'];
		}
		if(1 == C('yh_scan') && C('yh_site_tiaozhuan') == 1 && I('scan') == 'scan' && $this->getRobot()==false){
		$apiurl='http://app.tuiquanke.cn/follow?uid='.$this->tqkuid.'&tuid=666888'.$fuid.'&qr=1&time='.NOW_TIME;
		$qrcodeinfo=$this->_curl($apiurl);
		$query = parse_url($qrcodeinfo, PHP_URL_QUERY);
		parse_str($query, $arr);
		$data = array(
		'qrcode'=>$qrcodeinfo,
		'uid'=>$arr['ticket'],
		'status'=>200
		);
		
		exit(json_encode($data));
		
		}
		
	}
	
	public function getlogin(){
		if(IS_POST){
			$uid = md5(I('uid'));
			$result = S($uid);
			if($result){
				
				if(C('yh_site_tiaozhuan') == 1){ //认证的服务号
					$newOpenid = $result;
				}
				
			  $this->visitor->wechatlogin($result,$newOpenid);
				$data = array(
				'status'=>200
				);
			}else{
			$data = array(
			'status'=>400,
			);
			}
			
			exit(json_encode($data));
			
		}
	}

    public function findpwd()
    {
        if (0 < C('yh_sms_status')) {
            $this->assign('sms_status', true);
        }
        if (IS_POST) {
            $code = I('code');
            $phone = I('phone');
            if (session('sms_code') != $code || session('sms_phone') != $phone) {
                $this->ajaxReturn(0, '手机验证码输入不正确');
            }
            $password = I('password');
            $repassword = I('repassword');
            if ($password != $repassword) {
                $this->ajaxReturn(0, '两次密码输入不一致');
            }
            $mo = new userModel();
            $where = [
                'phone'=>$phone
            ];
            $res = $mo->where($where)->setField('password', md5($password));
            if ($res !== false) {
                return $this->ajaxReturn(1, '密码修改成功！', U('login/index'));
            }
            $this->ajaxReturn(0, '操作失败');
        }
		C('TOKEN_ON',true);//开启令牌验证
        $this->_config_seo([
            'title'=>'找回密码'
        ]);

        $this->display();
    }

    public function register()
    {
        if ($this->visitor->is_login) {
            $this->redirect('user/ucenter');
        }
        if (0 < C('yh_sms_status')) {
            $this->assign('sms_status', true);
        }

        $track_val=cookie('trackid');
        if (!empty($track_val)) {
            $track=unserialize($track_val);
            $uid=$track['t'];
            $this->assign('recode', $track['recode']);
            if ($uid>0) {
                $where=[
                    'id'=>$uid
                ];
            }
        }

        if (IS_POST) {
			if(!$this->tqkCheckToken($_POST)){
			    $this->ajaxReturn(0, '请不要多次重复提交！');
			    exit();
			  }
			
            $phone = I('phone');
            $username = I('username');
            $email = I('email');
            $open = I('open');
            $password =I('password');
            $repassword = I('repassword');
            $invocode = I('invocode');
            $code = I('code');
            if ($open != 1) {
                $this->ajaxReturn(0, '请阅读用户协议并且勾选同意后才可以注册');
            }
            if (!is_numeric($phone)) {
                $this->ajaxReturn(0, '非法操作');
            }

            if (0 < C('yh_sms_status') && (session('sms_code') != $code || session('sms_phone') != $phone)) {
                $this->ajaxReturn(0, '手机验证码输入不正确');
            }

            if ($invocode || $track_val) {
                if ($invocode) {
                    $where=[
                        'invocode' => $invocode
                    ];
                }

                $mod=new userModel();
                $exist = $mod->field('id,fuid,guid')->where($where)->find();
                if ($exist) {
                    $data['fuid']=$exist['id'];
                    $data['guid']=$exist['fuid'] ? $exist['fuid'] : 0;
                } else {
                    $this->ajaxReturn(0, '邀请码不存在或者填写有误！');
                }
            }

            if ($password != $repassword) {
                $this->ajaxReturn(0, '两次密码输入不一致');
            }

            $res = $this->visitor->register($username, $phone, $email, $password, $data);

            if ($res) {
                return $this->ajaxReturn(1, '注册成功', U('user/ucenter'));
            }

            $this->ajaxReturn(0, $this->visitor->error);
        }
       C('TOKEN_ON',true);//开启令牌验证
        $this->_config_seo([
            'title'=>'用户注册'
        ]);

        $this->display();
    }

    public function pwdcode()
    {
		 C('TOKEN_ON',true);//开启令牌验证
        if (IS_POST) {
			if(!$this->tqkCheckToken($_POST)){
			    $this->ajaxReturn(0, '验证失败，请刷新页面！');
			    exit();
			  }
            $phone = I('phone');
            $tempid=I('tempid');
            $ac=I('ac');
            $mod=new userModel();
            $res=$mod->where(['phone'=>$phone])->find();
            if ('reg' == $ac && $res) {
                return $this->ajaxReturn(0, '此手机号已经被占用');
            }

            if ('findpass' == $ac && !$res) {
                return $this->ajaxReturn(0, '此手机号不存在');
            }

            $code = rand(100000, 999999);
            $data=[
                'phone'=>I('phone'),
                'code'=>$code,
                //          'webname'=>trim(C('yh_site_name')),
                'temp_id'=>$tempid
            ];
            $res= $this->send_sms($data);
            if ($res) {
                session('sms_code', $code);
                session('sms_phone', $phone);
                return $this->ajaxReturn(1, '验证码已发送');
            }
            $this->ajaxReturn(0, '验证码发送失败');
        }
    }

    public function login()
    {
        if ($this->visitor->is_login) {
            $this->redirect(U('user/ucenter', '', ''));
        }

        if (IS_POST) {
            $username = I('username');

            $password = I('password');

            $remember = I('remember');
            $code = I('code');

            if ($this->check_verify($code) === false) {
                $this->ajaxReturn(0, '验证码错误!请点击验证码图片切换');
            }

            $res = $this->visitor->login($username, $password, $remember);

            if ($res) {
                $role=$this->visitor->get('webmaster');

                // $callback = I('callback','trim');
                if (empty($callback)) {
                    $callback=U('user/ucenter');
                }

                if (strpos($callback, '/login') !== false) {
                    $callback = U('user/ucenter');
                }
                return $this->ajaxReturn(1, '登录成功', $callback ? $callback : U('user/ucenter'), [
                    'id'=>$this->visitor->get('id'),
                    'nickname'=>$this->visitor->get('nickname'),
                    'role'=>$this->visitor->get('webmaster')
                ]);
            }

            $this->ajaxReturn(0, $this->visitor->error);
        }
    }

    public function logout()
    {
        $this->visitor->logout();
        redirect('/');
        exit();
        $callback = $_SERVER['HTTP_REFERER'];
        if (
            strpos($callback, '/login') !== false ||
            strpos($callback, '/user') !== false
            ) {
            $callback = C('yh_headerm_html');
        }

        redirect($callback);
    }
}
