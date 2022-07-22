<?php
namespace M\Action;

use Common\Model\userModel;

class LoginAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $this->visitor->logout();
        if (0 < C('yh_sms_status')) {
            $this->assign('sms_status', true);
        }
        if ($this->visitor->is_login) {
            $this->redirect(U('user/ucenter', '', ''));
        }
        $this->_config_seo([
            'title'=>'用户登录'
        ]);

        $this->display();
    }

    public function findpwd()
    {
        if (0 < C('yh_sms_status')) {
            $this->assign('sms_status', true);
        }
        if (IS_POST) {
            $code = I('verify');
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
		C('TOKEN_ON',true);
        $this->_config_seo([
            'title'=>'找回密码'
        ]);

        $this->display();
    }

    public function fillphone()
    {
        if ($this->visitor->get('phone')) {
			$this->visitor->logout();
			redirect('/');
            exit('非法操作');
        }
        if (0 < C('yh_sms_status')) {
            $this->assign('sms_status', true);
        }
        if (IS_POST) {
            $usermod = new userModel();
            $phone = I('phone');
            $verify = I('verify');
            $password = md5(I('pwd'));
            $openid = $this->visitor->get('openid');
            if (0 < C('yh_sms_status') && (session('sms_code') != $verify || session('sms_phone') != $phone)) {
                $this->ajaxReturn(0, '手机验证码输入不正确');
            }

            $exist = $usermod->field('id,password,money,wxAppOpenid,bdAppOpenid,reg_time,phone,score,frozen')->where([
                'phone' => $phone,
            ])->find();
            if ($exist) {
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
                    return $this->ajaxReturn(1, '合并账号成功请重新登录');
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
        $this->display('User/fillphone');
    }

    public function savephone()
    {
        if (IS_POST) {
            $phone = I('phone');
            $pwd = md5(I('pwd'));
            $openid=I('openid');
            $invocode=I('invocode');
            $nickname=I('nickname');
            $mod=new userModel();
            $exist = $mod->field('id,password,invocode')->where([
                'phone' => $phone
            ])->find();
            if ($exist) {
                if ($pwd==$exist['password']) {
                    $data=[
                        //'phone'=>$phone,
                        'avatar'=>I('avatar'),
                        'openid'=>$openid
                    ];
                    $openuid=F($openid);
                    $track_val=cookie('trackid');
                    if ($invocode || $track_val || $openuid) {
                        if (!empty($track_val)) {
                            $track=unserialize($track_val);
                            $uid=$track['t'];
                            if ($uid>0 && $exist['id']!=$uid) {
                                $wherex=[
                                    'id'=>$uid
                                ];
                            }
                        }

                        if ($invocode && $exist['invocode']!=$invocode) {
                            $wherex=[
                                'invocode' => $invocode
                            ];
                        }

                        if ($openuid>0 && $exist['id']!=$openuid) {
                            $wherex=[
                                'id'=>$openuid
                            ];
                        }

                        if ($wherex) {
                            $existx = $mod->field('id,fuid,guid')->where($wherex)->find();
                            if ($existx) {
                                $data['fuid']=$existx['id'];
                                $data['guid']=$existx['fuid'] ? $existx['fuid'] : 0;
                            }
                            //	        	else{
//	        	$this->ajaxReturn(0, '邀请码不存在,请尝试清理微信缓存重新登录');
//	        	}
                        }
                    }

                    $res=$mod->where('id='.$exist['id'])->save($data);
                    //$this->reinvi($exist['id']); 直接绑定手机号不赠送积分
                    $this->visitor->wechatlogin($openid);
                    return $this->ajaxReturn(1, '绑定微信登录成功！');
                }
                return $this->ajaxReturn(0, '输入的账号密码不正确！');
            }

            $info=[
                'username'=>$this->remoji($nickname),
                'phone'=>$phone,
                'nickname'=>$this->remoji($nickname),
                'password'=>$pwd,
                'reg_ip'=>get_client_ip(),
                'avatar'=>I('avatar'),
                'state'=>1,
                'status'=>1,
                'reg_time'=>time(),
                'last_time'=>time(),
                'create_time'=>time(),
                'openid'=>$openid,
				 'opid'=>$openid,
            ];

            $openuid=F($openid);
            $track_val=cookie('trackid');
            if ($invocode || $track_val || $openuid) {
                if ($openuid>0) {
                    $wherex=[
                        'id'=>$openuid
                    ];
                }

                if (!empty($track_val)) {
                    $track=unserialize($track_val);
                    $uid=$track['t'];
                    if ($uid>0) {
                        $wherex=[
                            'id'=>$uid
                        ];
                    }
                }

                if ($invocode) {
                    $wherex=[
                        'invocode' => $invocode
                    ];
                }
                $existx = $mod->field('id,fuid,guid')->where($wherex)->find();
                if ($existx) {
                    $info['fuid']=$existx['id'];
                    $info['guid']=$existx['fuid'] ? $existx['fuid'] : 0;
                }
//      	else{
//      	$this->ajaxReturn(0, '邀请码不存在,请尝试清理微信缓存重新登录');
//      	}
            }

            $res=$mod->add($info);
            $this->visitor->wechatlogin($openid);
            if ($res) {
                $this->reinvi($res);
                return $this->ajaxReturn(1, '微信登录成功！');
            }
            return $this->ajaxReturn(0, '微信登录失败！');
        }
    }

    public function register()
    {
        if (0 < C('yh_sms_status')) {
            $this->assign('sms_status', true);
        }
        if ($this->visitor->is_login) {
            $this->redirect(U('user/ucenter', '', ''));
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
            $phone = trimall(I('phone', '', 'htmlspecialchars'));

            $username = trimall(I('username', '', 'htmlspecialchars'));

            $email = trimall(I('email', '', 'htmlspecialchars'));

            $password =trimall(I('password', '', 'htmlspecialchars'));

            $repassword = trimall(I('repassword', '', 'htmlspecialchars'));

            $invocode = trimall(I('invocode', '', 'htmlspecialchars'));

            $verify = I('verify');

            if (!is_numeric($phone)) {
                $this->ajaxReturn(0, '非法操作');
            }

            if (0 < C('yh_sms_status') && (session('sms_code') != $verify || session('sms_phone') != $phone)) {
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
                    $this->ajaxReturn(0, '邀请码不存在或者大小写有误！');
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
       C('TOKEN_ON',true);
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
                //'webname'=>trim(C('yh_site_name')),
                'temp_id'=>$tempid
            ];
            $res= $this->send_sms($data);
            if ($res) {
                session('sms_code', $code);
                session('sms_phone', $phone);
                return $this->ajaxReturn(1, '验证码发送成功');
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
            $username = trimall(I('username'));

            $password = trimall(I('password'));

            $remember = I('remember');

            $verify = I('verify');

//     if($this->check_verify($verify) === false){
//        $this->ajaxReturn(0, '验证码错误');
//
//        }

            $res = $this->visitor->login($username, $password, $remember);

            if ($res) {
                if (empty($callback)) {
                    $callback=U('user/ucenter');
                }

                if (strpos($callback, '/login') !== false) {
                    $callback = U('user/ucenter');
                }

                return $this->ajaxReturn(1, '登录成功', $callback ? $callback : U('user/ucenter'), [
                    'id'=>$this->visitor->get('id'),
                    'nickname'=>$this->visitor->get('nickname')
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

        $this->visitor->logout();
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
