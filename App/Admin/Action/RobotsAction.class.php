<?php
namespace Admin\Action;

use Common\Model;
use Common\TagLib\Dir;
use Common\Model\robotsModel;
use Common\Model\itemscateModel;
class RobotsAction extends BaseAction
{
    private $_tbconfig = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->tqkuid = C('yh_app_kehuduan');
        $tkapi = trim(C('yh_api_line'));
        if (false === $tkapi) {
            $this->tqkapi = 'http://api.tuiquanke.cn';
        } else {
            $this->tqkapi = $tkapi;
        }
        $appkey = trim(C('yh_gongju'));
        if (!$appkey) {
            $this->error('请到 站点设置  填写通行密钥', U('setting/index'));
        } else {
            $this->getopenid($appkey);
        }
        $this->_mod = new robotsModel();
        $this->_cate_mod = new itemscateModel();
    }
    public function pdd()
    {
        $where['status'] = 1;
        $where['http_mode'] = 0;
        $robots = M('pddrobots')->where($where)->order('ordid asc')->select();
        $this->assign('list', $robots);
        $this->display();
    }
    public function index()
    {
        $robots = M('robots')->order('ordid asc')->select();
        $this->assign('list', $robots);
        $this->display();
    }
    public function edit()
    {
        if (IS_POST) {
            $id = I('id', '', 'trim');
            $name = I('name', '', 'trim');
            $http_mode = I('http_mode', '', 'trim');
            $cate_id = I('cate_id', '', 'trim');
            $keyword = I('keyword', '', 'trim');
            $page = I('page', '', 'trim');
            if (!$name || !trim($name)) {
                $this->error('请填写任务名称');
            }
            if (!$cate_id || !trim($cate_id)) {
                $this->error('请选择商品分类');
            }
            $data['name'] = $name;
            $data['http_mode'] = $http_mode;
            $data['cate_id'] = $cate_id;
            $data['keyword'] = $keyword;
            $data['page'] = $page;
            $data['sort'] = 0;
            $data['tb_cid'] = I('tb_cid', '', 'trim');
            $info = $this->_cate_mod->field('pid,ali_id')->where(array('id' => $cate_id))->find();
            $data['cid'] = $info['pid'];
            $data['recid'] = $info['ali_id'];
            $this->_mod->where(array('id' => $id))->save($data);
            $this->success(L('operation_success'));
        } else {
            $id = I('id', '', 'intval');
            $item = $this->_mod->where(array('id' => $id))->find();
            $spid = $this->_cate_mod->where(array('id' => $item['cate_id']))->getField('spid');
            if ($spid == 0) {
                $spid = $item['cate_id'];
            } else {
                $spid .= $item['cate_id'];
            }
            $this->assign('selected_ids', $spid);
            //分类选中
            $this->assign('info', $item);
            if (!function_exists("curl_getinfo")) {
                $this->error(L('curl_not_open'));
            }
            $this->display();
        }
    }
    public function add_do()
    {
        if (!function_exists("curl_getinfo")) {
            $this->error(L('curl_not_open'));
        }
        if (IS_POST) {
            $name = I('name', '', 'trim');
            $http_mode = I('http_mode', '', 'trim');
            $cate_id = I('cate_id', '', 'trim');
            $keyword = I('keyword', '', 'trim');
            $page = I('page', '', 'trim');
            if (!$name || !trim($name)) {
                $this->error('请填写任务名称');
            }
            if (!$cate_id || !trim($cate_id)) {
                $this->error('请选择商品分类');
            }
            $data['name'] = $name;
            $data['http_mode'] = $http_mode;
            $data['cate_id'] = $cate_id;
            $data['keyword'] = $keyword;
            $data['page'] = $page;
            $data['sort'] = 0;
            $data['tb_cid'] = I('tb_cid', '', 'trim');
            $info = $this->_cate_mod->field('pid,ali_id')->where(array('id' => $cate_id))->find();
            $data['cid'] = $info['pid'];
            $data['recid'] = $info['ali_id'];
            $this->_mod->add($data);
            $this->success(L('operation_success'));
        } else {
            $this->display();
        }
    }
    public function tuiquanke()
    {
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/Runtime/Data/tuiquanke_collect.php';
            $ret = opcache_invalidate($dir, TRUE);
        }
        $tuiquanke_collect = F('tuiquanke_collect');
        if (!$tuiquanke_collect || $tuiquanke_collect === false) {
            $page = 1;
        } else {
            $page = $tuiquanke_collect['page'];
        }
        $result_data = $this->tuiquanke_collect($page);
        if ($result_data) {
            $this->assign('result_data', $result_data);
            $resp = $this->fetch('collect');
            $this->ajaxReturn($page == 150 ? 0 : 1, $page == 150 ? '采集完成或者没有新数据了' : $page + 1, $resp);
        }
    }
    public function reset()
    {
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/runtime/Data/tuiquanke_pddcollect.php';
            $dir2 = $basedir . '/data/runtime/Data/tuiquanke_collect.php';
            $dir = $basedir . '/data/Runtime/Data/tuiquanke_jdcollect.php';
            $ret = opcache_invalidate($dir, TRUE);
            $ret2 = opcache_invalidate($dir2, TRUE);
            $ret3 = opcache_invalidate($dir3, TRUE);
        }
        F('tuiquanke_pddcollect', null);
        F('tuiquanke_collect', null);
        F('tuiquanke_jdcollect', null);
        $this->ajaxReturn(1, '操作成功！');
    }
    public function tuiquanke_jd()
    {
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/Runtime/Data/tuiquanke_jdcollect.php';
            $ret = opcache_invalidate($dir, TRUE);
        }
        $tuiquanke_collect = F('tuiquanke_jdcollect');
        if (!$tuiquanke_collect || $tuiquanke_collect === false) {
            $page = 1;
        } else {
            $page = $tuiquanke_collect['page'];
        }
        $result_data = $this->tuiquanke_collect_jd($page);
        if ($result_data) {
            $this->assign('result_data', $result_data);
            $resp = $this->fetch('collect');
            $this->ajaxReturn($page == 150 ? 0 : 1, $page == 150 ? '采集完成或者没有新数据了' : $page + 1, $resp);
        }
    }
    private function tuiquanke_collect_jd($page)
    {
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/Runtime/Data/tuiquanke_jdcollect.php';
            $ret = opcache_invalidate($dir, TRUE);
        }
        $tuiquanke_collect = F('tuiquanke_jdcollect');
        if (!$tuiquanke_collect || $page == 1) {
            $coll = 0;
            $totalcoll = 0;
        } else {
            $coll = $tuiquanke_collect['coll'];
            $totalcoll = $tuiquanke_collect['totalcoll'];
        }
        $file = TQK_DATA_PATH . 'start_jd.txt';
        if (!file_exists($file)) {
            return false;
        }
        $startId = file_get_contents($file);
        if (!$startId) {
            $startId = 0;
        }
        $map = array('start' => $startId, 'page_size' => 100, 'time' => NOW_TIME, 'tqk_uid' => $this->tqkuid);
        $token = $this->create_token(trim(C('yh_gongju')), $map);
        $map['token'] = $token;
        $url = $this->tqkapi . '/getjditems';
        $content = $this->_curl($url, $map);
        $json = json_decode($content, true);
        $json = $json['result'];
        $count = count($json);
        if ($count > 0) {
            $t = 0;
            $coll = 0;
            foreach ($json as $key => $val) {
                $raw[] = array('commission_rate' => $val['commission_rate'], 'quan' => $val['quan'], 'couponlink' => $val['couponlink'], 'pict' => $val['pict'], 'itemurl' => $val['itemurl'], 'coupon_price' => $val['coupon_price'], 'price' => $val['price'], 'owner' => $val['owner'], 'comments' => $val['comments'], 'cate_id' => $val['cate_id'], 'itemid' => $val['itemid'], 'title' => $val['title'], 'item_type' => $val['item_type'], 'start_time' => $val['start_time'], 'cid2' => $val['cid2'], 'end_time' => $val['end_time'], 'shop_name' => $val['shop_name'], 'shop_level' => $val['shop_level'], 'add_time' => NOW_TIME + $t);
                $coll++;
                $totalcoll++;
                $t++;
                $startId = $val['id'];
            }
            M('jditems')->addAll($raw, array(), true);
            file_put_contents($file, $startId);
        } else {
            $msg = "失败！";
        }
        $result_data['p'] = $page;
        $result_data['msg'] = $msg;
        $result_data['coll'] = $count;
        $result_data['totalcoll'] = $totalcoll;
        $result_data['totalnum'] = $count;
        $result_data['thiscount'] = count($item);
        $result_data['times'] = time();
        F('tuiquanke_jdcollect', array('coll' => $coll, 'page' => $page == 150 ? 0 : $page + 1, 'totalcoll' => $totalcoll));
        return $result_data;
    }
    public function tuiquanke_pdd()
    {
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/Runtime/Data/tuiquanke_pddcollect.php';
            $ret = opcache_invalidate($dir, TRUE);
        }
        $tuiquanke_collect = F('tuiquanke_pddcollect');
        if (!$tuiquanke_collect || $tuiquanke_collect === false) {
            $page = 1;
        } else {
            $page = $tuiquanke_collect['page'];
        }
        $result_data = $this->tuiquanke_collect_pdd($page);
        if ($result_data) {
            $this->assign('result_data', $result_data);
            $resp = $this->fetch('collect');
            $this->ajaxReturn($page == 150 ? 0 : 1, $page == 150 ? '采集完成或者没有新数据了' : $page + 1, $resp);
        }
    }
    private function tuiquanke_collect_pdd($page)
    {
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/Runtime/Data/tuiquanke_pddcollect.php';
            $ret = opcache_invalidate($dir, TRUE);
        }
        $tuiquanke_collect = F('tuiquanke_pddcollect');
        if (!$tuiquanke_collect || $page == 1) {
            $coll = 0;
            $totalcoll = 0;
        } else {
            $coll = $tuiquanke_collect['coll'];
            $totalcoll = $tuiquanke_collect['totalcoll'];
        }
        $file = TQK_DATA_PATH . 'start_pdd.txt';
        if (!file_exists($file)) {
            return false;
        }
        $startId = file_get_contents($file);
        if (!$startId) {
            $startId = 0;
        }
        $map = array('start' => $startId, 'pagesize' => 500, 'time' => time(), 'tqk_uid' => $this->tqkuid, 'ver' => 2);
        $token = $this->create_token(trim(C('yh_gongju')), $map);
        $map['token'] = $token;
        $url = $this->tqkapi . '/getpdditems';
        $result = $this->_curl($url, $map);
        $json = json_decode($result, true);
        $json = $json['result'];
        $count = count($json);
        if ($count > 0) {
            $t = 0;
            $coll = 0;
            foreach ($json as $key => $val) {
                $raw[] = array('goods_id' => $val['goods_id'], 'goods_name' => $val['goods_name'], 'goods_desc' => $val['goods_desc'], 'goods_thumbnail_url' => $val['goods_thumbnail_url'], 'goods_image_url' => $val['goods_image_url'], 'sold_quantity' => $val['sold_quantity'], 'min_group_price' => $val['min_group_price'], 'min_normal_price' => $val['min_normal_price'], 'mall_name' => $val['mall_name'], 'category_id' => $val['category_id'] ? $val['category_id'] : 0, 'coupon_discount' => $val['coupon_discount'], 'coupon_total_quantity' => $val['coupon_total_quantity'], 'promotion_rate' => $val['promotion_rate'], 'coupon_remain_quantity' => $val['coupon_remain_quantity'], 'coupon_start_time' => $val['coupon_start_time'], 'coupon_end_time' => $val['coupon_end_time'], 'search_id' => $val['search_id'], 'addtime' => $val['addtime']);
                $startId = $val['id'];
                // $res= $this->_ajax_pdd_publish_insert($raw);
                $coll++;
                $totalcoll++;
                $t++;
            }
            M('pdditems')->addAll($raw, array(), true);
            //sus
            file_put_contents($file, $startId);
        } else {
            // $page=149;
            $msg = "失败！";
        }
        $result_data['p'] = $page;
        $result_data['msg'] = $msg;
        $result_data['coll'] = $count;
        $result_data['totalcoll'] = $totalcoll;
        $result_data['totalnum'] = $count;
        $result_data['thiscount'] = count($item);
        $result_data['times'] = time();
        F('tuiquanke_pddcollect', array('coll' => $coll, 'page' => $page == 150 ? 0 : $page + 1, 'totalcoll' => $totalcoll));
        return $result_data;
    }
    private function _ajax_pdd_publish_insert($item)
    {
        $result = D('pdditems')->ajax_yh_publish($item);
        return $result;
    }
    private function tuiquanke_collect($page)
    {
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/Runtime/Data/tuiquanke_collect.php';
            $ret = opcache_invalidate($dir, TRUE);
        }
        $tuiquanke_collect = F('tuiquanke_collect');
        if (!$tuiquanke_collect || $page == 1) {
            $coll = 0;
            $totalcoll = 0;
        } else {
            $coll = $tuiquanke_collect['coll'];
            $totalcoll = $tuiquanke_collect['totalcoll'];
        }
        $file = TQK_DATA_PATH . 'start.txt';
        if (!file_exists($file)) {
            return false;
        }
        $startId = file_get_contents($file);
        if (!$startId) {
            $startId = 0;
        }
        $map = array('start' => $startId, 'pagesize' => 500, 'time' => time(), 'tqk_uid' => $this->tqkuid);
        $token = $this->create_token(trim(C('yh_gongju')), $map);
        $map['token'] = $token;
        $url = $this->tqkapi . '/getitems';
        $result = $this->_curl($url, $map);
        $json = json_decode($result, true);
        $PID = $json['pid'];
        $json = $json['result'];
        $count = count($json);
        if ($count > 0) {
            $t = 0;
            $coll = 0;
            foreach ($json as $key => $val) {
                $pic_url = str_replace("http://", "https://", $val['pic_url']);
                $link = 'https://uland.taobao.com/item/edetail?id=' . $val['num_iid'];
				$item_id = explode('-',$val['num_iid']);
                $raw[] = array(
                    'link' => $link,
                    'click_url' => '',
                    'pic_url' => $pic_url,
                    'title' => $val['title'],
                    'tags' => $val['tags'],
                    'coupon_start_time' => $val['coupon_start_time'],
                    'add_time' => strtotime(date('Y-m-d H:i:s')),
                    'coupon_end_time' => $val['coupon_end_time'],
                    'ali_id' => $val['ali_id'],
                    'cate_id' => $val['cate_id'],
                    'shop_name' => $val['shop_name'],
                    'shop_type' => $val['shop_type'],
                    'ems' => 1,
                    'num_iid' => $val['num_iid'],
					'item_id' => $item_id[1]?$item_id[1]:$val['num_iid'],
                    // 'sellerId'=>$tbdetail['sellerid'],
                    'change_price' => $val['change_price'],
                    'volume' => $val['volume'],
                    'commission' => $val['commission'],
                    'tuisong' => 0,
                    'area' => 0,
                    'pass' => 1,
                    'status' => 'underway',
                    'isshow' => 1,
                    // 'commission_rate'=>'',
                    'commission_rate' => $val['tk_commission_rate'],
                    //佣金比例
                    'tk_commission_rate' => $val['tk_commission_rate'],
                    'sellerId' => $val['sellerId'],
                    'nick' => $val['nick'],
                    'mobilezk' => $val['mobilezk'] ?: 0,
                    'hits' => 0,
                    'price' => $val['price'],
                    'coupon_price' => $val['coupon_price'],
                    'coupon_rate' => $val['coupon_rate'] ? $val['coupon_rate'] : 0,
                    'intro' => $val['intro'],
                    'up_time' => $val['up_time'],
                    'desc' => '',
                    'isq' => '1',
                    'quanurl' => 'https://uland.taobao.com/coupon/edetail?e=&activityId=' . $val['Quan_id'] . '&itemId=' . $val['num_iid'] . '&pid=' . $PID . '',
                    'quan' => $val['quan'],
                    'Quan_id' => $val['Quan_id'],
                    'Quan_condition' => 0,
                    'Quan_surplus' => $val['Quan_surplus'] ? $val['Quan_surplus'] : 0,
                    'Quan_receive' => $val['Quan_receive'] ? $val['Quan_receive'] : 0,
                    'is_commend' => $val['is_commend'] ? $val['is_commend'] : 0,
                );
                $startId = $val['id'];
                // $res= $this->_ajax_yh_publish_insert($raw);
                $coll++;
                $totalcoll++;
                $t++;
            }
            M('items_temp')->addAll($raw, array(), true);
            //fail
            file_put_contents($file, $startId);
        } else {
            //   $page=149;
            $msg = "失败！";
        }
        $result_data['p'] = $page;
        $result_data['msg'] = $msg;
        $result_data['coll'] = $count;
        $result_data['totalcoll'] = $totalcoll;
        $result_data['totalnum'] = $count;
        $result_data['thiscount'] = count($item);
        $result_data['times'] = time();
        F('tuiquanke_collect', array('coll' => $coll, 'page' => $page == 150 ? 0 : $page + 1, 'totalcoll' => $totalcoll));
        return $result_data;
    }
    private function _ajax_yh_publish_insert($item)
    {
        $result = D('items')->ajax_yh_publish($item);
        return $result;
    }
    public function collect()
    {
        $id = I('id', '', 'number_int');
        $auto = I('auto', 0, 'number_int');
        $p = I('p', 1, 'number_int');
        $where = array('id' => $id);
        $date = M('robots')->field('sort', true)->where($where)->find();
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/Runtime/Data/robot_setting.php';
            $ret = opcache_invalidate($dir, TRUE);
        }
        F('robot_setting', $date);
        if ($date) {
            if ($p > $date['page']) {
                if (function_exists('opcache_invalidate')) {
                    $basedir = $_SERVER['DOCUMENT_ROOT'];
                    $dir = $basedir . '/data/Runtime/Data/totalcoll.php';
                    $ret = opcache_invalidate($dir, TRUE);
                }
                F('totalcoll', NULL);
                $this->ajaxReturn(0, '已经采集完成' . $date['page'] . '页！请返回，谢谢');
            }
            $result_data = $this->api_collect($date, $p);
            $this->assign('result_data', $result_data);
            $resp = $this->fetch('collect');
            if ($result_data['coll'] <= 0) {
                $this->ajaxReturn(0, '已经采集完成！请返回，谢谢', $resp);
            }
            $this->ajaxReturn(1, '', $resp);
        } else {
            $this->ajaxReturn(0, 'error');
        }
    }
    public function api_collect($date, $p)
    {
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/Runtime/Data/totalcoll.php';
            $dir2 = $basedir . '/data/Runtime/Data/robots_time.php';
            $ret = opcache_invalidate($dir, TRUE);
            $ret = opcache_invalidate($dir2, TRUE);
        }
        M('robots')->where(array('id' => $date['id']))->save(array('last_page' => $p, 'last_time' => time()));
        if (false === ($totalcoll = F('totalcoll'))) {
            $totalcoll = 0;
        }
        if (false === ($robots_time = F('robots_time'))) {
            $robots_time = time();
            F('robots_time', time());
        }
        $cate = array();
        $appkey = trim(C('yh_taobao_appkey'));
        $appsecret = trim(C('yh_taobao_appsecret'));
        $apppid = trim(C('yh_taobao_pid'));
        $apppid = explode('_', $apppid);
        $AdzoneId = $apppid[3];
        if (!empty($appkey) && !empty($appsecret) && !empty($AdzoneId)) {
            vendor("taobao.taobao");
            $c = new \TopClient();
            $c->appkey = $appkey;
            $c->secretKey = $appsecret;
            $req = new \TbkDgMaterialOptionalUpgradeRequest();
            $req->setPageSize("100");
            $req->setPageNo("" . $p . "");
            if ($date['http_mode'] == 1) {
                $req->setQ($date['keyword']);
            } else {
                $req->setCat($date['tb_cid']);
            }
            $req->setHasCoupon("true");
            $req->setAdzoneId($AdzoneId);
            $resp = $c->execute($req);
            $resp = json_decode(json_encode($resp), true);
            $datares = $resp['result_list']['map_data'];
        }


        if ($datares) {
            $t = 0;
            $coll = 0;
            $now = time();
            foreach ($datares as $key => $v) {


                $title = $v['item_basic_info']['short_title']?$v['item_basic_info']['short_title']:$v['item_basic_info']['title'];
                if ($this->FilterWords($title) || !$v['item_id']) {
                    continue;
                }
                $coupon_price =  $v['price_promotion_info']['final_promotion_price']?$v['price_promotion_info']['final_promotion_price']:$v['price_promotion_info']['zk_final_price'];
                $quan = $v['price_promotion_info']['final_promotion_path_list']['final_promotion_path_map_data']['promotion_fee'];
                $coupon_id = $v['price_promotion_info']['final_promotion_path_list']['final_promotion_path_map_data']['promotion_id'];
                $link = 'https://uland.taobao.com/item/edetail?id=' . $v['item_id'];
                $receive = 100;

                if ($coupon_id && !is_numeric($coupon_id)) {
                    $url = $v['publish_info']['coupon_share_url'] ? $v['publish_info']['coupon_share_url']:$v['publish_info']['click_url'];
                    $pic_url =  $v['item_basic_info']['white_image']?$v['item_basic_info']['white_image']:$v['item_basic_info']['pict_url'];
					$item_id = explode('-',$v['item_id']);
                    $raw[] = array(
                        'link' => $link,
                        'click_url' => '',
                        'pic_url' =>$pic_url,
                        'title' => $title,
                        'coupon_start_time' => NOW_TIME,
                        'add_time' => strtotime(date('Y-m-d H:i:s')),
                        'coupon_end_time' => substr($v['price_promotion_info']['final_promotion_path_list']['final_promotion_path_map_data']['promotion_end_time'],0,-3),
                        'ali_id' => $v['item_basic_info']['category_id'],
                        'cate_id' => $date['cid'] ?: $date['cate_id'],
                        'shop_name' => $v['item_basic_info']['shop_title'],
                        'shop_type' => $v['item_basic_info']['user_type'] == 1 ? 'B' : 'C',
                        'ems' => 1,
                        'num_iid' => $v['item_id'],
                        'item_id' =>$item_id[1]?$item_id[1]:$v['num_iid'],
                        'volume' => $v['item_basic_info']['volume'],
                        'commission' => $v['publish_info']['income_rate']*100,
                        'tuisong' => 0, 'pass' => 1, 'status' => 'underway', 'isshow' => 1,
                        'commission_rate' =>  $v['publish_info']['income_rate']*100,
                        'tk_commission_rate' =>  $v['publish_info']['income_rate']*100,
                        'sellerId' => $v['item_basic_info']['seller_id'],
                        'nick' => '0',
                        'area' => 0,
                        'mobilezk' => 0, 'hits' => 0,
                        'price' => $v['price_promotion_info']['zk_final_price'],
                        'coupon_price' => $coupon_price,
                        'coupon_rate' => intval(($v['price_promotion_info']['zk_final_price'] - $quan) / $v['price_promotion_info']['zk_final_price'] * 100 * 100), 'intro' => '', 'up_time' => $now + $t, 'desc' => '', 'isq' => '1',
                        'quanurl' => 'http:'.$url,
                        'quan' => $quan,
                        'Quan_id' => $coupon_id,
                        'Quan_condition' => 0,
                        'Quan_surplus' => $receive * 10,
                        'Quan_receive' => $receive
                    );

                    $coll++;
                    $totalcoll++;
                }

                $thiscount++;
                $t++;
            }


            M('items')->addAll($raw, array(), true);
            if (function_exists('opcache_invalidate')) {
                $basedir = $_SERVER['DOCUMENT_ROOT'];
                $dir = $basedir . '/data/Runtime/Data/totalcoll.php';
                $ret = opcache_invalidate($dir, TRUE);
            }
            F('totalcoll', $totalcoll);
            if ($sus == 'no') {
                $p = $date['page'] + 1;
            }
        } else {
            $p = 499;
        }
        $result_data['p'] = $p;
        $result_data['msg'] = $msg;
        $result_data['coll'] = $coll;
        $result_data['totalcoll'] = $totalcoll;
        $result_data['totalnum'] = $totalnum;
        $result_data['thiscount'] = $thiscount;
        $result_data['times'] = lefttime(time() - $robots_time);
        return $result_data;
    }
}