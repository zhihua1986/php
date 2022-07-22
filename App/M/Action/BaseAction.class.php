<?php
namespace M\Action;

use Common\Action\FirstendAction;
use Common\Model\userModel;
use Common\Model\navModel;

class BaseAction extends FirstendAction
{
    public function _initialize()
    {
        parent::_initialize();
		// 网站状态
		if (! C('yh_site_status')) {
		    header('Content-Type:text/html; charset=utf-8');
		    exit(C('yh_closed_reason'));
		}
        $tkapi=trim(C('yh_api_line'));
        $this->tqkuid=C('yh_app_kehuduan');
        $this->assign('trackurl', $this->trackid());
        $this->assign('openduoduo', C('yh_openduoduo'));
        $this->accessKey = trim(C('yh_gongju'));
        if (false ===$tkapi) {
            $this->tqkapi = 'http://api.tuiquanke.com';
        } else {
            $this->tqkapi = $tkapi;
        }
        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if (strpos($useragent, 'miniprogram')<=0 && strpos($useragent, 'micromessenger')>0 && C('yh_site_tiaozhuan')>0 && 'perfect'!= ACTION_NAME && 'logout'!= ACTION_NAME && 'contactus'!= ACTION_NAME && 'findpwd'!= ACTION_NAME) {
			$this->weixinlogin($this->fullurl());
            $this->assign('WechatClient', true);
        }
        $this->assign('alertwin', $this->_alert_adv());

        if ($this->visitor->is_login) {
            $info = $this->CreateJdPid($this->memberinfo);
        }
    }

    protected function _alert_adv()
    {
        $Ad = S('alertadwap');
        if ($Ad) {
            $adlist = $Ad;
        } else {
            $adlist = M('ad')->where('beginTime<'.NOW_TIME.' and endTime>'.NOW_TIME.' and status=4 and add_time = 0')->order('id desc')->find();
            S('alertadwap', $adlist, 3600);
        }
        return $adlist;
    }

    protected function fullurl()
    {
		$mdomain = ((int)$_SERVER['SERVER_PORT'] == 80 ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
        $url = $_SERVER["REQUEST_URI"];
		$requestUrl = strtolower($url);
		if(strpos($url, 'pdditem')>0 || strpos($url, 'jditem')>0){
			$requestUrl = $url;
		}
		$url=urlencode($mdomain.$requestUrl);
		return $url;
    }

    protected function nav()
    {
        $where=[
            'status'=>1,
            'type'=>'main'
        ];
        $mod=new navModel();
        $navlist=$mod->cache(true, 50 * 60)->field('name,alias,link,target')->where($where)->order('ordid asc')->select();
        return $navlist;
    }

    protected function weixinlogin($Backurl)
    {
        if (C('yh_site_tiaozhuan')>0 && $this->visitor->is_login == false) {
            $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
            if (IS_POST && strpos($useragent, 'micromessenger')>0) {
                $param=I('post.');
                if ($param['wx_openkey']==$this->accessKey && $param['wx_status']=='ok') {
                    $res=$this->_wechat_login($param);
                    if (!$res) {
                        $mod=new userModel();
                        $track_val=cookie('trackid');
                        if ($track_val) {
                            $track=unserialize($track_val);
                            $uid=$track['t'];
                            if ($uid>0) {
                                $where=[
                                    'id'=>$uid
                                ];
                            }
                            $exist = $mod->field('id,username,nickname,phone,email,fuid,guid,invocode,avatar,password,score,tbname,money,webmaster,webmaster_rate,webmaster_pid,oid')->where($where)->find();
                        }
                        $now=NOW_TIME;
                        $phone = $now;
                        $pwd = md5($param['wx_openid']);
                        $openid=$param['wx_openid'];
                        $nickname=$param['wx_nickname'];
						$unionid=$param['wx_unionid'];
                        $nickname=$this->remoji($nickname);
                        $agentcondition=trim(C('yh_agentcondition'));
                        if (0 >= $agentcondition) {
                            $tbname = 1;
                        } else {
                            $tbname = 0;
                        }
                        $info=[
                            'username'=>$nickname ? $nickname : '匿名',
                            'nickname'=>$nickname ? $nickname : '匿名',
                            'password'=>$pwd,
                            'reg_ip'=>get_client_ip(),
                            'avatar'=>$param['wx_headimgurl'],
                            'state'=>1,
                            'tbname'=>$tbname,
                            'status'=>1,
                            'reg_time'=>$now,
                            'last_time'=>$now,
                            'create_time'=>$now,
                            'openid'=>$openid,
							//'opid'=>$openid,
							'unionid'=>$unionid,
                            'fuid'=>$exist['id'] ? $exist['id'] : 0,
                            'guid'=>$exist['fuid'] ? $exist['fuid'] : 0
                        ];
						
						if(C('yh_site_tiaozhuan') == 1){
							
							$info['opid'] = $openid;
							
						}
						
                        $res=$mod->add($info);
                        $this->reinvi($res);
                        $this->visitor->wechatlogin($openid);
                    }
                    cookie("iswechat", 'error3', 1200);
                    $url =  urldecode(I('wx_callback', '', 'trim'));
                   echo('<script>window.location.href="'.$url.'"</script>');
                   exit;
                }
            }

            if (strpos($useragent, 'micromessenger')>0 && $this->visitor->is_login == false) {
			$callback=base64_encode($Backurl);
			$openid=$this->tqkuid;
			$wechatType = C('yh_site_tiaozhuan');
			if($wechatType == 2){
			$appid = 'wx6493748b69e1f112';
			}else{
			 $appid = trim(C('yh_wxappid'));
			}
			$redirect_uri=urlencode('http://app.tuiquanke.cn/wechatlogin/auth?uid='.$openid.'&callback='.$callback);
			$url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state='.$openid.'&component_appid=wx017a5ed0dd2ad861';
               // $apiurl='http://app.tuiquanke.cn/wechatlogin?uid='.$this->tqkuid.'&time='.NOW_TIME.'&callback='.$Backurl;
                echo('<script>window.location.href="'.$url.'"</script>');
                exit;
            }
        }
    }

    protected function pddquery($key)
    {
        $skuid = $this->pdditemid($key);
        if ($skuid) {
            $info = $this->PddGoodsSearch('', '', $skuid);
            if ($info['goodslist'][0]['goods_sign']) {
                $url = U('pdditem/jump/'.$info['goodslist'][0]['goods_sign'].'');
                echo('<script>window.location.href="'.$url.'"</script>');
            }
        }
    }

    protected function buyer($num='1')
    {
        $cach_name="buyercache";
        $arraytime=['35', '40', '50', '33', '36', '42', '48', '27', '6', '13', '1', '12', '3', '14', '5', '16', '7', '18', '9', '10', '15', '20', '22', '30', '35', '40', '50', '33', '36', '42', '48', '27', '6', '13'];
        $datainfo = S($cach_name);
        if (false === $datainfo || empty($datainfo)) {
            $userlist=M('user')->field('nickname,avatar')->where('state=0')->order('id desc')->limit(100)->select();
            S($cach_name, $userlist, 86400);
            $datainfo=$userlist;
        }
        $result=array_rand($datainfo, $num);
        $timelist=array_rand($arraytime, $num);
        foreach ($result as $k=>$v) {
            $data[$k]['nickname']=$this->shortname($datainfo[$v]['nickname']);
            $data[$k]['avatar']=$datainfo[$v]['avatar'];
            $data[$k]['time']=$timelist[$k];
        }

        return $data;
    }

    protected function trackid()
    {
        $track=I('t', '0', 'number_int');
        $isagent=cookie('Screateagent');
        $mod=new userModel();
        if (($track && $track!=$isagent && is_numeric($track)) || (false === $isagent && $track && is_numeric($track))) {
            $res=$mod->field('webmaster_pid,pdd_pid,invocode,jd_pid')->where(['id'=>$track])->find();
            if ($res) {
                $data=[
                    't_pid'=>$res['webmaster_pid'],
                    //      	'p_pid'=>$res['pdd_pid'],
                    'jd_pid'=>$res['jd_pid'],
                    'recode'=>$res['invocode'],
                    't'=>$track,
                ];
                $data=serialize($data);
            }
            cookie("trackid", $data, 86400 * 15);
            cookie('Screateagent', $track);
        }
    }

    protected function shortname($txt)
    {
        $username= mb_substr($txt, 0, 1, 'utf-8').'***'. mb_substr($txt, -1, 1, 'utf-8');
        return $username;
    }

    protected function getImage($url, $save_dir='', $filename='', $type=0)
    {
        if (trim($url)=='') {
            return ['file_name'=>'', 'save_path'=>'', 'error'=>1];
        }
        if (trim($save_dir)=='') {
            $save_dir='./';
        }
        if (trim($filename)=='') {
            $ext=strrchr($url, '.');
            if ($ext!='.gif'&&$ext!='.jpg'&&$ext!='.png'&&$ext!='.jpeg') {
                //return array('file_name'=>'','save_path'=>'','error'=>3);
                $ext='.jpg';
            }
            $filename=time().rand(0, 10000).$ext;
        }
        if (0!==strrpos($save_dir, '/')) {
            $save_dir.="/" . C("yh_attach_path") . 'avatar/';
        }
        if (!file_exists($save_dir)&&!mkdir($save_dir, 0777, true)) {
            return ['file_name'=>'', 'save_path'=>'', 'error'=>5];
        }
        if ($type) {
            $ch=curl_init();
            $timeout=10;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        $fp2=@fopen($save_dir.$filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img,$url);
        $save_dir = mb_substr($save_dir, 2);
        return ['file_name'=>$filename, 'save_path'=>$save_dir.$filename, 'error'=>0];
    }
}
