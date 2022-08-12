<?php
namespace Common\Action;

use Common\Tqklib\user_visitor;
use Common\Model\userModel;

/**
 * 前台控制器基类
 */
class FirstendAction extends TopAction
{
    protected $visitor = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->_init_visitor();
        $this->_cate_mod = D('itemscate')->cache(true, 100 * 60);
        $this->cur_url= strtolower($_SERVER["REQUEST_URI"]);
        $this->assign('nav_curr', $this->cur_url);
        if (S('catetree')) {
            $catetree= S('catetree');
        } else {
            $catetree=$this->_cate_mod->where('status=1 and pid=0')->field('id,name,cateimg')->order('ordid desc')->select();
            S('catetree', $catetree);
        }
        $this->assign('catetree', $catetree);
        $this->assign('request_url', $this->cur_url);
        $this->assign('islogin', C('yh_islogin'));
        $this->assign('openinvocode', C('yh_invocode'));
        $this->assign('isfanli', C('yh_isfanli'));
    }
    /**
     * 初始化访问者
     */
    private function _init_visitor()
    {
        $this->visitor = new user_visitor();
        $this->memberinfo=$this->visitor->info;
        $this->assign('visitor', $this->memberinfo);
		
    }
	
	
	protected function Getspecial()
	{
	    $url=$this->tqkapi."/getrelationid";
	    $data=[
	        //'key'=>$this->_userappkey,
			//'tqk_uid'=>$this->tqkuid,
	        'time'=>time(),
			'info_type'=>2,
	        'tqk_uid'=>	$this->tqkuid,
	    ];
	    $token=$this->create_token(trim(C('yh_gongju')), $data);
	    $data['token']=$token;
	    $data=$this->_curl($url, $data, true);
	    $result=json_decode($data, true);
	    if ($result['code'] == 200) {
	        $Data = [];
	        $Mod = new UserModel();
	        if ($result['result']['external_id'] == $this->memberinfo['id']) {
	            $Data = [
	                'special_id' =>$result['result']['special_id'],
	            ];
	        } else {
	            foreach ($result['result'] as $k=>$v) {
	                if ($v['external_id'] == $this->memberinfo['id']) {
	                    $Data = [
	                        'special_id' =>$v['special_id'],
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
	            return false;
	        }
	    } else {
	        return false;
	    }
		
		return true;
		
	}
	
	protected function Tbconvert($num_iid,$memberinfo=array(),$Quan_id=''){
		$apiurl=$this->tqkapi.'/gconvert';
		$apidata=[
		    'tqk_uid'=>$this->tqkuid,
		    'time'=>time(),
		    'good_id'=>''.$num_iid.''
		];
		$pid = trim(C('yh_taobao_pid'));
		if($memberinfo && $memberinfo['special_id'] < 2 ){
			$apidata['ExternalId'] = $memberinfo['id'];
		}elseif($memberinfo){
			$apidata['SpecialId'] = $memberinfo['special_id'];
		}
		$token=$this->create_token(trim(C('yh_gongju')), $apidata);
		$apidata['token']=$token;
		$res= $this->_curl($apiurl, $apidata, false);
		$res = json_decode($res, true);
		$me=$res['me'];
		
		if($res && $memberinfo && $memberinfo['special_id'] < 2 ){
			
			$quanurl =$res['quanurl'];	
			return $quanurl;
		
		}elseif($res && \strlen($res['me'])>5){
		    if ($Quan_id){
				$activityId =$Quan_id ? '&activityId='.$Quan_id : '';
		        $quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.$activityId.'&itemId='.$num_iid.'&pid='.$pid.'&af=1';
		    } else {
		        $quanurl =$res['quanurl'];
		    }
			
			return $quanurl;
			
		}else{
			
			$link = 'https://uland.taobao.com/coupon/edetail?e=&activityId='.$Quan_id.'&itemId='.$num_iid.'&pid='. $pid .'';
			
		    if ($Quan_id) {
				$quanurl= $link;
		    } else {
		        $quanurl =$res['item_url']?:$link;
		    }
			
			return $quanurl;
			
		}
		
		
		return false;
		
	}
	

    protected function parent_pid()
    {
        $pid=trim(C('yh_taobao_pid'));
        $apppid=explode('_', $pid);
        return '_'.$apppid[3];
    }

    protected function _wechat_login($data)
    {
        if ($data) {
			$openid = $data['wx_openid'];
			$UnionId = C('yh_unionid');
			if($UnionId == 1 && $data['wx_unionid']){
			$mod = D('user');
			$openid = $mod->where(array('unionid'=>$data['wx_unionid']))->getField('openid');
				if(!$openid){
					$mod->where(array('opid'=>$data['wx_openid']))->save(array('unionid'=>$data['wx_unionid']));
					$openid = $data['wx_openid'];
				}
			
			}
			
			if(C('yh_site_tiaozhuan') == 1){ //认证的服务号
			$newOpenid = $data['wx_openid'];
			}
			
            $res = $this->visitor->wechatlogin($openid,$newOpenid,$data);
			
            return $res;
        }
    }

    protected function CreateJdPid($user)
    {
        $pid = trim(C('yh_jdpid'));
        $key = trim(C('yh_jdauthkey'));
        $uid = $user['id'];
        if ($pid && $key && C('yh_openjd') == 1 && $user['jd_pid']<1) {
            $apiurl=$this->tqkapi.'/Createjdpid';
            $apidata=[
                'tqk_uid'=>$this->tqkuid,
                'time'=>time(),
                'pid'=>$pid,
                'uid'=>$uid,
            ];
            $token=$this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token']=$token;
            $res= $this->_curl($apiurl, $apidata, false);
            $res = json_decode($res, true);
            if ($res['code'] == 200 && $res['result']['return']==0 && $res['result']['result']['positionid']) {
                $data = [
                    'unionid'=>$res['result']['result']['unionId'],
                    'name'=>$uid,
                    'positionid'=>$res['result']['result']['positionid'],
                    'type'=>1,
                    'uid'=>$uid,
                    'status'=>1
                ];
                $result = M('jdpositionid')->add($data);

                if ($result) {
                    $result = M('user')->where(['id'=>$uid])->setField('jd_pid', $res['result']['result']['positionid']);
                    $this->visitor->wechatlogin($user['openid']); // update userinfo
					return $res['result']['result']['positionid'];
                }
            }else{
				
				$MonthTime = strtotime(date('Y-m-01', strtotime('last day of -1 month')));
				$Sqlwhere = array(
				'jd_pid'=>array('gt',1),
				'last_time'=>array('lt',$MonthTime),
				);
				$Res = M('user')->field('id,jd_pid')->where($Sqlwhere)->order('id asc')->find();
				if ($Res && $Res['jd_pid']) {
					$Sql = 'Update tqk_user set jd_pid='.$Res['jd_pid'].' where id='.$uid.';Update tqk_user set jd_pid="0" where id ='.$Res['id'].'';
					M()->execute($Sql);
				    $this->visitor->wechatlogin($user['openid']); // update userinfo
					return $Res['jd_pid'];
				}
				
				
			}
			
			

           // S('createjdpid_'.$uid, true);
        }
    }

    protected function GetTrackid($field)
    {
        $track_val=cookie('trackid');
        if (!empty($track_val)) {
            $track=unserialize($track_val);
            if ($track[$field]) {
                return $track[$field];
            }
            return false;
        }
    }
	
	protected function phoneBill($uid = ''){
		
		$pid = trim(C('yh_youhun_secret'));
		if($pid){
		$where=[
		    'type'=>'pdd.ddk.resource.url.gen',
		    'data_type'=>'JSON',
		    'timestamp'=>$this->msectime(),
		    'client_id'=>trim(C('yh_pddappkey')),
			'generate_we_app'=>'true',
			'resource_type'=>39997,
			'pid'=>$pid
		];
		
		if($uid){
			$where['custom_parameters'] = $uid;
		}
		
		$where['sign']=$this->create_pdd_sign(trim(C('yh_pddsecretkey')), $where);
		$pdd_api='http://gw-api.pinduoduo.com/api/router';
		$result=$this->_curl($pdd_api, $where, true);
		$data=json_decode($result, true);
		return $data;
		
		}
		
		return false;
		
	}

    protected function taobaodetail($id)
    {
        $appkey = trim(C('yh_taobao_appkey'));
        $appsecret = trim(C('yh_taobao_appsecret'));
        if (!empty($appkey) && !empty($appsecret) && !empty($id)) {
            vendor('taobao.taobao');
            $c = new \TopClient();
            $c->appkey = $appkey;
            $c->secretKey = $appsecret;
            $req = new \TbkItemInfoGetRequest();
          //  $req->setFields("free_shipment,ratesum,shop_dsr,is_prepay,num_iid,user_type,title,seller_id,volume,pict_url,reserve_price,zk_final_price,item_url,provcity,nick");
            $req->setPlatform("1");
            $req->setNumIids($id);
            $resp = $c->execute($req);
            $resparr = xmlToArray($resp);
            $newitem = $resparr['results']['n_tbk_item'];
            if ($newitem) {
                return $newitem;
            }
        }
        return false;
    }

    protected function GetTbDetail($id)
    {
        $appkey=trim(C('yh_taobao_appkey'));
        $appsecret=trim(C('yh_taobao_appsecret'));
        $apppid=trim(C('yh_taobao_pid'));
        $apppid=explode('_', $apppid);
        $AdzoneId=$apppid[3];
		$key = 'https://uland.taobao.com/item/edetail?id='.$id;
        $key = 'https://item.taobao.com/item.htm?id='.$id;
        vendor("taobao.taobao");
        $c = new \TopClient();
        $c->appkey = $appkey;
        $c->secretKey = $appsecret;
        $c->format = 'json';
        $req = new \TbkDgMaterialOptionalRequest();
        $req->setAdzoneId($AdzoneId);
        $req->setPlatform("1");
        $req->setPageSize("1");
        $req->setSort("tk_total_sales_des");
        if ($key) {
            $req->setQ((string)$key);
        }
        $req->setPageNo(1);
        $req->setSort("tk_des");
        $resp = $c->execute($req);
        $resp = json_decode(json_encode($resp), true);
        $resp=$resp['result_list']['map_data'][0];
        return $resp;
    }

    // 自动表单令牌验证
    protected function tqkCheckToken($data)
    {
        // 支持使用token(false) 关闭令牌验证
        if (isset($this->options['token']) && !$this->options['token']) {
            return true;
        }
        if (C('TOKEN_ON')) {
            $name   = C('TOKEN_NAME', null, '__hash__');
            if (!isset($data[$name]) || !isset($_SESSION[$name])) { // 令牌数据无效
                return false;
            }

            // 令牌验证
            list($key, $value)  =  explode('_', $data[$name]);
            if (isset($_SESSION[$name][$key]) && $value && $_SESSION[$name][$key] === $value) { // 防止重复提交
                    unset($_SESSION[$name][$key]); // 验证完成销毁session
                    return true;
            }
            // 开启TOKEN重置
            if (C('TOKEN_RESET')) {
                unset($_SESSION[$name][$key]);
            }
            return false;
        }
        return true;
    }

    protected function agent_pid()
    { //废弃
        $track_val=cookie('trackid');
        if (!empty($track_val)) {
            $track=unserialize($track_val);
            $track='_'.$track['t_pid'];
            $par_pid=$this->parent_pid();
            $pid=str_replace($par_pid, $track, trim(C('yh_taobao_pid')));
            return $pid;
        }
        return '';
    }

    /**
     * SEO设置
     * @param mixed $seo_info
     * @param mixed $data
     * @param mixed $assign
     */
    protected function _config_seo($seo_info = [], $data = [], $assign='true')
    {
        $page_seo = [
            'title' => C('yh_site_title'),
            'keywords' => C('yh_site_keyword'),
            'description' => C('yh_site_description')
        ];
        $page_seo = array_merge($page_seo, $seo_info);
        // 开始替换
        $searchs = [
            '{site_name}',
            '{site_title}',
            '{site_keywords}',
            '{site_description}'
        ];
        $replaces = [
            C('yh_site_name'),
            C('yh_site_title'),
            C('yh_site_keyword'),
            C('yh_site_description')
        ];
        preg_match_all("/\{([a-z0-9_-]+?)\}/", implode(' ', array_values($page_seo)), $pageparams);
        if ($pageparams) {
            foreach ($pageparams[1] as $var) {
                $searchs[] = '{' . $var . '}';
                $replaces[] = $data[$var] ? strip_tags($data[$var]) : '';
            }
            // 符号
            $searchspace = [
                '((\s*\-\s*)+)',
                '((\s*\,\s*)+)',
                '((\s*\|\s*)+)',
                '((\s*\t\s*)+)',
                '((\s*_\s*)+)'
            ];
            $replacespace = [
                '-',
                ',',
                '|',
                ' ',
                '_'
            ];
            foreach ($page_seo as $key => $val) {
                $page_seo[$key] = trim(preg_replace($searchspace, $replacespace, str_replace($searchs, $replaces, $val)), ' ,-|_');
            }
        }

        if ($assign) {
            $this->assign('page_seo', $page_seo);
        } else {
            return 	$page_seo;
        }
    }

    protected function invicode($uid)
    {
        $mod=new userModel();
        //$code=$this->randStr($uid,6);
        $str=800;
        $newstr=$str+$uid;
        $num=sprintf("%06d", $newstr);
        $data=[
            'invocode'=>$num
        ];
        $res=$mod->where('id='.$uid)->save($data);

        if ($res) {
            return $code;
        }
    }

    protected function reinvi($uid)
    {
        $reinvi=F('reinvi_'.$uid);
        $score=trim(C('yh_reinte'));
        if ($score>0 && $uid && false ===$reinvi) {
            F('reinvi_'.$uid, $uid);
            $mod=new userModel();
            $mod->where("id='".$uid."'")->setInc('score', $score);
            M('basklistlogo')->add([
                'uid'=>$uid,
                'integray'=>$score,
                'remark'=>'注册送+'.$score,
                'order_sn'=>'--',
                'create_time'=>NOW_TIME,
            ]);
        }
    }

    /**
     * 连接用户中心
     */
    protected function _user_server()
    {
        $passport = new passport(C('yh_integrate_code'));
        return $passport;
    }

    /**
     * 前台分页统一
     * @param mixed $count
     * @param mixed $pagesize
     * @param null|mixed $path
     */
    protected function _pager($count, $pagesize, $path = null)
    {
        $pager = new Page($count, $pagesize);
        if ($path) {
            $pager->path = $path;
        }
        $pager->rollPage = 3;
        $pager->setConfig('header', '条记录');
        $pager->setConfig('prev', '上一页');
        $pager->setConfig('next', '下一页');
        $pager->setConfig('first', '第一页');
        $pager->setConfig('last', '最后一页');
        $pager->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
        return $pager;
    }

    protected function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = ['nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    public function _empty()
    {
        $this->display(ACTION_NAME);
    }
	
	
	protected function Takeout()
	{
	
	$part1[] =  [
	     'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01D23onG2MgYl28v3kI_!!3175549857.jpg',
	     'name'=>'100元高德打车券',
			  'url'=>'/index.php?c=elm&a=gaode&type=1'
	 ];
	 $part1[] = [
	     'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01nnJqO22MgYl9P1AgK_!!3175549857.jpg',
	     'name'=>'饿了么红包 最高66',
	     'url'=>'/index.php?c=elm&a=minapp&ac=wm&type=3',
	 ];
	 
		if (C('yh_openduoduo')){
	  	$part1[] =  [
	          'img'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01ZVLZFa2MgYl9OJjgs_!!3175549857.jpg',
	          'name'=>'话费流量充值享折扣',
	          'url'=>'/index.php?c=elm&a=chong',
	      ];
	  }
		
	if(C('yh_dm_cid_kfc') == 1){
			 $part1[] =  [
			   'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01h2nFyx2MgYl4UaieB_!!3175549857.jpg',
			   'name'=>'肯德基5折起',
			   'url'=>'/index.php?c=elm&a=other&id=5933&type=1',
			 ];
	}
	if(C('yh_dm_cid_qz') == 1){
			 $part1[] =  [
			'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN017ShJrA2MgYl6pLrQg_!!3175549857.jpg',
			'name'=>'特惠电影票',
			'url'=>'/index.php?c=elm&a=other&id=6680&type=1',
			 ];
	}
	
	if(C('yh_dm_cid_dd') == 1){
			 $part1[] =  [
			  'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN012ghZIt2MgYl860dth_!!3175549857.jpg',
			  'name'=>'滴滴打车 最高立减10元',
			  'url'=>'/index.php?c=elm&a=other&id=12485&type=3',
			 ];
			 $part1[] =  [
			  'img'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01xJ4TV52MgYlCmAFwB_!!3175549857.jpg',
			  'name'=>'汽车加油 最高立减20',
			  'url'=>'/index.php?c=elm&a=other&id=15200&type=3'
			 ];
			 $part1[] =  [
			  'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01FThA9Z2MgYlDYYxy5_!!3175549857.jpg',
			  'name'=>'滴滴货运券 最高立减15',
			  'url'=>'/index.php?c=elm&a=other&id=12644&type=3'
			 ];
			 $part1[] =  [
			  'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN018ljXs02MgYlDYdStB_!!3175549857.jpg',
			  'name'=>'花小猪打车，首单立减10元',
			  'url'=>'/index.php?c=elm&a=other&id=14801&type=3'
			 ];
	}
	
	$part1[] =  [
	     'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01YQ6aQ62MgYl6UfWlP_!!3175549857.jpg',
	     'name'=>'饿了么商超红包 29减10',
	     'url'=>'/index.php?c=elm&a=minapp&ac=sc&type=3',
	 ];
	
	 $part2 = [];
	 if(C('yh_dm_cid_mt') == 1){
	 	$part2[] = [
	 	    'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01LDi4tP2MgYlAbzd0K_!!3175549857.jpg',
	 	    'name'=>'美团外卖红包天天领',
			'url'=> '/index.php?c=elm&a=other&id=10124&type=3',
	 	];
		$part2[] =[
		    'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01RumGh62MgYlCReb86_!!3175549857.jpg',
		    'name'=>'美团闪购红包',
			'url'=> '/index.php?c=elm&a=other&id=10127&type=3',
		];
		$part2[] =[
		    'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN011sI7Ps2MgYl9QDguL_!!3175549857.jpg',
		    'name'=>'美团优惠券商城',
			'url'=> '/index.php?c=elm&a=other&id=10130&type=3',
		];
		
	 }elseif(C('yh_openmt') == 1){
	 	
	 	$part2[] =  [
	 	    'img'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01LDi4tP2MgYlAbzd0K_!!3175549857.jpg',
	 	    'name'=>'美团外卖红包天天领',
	 	    'url'=> '/index.php?c=elm&a=meituan&id=33&type=1',
	 	];
		$part2[] =[
		    'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01FhOpCj2MgYl9ZgFLE_!!3175549857.jpg',
		    'name'=>'美团生鲜红包天天领',
		    'url'=> '/index.php?c=elm&a=meituan&id=4&type=1',
		];
		$part2[] =[
		    'img'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN010oxYlw2MgYl2JeUni_!!3175549857.jpg',
		    'name'=>'美团优选便宜有好货',
		    'url'=> '/index.php?c=elm&a=meituan&id=105&type=1',
		];
	 	
	 }
	
		$data = array_merge($part1,$part2);
	
	    return $data;
	}
	
	protected function ElmTab(){
		
		$data = array(
			array(
			'name'=>'外卖',
			'title'=>'饿了么红包',
			'id'=>"2192",
			'color'=>'#1193FE',
			'banner'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01tWwBvr2MgYl0j6gSp_!!3175549857.png',
			'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i1/3175549857/O1CN01PMMQvm2MgYl0qtuxU_!!3175549857.jpg"),
			),
			array(
			'name'=>'果蔬超市',
			'id'=>'4441',
			'title'=>'饿了么果超市红包',
			'color'=>'#79BA37',
			'banner'=>'https://img.alicdn.com/imgextra/i4/3175549857/O1CN010vObyk2MgYkwystU1_!!3175549857.jpg',
			'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i3/3175549857/O1CN014aB90d2MgYl7zw6xw_!!3175549857.jpg"),
			)
			);
			
			return $data;
		
	}
	
	protected function MeituanDmTab(){
		
		$data = array(
			array(
			'name'=>'美团外卖',
			'title'=>'美团外卖红包',
			'id'=>"10124",
			'color'=>'#F8D247',
			'banner'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN016Yqt7Y2MgYl7kRGz7_!!3175549857.jpg',
			'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i1/3175549857/O1CN01mB03k42MgYl3dRFlP_!!3175549857.jpg"),
			),
			array(
			'name'=>'美团闪购',
			'title'=>'美团闪购红包',
			'id'=>"10127",
			'color'=>'#BC0601',
			'banner'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01avgJmx2MgYl0vI0II_!!3175549857.jpg',
			'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i2/3175549857/O1CN01oWYd1r2MgYl5oWfbC_!!3175549857.jpg"),
			),array(
			'name'=>'优惠券商城',
			'title'=>'美团优惠券商城',
			'id'=>"10130",
			'color'=>'#FF4827',
			'banner'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01yG2pg12MgYl7lRNAB_!!3175549857.jpg',
			'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i3/3175549857/O1CN01KAv5qE2MgYl9eTCLH_!!3175549857.jpg"),
			)
			);
			
			return $data;
		
	}
	
	protected function MeituanTab(){
		
		$data = array(
			array(
			'name'=>'美团外卖',
			'title'=>'美团外卖红包',
			'id'=>"33",
			'color'=>'#F8D247',
			'banner'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN016Yqt7Y2MgYl7kRGz7_!!3175549857.jpg',
			'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i1/3175549857/O1CN01mB03k42MgYl3dRFlP_!!3175549857.jpg"),
			),
			array(
			'name'=>'美团生鲜',
			'title'=>'美团生鲜红包',
			'id'=>"4",
			'color'=>'#33B865',
			'banner'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01dvq5pe2MgYl7LB6Oy_!!3175549857.jpg',
			'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i4/3175549857/O1CN01lGRKTK2MgYkurZAKi_!!3175549857.jpg"),
			),
			array(
			'name'=>'美团优选',
			'title'=>'美团优选红包',
			'id'=>"105",
			'color'=>'#FFA401',
			'banner'=>'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01TTgDj42MgYl4KH1e3_!!3175549857.jpg',
			'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i3/3175549857/O1CN011kzMKa2MgYl02xsF5_!!3175549857.jpg"),
			),
			);
			
			
		
			
			return $data;
		
		
	}
	
	protected function DidiTab(){
		
	$data =	array(
		array(
		'name'=>'滴滴打车',
		'title'=>'滴滴打车券',
		'id'=>"12485",
		'color'=>'#FF7E01',
		'banner'=>'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01SayEZs2MgYl89Txs6_!!3175549857.jpg',
		'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i4/3175549857/O1CN01MegUt02MgYl6ILQPh_!!3175549857.jpg"),
		),
		array(
		'name'=>'滴滴加油',
		'title'=>'滴滴加油券',
		'id'=>"15200",
		'color'=>'#FE911B',
		'banner'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01rzLOYt2MgYkzT3xg3_!!3175549857.jpg',
		'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i4/3175549857/O1CN01cKSNtx2MgYl8AFUul_!!3175549857.jpg"),
		),
		array(
		'name'=>'滴滴货运',
		'title'=>'滴滴货运券',
		'id'=>"12644",
		'color'=>'#01C897',
		'banner'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01ud9kMk2MgYl13KDKI_!!3175549857.jpg',
		'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i2/3175549857/O1CN01qblzKE2MgYkwT5pHM_!!3175549857.jpg"),
		),
		array(
		'name'=>'花小猪打车',
		'title'=>'花小猪打车券',
		'id'=>"14801",
		'color'=>'#A300ED',
		'banner'=>'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01XqgD572MgYl2izGXn_!!3175549857.jpg',
		'poster'=>trim(C('yh_site_url')).'/?c=outputpic&a=outimg&url='.base64_encode("https://img.alicdn.com/imgextra/i4/3175549857/O1CN01vaP5wg2MgYl6IIGyv_!!3175549857.jpg"),
		)
		);
		
		return $data;
	}
	
	
}
