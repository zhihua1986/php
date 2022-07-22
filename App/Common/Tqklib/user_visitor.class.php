<?php
namespace Common\Tqklib;
/**
 * 访问者
 *
 * @author andery
 */
class user_visitor
{
    // 登陆状态
    public $is_login = false;

    public $info = null;

    public $error = '';
    
    public $field = 'tb_open_uid,special_id,id,username,openid,nickname,phone,email,fuid,guid,invocode,avatar,password,score,tbname,money,webmaster,webmaster_rate,oid,webmaster_pid,jd_pid,opid';
    
    public function __construct()
    {
        $this->check();
    }
    
    public function check()
    {
        if (session('user_info')) {
            // 已经登陆
            $this->info = session('user_info');
            $this->is_login = true;
        } else {
            $this->is_login = false;
        }
        
        return $this->is_login;
    }

    /**
     * 获取用户信息
     */
    public function get($key = null, $real = false)
    {
        $info = null;
        if (is_null($key) && $this->info['id']) {
            $info = M('user')->find($this->info['id']);
        } else {
            if (isset($this->info[$key]) && $real === false) {
                return $this->info[$key];
            } else {
                $info = M('user')->where(array(
                    'id' => $this->info['id']
                ))->getField($key);
            }
        }
        return $info;
    }
	
	
	 /**
     * 微信登陆
     */
    public function wechatlogin($username,$newOpenid='',$param=array())
    {
		$where['opid'] = $username;
		$where['openid'] = $username;
		$where['_logic'] = 'or';
        $user_info = D('user')->where($where)
            ->field($this->field)
            ->find();
        if ($user_info){
		
			if(($user_info['nickname']=='匿名' || !$user_info['opid']) && C('yh_site_tiaozhuan') == 1){
				$nickname=$this->remojio($param['wx_nickname']);
				$opend=array(
				'opid'=>$username,
				);
				if($user_info['nickname']=='匿名' && $nickname){
				$opend['nickname'] = $nickname?$nickname:'匿名';
				$opend['username'] = $nickname?$nickname:'匿名';
				$opend['avatar'] = $param['wx_headimgurl']?$param['wx_headimgurl']:'/Public/static/tqkpc/images/noimg.png';
				}
				M('user')->where(array('id'=>$user_info['id']))->save($opend);
			}
			
		$this->assign_info($user_info,$newOpenid);
         return true;
        }
        
        $this->error = '账号不存在';
        
        return false;
    }

    /**
     * 登陆
     */
    public function login($username, $password, $remember = null)
    {
        $where['username'] = $username;
        $where['phone'] = $username;
        $where['openid'] = $username;
        $where['_logic'] = 'or';
        
        $user_info = M('user')->where($where)
            ->field($this->field)
            ->find();
        
        if ($user_info) {
            if ($user_info['password'] == md5($password)) {
              //  if ($remember) {
                    $this->remember();
              //}
              if(mb_strlen($user_info['openid'])< 5){
              	$opid=$this->getRandOnlyId();
              	$opend=array(
              	'openid'=>$opid
              	);
              	M('user')->where(array('id'=>$user_info['id']))->save($opend);
              	$user_info['openid']=$opid;
              }
              $this->assign_info($user_info);
                return true;
            }
            
            $this->error = '账号或者密码不正确';
            
            return false;
        }
        
        $this->error = '账号不存在';
        
        return false;
    }
    
	
	protected function remojio($str)
	{
	    $str = preg_replace_callback(
	        '/./u',
	        function (array $match) {
	            return \strlen($match[0]) >= 4 ? '' : $match[0];
	        },
	        $str
	   );
	    $str = preg_replace('# #', '', $str);
	    return $str;
	}
	
    public function info_update()
    {
        $id = $this->info['id'];
        
        $where = array(
            'id'=>$id
        );
        
        $user_info = M('user')->where($where)->field($this->field)->find();
        
        $this->assign_info($user_info);
    }

    /**
     * 登陆会话
     */
    public function assign_info($user_info,$newOpenid='')
    {
        $where = array(
            'id'=>$user_info['id']
        );
        
        $data = array(
            'last_time'=>time(),
            'last_ip'=>get_client_ip(),
            'login_count'=>array('exp','login_count+1')
        );
		
		if($newOpenid){
			$data['opid']=$newOpenid;
		}
        
        D('user')->where($where)->save($data);
        unset($user_info['password']);
		setcookie("nickname", $user_info['nickname'],time()+3600*24,'/');
        session('user_info', $user_info);
        cookie("islogin", '1');
        $this->info = $user_info;
    }

    /**
     * 记住密码
     */
    public function remember()
    {  
    	    setcookie(session_name(), $_COOKIE[session_name()], time() + 30*24*60*60, '/');
       // cookie(session_name(), cookie(session_name()), 3600 * 24 * 14);
    }

    public function register($username, $phone, $email, $password, $data = array())
    {
        if($username){
            if($this->check_username($username)){
                $this->error = '账号已经存在';
                return false;
            }
        }else if($phone){
            if($this->check_phone($phone)){
                $this->error = '手机号码已经存在';
                return false;
            }
            if(!$username){
                $username = substr($phone, 3);
            }
        }else if($email){
            if($this->check_email($email)){
                $this->error = '邮箱地址已经存在';
                return false;
            }
            if(!$username){
                $username = reset(explode('@', $email));
            }
        }else{
            $this->error = '账号信息不能为空';
            return false;
        }
        
        if(!$password){
            $this->error = '密码不能为空';
            return false;
        }
        $data['username'] = $username;
        $data['nickname'] = isset($data['nickname'])?$data['nickname']:$username;
        $data['phone'] = $phone;
        $data['email'] = $email;
        $data['password'] = md5($password);
        $data['money'] = 0;
		$data['avatar'] = '/Public/static/tqkpc/images/noimg.png';
        $data['frozen'] = 0;
        $data['score'] = 0;
        $data['reg_ip'] = get_client_ip();
        $data['reg_time'] = time();
        $data['last_time'] = 0;
        $data['last_ip'] = 0;
        $data['login_count'] = 0;
        $agentcondition=trim(C('yh_agentcondition'));
		if(0 >= $agentcondition){
		 $data['tbname'] = 1;
		}
        $data['create_time'] = time();
        $data['status'] = 1;
        $data['state'] = 1;
        $data['openid'] = $this->getRandOnlyId();
        $data['fuid'] = isset($data['fuid'])?$data['fuid']:0;
        $data['guid'] = isset($data['guid'])?$data['guid']:0;
        
        $mo = M('user');
        
        $res = false;
        if($mo->create($data)){
            $res = $mo->add();
        }
        
        if($res === false){
            $this->error = $mo->getError()?$mo->getError():$mo->getDbError();
            return false;
        }
        
        $where = array(
            'id'=>$res
        );
        
		$score=trim(C('yh_reinte'));
		$reinvi=F('reinvi_'.$res);
		if($score>0 && $res && false ===$reinvi){
		$mo->where("id='".$res."'")->setInc('score',$score);
		M('basklistlogo')->add(array(
		    'uid'=>$res,
		    'integray'=>$score,
		    'remark'=>'注册送+'.$score,
		    'order_sn'=>'--',
		    'create_time'=>NOW_TIME,
		    ));
		  F('reinvi_'.$res,$res);
		}
        
        
        $user_info = $mo->where($where)
            ->field($this->field)
            ->find();
        
        $this->assign_info($user_info);
        
        return true;
    }

    public function check_username($username)
    {
        $exist = M('user')->where(array(
            'username' => $username
        ))->count('id');
        
        if ($exist) {
            return true;
        }
        return false;
    }

private function getRandOnlyId() {
        $endtime=1356019200;
        $curtime=time();
        $newtime=$curtime-$endtime;
        $rand=rand(0,9999999999);
        $all=$rand.$newtime;
        $onlyid=base_convert($all,10,36);
        return $onlyid;
}

    public function check_phone($phone)
    {
        $exist = M('user')->where(array(
            'phone' => $phone
        ))->count('id');
        
        if ($exist) {
            return true;
        }
        return false;
    }

    public function check_email($email)
    {
        $exist = M('user')->where(array(
            'email' => $email
        ))->count('id');
        
        if ($exist) {
            return true;
        }
        return false;
    }

    /**
     * 退出
     */
    public function logout()
    {
		setcookie("nickname", null,time() - 3600,'/');
        session('user_info', null);
        cookie("islogin", 0);
    }
}