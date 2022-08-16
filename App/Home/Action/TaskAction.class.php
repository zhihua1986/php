<?php
namespace Home\Action;

use Common\Model\userModel;
use Common\Model\orderModel;
use Common\Model\itemsModel;
use Common\Model\articleModel;
use Common\Model\recordsModel;
use Common\Api\Weixin;
class TaskAction extends BaseAction
{
    private $accessKey = '';
    public function _initialize()
    {
        parent::_initialize();
        $this->accessKey = trim(C('yh_gongju'));
    }
    public function run()
    {
        set_time_limit(30);
        $this->check_key();
        $now = NOW_TIME;
        $domain = trim(C('yh_site_url'));
        if (C('yh_openduoduo')) {
            $pddstatus = 1;
        }
        if (C('yh_openjd')) {
            $jdstatus = 1;
        }
        if (C('yh_openmt')) {
            $mtstatus = 1;
        }
        if (C('yh_dmappkey') > 0) {
            $dmstatus = 1;
        }
        if (C('yh_tempid_4_time') && C('yh_notice') == 1) {
            $Push = 1;
        }
        if (C('yh_ipban_switch') == 1) {
            $ThirdParty = 1;
        } else {
            $Self = 1;
        }
        $task = array(
            array('value' => 'TimedPush', 'query' => '/index.php?c=task&a=TimedPush&key=' . $this->accessKey, 'interval' => 60, 'status' => $Push ? $Push : 0),
          //  array('value' => 'OrderBind', 'query' => '/index.php?c=task&a=OrderBind&key=' . $this->accessKey, 'interval' => 90, 'status' => 1), 自动分配pid关联用户
            array('value' => 'DelRecords', 'query' => '/index.php?c=task&a=DelRecords&key=' . $this->accessKey, 'interval' => 3500, 'status' => 1),
            array('value' => 'Tbcollect', 'query' => '/index.php?c=task&a=Tbcollect&key=' . $this->accessKey, 'interval' => 200, 'status' => 1),
            array('value' => 'Jdcollect', 'query' => '/index.php?c=task&a=Jdcollect&key=' . $this->accessKey, 'interval' => 240, 'status' => $jdstatus ? $jdstatus : 0),
            array('value' => 'Meituanreceipt', 'query' => '/index.php?c=task&a=Meituanreceipt&key=' . $this->accessKey, 'interval' => 480, 'status' => $mtstatus ? $mtstatus : 0),
            array('value' => 'Duomaireceipt', 'query' => '/index.php?c=task&a=Duomaireceipt&key=' . $this->accessKey, 'interval' => 480, 'status' => $dmstatus ? 1 : 0),
            array('value' => 'DmOrderPay', 'query' => '/index.php?c=task&a=DmOrderPay&key=' . $this->accessKey, 'interval' => 600, 'status' => $dmstatus ? 1 : 0),
            array('value' => 'Tborderpay', 'query' => '/index.php?c=task&a=Tborderpay&key=' . $this->accessKey, 'interval' => 60, 'status' => $Self?$Self:0),
            array('value'=>'UpdateItemId','query'=>'/index.php?c=task&a=UpdateItemId&key='.$this->accessKey,'interval'=>60,'status'=>1),
            array('value' => 'Tborderscene', 'query' => '/index.php?c=task&a=Tborderscene&key=' . $this->accessKey, 'interval' => 180, 'status' => $Self ? $Self : 0),
            //array('value'=>'Tborderfail','query'=>'/index.php?c=task&a=Tborderfail&key='.$this->accessKey,'interval'=>240,'status'=>$Self?$Self:0),
            array('value' => 'DelFailure', 'query' => '/index.php?c=task&a=DelFailure&key=' . $this->accessKey, 'interval' => 1800, 'status' => 1),
            array('value' => 'Userorderbind', 'query' => '/index.php?c=task&a=Userorderbind&key=' . $this->accessKey, 'interval' => 120, 'status' => 1),
            array('value' => 'Cleartbitems', 'query' => '/index.php?c=task&a=Cleartbitems&key=' . $this->accessKey, 'interval' => 3600, 'status' => 1),
            //array('value'=>'Clearpdditems','query'=>'/index.php?c=task&a=Clearpdditems&key='.$this->accessKey,'interval'=>3660,'status'=>$pddstatus?$pddstatus:0),
            array('value' => 'Clearjditems', 'query' => '/index.php?c=task&a=Clearjditems&key=' . $this->accessKey, 'interval' => 3720, 'status' => $jdstatus ? $jdstatus : 0),
            array('value' => 'Userreceipt', 'query' => '/index.php?c=task&a=Userreceipt&key=' . $this->accessKey, 'interval' => 320, 'status' => 1),
            array('value' => 'sitemap', 'query' => '/index.php?c=task&a=sitemap&key=' . $this->accessKey, 'interval' => 7000, 'status' => 1),
            array('value' => 'Pddorderpay', 'query' => '/index.php?c=task&a=Pddorderpay&key=' . $this->accessKey, 'interval' => 200, 'status' => $pddstatus ? $pddstatus : 0),
            array('value' => 'PddReceipt', 'query' => '/index.php?c=task&a=PddReceipt&key=' . $this->accessKey, 'interval' => 900, 'status' => $pddstatus ? $pddstatus : 0),
            array('value' => 'TqkOrderPay', 'query' => '/index.php?c=task&a=TqkOrderPay&key=' . $this->accessKey, 'interval' => 480, 'status' => $ThirdParty ? $ThirdParty : 0),
            //array('value'=>'TqkOrderSettle','query'=>'/index.php?c=task&a=TqkOrderSettle&key='.$this->accessKey,'interval'=>1080,'status'=>$ThirdParty?$ThirdParty:0),
            //array('value'=>'TqkOrderFail','query'=>'/index.php?c=task&a=TqkOrderFail&key='.$this->accessKey,'interval'=>960,'status'=>$ThirdParty?$ThirdParty:0),
            array('value' => 'TqkOrderScene', 'query' => '/index.php?c=task&a=TqkOrderScene&key=' . $this->accessKey, 'interval' => 180, 'status' => $ThirdParty ? $ThirdParty : 0),
            array('value' => 'JdOrderPay', 'query' => '/index.php?c=task&a=JdOrderPay&key=' . $this->accessKey, 'interval' => 300, 'status' => $jdstatus ? $jdstatus : 0),
            array('value' => 'JdReceipt', 'query' => '/index.php?c=task&a=JdReceipt&key=' . $this->accessKey, 'interval' => 960, 'status' => $jdstatus ? $jdstatus : 0),
        );
        $data = [];
        $t = 0;
        $lockName = 'lockvule';
        $lock = S($lockName);
        if (!$lock) {
            S($lockName, 1);
            foreach ($task as $v => $k) {
                if (function_exists('opcache_invalidate')) {
                    $basedir = $_SERVER['DOCUMENT_ROOT'];
                    $dir = $basedir . '/data/Runtime/Data/task/' . $k['value'] . '.php';
                    $ret = opcache_invalidate($dir, TRUE);
                }
                $runtime = F($k['value']);
                if ($now - $runtime > $k['interval'] && $k['status'] == 1) {
                    $url = $domain . $k['query'] . '&t=' . $t;
                    $data[]['url'] = $url;
                    F($k['value'], $now);
                    echo "success:" . $k['value'] . '<br/>';
                }
                $t++;
            }
            $res = $this->getMultiCurlResult($data);
            S($lockName, null);
        }
    }
	
	public function UpdateItemId(){
		$this->timeout();
		$this->check_key();
		$mod = new itemsModel();
		$where['status'] = '1';
		$result=$mod->where($where)->field('id,num_iid,item_id')->order('id desc')->limit(50)->select();
		 if ($result) {
		     $data = array();
		   foreach ($result as $k => $v) {
		   				$info = $this->taobaodetail($v['num_iid']);
		   				if($info['num_iid']){
		   					$num_iid = $info['num_iid'];
		   					$data[$k]['num_iid'] = $num_iid;
		   					 $data[$k]['volume'] = $info['volume']?$info['volume']:0;
		   					 $data[$k]['id'] = $v['id'];
		   					$item_id = explode('-',$num_iid);
		   					$data[$k]['item_id'] = $item_id[1]?$item_id[1]:$num_iid;
		   					 $data[$k]['status'] = 'underway';
						 }else{
						 	$res = $mod->where(array('num_iid'=>$num_iid))->delete();
						 }
		   }
		     $res = $this->db_batch_update('tqk_items', array_values($data), 'id');
		    $this->Log('UpdateItemId', $res);
		 }
		 
		 $json = array('state' => 'yes', 'msg' => $res);
		 exit(json_encode($json, JSON_UNESCAPED_UNICODE));
		
		
	}
	
	protected function db_batch_update($table_name = 'tqk_items', $data = array(), $field = ''){
		
		if (!$table_name || !$data || !$field) {
		    return false;
		} else {
		    $sql = 'UPDATE ' . $table_name;
		}
		$con = array();
		$con_sql = array();
		$fields = array();
		foreach ($data as $key => $value) {
		    $x = 0;
		    foreach ($value as $k => $v) {
		        if ($k != $field && !$con[$x] && $x == 0) {
		            $con[$x] = " set {$k} = (CASE {$field} ";
		        } elseif ($k != $field && !$con[$x] && $x > 0) {
		            $con[$x] = " {$k} = (CASE {$field} ";
		        }
		        if ($k != $field) {
		            $temp = $value[$field];
		            $con_sql[$x] .= " WHEN '{$temp}' THEN '{$v}' ";
		            $x++;
		        }
		    }
		    $temp = $value[$field];
		    if (!in_array($temp, $fields)) {
		        $fields[] = $temp;
		    }
		}
		$num = count($con) - 1;
		foreach ($con as $key => $value) {
		    foreach ($con_sql as $k => $v) {
		        if ($k == $key && $key < $num) {
		            $sql .= $value . $v . ' end),';
		        } elseif ($k == $key && $key == $num) {
		            $sql .= $value . $v . ' end)';
		        }
		    }
		}
		$str = implode(',', $fields);
		$sql .= " where {$field} in({$str})";
		$res = M()->execute($sql);
		return $res;
		
	}
	
    public function TimedPush()
    {
        $this->timeout();
        $this->check_key();
        $tempid = trim(C('yh_tempid_4'));
        $now = NOW_TIME;
        $year = date('Y-m-d', $now);
        $time = strtotime($year . trim(C('yh_tempid_4_time')));
        $push = S('timedpush');
        if (!$push && $now - $time > 0 && $now - $time < 120) {
            $weekend = strtotime(date("Y-m-d H:i:s", strtotime('-30 days')));
            $mod = new userModel();
            $where = array('opid' => array('exp', 'is not null'), 'special_id' => array(array('exp', 'is null'), array('eq', 2), 'or'), 'last_time' => array('egt', $weekend));
            $result = $mod->where($where)->field('opid,id,last_time')->select();
            if ($result) {
                foreach ($result as $k => $v) {
                    $data = array('openid' => $v['opid']);
                    Weixin::TimePush($data);
                }
            }
            S('timedpush', true, 180);
        }
    }
    public function DelRecords()
    {
        $this->timeout();
        $this->check_key();
        $mod = new recordsModel();
        $NowTime = NOW_TIME;
        $where = 'create_time <' . ($NowTime - 86400 * 2);
        $res = $mod->lock(true)->where($where)->delete();
        $this->Log('DelRecords', $res);
        exit;
    }
    public function DmOrderPay()
    {
        $this->timeout();
        $this->check_key();
        $starttime = NOW_TIME - 1200;
        $endtime = NOW_TIME;
        $json = $this->GetDmOrder($starttime, $endtime);
        $this->Log('DmOrderPay', $json);
        if ($json && $json['data']) {
            $raw = array();
            $n = 0;
            foreach ($json['data'] as $key => $query) {
                if ($query['order_sn'] && $query['site_id'] == trim(C('yh_dm_pid'))) {
                    $raw = array(
                        'ads_id' => $query['ads_id'],
                        'goods_id' => $query['details'][0]['goods_id'],
                        'orders_price' => $query['orders_price'],
                        'goods_name' => $query['details'][0]['goods_name'],
                        //'goods_img'=>$query['details'][0]['goods_img'],
                        'order_status' => $query['details'][0]['order_status'],
                        'order_commission' => $query['details'][0]['order_commission'],
                        'order_sn' => $query['details'][0]['order_sn'],
                        'order_time' => strtotime($query['order_time']),
                        'uid' => $query['euid'],
                        'status' => $query['status'],
                        'confirm_price' => $query['confirm_price'],
                        'confirm_siter_commission' => $query['confirm_siter_commission'],
                        'charge_time' => $query['charge_time'] != '0000-00-00 00:00:00' ? strtotime($query['charge_time']) : '',
                        'ads_name' => $query['ads_name'],
                        'leve1' => trim(C('yh_bili1')),
                        'leve2' => trim(C('yh_bili2')),
                        'leve3' => trim(C('yh_bili3')),
                    );
                    if ($this->_ajax_duomai_order($raw)) {
                        $n++;
                    }
                }
            }
            $json = array('state' => 'yes', 'msg' => $n);
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
    }
    public function dmorder()
    {
        $hash = trim(C('yh_dmsecret'));
        $query = $_REQUEST;
        $checksum = $query['checksum'];
        $id = $query['id'];
        unset($query['checksum'], $query['id']);
        ksort($query);
        $localsum = md5(join('', array_values($query)) . $hash);
        if ($localsum == $checksum && $query['order_sn']) {
            $raw = array(
                'ads_id' => $query['ads_id'],
                // 'goods_id'=>$query['goods_id'],
                'orders_price' => $query['orders_price'],
                'goods_name' => $query['goods_name'],
                // 'goods_img'=>$query['goods_img'],
                'order_status' => $query['order_status'],
                'order_commission' => $query['siter_commission'],
                'order_sn' => $query['order_sn'],
                'order_time' => strtotime($query['order_time']),
                'uid' => $query['euid'],
                'status' => $query['status'],
                'confirm_price' => $query['confirm_price'],
                'confirm_siter_commission' => $query['confirm_siter_commission'],
                'charge_time' => $query['charge_time'] != '0000-00-00 00:00:00' ? strtotime($query['charge_time']) : '',
                'ads_name' => $query['ads_name'],
                'leve1' => trim(C('yh_bili1')),
                'leve2' => trim(C('yh_bili2')),
                'leve3' => trim(C('yh_bili3')),
            );
            if ($this->_ajax_duomai_order($raw) == 2) {
                $json = '1';
            } else {
                $json = '0';
            }
        } else {
            $json = '-1';
        }
        exit($json);
    }
    protected function MtSign($params)
    {
        unset($params["sign"]);
        $secret = trim(C('yh_mtsign'));
        ksort($params);
        $str = $secret;
        foreach ($params as $key => $value) {
            $str .= $key . $value;
        }
        $str .= $secret;
        $sign = md5($str);
        return $sign;
    }
    public function mtorder()
    {
        //$this->check_key();
        if (empty($_POST) && false !== strpos($_SERVER['CONTENT_TYPE'], 'application/json')) {
            $content = file_get_contents('php://input');
            $val = (array) json_decode($content, true);
        } else {
            $val = $_POST;
        }
        $sign = $this->MtSign($val);
        if ($val && $sign == $val['sign']) {
            if ($val['orderid']) {
                $raw = array('orderid' => $val['orderid'], 'smstitle' => $val['smstitle'], 'paytime' => $val['paytime'], 'payprice' => $val['direct'], 'profit' => $val['direct'] * $val['ratio'], 'sid' => $val['sid'], 'status' => $val['status'], 'leve1' => trim(C('yh_bili1')), 'leve2' => trim(C('yh_bili2')), 'leve3' => trim(C('yh_bili3')));
                if ($this->_ajax_meituan_order($raw)) {
                    $json = array('errcode' => '0', 'errmsg' => 'ok');
                } else {
                    $json = array('errcode' => '1', 'errmsg' => 'err');
                }
            }
        } else {
            $json = array('errcode' => '1', 'errmsg' => 'err');
        }
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    public function sitemap()
    {
        $this->timeout();
        $this->check_key();
        $mod = new articleModel();
        $list = $mod->field('id,url')->where('status=1')->order('id desc')->limit(5000)->select();
        $data = '';
        $data = $data . C('yh_site_url') . "\r\n";
        if (C('APP_SUB_DOMAIN_DEPLOY') && C('URL_MODEL') == 2) {
            $data = $data . C('yh_site_url') . "/jiu\r\n";
            $data = $data . C('yh_site_url') . "/top100\r\n";
            $data = $data . C('yh_site_url') . "/brand\r\n";
            $data = $data . C('yh_site_url') . "/cate\r\n";
            $data = $data . C('yh_site_url') . "/jd\r\n";
            $data = $data . C('yh_site_url') . "/pdd\r\n";
            $data = $data . C('yh_site_url') . "/article\r\n";
        } else {
            $data = $data . C('yh_site_url') . "/index.php/jiu\r\n";
            $data = $data . C('yh_site_url') . "/index.php/top100\r\n";
            $data = $data . C('yh_site_url') . "/index.php/brand\r\n";
            $data = $data . C('yh_site_url') . "/index.php/cate\r\n";
            $data = $data . C('yh_site_url') . "/index.php/jd\r\n";
            $data = $data . C('yh_site_url') . "/index.php/pdd\r\n";
            $data = $data . C('yh_site_url') . "/index.php/article\r\n";
        }
        foreach ($list as $key => $val) {
            if (C('APP_SUB_DOMAIN_DEPLOY') && C('URL_MODEL') == 2) {
                $data = $data . C('yh_site_url') . $val['url'] . "\r\n";
            } else {
                $data = $data . C('yh_site_url') . U('/article/read', array('id' => $val['id'])) . "\r\n";
            }
        }
        $WenJian = $_SERVER["DOCUMENT_ROOT"] . '/sitemap.txt';
        file_put_contents($WenJian, $data);
    }
    public function JdOrderPay()
    {
		$this->timeout();
       $this->check_key();
        $starttime = NOW_TIME - 1200;
        $endtime = NOW_TIME;
        $result = $this->jdorderquery($starttime, $endtime);
        $this->Log('JdOrderPay', $result);
        $info = json_decode($result, true);
        $json = $info['data'];
        if ($json && $json['id']) {
            $raw = array();
            $n = 0;
            $payMonth = strtotime($json['payMonth']);
            if ($json['validCode'] == '17') {
                $ComputingTime = abs(C('yh_ComputingTime')) * 86400;
                $payMonth = NOW_TIME + $ComputingTime;
            }
            $raw['addTime'] = NOW_TIME;
            $raw['oid'] = $json['id'];
            $raw['orderId'] = $json['parentId']?$json['parentId']:$json['orderId'];
            $raw['orderTime'] = strtotime($json['orderTime']);
            $raw['finishTime'] = strtotime($json['finishTime']);
            $raw['modifyTime'] = strtotime($json['modifyTime']);
            $raw['skuId'] = $json['skuId'];
            $raw['skuName'] = $json['skuName'];
            $raw['estimateCosPrice'] = $json['actualCosPrice'] > 0 ? $json['actualCosPrice'] : $json['estimateCosPrice'];
            //预估计佣金额
            $raw['estimateFee'] = $json['actualFee'] > 0 ? $json['actualFee'] : $json['estimateFee'];
            //推客的预估佣金
            $raw['actualCosPrice'] = $json['actualCosPrice'];
            //实际计算佣金的金额
            $raw['actualFee'] = $json['actualFee'];
            //分得的实际佣金
            $raw['positionId'] = $json['positionId'];
            $raw['validCode'] = $json['validCode'];
            $raw['payMonth'] = $payMonth;
            $raw['leve1'] = trim(C('yh_bili1'));
            $raw['leve2'] = trim(C('yh_bili2'));
            $raw['leve3'] = trim(C('yh_bili3'));
            if ($this->_ajax_jd_order_insert($raw)) {
                $n++;
            }
        } elseif ($json) {
            $raw = array();
            $n = 0;
            foreach ($json as $key => $val) {
                if ($val['id']) {
                    $payMonth = strtotime($val['payMonth']);
                    if ($val['validCode'] == '17') {
                        $ComputingTime = abs(C('yh_ComputingTime')) * 86400;
                        $payMonth = NOW_TIME + $ComputingTime;
                    }
                    $raw = array('addTime' => NOW_TIME, 'oid' => $val['id'], 'orderId' => $val['orderId'], 'orderTime' => strtotime($val['orderTime']), 'finishTime' => strtotime($val['finishTime']), 'modifyTime' => strtotime($val['modifyTime']), 'skuId' => $val['skuId'], 'skuName' => $val['skuName'], 'estimateCosPrice' => $val['estimateCosPrice'], 'estimateFee' => $val['estimateFee'], 'actualCosPrice' => $val['actualCosPrice'], 'actualFee' => $val['actualFee'], 'positionId' => $val['positionId'], 'validCode' => $val['validCode'], 'payMonth' => $payMonth, 'leve1' => trim(C('yh_bili1')), 'leve2' => trim(C('yh_bili2')), 'leve3' => trim(C('yh_bili3')));
                    if ($this->_ajax_jd_order_insert($raw)) {
                        $n++;
                    }
                }
            }
        }
        $json = array('state' => 'yes', 'msg' => $n);
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    public function Pddorderpay()
    {
        $this->timeout();
        $this->check_key();
        $starttime = time() - 1200;
        $map = array('start' => $starttime, 'pagesize' => 100, 'page' => 1, 'time' => NOW_TIME, 'endtime' => NOW_TIME, 'tqk_uid' => $this->tqkuid);
        $token = $this->create_token(trim(C('yh_gongju')), $map);
        $map['token'] = $token;
        $url = $this->tqkapi . '/pddgetorder';
        $content = $this->_curl($url, $map);
        $json = json_decode($content, true);
        $this->Log('Pddorderpay', $json);
        $json = $json['result']['order_list_get_response']['order_list'];
        $count = count($json);
        if ($count > 0) {
            $n = 0;
            foreach ($json as $key => $val) {
                if ($val['order_status'] >= 0 && $val['p_id'] == trim(C('yh_youhun_secret'))) {
                    $raw = array('goods_id' => $val['goods_id'], 'order_sn' => $val['order_sn'], 'order_status' => $val['order_status_desc'], 'order_amount' => $val['order_amount'] / 100, 'promotion_amount' => $val['promotion_amount'] / 100, 'p_id' => $val['p_id'], 'order_pay_time' => $val['order_pay_time'], 'order_settle_time' => $val['order_settle_time'], 'order_verify_time' => $val['order_verify_time'], 'goods_name' => $val['goods_name'], 'status' => $val['order_status'], 'uid' => $val['custom_parameters'], 'leve1' => trim(C('yh_bili1')), 'leve2' => trim(C('yh_bili2')), 'leve3' => trim(C('yh_bili3')));
                    if ($this->_ajax_pdd_order_insert($raw)) {
                        $n++;
                    }
                }
            }
        }
        $json = array('state' => 'yes', 'msg' => $n);
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    public function TqkOrderPay()
    {
        $this->timeout();
        $this->check_key();
        $time = NOW_TIME;
        $starttime = $time - 1200;
        $start_time = date('Y-m-d H:i:s', $starttime);
        $end_time = date('Y-m-d H:i:s', $time);
        $t = I('t');
        $condition = array(
            'query_type' => 4,
            //'tk_status' =>12,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'order_scene' => 1,
            'time' => NOW_TIME . $t,
            'tqk_uid' => $this->tqkuid,
        );
        $token = $this->create_token(trim(C('yh_gongju')), $condition);
        $condition['token'] = $token;
        $url = $this->tqkapi . '/getorder';
        $content = $this->_curl($url, $condition);
        $itemId = json_decode($content, true);
        $this->Log('TqkOrderPay', $content);
        $datares = $itemId['result']['data']['results']['publisher_order_dto'];
        $json = $this->UpdatePayOrder($datares);
        //付款订单
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    public function TqkOrderFail()
    {
        $this->timeout();
        $this->check_key();
        $file = TQK_DATA_PATH . 'invalid.txt';
        if (!file_exists($file)) {
            return false;
        }
        $startId = file_get_contents($file);
        $where = array('status' => 1, 'add_time' => array('gt', $startId));
        $mod = new orderModel();
        $result = $mod->field('add_time')->where($where)->order('id asc')->find();
        if ($result) {
            $starttime = $result['add_time'];
            $endtime = $result['add_time'] + 1100;
            $start_time = date('Y-m-d H:i:s', $starttime);
            $end_time = date('Y-m-d H:i:s', $endtime);
            $t = I('t');
            $condition = array('query_type' => 2, 'tk_status' => 13, 'start_time' => $start_time, 'end_time' => $end_time, 'order_scene' => 1, 'time' => NOW_TIME . $t, 'tqk_uid' => $this->tqkuid);
            $token = $this->create_token(trim(C('yh_gongju')), $condition);
            $condition['token'] = $token;
            $url = $this->tqkapi . '/getorder';
            $content = $this->_curl($url, $condition);
            $itemId = json_decode($content, true);
            $this->Log('TqkOrderFail', $content);
            $datares = $itemId['result']['data']['results']['publisher_order_dto'];
            $json = $this->UpdateFailOrder($datares);
            file_put_contents($file, $result['add_time']);
        }
        if (!$result['add_time']) {
            file_put_contents($file, 0);
        }
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    public function TqkOrderSettle()
    {
        $this->timeout();
        $this->check_key();
        $time = NOW_TIME;
        $starttime = $time - 1200;
        $start_time = date('Y-m-d H:i:s', $starttime);
        $end_time = date('Y-m-d H:i:s', $time);
        $t = I('t');
        $condition = array('query_type' => 3, 'tk_status' => 3, 'start_time' => $start_time, 'end_time' => $end_time, 'order_scene' => 1, 'time' => NOW_TIME . $t, 'tqk_uid' => $this->tqkuid);
        $token = $this->create_token(trim(C('yh_gongju')), $condition);
        $condition['token'] = $token;
        $url = $this->tqkapi . '/gettborder';
        $content = $this->_curl($url, $condition);
        $itemId = json_decode($content, true);
        $this->Log('TqkOrderSettle', $content);
        $datares = $itemId['result']['data']['results']['publisher_order_dto'];
        $json = $this->UpdateSettleOrder($datares);
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    public function JdReceipt()
    {
        $this->timeout();
        $this->check_key();
        $usermod = M('user');
        $ordermod = M('jdorder');
        $cashmod = M('usercash');
        $ComputingTime = abs(C('yh_ComputingTime')) * 86400;
        $NowTime = NOW_TIME;
        $where['_string'] = '(finishTime < ' . ($NowTime - $ComputingTime) . ' and settle = 0 and validCode = 17) and (uid>0 or fuid is not null or guid is not null)';
        $field = 'id,oid,skuName,orderId,orderTime,finishTime,modifyTime,estimateCosPrice,estimateFee,actualCosPrice,actualFee,positionId,uid,fuid,guid,leve1,leve2,leve3,settle,validCode,payMonth';
        $list = $ordermod->field($field)->where($where)->order('id desc')->limit(100)->select();
        $this->Log('jdReceipt', $list);
        if ($list) {
            $count = 0;
            foreach ($list as $k => $v) {
                $this->SettleJd($v, $usermod, $ordermod, $cashmod);
                $count++;
            }
        }
        exit;
    }
    public function PddReceipt()
    {
        $this->timeout();
        $this->check_key();
        $usermod = M('user');
        $ordermod = M('pddorder');
        $cashmod = M('usercash');
        $ComputingTime = abs(C('yh_ComputingTime')) * 86400;
        $NowTime = NOW_TIME;
        $where['_string'] = '((order_settle_time < ' . ($NowTime - $ComputingTime) . ' and (status = 5 or status = 3) and settle = 0) or (order_verify_time < ' . ($NowTime - $ComputingTime) . ' and (status = 5 or status = 3) and settle = 0)) and (uid>0 or fuid is not null or guid is not null)';
        $field = 'id,goods_name,order_sn,order_amount,promotion_amount,uid,leve1,leve2,leve3,goods_id,fuid,guid,status,settle';
        $list = $ordermod->field($field)->where($where)->order('id desc')->limit(100)->select();
        $this->Log('PddReceipt', $list);
        if ($list) {
            $count = 0;
            foreach ($list as $k => $v) {
                $this->SettlePdd($v, $usermod, $ordermod, $cashmod);
                $count++;
            }
        }
        exit;
    }
    public function Duomaireceipt()
    {
        $this->timeout();
        $this->check_key();
        $ComputingTime = abs(C('yh_ComputingTime')) * 86400;
        $now = NOW_TIME;
        $usermod = M('user');
        $ordermod = M('dmorder');
        $cashmod = M('usercash');
        $where['_string'] = '(' . ($now - $ComputingTime) . '>charge_time and status = 1 and settle = 0) and (uid>0 or fuid is not null or guid is not null)';
        $field = 'id,goods_name,order_sn,order_time,orders_price,order_commission,status,uid,fuid,guid,leve1,leve2,leve3,settle,charge_time';
        $list = $ordermod->field($field)->where($where)->order('id desc')->limit(100)->select();
        $this->Log('Duomaireceipt', $list);
        if ($list) {
            $count = 0;
            foreach ($list as $k => $v) {
                $this->SettleDuomai($v, $usermod, $ordermod, $cashmod);
                $count++;
            }
        }
        exit;
    }
    public function Meituanreceipt()
    {
        $this->timeout();
        $this->check_key();
        $ComputingTime = abs(C('yh_wm_settle')) * 86400;
        $now = NOW_TIME;
        $usermod = M('user');
        $ordermod = M('mtorder');
        $cashmod = M('usercash');
        $where['_string'] = '(' . ($now - $ComputingTime) . '>settle_time and status = 8 and settle = 0) and (uid>0 or fuid is not null or guid is not null)';
        $field = 'id,smstitle,orderid,paytime,payprice,profit,sid,status,uid,fuid,guid,leve1,leve2,leve3,settle,settle_time';
        $list = $ordermod->field($field)->where($where)->order('id desc')->limit(100)->select();
        $this->Log('Meituanreceipt', $list);
        if ($list) {
            $count = 0;
            foreach ($list as $k => $v) {
                $this->SettleMeituan($v, $usermod, $ordermod, $cashmod);
                $count++;
            }
        }
        exit;
    }
    public function Userreceipt()
    {
        $this->timeout();
        $this->check_key();
        $usermod = M('user');
        $ordermod = M('order');
        $cashmod = M('usercash');
        $line = S('Userreceipt');
        if ($line == 1) {
            $ComputingTime = abs(C('yh_ComputingTime')) * 86400;
            $now = NOW_TIME;
            $where['_string'] = '(' . ($now - $ComputingTime) . '>up_time and status = 3 and settle = 0) and (uid>0 or fuid is not null or guid is not null) and goods_iid is not null';
            S('Userreceipt', 2);
        } else {
            $ComputingTime = abs(C('yh_wm_settle')) * 86400;
            $now = NOW_TIME;
            $where['_string'] = '(' . ($now - $ComputingTime) . '>up_time and status = 3 and settle = 0) and (uid>0 or fuid is not null or guid is not null) and goods_iid is null';
            S('Userreceipt', 1);
        }
        $field = 'id,goods_title,uid,fuid,guid,income,leve1,leve2,leve3,up_time,orderid,relation_id,price';
        $list = $ordermod->field($field)->where($where)->order('id desc')->limit(100)->select();
        $this->Log('Userreceipt', $list);
        if ($list) {
            $count = 0;
            foreach ($list as $k => $v) {
                $this->SettleUser($v, $usermod, $ordermod, $cashmod);
                $count++;
            }
        }
        exit;
    }
    public function Clearjditems()
    {
        $this->timeout();
        $this->check_key();
        $NowTime = NOW_TIME;
        $where = 'end_time <' . $NowTime;
        $res = M('jditems')->where($where)->delete();
        $this->Log('Clearjditems', $res);
        exit;
    }
    public function Clearpdditems()
    {
        $this->timeout();
        $this->check_key();
        $NowTime = NOW_TIME;
        $where = 'coupon_end_time <' . $NowTime;
        $res = M('pdditems')->where($where)->delete();
        $this->Log('Clearpdditems', $res);
        exit;
    }
    public function Cleartbitems()
    {
        $this->timeout();
        $this->check_key();
        $mod = new itemsModel();
        $NowTime = NOW_TIME;
        $where = 'coupon_end_time <' . $NowTime;
        $res = $mod->lock(true)->where($where)->delete();
        $this->Log('Cleartbitems', $res);
        exit;
    }
    public function Hotcollect()
    {
        $this->timeout();
        $this->check_key();
        vendor("taobao.taobao");
        $appkey = trim(C('yh_taobao_appkey'));
        $appsecret = trim(C('yh_taobao_appsecret'));
        $apppid = trim(C('yh_taobao_pid'));
        $apppid = explode('_', $apppid);
        $AdzoneId = $apppid[3];
        $c = new \TopClient();
        $c->appkey = $appkey;
        $c->secretKey = $appsecret;
        $c->format = 'json';
        $req = new \TbkDgMaterialOptionalRequest();
        $req->setAdzoneId($AdzoneId);
        $req->setPlatform("2");
        $req->setPageSize("100");
        $req->setMaterialId("28026");
        $req->setSort("total_sales_des");
        $req->setNeedPrepay("true");
        $req->setIncludeGoodRate("true");
        $req->setHasCoupon("true");
        $req->setQ('true');
        $req->setNpxLevel("2");
        $req->setPageNo("1");
        $resp = $c->execute($req);
        $this->Log('Hotcollect', $resp);
        $resp = json_decode(json_encode($resp), true);
        $resp = array_reverse($resp['result_list']['map_data']);
        $patterns = "/\\d+/";
        $t = 0;
        $now = time();
        $raw = array();
        foreach ($resp as $k => $val) {
            if ($val['coupon_id'] && $val['coupon_amount'] < 400) {
                $end_time = strtotime($val['coupon_end_time']);
                if (($end_time - $now) / 86400 > 1) {
                    $end_time = $now + 86400 * 1;
                }
                $pic_url = str_replace('http://', 'https://', $val['pict_url']);
                $pic_url = str_replace('_400x400', '', $pic_url);
                if (strpos($pic_url, 'https://') === false) {
                    $pic_url = str_replace('//', 'https://', $pic_url);
                }
                if (strpos($val['coupon_share_url'], 'https://') === false) {
                    $val['coupon_share_url'] = str_replace('//', 'https://', $val['coupon_share_url']);
                }
				$item_id = explode('-',$val['item_id']);
                $raw[] = array(
                    'link' => 'https://item.taobao.com/item.htm?id=' . $val['item_id'],
                    'click_url' => '',
                    'pic_url' => $pic_url,
                    'title' => $val['title'],
                    'coupon_start_time' => NOW_TIME,
                    'add_time' => strtotime(date('Y-m-d H:i:s')),
                    'coupon_end_time' => $end_time,
                    'ali_id' => 28026,
                    'cate_id' => 28026,
                    'shop_name' => '',
                    'shop_type' => $val['user_type'] == 1 ? 'B' : 'C',
                    'ems' => 1,
                    'num_iid' => $val['item_id'],
					'item_id' => $item_id[1]?$item_id[1]:$val['item_id'],
                    'volume' => $val['volume'],
                    'commission' => '0',
                    'tuisong' => 1,
                    'area' => 0,
                    'pass' => 1,
                    'status' => 'underway',
                    'isshow' => 1,
                    'commission_rate' => $val['commission_rate'],
                    //佣金比例
                    'tk_commission_rate' => $val['commission_rate'],
                    'sellerId' => $val['seller_id'],
                    'nick' => '',
                    'mobilezk' => 0,
                    'hits' => 0,
                    'price' => $val['zk_final_price'],
                    'coupon_price' => $val['zk_final_price'] - $val['coupon_amount'],
                    'coupon_rate' => 0,
                    'intro' => $val['item_description'],
                    'up_time' => $now + $t,
                    'desc' => '',
                    'isq' => '1',
                    'quanurl' => $val['coupon_share_url'],
                    'quan' => $val['coupon_amount'],
                    'Quan_id' => $val['coupon_id'],
                    'Quan_condition' => 0,
                    'Quan_surplus' => $receive * 10,
                    'Quan_receive' => $receive,
                );
                $t++;
            }
        }
        M('items')->lock(true)->addAll($raw, array(), true);
        exit;
    }
    public function DelFailure()
    {
        $this->timeout();
        $this->check_key();
        $t = I('t');
        $map = array('time' => NOW_TIME . $t, 'tqk_uid' => $this->tqkuid);
        $token = $this->create_token(trim(C('yh_gongju')), $map);
        $map['token'] = $token;
        $url = $this->tqkapi . '/disabled';
        $content = $this->_curl($url, $map);
        $itemId = json_decode($content, true);
        $this->Log('DelFailure', $content);
        $itemId = $itemId['data'];
        if (!$itemId) {
            $json = array('data' => array(), 'result' => array(), 'state' => 'no', 'msg' => '商品ID不能为空');
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        $model = M('items');
		$splitArray = array_chunk( $itemId, 50 );
		foreach($splitArray as $k=>$v){
			$itemId = implode(",", $v);
			$where = array('item_id' => array('in', $itemId));
			$model->lock(true)->where($where)->delete();
		}
        $json = array('data' => array(), 'result' => 'ok', 'state' => 'yes', 'msg' => '清理成功');
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
	
    public function OrderBind()
    {
        $this->timeout();
        $this->check_key();
        $mod = new orderModel();
        $nowtime = NOW_TIME - 86400 * 2;
        $line = S('OrderBind');
        if ($line == 1) {
            $sql = 'select a.id,a.item_id,a.orderid,a.income,a.price,a.goods_title,a.ad_id,b.id as bid,b.itemid,c.webmaster_rate as rate,c.fuid,c.guid,c.id as uid from
	tqk_order as a LEFT JOIN tqk_records as b ON a.ad_id = b.ad_id AND a.item_id = b.itemid LEFT JOIN tqk_user as c ON c.id = b.uid where a.uid = 0 AND a.add_time>' . $nowtime;
            S('OrderBind', 2);
        } else {
            $sql = 'select a.id,a.item_id,a.orderid,a.income,a.price,a.goods_title,a.ad_id,b.id as bid,b.itemid,c.webmaster_rate as rate,c.fuid,c.guid,c.id as uid from
	tqk_order as a LEFT JOIN tqk_records as b ON a.ad_id = b.ad_id AND b.uid = b.itemid AND a.click_time - b.create_time < 3600 LEFT JOIN tqk_user as c ON c.id = b.uid where a.uid = 0 AND a.add_time>' . $nowtime;
            S('OrderBind', 1);
        }
        $Model = M();
        $list_child = $Model->query($sql);
        $this->Log('OrderBind', $list_child);
        foreach ($list_child as $k => $v) {
            if ($v['uid'] && $v['bid']) {
                $data = array('uid' => $v['uid'], 'fuid' => $v['fuid'], 'guid' => $v['guid'], 'leve1' => $v['rate'] ? $v['rate'] : trim(C('yh_bili1')), 'leve2' => trim(C('yh_bili2')), 'leve3' => trim(C('yh_bili3')));
                $map = array('id' => $v['id']);
                $mo = $mod->where($map)->save($data);
                if ($v['uid'] > 0) {
                    $wdata = array('url' => 'c=user&a=myorder', 'uid' => $v['uid'], 'keyword1' => $v['orderid'], 'keyword2' => $v['goods_title'], 'keyword3' => $v['price'], 'keyword4' => $v['income'] * ($data['leve1'] / 100));
                    Weixin::orderTaking($wdata);
                }
                // if($line == 1){
                // $this->Decords($v['bid']);
                // }
            }
        }
        exit;
    }
    public function Userorderbind()
    {
        $this->timeout();
        $this->check_key();
        $pid = trim(C('yh_taolijin_pid'));
        $apppid = explode('_', $pid);
        $AdzoneId = $apppid[3];
        $mod = new orderModel();
        $stime = strtotime("-30 day");
        if ($AdzoneId) {
           // $sql = 'select a.oid,a.webmaster_rate as rate,a.fuid,a.guid,a.id as uid,b.id,b.orderid,b.goods_title,b.price,b.income from tqk_user as a LEFT JOIN tqk_order as b ON a.oid=b.oid OR (a.webmaster_pid=b.relation_id AND b.relation_id>0) where b.uid=0 and b.ad_id<>' . $AdzoneId . ' and b.settle=0 and b.add_time>' . $stime;
			$sql = 'select a.oid,a.webmaster_rate as rate,a.fuid,a.guid,a.id as uid,b.id,b.orderid,b.goods_title,b.price,b.income from tqk_user as a LEFT JOIN tqk_order as b ON a.oid=b.oid OR (a.special_id=b.special_id AND b.special_id>2) where b.uid=0 and b.ad_id<>' . $AdzoneId . ' and b.settle=0 and b.add_time>' . $stime;
		} else {
            $sql = 'select a.oid,a.webmaster_rate as rate,a.fuid,a.guid,a.id as uid,b.id,b.id,b.orderid,b.goods_title,b.price,b.income from tqk_user as a LEFT JOIN tqk_order as b ON a.oid=b.oid OR (a.special_id=b.special_id AND b.special_id>2) where b.uid=0 and b.settle=0 and b.add_time>' . $stime;
        }
        $Model = M();
        $list_child = $Model->cache(true, 5 * 60)->query($sql);
        $this->Log('Userorderbind', $list_child);
        foreach ($list_child as $k => $v) {
            if ($v['uid']) {
                $data = array('uid' => $v['uid'], 'fuid' => $v['fuid'], 'guid' => $v['guid'], 'leve1' => $v['rate'] ? $v['rate'] : trim(C('yh_bili1')), 'leve2' => trim(C('yh_bili2')), 'leve3' => trim(C('yh_bili3')));
                $map = array('id' => $v['id']);
                $mo = $mod->where($map)->save($data);
                if ($v['uid'] > 0) {
                    $wdata = array('url' => 'c=user&a=myorder', 'uid' => $v['uid'], 'keyword1' => $v['orderid'], 'keyword2' => $v['goods_title'], 'keyword3' => $v['price'], 'keyword4' => $v['income'] * ($data['leve1'] / 100));
                    Weixin::orderTaking($wdata);
                }
            }
        }
        exit;
    }
    public function Tborderfail()
    {
        $this->timeout();
        $this->check_key();
        $file = TQK_DATA_PATH . 'invalid.txt';
        if (!file_exists($file)) {
            return false;
        }
        $startId = file_get_contents($file);
        $where = array('status' => 1, 'add_time' => array('gt', $startId));
        $mod = new orderModel();
        $result = $mod->field('add_time')->where($where)->order('id asc')->find();
        if ($result) {
            $starttime = $result['add_time'];
            $endtime = $result['add_time'] + 1100;
            vendor("taobao.taobao");
            $c = new \TopClient();
            $appkey = trim(C('yh_taobao_appkey'));
            $appsecret = trim(C('yh_taobao_appsecret'));
            $c->appkey = $appkey;
            $c->secretKey = $appsecret;
            $req = new \TbkOrderDetailsGetRequest();
            $req->setQueryType("2");
            $req->setPageSize("100");
            $req->setTkStatus("13");
            $req->setStartTime(date('Y-m-d H:i:s', $starttime));
            $req->setEndTime(date('Y-m-d H:i:s', $endtime));
            $req->setOrderScene("1");
            $resp = $c->execute($req);
            $resp = json_decode(json_encode($resp), true);
            $this->Log('Tborderfail', $resp);
            $datares = $resp['data']['results']['publisher_order_dto'];
            $json = $this->UpdateFailOrder($datares);
            file_put_contents($file, $result['add_time']);
        }
        if (!$result['add_time']) {
            file_put_contents($file, 0);
        }
        $json = array('state' => 'yes', 'msg' => $count);
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    public function Jdcollect()
    {
        $this->timeout();
        $this->check_key();
        $file = TQK_DATA_PATH . 'start_jd.txt';
        if (!file_exists($file)) {
            return false;
        }
        $startId = file_get_contents($file);
        if (!$startId) {
            $startId = 1;
        }
        $t = I('t');
        $map = array('start' => $startId, 'page_size' => 100, 'time' => NOW_TIME . $t, 'tqk_uid' => $this->tqkuid);
        $token = $this->create_token(trim(C('yh_gongju')), $map);
        $map['token'] = $token;
        $url = $this->tqkapi . '/getjditems';
        $content = $this->_curl($url, $map);
        $this->Log('Jdcollect', $content);
        $json = json_decode($content, true);
        $json = $json['result'];
        $count = count($json);
        if ($count > 0) {
            foreach ($json as $key => $val) {
                $startId = $val['id'];
                if ($this->FilterWords($val['title'])) {
                    continue;
                }
                $raw[] = array('commission_rate' => $val['commission_rate'], 'quan' => $val['quan'], 'couponlink' => $val['couponlink'], 'pict' => $val['pict'], 'itemurl' => $val['itemurl'], 'coupon_price' => $val['coupon_price'], 'price' => $val['price'], 'owner' => $val['owner'], 'comments' => $val['comments'], 'cate_id' => $val['cate_id'], 'itemid' => $val['itemid'], 'title' => $val['title'], 'item_type' => $val['item_type'], 'start_time' => $val['start_time'], 'cid2' => $val['cid2'], 'end_time' => $val['end_time'], 'shop_name' => $val['shop_name'], 'shop_level' => $val['shop_level'], 'add_time' => NOW_TIME + $t);
            }
            M('jditems')->lock(true)->addAll($raw, array(), true);
            file_put_contents($file, $startId);
            exit;
        }
    }
    public function Pddcollect()
    {
        $this->timeout();
        $this->check_key();
        $file = TQK_DATA_PATH . 'start_pdd.txt';
        if (!file_exists($file)) {
            return false;
        }
        $startId = file_get_contents($file);
        if (!$startId) {
            $startId = 1;
        }
        $t = I('t');
        $map = array('start' => $startId, 'page_size' => 100, 'time' => NOW_TIME . $t, 'tqk_uid' => $this->tqkuid, 'ver' => 2);
        $token = $this->create_token(trim(C('yh_gongju')), $map);
        $map['token'] = $token;
        $url = $this->tqkapi . '/getpdditems';
        $content = $this->_curl($url, $map);
        $this->Log('Pddcollect', $content);
        $json = json_decode($content, true);
        $json = $json['result'];
        // $json=$json['goods_search_response']['goods_list'];
        $count = count($json);
        if ($count > 0) {
            foreach ($json as $key => $val) {
                if ($this->FilterWords($val['goods_name'])) {
                    continue;
                }
                $raw[] = array('goods_id' => $val['goods_id'], 'goods_name' => $val['goods_name'], 'goods_desc' => $val['goods_desc'], 'goods_thumbnail_url' => $val['goods_thumbnail_url'], 'goods_image_url' => $val['goods_image_url'], 'sold_quantity' => $val['sold_quantity'], 'min_group_price' => $val['min_group_price'], 'min_normal_price' => $val['min_normal_price'], 'mall_name' => $val['mall_name'], 'category_id' => $val['category_id'] ? $val['category_id'] : 0, 'coupon_discount' => $val['coupon_discount'], 'promotion_rate' => $val['promotion_rate'], 'coupon_total_quantity' => $val['coupon_total_quantity'], 'coupon_remain_quantity' => $val['coupon_remain_quantity'], 'coupon_start_time' => $val['coupon_start_time'], 'coupon_end_time' => $val['coupon_end_time'], 'search_id' => $val['search_id'], 'addtime' => $val['addtime']);
                $startId = $val['id'];
            }
            M('pdditems')->lock(true)->addAll($raw, array(), true);
            file_put_contents($file, $startId);
            exit;
        }
    }
    protected function getMultiCurlResult($data = [], $timeout = 30)
    {
        $request = [];
        $requestResource = curl_multi_init();
        foreach ($data as $k => $v) {
            $option = [CURLOPT_TIMEOUT => $timeout, CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090730 Firefox/3.5.2 GTB5'];
            if (!isset($v['url']) || !$v['url']) {
                return null;
            }
            $option[CURLOPT_URL] = trim($v['url']);
            if (stripos($v['url'], 'https') === 0) {
                $option[CURLOPT_SSL_VERIFYPEER] = false;
            }
            if (isset($v['data'])) {
                $option[CURLOPT_POST] = true;
                $option[CURLOPT_POSTFIELDS] = http_build_query($v['data']);
            }
            $request[$k] = curl_init();
            curl_setopt_array($request[$k], $option);
            curl_multi_add_handle($requestResource, $request[$k]);
        }
        $running = null;
        $result = [];
        do {
            curl_multi_exec($requestResource, $running);
            curl_multi_select($requestResource);
        } while ($running > 0);
        foreach ($request as $k => $v) {
            $result[$k] = curl_multi_getcontent($v);
            curl_multi_remove_handle($requestResource, $v);
        }
        curl_multi_close($requestResource);
        return $result;
    }
    protected function asycurl($url)
    {
        //  $time = time();
        $ch = curl_init();
        $headers = array("Content-type: application/json;charset='utf-8'", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        //
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        //
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //  curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090730 Firefox/3.5.2 GTB5');
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        //     $mh = curl_multi_init();
        //     curl_multi_add_handle($mh,$ch);
        //     $running = null;
        //     do{
        //     	usleep(10000);
        //     $result = curl_multi_exec($mh,$running);
        //     }while($running>0);
        //     curl_multi_remove_handle($mh,$ch);
        //     curl_multi_close($mh);
        // if (empty($result)) {
        return $result;
        // }
    }
    protected function timeout()
    {
        set_time_limit(30);
        ignore_user_abort(true);
        if (!function_exists("fastcgi_finish_request")) {
            function fastcgi_finish_request()
            {
            }
        } else {
            fastcgi_finish_request();
        }
    }
    public function Tbcollect()
    {
       $this->timeout();
       $this->check_key();
        $file = TQK_DATA_PATH . 'start.txt';
        if (!file_exists($file)) {
            return false;
        }
        $startId = file_get_contents($file);
        if (!$startId) {
            $startId = 0;
        }
        $t = I('t');
        $map = array('start' => $startId, 'pagesize' => 20, 'time' => NOW_TIME . $t, 'tqk_uid' => $this->tqkuid);
        $token = $this->create_token(trim(C('yh_gongju')), $map);
        $map['token'] = $token;
        $url = $this->tqkapi . '/getitems';
        $content = $this->_curl($url, $map);
        $json = json_decode($content, true);
        $this->Log('Tbcollect', $content);
        $PID = trim(C('yh_taobao_pid'));
        $json = $json['result'];
        $count = count($json);
        if ($count > 0) {
            foreach ($json as $key => $val) {
                $pic_url = str_replace("http://", "https://", $val['pic_url']);
                $startId = $val['id'];
                if ($this->FilterWords($val['title'])) {
                    continue;
                }
                $link = 'https://item.taobao.com/item.htm?id=' . $val['num_iid'];
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
                   // 'status' => 'underway',
				   'status' => '1',
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
            }
            $ret = M('items')->lock(true)->addAll($raw, array(), true);
            file_put_contents($file, $startId);
            $json = array('data' => array(), 'total' => $count, 'result' => array(), 'state' => 'yes', 'msg' => '正常');
        } else {
            $json = array('data' => array(), 'total' => 0, 'result' => array(), 'state' => 'yes', 'msg' => '商品采集完啦！');
        }
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    protected function check_key()
    {
        $json = array('state' => 'no', 'msg' => '通行密钥不正确');
        $key = I('key');
        if (!$key || $key != $this->accessKey) {
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
    }
    public function TqkOrderScene() //同步会员管理订单
    {
        $this->timeout();
        $this->check_key();
		$time = NOW_TIME;
		$starttime = $time - 1200;
		$start_time = date('Y-m-d H:i:s', $starttime);
		$end_time = date('Y-m-d H:i:s', $time);
        $t = I('t');
        $condition = array('query_type' => 4, 'start_time' => $start_time, 'end_time' => $end_time, 'order_scene' => 3, 'time' => NOW_TIME . $t, 'tqk_uid' => $this->tqkuid);
        $token = $this->create_token(trim(C('yh_gongju')), $condition);
        $condition['token'] = $token;
        $url = $this->tqkapi . '/getorder';
        $content = $this->_curl($url, $condition);
        $itemId = json_decode($content, true);
        $this->Log('TqkOrderScene', $content);
        $datares = $itemId['result']['data']['results']['publisher_order_dto'];
        $json = $this->UpdateSceneOrder($datares);
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    public function Tborderscene()
    {
        $this->timeout();
        $this->check_key();
        $now = NOW_TIME;
        $starttime = $now - 1320;
        $endtime = $now - 120;
        vendor("taobao.taobao");
        $c = new \TopClient();
        $appkey = trim(C('yh_taobao_appkey'));
        $appsecret = trim(C('yh_taobao_appsecret'));
        $c->appkey = $appkey;
        $c->secretKey = $appsecret;
        $req = new \TbkOrderDetailsGetRequest();
        $req->setQueryType("4");
        $req->setPageSize("100");
        //$req->setTkStatus("12");
        $req->setStartTime(date('Y-m-d H:i:s', $starttime));
        $req->setEndTime(date('Y-m-d H:i:s', $endtime));
        $req->setOrderScene("3"); //会员管理
        $resp = $c->execute($req);
        $resp = json_decode(json_encode($resp), true);
        $this->Log('Tborderscene', $resp);
        $datares = $resp['data']['results']['publisher_order_dto'];
        $json = $this->UpdateSceneOrder($datares);
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    private function UpdateSceneOrder($datares)
    {
        $apppid = trim(C('yh_taobao_pid'));
        $apppid = explode('_', $apppid);
        $AdzoneId = $apppid[2];
        $val = $datares;
        if ($val['site_id'] == $AdzoneId && $datares) {
            $item = array();
            $count = 0;
            $item['orderid'] = $val['trade_id'];
            $item['add_time'] = $val['tb_paid_time']?strtotime($val['tb_paid_time']):strtotime($val['modified_time']);
            $item['status'] = $val['tk_status'];
            $item['price'] = $val['alipay_total_price'];
            $item['goods_iid'] = $val['item_id'];
            $item['goods_title'] = $val['item_title'];
            $item['goods_num'] = $val['item_num'];
			$item_id = explode('-',$val['item_id']);
			 $item_id = $item_id[1]?$item_id[1]:$val['item_id'];
			 $item['item_id'] = $item_id;
            $item['ad_id'] = $val['adzone_id'];
            $item['click_time'] = strtotime($val['click_time']);
            $item['income'] = $val['pub_share_pre_fee'];
            $item['ad_name'] = $val['adzone_name'] ? $val['adzone_name'] : '会员订单';
            $item['goods_rate'] = $val['total_commission_rate'] * 100;
            $item['oid'] = substr($val['trade_id'], -6, 6);
            $item['leve1'] = trim(C('yh_bili1'));
            $item['leve2'] = trim(C('yh_bili2'));
            $item['leve3'] = trim(C('yh_bili3'));
            $item['relation_id'] = $val['relation_id'];
            $item['special_id'] = $val['special_id'];
            if ($this->_api_Scene_publish_insert($item)) {
                $count++;
            }
        } elseif ($datares) {
            $item = array();
            $count = 0;
            foreach ($datares as $val) {
                if ($val['site_id'] == $AdzoneId) {
                    $item['orderid'] = $val['trade_id'];
                    $item['add_time'] = $val['tb_paid_time']?strtotime($val['tb_paid_time']):strtotime($val['modified_time']);
                    $item['status'] = $val['tk_status'];
                    $item['price'] = $val['alipay_total_price'];
                    $item['goods_iid'] = $val['item_id'];
					$item_id = explode('-',$val['item_id']);
					 $item_id = $item_id[1]?$item_id[1]:$val['item_id'];
					 $item['item_id'] = $item_id;
                    $item['goods_title'] = $val['item_title'];
                    $item['goods_num'] = $val['item_num'];
                    $item['ad_id'] = $val['adzone_id'];
                    $item['click_time'] = strtotime($val['click_time']);
                    $item['income'] = $val['pub_share_pre_fee'];
                    $item['ad_name'] = $val['adzone_name'] ? $val['adzone_name'] : '会员订单';
                    $item['goods_rate'] = $val['total_commission_rate'] * 100;
                    $item['oid'] = substr($val['trade_id'], -6, 6);
                    $item['leve1'] = trim(C('yh_bili1'));
                    $item['leve2'] = trim(C('yh_bili2'));
                    $item['leve3'] = trim(C('yh_bili3'));
                    $item['relation_id'] = $val['relation_id'];
                    $item['special_id'] = $val['special_id'];
                    if ($this->_api_Scene_publish_insert($item)) {
                        $count++;
                    }
                }
            }
        } else {
            $count = 0;
        }
        $json = array('state' => 'yes', 'msg' => $count);
        return $json;
    }
    public function Tbordersettle()
    {
        $this->timeout();
        $this->check_key();
        $starttime = time() - 1200;
        vendor("taobao.taobao");
        $c = new \TopClient();
        $appkey = trim(C('yh_taobao_appkey'));
        $appsecret = trim(C('yh_taobao_appsecret'));
        $c->appkey = $appkey;
        $c->secretKey = $appsecret;
        $req = new \TbkOrderDetailsGetRequest();
        $req->setQueryType("3");
        $req->setPageSize("100");
        $req->setTkStatus("3");
        $req->setStartTime(date('Y-m-d H:i:s', $starttime));
        $req->setEndTime(date('Y-m-d H:i:s', time()));
        $req->setOrderScene("1");
        $resp = $c->execute($req);
        $resp = json_decode(json_encode($resp), true);
        $this->Log('Tbordersettle', $resp);
        $datares = $resp['data']['results']['publisher_order_dto'];
        $json = $this->UpdateSettleOrder($datares);
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    public function Tborderpay()
    {
        $this->timeout();
        $this->check_key();
        vendor("taobao.taobao");
        $c = new \TopClient();
        $appkey = trim(C('yh_taobao_appkey'));
        $appsecret = trim(C('yh_taobao_appsecret'));
        $c->appkey = $appkey;
        $c->secretKey = $appsecret;
        $req = new \TbkOrderDetailsGetRequest();
        $starttime = time() - 1200;
        $req->setQueryType("4");
        $req->setPageSize("100");
        //$req->setTkStatus("14");
        $req->setStartTime(date('Y-m-d H:i:s', $starttime));
        $req->setEndTime(date('Y-m-d H:i:s', time()));
        $req->setOrderScene("1");
        $resp = $c->execute($req);
        $resp = json_decode(json_encode($resp), true);
        $this->Log('Tborderpay', $resp);
        $datares = $resp['data']['results']['publisher_order_dto'];
        $json = $this->UpdatePayOrder($datares);
        exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
    protected function Log($name, $data)
    {
        if (function_exists('opcache_invalidate')) {
            $basedir = $_SERVER['DOCUMENT_ROOT'];
            $dir = $basedir . '/data/Runtime/Data/task/' . $name . '.php';
            $ret = opcache_invalidate($dir, TRUE);
        }
        F('task/' . $name, $data);
    }
    private function UpdateFailOrder($datares)
    {
        $val = $datares;
        if ($datares && $val['alipay_total_price'] > 0 && $val['trade_id']) {
            $item = array();
            $count = 0;
            $item['status'] = 2;
            $item['orderid'] = $val['trade_id'];
            $item['price'] = $val['alipay_total_price'];
            $item['goods_iid'] = $val['item_id'];
			$item_id = explode('-',$val['item_id']);
			 $item_id = $item_id[1]?$item_id[1]:$val['item_id'];
			 $item['item_id'] = $item_id;
            if ($this->api_yh_publish_fail($item)) {
                $count++;
            }
        } elseif ($datares) {
            $item = array();
            $count = 0;
            foreach ($datares as $val) {
                $item['orderid'] = $val['trade_id'];
                $item['status'] = 2;
                $item['price'] = $val['alipay_total_price'];
                $item['goods_iid'] = $val['item_id'];
				$item_id = explode('-',$val['item_id']);
				$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
				$item['item_id'] = $item_id;
                if ($this->api_yh_publish_fail($item)) {
                    $count++;
                }
            }
        } else {
            $count = 0;
        }
        $json = array('state' => 'yes', 'msg' => $count);
        return $json;
    }
    private function UpdateSettleOrder($datares)
    {
        $val = $datares;
        if ($datares && $val['trade_id'] && $val['alipay_total_price'] > 0) {
            $item = array();
            $count = 0;
            $item['orderid'] = $val['trade_id'];
            $item['status'] = 3;
            $item['price'] = $val['alipay_total_price'];
            $item['income'] = $val['pub_share_fee'];
            //新增
            $item['goods_iid'] = $val['item_id'];
			$item_id = explode('-',$val['item_id']);
			$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
			$item['item_id'] = $item_id;
            $item['up_time'] = strtotime($val['tk_earning_time']);
            if ($this->api_yh_publish_update($item)) {
                $count++;
            }
        } elseif ($datares) {
            $item = array();
            $count = 0;
            foreach ($datares as $val) {
                $item['orderid'] = $val['trade_id'];
                $item['status'] = 3;
                $item['price'] = $val['alipay_total_price'];
                $item['income'] = $val['pub_share_fee'];
                //新增
                $item['goods_iid'] = $val['item_id'];
				$item_id = explode('-',$val['item_id']);
				$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
				$item['item_id'] = $item_id;
                $item['up_time'] = strtotime($val['tk_earning_time']);
                if ($this->api_yh_publish_update($item)) {
                    $count++;
                }
            }
        } else {
            $count = 0;
        }
        $json = array('state' => 'yes', 'msg' => $count);
        return $json;
    }
    private function UpdatePayOrder($datares)
    {
        $apppid = trim(C('yh_taobao_pid'));
        $apppid = explode('_', $apppid);
        $AdzoneId = $apppid[2];
        $val = $datares;
        if ($val['site_id'] == $AdzoneId && $datares) {
            $item = array();
			
			$item_id = explode('-',$val['item_id']);
			$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
            $count = 0;
            $item['orderid'] = $val['trade_id'];
            $item['add_time'] = $val['tb_paid_time'] ? strtotime($val['tb_paid_time']) : strtotime($val['tk_create_time']);
            $item['status'] = $val['tk_status'];
            $item['nstatus'] = 1;
			$item['refund_tag'] = $val['refund_tag']; // 1 维权
            $item['price'] = $val['alipay_total_price'];
            $item['goods_iid'] = $val['item_id'];
			$item['item_id'] = $item_id;
            $item['goods_title'] = $val['item_title'];
            $item['goods_num'] = $val['item_num'];
            $item['ad_id'] = $val['adzone_id'];
            $item['click_time'] = strtotime($val['click_time']);
            $item['tb_deposit_time'] = $val['tb_deposit_time'];
            $item['up_time'] = $val['tk_earning_time'] ? strtotime($val['tk_earning_time']) : '';
            $pub_share_fee = floatval($val['pub_share_fee']);
            $item['income'] = $pub_share_fee > 0 ? $pub_share_fee : $val['pub_share_pre_fee'];
            $item['ad_name'] = $val['adzone_name'];
            $item['goods_rate'] = $val['total_commission_rate'] * 100;
            $item['oid'] = substr($val['trade_id'], -6, 6);
            $item['leve1'] = trim(C('yh_bili1'));
            $item['leve2'] = trim(C('yh_bili2'));
            $item['leve3'] = trim(C('yh_bili3'));
            $item['relation_id'] = $val['relation_id'];
            $item['special_id'] = $val['special_id'];
            if ($this->_api_yh_publish_insert($item)) {
                $count++;
            }
        } elseif ($datares) {
            $item = array();
            $count = 0;
            foreach ($datares as $val) {
                if ($val['site_id'] == $AdzoneId) {
                    $item['orderid'] = $val['trade_id'];
                    $item['add_time'] = $val['tb_paid_time'] ? strtotime($val['tb_paid_time']) : strtotime($val['tk_create_time']);
                    $item['status'] = $val['tk_status'];
                    $item['nstatus'] = 1;
					$item['refund_tag'] = $val['refund_tag']; // 1 维权
                    $item['price'] = $val['alipay_total_price'];
                    $item['goods_iid'] = $val['item_id'];
					$item_id = explode('-',$val['item_id']);
					$item_id = $item_id[1]?$item_id[1]:$val['item_id'];
					$item['item_id'] = $item_id;
                    $item['goods_title'] = $val['item_title'];
                    $item['goods_num'] = $val['item_num'];
                    $item['ad_id'] = $val['adzone_id'];
                    $item['click_time'] = strtotime($val['click_time']);
                    $item['up_time'] = $val['tk_earning_time'] ? strtotime($val['tk_earning_time']) : '';
                    $pub_share_fee = floatval($val['pub_share_fee']);
                    $item['income'] = $pub_share_fee > 0 ? $pub_share_fee : $val['pub_share_pre_fee'];
                    $item['tb_deposit_time'] = $val['tb_deposit_time'];
                    $item['ad_name'] = $val['adzone_name'];
                    $item['goods_rate'] = $val['total_commission_rate'] * 100;
                    $item['oid'] = substr($val['trade_id'], -6, 6);
                    $item['leve1'] = trim(C('yh_bili1'));
                    $item['leve2'] = trim(C('yh_bili2'));
                    $item['leve3'] = trim(C('yh_bili3'));
                    $item['relation_id'] = $val['relation_id'];
                    $item['special_id'] = $val['special_id'];
                    if ($this->_api_yh_publish_insert($item)) {
                        $count++;
                    }
                }
            }
        } else {
            $count = 0;
        }
        $json = array('state' => 'yes', 'msg' => $count);
        return $json;
    }
    private function Decords($id)
    {
        $mod = D('records');
        $where = array('id' => $id);
        $res = $mod->where($where)->delete();
    }
}