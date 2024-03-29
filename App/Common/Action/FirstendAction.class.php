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
        $this->cur_url = strtolower($_SERVER["REQUEST_URI"]);
        $this->assign('nav_curr', $this->cur_url);
        if (S('catetree')) {
            $catetree = S('catetree');
        } else {
            $catetree = $this->_cate_mod->where('status=1 and pid=0')->field('id,name,cateimg')->order('ordid desc')->select();
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
        $this->memberinfo = $this->visitor->info;
        $this->assign('visitor', $this->memberinfo);

    }


    protected function Getspecial()
    {
        $result = S('special');
        if (!$result) {
            $url = $this->tqkapi . "/getrelationid";
            $data = [
                'time' => time(),
                //'info_type'=>2,
                'tqk_uid' => $this->tqkuid,
            ];
            $token = $this->create_token(trim(C('yh_gongju')), $data);
            $data['token'] = $token;
            $data = $this->_curl($url, $data, true);
            $result = json_decode($data, true);
            S('special', $result, 120);
        }

        return $result;


    }

    /**
     * @param $num_iid
     * @param $memberinfo
     * @param $Quan_id
     * @return mixed|string
     */
    protected function Tbconvert($num_iid, $memberinfo = array(), $Quan_id = '')
    {

        $cacheName = md5($num_iid . $memberinfo['id'] . $memberinfo['special_id'] . $memberinfo['webmaster_pid']);
        $cacheResult = S($cacheName);
        if ($cacheResult) {
            return $cacheResult;
        }
        $apiurl = $this->tqkapi . '/gconvert';
        $apidata = [
            'tqk_uid' => $this->tqkuid,
            'time' => time(),
            'good_id' => '' . $num_iid . ''
        ];
        $pid = trim(C('yh_taobao_pid'));
        $relation = '';
        if($memberinfo && C('yh_bingtaobao') == 0 && !is_null(C('yh_bingtaobao'))){
        $R = A("Records");
        $Arr = explode('-',$num_iid);
        $itemId = $Arr[1]?$Arr[1]:$num_iid;
        $res= $R ->content($itemId,$memberinfo['id']);
        $pid = $res['pid'];
        $apidata['pid'] = $pid;

        }



        if ($memberinfo && $memberinfo['webmaster_pid'] > 2 && C('yh_ipban_switch') == 2) {
            $apidata['RelationId'] = $memberinfo['webmaster_pid'];
            $apidata['protype'] = 2; //推广赚
            $relation = '&relationId=' . $memberinfo['webmaster_pid'];
        }
        $token = $this->create_token(trim(C('yh_gongju')), $apidata);
        $apidata['token'] = $token;
        $res = $this->_curl($apiurl, $apidata, false);
        $res = json_decode($res, true);

        $me = $res['me'];
        if ($res && $memberinfo && $memberinfo['special_id'] < 2) {

            $quanurl = $res['coupon_info']?$res['quanurl']:$res['item_url'];
            $quanurl = $quanurl.$relation;
            S($cacheName, $quanurl);
            return $quanurl;

        } elseif ($res && \strlen($res['me']) > 5) {
            if ($Quan_id) {
                $activityId = $Quan_id ? '&activityId=' . $Quan_id : '';
                $quanurl = 'https://uland.taobao.com/coupon/edetail?e=' . $me . $activityId . '&pid=' . $pid . $relation . '&af=1';
            } else {
                $quanurl = $res['coupon_info']?$res['quanurl']:$res['item_url'];
                $quanurl = $quanurl.$relation;
            }
            S($cacheName, $quanurl);
            return $quanurl;

        } else {

            return false;
    /*
            $link = 'https://uland.taobao.com/coupon/edetail?itemId=' . $num_iid . '&activityId=' . $Quan_id . '&pid=' . $pid . $relation . '';

            if ($Quan_id) {
                $quanurl = $link;
            } else {
                $quanurl = $res['item_url'] ?: $link;
            }
            S($cacheName, $quanurl);
            return $quanurl;
            */

        }


        return false;

    }


    protected function parent_pid()
    {
        $pid = trim(C('yh_taobao_pid'));
        $apppid = explode('_', $pid);
        return '_' . $apppid[3];
    }

    protected function _wechat_login($data)
    {
        if ($data) {
            $openid = $data['wx_openid'];
            $UnionId = C('yh_unionid');
            if ($UnionId == 1 && $data['wx_unionid']) {
                $mod = D('user');
                $openid = $mod->where(array('unionid' => $data['wx_unionid']))->getField('openid');
                if (!$openid) {
                    $mod->where(array('opid' => $data['wx_openid']))->save(array('unionid' => $data['wx_unionid']));
                    $openid = $data['wx_openid'];
                }

            }

            if (C('yh_site_tiaozhuan') == 1) { //认证的服务号
                $newOpenid = $data['wx_openid'];
            }
            $visitor = new user_visitor();
            $res = $visitor->wechatlogin($openid, $newOpenid, $data);

            return $res;
        }
    }


    /**
     * @param $user
     * @return mixed|void
     */
    protected function CreateElmPid($user)
    {
        $pid = trim(C('yh_elmpid'));
        $appkey = trim(C('yh_elmkey'));
        $secret = trim(C('yh_elmsecret'));
        $uid = $user['id'];
        if ($pid && $appkey && C('yh_elm') == 1 && strlen($user['elm_pid']) < 3) {
            vendor("taobao.taobao");
            $adName = $uid;
            $c = new \TopClient();
            $c->appkey = $appkey;
            $c->secretKey = $secret;
            $req = new \AlibabaAlscUnionMediaZoneAddRequest();
            $req->setZoneName($adName);
            $resp = $c->execute($req);
            $resparr = json_decode(json_encode($resp), true);
            if ($resparr && $resparr['result']['pid']) {
                $result = M('user')->where(['id' => $uid])->setField('elm_pid', $resparr['result']['pid']);
                $visitor = new user_visitor();
                $visitor->wechatlogin($user['openid']); // update userinfo
                return $resparr['result']['pid'];

            }

            $MonthTime = strtotime(date('Y-m-01', strtotime('last day of -1 month')));
            $Sqlwhere = array(
                'elm_pid' => array('gt', 0),
                'last_time' => array('lt', $MonthTime),
            );
            $Res = M('user')->field('id,elm_pid')->where($Sqlwhere)->order('id asc')->find();
            if ($Res && $Res['elm_pid']) {
                $Sql = 'Update tqk_user set elm_pid=' . $Res['elm_pid'] . ' where id=' . $uid . ';Update tqk_user set elm_pid="0" where id =' . $Res['id'] . '';
                M()->execute($Sql);
                $visitor = new user_visitor();
                $visitor->wechatlogin($user['openid']); // update userinfo
                return $Res['elm_pid'];
            }


            return false;

        }
    }


    /**
     * @param $type
     * @param $size
     * @param $p
     * @param $id
     * @return mixed
     */
    protected function  CallTophot($type='',$size='',$p='',$id='',$op=''){

        $CacheName = md5('TophotCate'.$type.$size.$p.$id,$op);
        if(false === $data = S($CacheName)){

            $apiurl=$this->tqkapi.'/tophot';
            $data=[
                'time'=>time(),
                'tqk_uid'=>	$this->tqkuid,
            ];
            if($type){
                $data['type']= $type;
            }
            if($size){
                $data['size']= $size;
            }
            if($id){
                $data['id']= $id;
            }
            if($p){
                $data['p']= $p;
            }
            if($op){
                $data['op']= $op;
            }
            $token=$this->create_token(trim(C('yh_gongju')), $data);
            $data['token']=$token;
            $result=$this->_curl($apiurl, $data, true);
            $data=json_decode($result, true);

            S($CacheName,$data,300);
        }

        return $data;
    }

    /**
     * @param $user
     * @return mixed|void
     */
    protected function CreateJdPid($user)
    {
        $pid = trim(C('yh_jdpid'));
        $key = trim(C('yh_jdauthkey'));
        $uid = $user['id'];
        if ($pid && $key && C('yh_openjd') == 1 && $user['jd_pid'] < 1) {
            $apiurl = $this->tqkapi . '/Createjdpid';
            $apidata = [
                'tqk_uid' => $this->tqkuid,
                'time' => time(),
                'pid' => $pid,
                'uid' => $uid,
            ];
            $token = $this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token'] = $token;
            $res = $this->_curl($apiurl, $apidata, false);
            $res = json_decode($res, true);
            if ($res['code'] == 200 && $res['result']['return'] == 0 && $res['result']['result']['positionid']) {
                $data = [
                    'unionid' => $res['result']['result']['unionId'],
                    'name' => $uid,
                    'positionid' => $res['result']['result']['positionid'],
                    'type' => 1,
                    'uid' => $uid,
                    'status' => 1
                ];
                $result = M('jdpositionid')->add($data);

                if ($result) {
                    $result = M('user')->where(['id' => $uid])->setField('jd_pid', $res['result']['result']['positionid']);
                    $visitor = new user_visitor();
                    $visitor->wechatlogin($user['openid']); // update userinfo
                    return $res['result']['result']['positionid'];
                }
            } else {

                $MonthTime = strtotime(date('Y-m-01', strtotime('last day of -1 month')));
                $Sqlwhere = array(
                    'jd_pid' => array('gt', 1),
                    'last_time' => array('lt', $MonthTime),
                );
                $Res = M('user')->field('id,jd_pid')->where($Sqlwhere)->order('id asc')->find();
                if ($Res && $Res['jd_pid']) {
                    $Sql = 'Update tqk_user set jd_pid=' . $Res['jd_pid'] . ' where id=' . $uid . ';Update tqk_user set jd_pid="0" where id =' . $Res['id'] . '';
                    M()->execute($Sql);
                    $visitor = new user_visitor();
                    $visitor->wechatlogin($user['openid']); // update userinfo
                    return $Res['jd_pid'];
                }


            }


            // S('createjdpid_'.$uid, true);
        }
    }

    protected function GetTrackid($field)
    {
        $track_val = cookie('trackid');
        if (!empty($track_val)) {
            $track = unserialize($track_val);
            if ($track[$field]) {
                return $track[$field];
            }
            return false;
        }
    }

    /**
     * @param $uid
     * @return false|mixed
     */
    protected function phoneBill($uid = '',$type='',$url='')
    {

        $pid = trim(C('yh_youhun_secret'));
        if ($pid) {
            $where = [
                'type' => 'pdd.ddk.resource.url.gen',
                'data_type' => 'JSON',
                'timestamp' => $this->msectime(),
                'client_id' => trim(C('yh_pddappkey')),
                'generate_we_app' => 'true',
                'resource_type' => $type?$type:39997,
                'pid' => $pid
            ];

            if ($uid) {
                $where['custom_parameters'] = $uid;
            }

            if($url){
                $where['url']=$url;
            }

            $where['sign'] = $this->create_pdd_sign(trim(C('yh_pddsecretkey')), $where);
            $pdd_api = 'http://gw-api.pinduoduo.com/api/router';
            $result = $this->_curl($pdd_api, $where, true);
            $data = json_decode($result, true);
            return $data;

        }

        return false;

    }

    /**
     * @param $id
     * @return false|mixed
     */
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

    /**
     * @param $id
     * @return array|mixed
     */
    protected function GetTbDetail($id)
    {

        $CacheName = md5($id);
        $CacheResult = S($CacheName);
        if ($CacheResult) {
           return $CacheResult;
        }
        $appkey = trim(C('yh_taobao_appkey'));
        $appsecret = trim(C('yh_taobao_appsecret'));
        $apppid = trim(C('yh_taobao_pid'));
        $apppid = explode('_', $apppid);
        $AdzoneId = $apppid[3];
        $key = 'https://uland.taobao.com/item/edetail?id=' . $id;
        vendor("taobao.taobao");
        $c = new \TopClient();
        $c->appkey = $appkey;
        $c->secretKey = $appsecret;
        $c->format = 'json';
        $req = new \TbkDgMaterialOptionalUpgradeRequest();
        $req->setAdzoneId($AdzoneId);
        $req->setPageSize("1");
        $req->setSort("tk_total_sales_des");
        if ($key) {
            $req->setQ((string)$key);
        }
        $req->setPageNo(1);
        $req->setSort("tk_des");
        $resp = $c->execute($req);
        $resp = json_decode(json_encode($resp), true);
        $resp = $resp['result_list']['map_data'][0];

        if($resp){
         $item = array();
         $url = $resp['publish_info']['coupon_share_url'] ? $resp['publish_info']['coupon_share_url']:$resp['publish_info']['click_url'];
        $item['title'] =  $resp['item_basic_info']['short_title']?$resp['item_basic_info']['short_title']:$resp['item_basic_info']['title'];
        $item['sellerId']=$resp['item_basic_info']['seller_id'];
        $item['pic_url']=$resp['item_basic_info']['white_image']?$resp['item_basic_info']['white_image']:$resp['item_basic_info']['pict_url'];
        $item['price']=$resp['price_promotion_info']['zk_final_price'];
        $item['quan']=$resp['price_promotion_info']['final_promotion_path_list']['final_promotion_path_map_data'][0]['promotion_fee'];
        $item['link']='https:'.$url;
        $item['commission_rate']=$resp['publish_info']['income_rate']*100;
        $item['tk_commission_rate']=$resp['publish_info']['income_rate']*100;
        $item['click_url']='https:'.$url;
        $item['volume']=$resp['item_basic_info']['volume'];
        $item['coupon_price']=$resp['price_promotion_info']['final_promotion_price']?$resp['price_promotion_info']['final_promotion_price']:$resp['price_promotion_info']['zk_final_price'];
        $item['coupon_end_time']=substr($resp['price_promotion_info']['final_promotion_path_list']['final_promotion_path_map_data'][0]['promotion_end_time'],0,-3);
        $item['ems']=2;
        $item['quanurl']='https:'.$url;
        $item['Quan_id']= $resp['price_promotion_info']['final_promotion_path_list']['final_promotion_path_map_data'][0]['promotion_id'];
        $item['coupon_price']= $resp['price_promotion_info']['final_promotion_price']?$resp['price_promotion_info']['final_promotion_price']:$resp['price_promotion_info']['zk_final_price'];
        $item['num_iid']=$resp['item_id'];
        if ($resp['item_basic_info']['user_type']=="1") {
            $item['shop_type']='B';
        } else {
            $item['shop_type']='C';
        }
        }

        S($CacheName, $item);
        return $item;
    }

    // 自动表单令牌验证
    protected function tqkCheckToken($data)
    {
        // 支持使用token(false) 关闭令牌验证
        if (isset($this->options['token']) && !$this->options['token']) {
            return true;
        }
        if (C('TOKEN_ON')) {
            $name = C('TOKEN_NAME', null, '__hash__');
            if (!isset($data[$name]) || !isset($_SESSION[$name])) { // 令牌数据无效
                return false;
            }

            // 令牌验证
            list($key, $value) = explode('_', $data[$name]);
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
        $track_val = cookie('trackid');
        if (!empty($track_val)) {
            $track = unserialize($track_val);
            $track = '_' . $track['t_pid'];
            $par_pid = $this->parent_pid();
            $pid = str_replace($par_pid, $track, trim(C('yh_taobao_pid')));
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
    protected function _config_seo($seo_info = [], $data = [], $assign = 'true')
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
            return $page_seo;
        }
    }

    protected function invicode($uid)
    {
        $mod = new userModel();
        //$code=$this->randStr($uid,6);
        $str = 800;
        $newstr = $str + $uid;
        $num = sprintf("%06d", $newstr);
        $data = [
            'invocode' => $num
        ];
        $res = $mod->where('id=' . $uid)->save($data);

        if ($res) {
            return $num;
        }
    }

    protected function reinvi($uid)
    {
        $reinvi = F('reinvi_' . $uid);
        $score = trim(C('yh_reinte'));
        if ($score > 0 && $uid && false === $reinvi) {
            F('reinvi_' . $uid, $uid);
            $mod = new userModel();
            $mod->where("id='" . $uid . "'")->setInc('score', $score);
            M('basklistlogo')->add([
                'uid' => $uid,
                'integray' => $score,
                'remark' => '注册送+' . $score,
                'order_sn' => '--',
                'create_time' => NOW_TIME,
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

    protected function fullurl()
    {
        $mdomain = ((int)$_SERVER['SERVER_PORT'] == 80 ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
        $url = $_SERVER["REQUEST_URI"];
        // $requestUrl = strtolower($url);
        // if(strpos($url, 'pdditem')>0 || strpos($url, 'jditem')>0){
        $requestUrl = $url;
        // }
        $url = urlencode($mdomain . $requestUrl);
        return $url;
    }


    protected function Takeout()
    {
/*
        if (C('yh_openjd') == 1) {
            $part1[] = [
                'img' => 'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01ZVLZFa2MgYl9OJjgs_!!3175549857.jpg',
                'name' => '话费流量充值享折扣',
                'url' => '/index.php?c=elm&a=chong',
            ];
        }*/

        if (C('yh_dm_cid_kfc') == 1) {
            $part1[] = [
                'img' => 'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01h2nFyx2MgYl4UaieB_!!3175549857.jpg',
                'name' => '肯德基5折起',
                'url' => '/index.php?c=elm&a=other&id=5933&type=1',
            ];
        }
        if (C('yh_dm_cid_qz') == 1) {
            $part1[] = [
                'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN017ShJrA2MgYl6pLrQg_!!3175549857.jpg',
                'name' => '特惠电影票',
                'url' => '/index.php?c=elm&a=other&id=6680&type=1',
            ];
        }

        if (C('yh_dm_cid_dd') == 1) {
            $part1[] = [
                'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN012ghZIt2MgYl860dth_!!3175549857.jpg',
                'name' => '滴滴打车 最高立减10元',
                'url' => '/index.php?c=elm&a=other&id=12485&type=3&aid=207059212323',
            ];
            $part1[] = [
                'img' => 'https://img.alicdn.com/imgextra/i4/3175549857/O1CN01xJ4TV52MgYlCmAFwB_!!3175549857.jpg',
                'name' => '汽车加油 最高立减20',
                'url' => '/index.php?c=elm&a=other&id=15200&type=3&aid=206888136013'
            ];
            $part1[] = [
                'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01FThA9Z2MgYlDYYxy5_!!3175549857.jpg',
                'name' => '滴滴货运券 最高立减15',
                'url' => '/index.php?c=elm&a=other&id=12644&type=3&aid=209118946358'
            ];
            $part1[] = [
                'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN018ljXs02MgYlDYdStB_!!3175549857.jpg',
                'name' => '花小猪打车，首单立减10元',
                'url' => '/index.php?c=elm&a=other&id=14801&type=3&aid=208743698546'
            ];
        }


        if (C('yh_elm') == 1) {
            $CacheName = 'ElmListhot';
            if(false === $data = S($CacheName)){
                $apiurl=$this->tqkapi.'/elmlist';
                $data=[
                    'key'=>$this->_userappkey,
                    'time'=>time(),
                    'tqk_uid'=>	$this->tqkuid,
                    'type'=>$type,
                ];
                $token=$this->create_token(trim(C('yh_gongju')), $data);
                $data['token']=$token;
                $result=$this->_curl($apiurl, $data, true);
                $data=json_decode($result, true);
                S($CacheName,$data);
            }
            $time = time();
            foreach ($data as $k=>$v){
                if(strtotime($v['time'])>$time){
                    $part1[] = [
                        'img' => $v['img'],
                        'name' => $v['title'],
                        'url' => '/index.php?c=elm&a=elmlink&id='.$v['id'].'&type=3'
                    ];

                }
            }


        }


        $part2 = [];
        if (C('yh_dm_cid_mt') == 1) {
            $part2[] = [
                'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01LDi4tP2MgYlAbzd0K_!!3175549857.jpg',
                'name' => '美团外卖红包天天领',
                'url' => '/index.php?c=elm&a=other&id=10124&type=3',
            ];
//            $part2[] = [
//                'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01RumGh62MgYlCReb86_!!3175549857.jpg',
//                'name' => '美团闪购红包',
//                'url' => '/index.php?c=elm&a=other&id=10127&type=3',
//            ];
            $part2[] = [
                'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN011sI7Ps2MgYl9QDguL_!!3175549857.jpg',
                'name' => '美团优惠券商城',
                'url' => '/index.php?c=elm&a=other&id=10130&type=3',
            ];

        } elseif (C('yh_openmt') == 1) {

            $part2[] = [
                'img' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01LDi4tP2MgYlAbzd0K_!!3175549857.jpg',
                'name' => '美团外卖红包天天领',
                'url' => '/index.php?c=elm&a=meituan&id=33&type=1',
            ];
//            $part2[] = [
//                'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01FhOpCj2MgYl9ZgFLE_!!3175549857.jpg',
//                'name' => '美团生鲜红包天天领',
//                'url' => '/index.php?c=elm&a=meituan&id=4&type=1',
//            ];
//            $part2[] = [
//                'img' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN010oxYlw2MgYl2JeUni_!!3175549857.jpg',
//                'name' => '美团优选便宜有好货',
//                'url' => '/index.php?c=elm&a=meituan&id=105&type=1',
//            ];

        }

        $data = array_merge($part1, $part2);

        return $data;
    }

    protected function ElmTab()
    {

        $data = array(
            array(
                'name' => '外卖红包',
                'title' => '饿了么红包',
                'id' => "2192",
                'color' => '#1193FE',
                'banner' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01tWwBvr2MgYl0j6gSp_!!3175549857.png',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i1/3175549857/O1CN01PMMQvm2MgYl0qtuxU_!!3175549857.jpg"),
            ),
            array(
                'name' => '果蔬超市红包',
                'id' => '4441',
                'title' => '饿了么超市红包',
                'color' => '#79BA37',
                'banner' => 'https://img.alicdn.com/imgextra/i4/3175549857/O1CN010vObyk2MgYkwystU1_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i3/3175549857/O1CN014aB90d2MgYl7zw6xw_!!3175549857.jpg"),
            )
        );

        return $data;

    }

    protected function MeituanDmTab()
    {

        $data = array(
            array(
                'name' => '美团外卖',
                'title' => '美团外卖红包',
                'id' => "10124",
                'color' => '#F8D247',
                'banner' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN016Yqt7Y2MgYl7kRGz7_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i1/3175549857/O1CN01mB03k42MgYl3dRFlP_!!3175549857.jpg"),
            ),
//            array(
//                'name' => '美团闪购',
//                'title' => '美团闪购红包',
//                'id' => "10127",
//                'color' => '#BC0601',
//                'banner' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01avgJmx2MgYl0vI0II_!!3175549857.jpg',
//                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i2/3175549857/O1CN01oWYd1r2MgYl5oWfbC_!!3175549857.jpg"),
//            ),
array(
                'name' => '优惠券商城',
                'title' => '美团优惠券商城',
                'id' => "10130",
                'color' => '#FF4827',
                'banner' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01yG2pg12MgYl7lRNAB_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i3/3175549857/O1CN01KAv5qE2MgYl9eTCLH_!!3175549857.jpg"),
            )
        );

        return $data;

    }

    /**
     * @return array[]
     */
    protected function UxuanTab()
    {

        $data = array(
            array(
                'name' => '超级U选',
                'title' => '优选天猫爆品，享官方补贴！',
                'id' => "4180",
                'color' => '#FEA1C0',
                'banner' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01FAQTzx2MgYqI3YPaZ_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i1/3175549857/O1CN01mB03k42MgYl3dRFlP_!!3175549857.jpg"),
            ),
            array(
                'name' => 'U选快抢',
                'title' => '好货最高9.9元',
                'id' => "4185",
                'color' => '#F9A24B',
                'banner' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN0179eBoh2MgYqHRBq74_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i4/3175549857/O1CN01lGRKTK2MgYkurZAKi_!!3175549857.jpg"),
            ),
            array(
                'name' => 'U选特惠',
                'title' => '优选全网低价',
                'id' => "4187",
                'color' => '#FEA1C0',
                'banner' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01JiIE4F2MgYqKqE4ge_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i3/3175549857/O1CN011kzMKa2MgYl02xsF5_!!3175549857.jpg"),
            ),
        );


        return $data;


    }

    protected function MeituanTab()
    {

        $data = array(
            array(
                'name' => '美团外卖',
                'title' => '美团外卖红包',
                'id' => "33",
                'color' => '#F8D247',
                'banner' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN016Yqt7Y2MgYl7kRGz7_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i1/3175549857/O1CN01mB03k42MgYl3dRFlP_!!3175549857.jpg"),
            ),
//            array(
//                'name' => '美团生鲜',
//                'title' => '美团生鲜红包',
//                'id' => "221",
//                'color' => '#33B865',
//                'banner' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01dvq5pe2MgYl7LB6Oy_!!3175549857.jpg',
//                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i4/3175549857/O1CN01lGRKTK2MgYkurZAKi_!!3175549857.jpg"),
//            ),
//            array(
//                'name' => '美团优选',
//                'title' => '美团优选红包',
//                'id' => "203",
//                'color' => '#FFA401',
//                'banner' => 'https://img.alicdn.com/imgextra/i2/3175549857/O1CN01TTgDj42MgYl4KH1e3_!!3175549857.jpg',
//                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i3/3175549857/O1CN011kzMKa2MgYl02xsF5_!!3175549857.jpg"),
//            ),
        );


        return $data;


    }

    protected function DidiTab()
    {

        $data = array(
            array(
                'name' => '滴滴打车',
                'title' => '滴滴打车券',
                'id' => "12485",
                'activity'=>'207059212323',
                'color' => '#FF7E01',
                'banner' => 'https://img.alicdn.com/imgextra/i1/3175549857/O1CN01SayEZs2MgYl89Txs6_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i4/3175549857/O1CN01MegUt02MgYl6ILQPh_!!3175549857.jpg"),
            ),
            array(
                'name' => '滴滴加油',
                'title' => '滴滴加油券',
                'id' => "15200",
                'activity'=>'206888136013',
                'color' => '#FE911B',
                'banner' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01rzLOYt2MgYkzT3xg3_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i4/3175549857/O1CN01cKSNtx2MgYl8AFUul_!!3175549857.jpg"),
            ),
            array(
                'name' => '滴滴货运',
                'title' => '滴滴货运券',
                'id' => "12644",
                'activity'=>'209118946358',
                'color' => '#01C897',
                'banner' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01ud9kMk2MgYl13KDKI_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i2/3175549857/O1CN01qblzKE2MgYkwT5pHM_!!3175549857.jpg"),
            ),
            array(
                'name' => '花小猪打车',
                'title' => '花小猪打车券',
                'id' => "14801",
                'activity'=>'208743698546',
                'color' => '#A300ED',
                'banner' => 'https://img.alicdn.com/imgextra/i3/3175549857/O1CN01XqgD572MgYl2izGXn_!!3175549857.jpg',
                'poster' => trim(C('yh_site_url')) . '/?c=outputpic&a=outimg&url=' . base64_encode("https://img.alicdn.com/imgextra/i4/3175549857/O1CN01vaP5wg2MgYl6IIGyv_!!3175549857.jpg"),
            )
        );

        return $data;
    }



    /**
     * @param $pr
     * @param $accesskey
     * @return string
     */
    protected function get_didi_sign($pr, $accesskey)
    {
        ksort($pr);
        $ptr = array();
        foreach ($pr as $key => $val) {
            array_push($ptr, $key . "=" . $val);
        }
        $source = urlencode(implode("&", $ptr)).$accesskey;
        $sign = urlencode(base64_encode(sha1($source)));
        return $sign;
    }

    /**
     * @param $url
     * @param $jsonStr
     * @param $header
     * @return bool|string
     */
    protected  function DidiPost($url, $jsonStr, $header){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $parse_url = parse_url($url);
        if($parse_url["scheme"]=="https"){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * @param $url
     * @param $params
     * @param $header
     * @return bool|string
     */
    protected  function DidiGet($url, $params, $header){
        $query = '';
        foreach ($params as $param => $value) {
            $query .= $param.'='.$value .'&';
        }
        $url = $url.'?'.$query;
        $ch = curl_init((string)$url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $parse_url = parse_url($url);
        if($parse_url["scheme"]=="https"){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * @param $source_id
     * @param $activity
     * @return array|false|mixed|void|null
     */
    protected function CreateDidiLink($source_id='m001',$activity,$link_type='h5'){
        $CacheName = 'Didi_'.$source_id.$activity;
        $data =  F($CacheName);
        if(!$data){
            $generate_link_url = "https://union.didi.cn/openapi/v1.0/link/generate";
            $generate_code_url = "https://union.didi.cn/openapi/v1.0/code/generate";

            $param_to_sign = array(
                "App-Key"      =>    trim(C('yh_ddappkey')),
                "Timestamp"    =>    time(),
                "source_id"    =>    $source_id, //	来源ID
                "activity_id"  =>    $activity,  //活动ID
                "link_type"    =>    $link_type, //mini  or  h5
                "promotion_id" =>    trim(C('yh_ddpid')) //推广位ID
            );

            $sign = $this->get_didi_sign($param_to_sign, trim(C('yh_ddsecret')));
            $json = sprintf("{\"activity_id\":%d,\"source_id\":\"%s\",\"link_type\":\"%s\",\"promotion_id\":%s}", $activity, $source_id, $link_type, trim(C('yh_ddpid')));
            $header = array(
                "App-Key: ".$param_to_sign['App-Key'],
                "Timestamp: ". $param_to_sign['Timestamp'],
                "Sign: ".$sign,
                "Content-Type: application/json",
                "Content-Length: ".strlen($json)
            );

            $link_response = json_decode($this->DidiPost($generate_link_url, $json, $header), true);

            if ($link_response["errno"] != 0) {
                return;
            }
            $Timestamp = time();
            $dsi = $link_response["data"]["dsi"];
            $param_to_sign = array(
                "App-Key"      =>    trim(C('yh_ddappkey')),
                "Timestamp"    =>    $Timestamp,
                "source_id"    =>    $source_id,
                "dsi"          =>    $dsi,
                "type"         =>    "mini"
            );
            $param = array(
                "source_id"    =>    $source_id,
                "dsi"          =>    $dsi,
                "type"         =>    "mini"
            );
            $sign = $this->get_didi_sign($param_to_sign, trim(C('yh_ddsecret')));
            $header = array(
                "App-Key: ".trim(C('yh_ddappkey')),
                "Timestamp: ". $Timestamp,
                "Sign: ".$sign
            );
            $response = json_decode($this->DidiGet($generate_code_url, $param, $header),true);
            $data = array(
                'wx_qrcode'=>$response['data']['code_link'],
                'cps_short'=>$link_response["data"]["link"],
                'wx_path'=>$link_response['data']['link'],
                'wx_appid'=>$link_response["data"]["app_id"],
            );

            F($CacheName,$data);

        }

        return $data;

    }


    /**
     * @param $sid
     * @param $key
     * @param $page
     * @param $sort
     * @param $items_list
     * @return array
     */
    protected function GetApiList($sid,$key,$page,$sort,$items_list,$goodslist){

        $appkey=trim(C('yh_taobao_appkey'));
        $appsecret=trim(C('yh_taobao_appsecret'));
        $apppid=trim(C('yh_taobao_pid'));
        $apppid=explode('_', $apppid);
        $AdzoneId=$apppid[3];
        $count=count($items_list);

        if(!empty($appkey) && !empty($appsecret)  && $count<=20 && !empty($AdzoneId)){
            vendor('taobao.taobao');
            $c = new \TopClient();
            $c->appkey = $appkey;
            $c->secretKey = $appsecret;
            $c->format = 'json';
            $req = new \TbkDgMaterialOptionalUpgradeRequest();

            $req->setPageSize("20");
            $req->setPageNo($page?$page:1);
            if ($sort=='hot') {
                $req->setSort("total_sales_des");
            } elseif ($sort=='price') {
                $req->setSort("price_asc");
            } elseif ($sort=='rate') {
                $req->setSort("tk_rate_des");
            } else {
                $req->setSort("total_sales_des");
            }

            if ($sid) {
                $req->setCat("".$sid."");
            }
            if ($key) {
                if(mb_strlen($key)>20){
                    $key = mb_substr($key, 0, -1);
                };
                $req->setQ((string)$key);
            }
            $req->setAdzoneId($AdzoneId);
            $resp = $c->execute($req);

            $resp = json_decode(json_encode($resp), true);
            $resp=$resp['result_list']['map_data'];

            $patterns = "/\d+/";
            foreach ($resp as $k=>$v) {
                $title = $v['item_basic_info']['short_title']?$v['item_basic_info']['short_title']:$v['item_basic_info']['title'];
                if ($this->FilterWords($title) || !$v['item_id']) {
                    continue;
                }
                $coupon_price =  $v['price_promotion_info']['final_promotion_price']?$v['price_promotion_info']['final_promotion_price']:$v['price_promotion_info']['zk_final_price'];
                $quan = $v['price_promotion_info']['final_promotion_path_list']['final_promotion_path_map_data'][0]['promotion_fee'];
                $coupon_id = $v['price_promotion_info']['final_promotion_path_list']['final_promotion_path_map_data'][0]['promotion_id'];
                $goodslist[$k+$count]['quan']=$quan;
                $goodslist[$k+$count]['coupon_click_url']=$v['publish_info']['coupon_share_url'] ? $v['publish_info']['coupon_share_url']:$v['publish_info']['click_url'];
                $goodslist[$k+$count]['num_iid']=$v['item_id'];
                $goodslist[$k+$count]['title']=$title;
                $goodslist[$k+$count]['coupon_id']=$coupon_id;
                $goodslist[$k+$count]['coupon_price']=$coupon_price;
                if ($v['item_basic_info']['user_type']=="1") {
                    $goodslist[$k+$count]['shop_type']='B';
                } else {
                    $goodslist[$k+$count]['shop_type']='C';
                }
                $goodslist[$k+$count]['commission_rate']=$v['publish_info']['income_rate']*100; //比例
                $goodslist[$k+$count]['price']=$v['price_promotion_info']['zk_final_price'];
                $goodslist[$k+$count]['volume']=$v['item_basic_info']['volume'];
                $goodslist[$k+$count]['pic_url']=$v['item_basic_info']['white_image']?$v['item_basic_info']['white_image']:$v['item_basic_info']['pict_url'];
                $goodslist[$k+$count]['category_id']=$v['item_basic_info']['category_id'];

            }


            return $goodslist;

        }


        return $goodslist;


    }


}
