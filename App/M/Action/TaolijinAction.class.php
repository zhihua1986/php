<?php
namespace M\Action;

class TaolijinAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->ip = get_client_ip();
        $this->endtime = strtotime('23:59:59')-time();
    }

    public function chalijin()
    {
        if (IS_POST) {
            $numiid = I('numiid');
            $info = S('chalijin_'.$numiid);
            $limit = S($this->ip) ? S($this->ip) : 0;

            if ($limit>=3) {
                $this->ajaxReturn(0, '今天3次机会已用完，请明天再来。');
            }
			
			if ($this->FilterWords($numiid)) {
			    $this->ajaxReturn(0, '创建淘礼金失败，请联系客服！');
			}

            if ($info) {
                $apiurl=$this->tqkapi.'/chalijin';
                $TljPid = trim(C('yh_taolijin_pid'));
                $TbPid = trim(C('yh_taobao_pid'));
				
				if (!$TljPid) {
				    $this->ajaxReturn(0, '淘礼金专属PID没有设置');
				}

                $lim = (float)C('yh_jingpintie');
                if ($lim>1 && $info['lijin']>$lim) {
                    $info['lijin'] = $lim;
                }
                $apidata=[
                    'tqk_uid'=>$this->tqkuid,
                    'time'=>time(),
                    'numiid'=>$info['num_iid'],
                    'lijin'=>$info['lijin'],
                    'pid'=>$TljPid ? $TljPid : $TbPid,
                    'commission_rate'=>$info['commission_rate']
                ];
                $token=$this->create_token(trim(C('yh_gongju')), $apidata);
                $apidata['token']=$token;
                $data= $this->_curl($apiurl, $apidata, false);
                S($this->ip, $limit+1);
                exit($data);
            }
            $this->ajaxReturn(0, '数据异常，请刷新页面重新查询！');
        }
    }

    public function so()
    {
        if (IS_POST) {
            $content = I('content');
           if(false !== strpos($content,'https://')) {
           	$linkid =$this->_itemid($content);	
           }
           
           if(!$linkid){
           	preg_match('/([a-zA-Z0-9]{11})/',$content,$allhtml1);
           	if($allhtml1[1] && !is_numeric($allhtml1[1])){
           	$kouling = $allhtml1[1];
           	}
           }
		   
		   if(!$linkid && !$kouling){
		   	if(false !== strpos($content,'https://m.tb.cn') && preg_match('/[\x{4e00}-\x{9fa5}]/u', $content)>0){
		   		$kouling = true;
		   	}elseif(strlen($content)>24 && preg_match('/[\x{4e00}-\x{9fa5}]/u', $content)>0){
				$kouling = true;
			}
		   }
		   
            if (($kouling || $linkid) && strstr($content, 'http')) {
                $apiurl=$this->tqkapi.'/so';
                $apidata=[
                    'tqk_uid'=>$this->tqkuid,
                    'time'=>time(),
                    'k'=>$content,
                    'hash'=>true,
                ];
                $token=$this->create_token(trim(C('yh_gongju')), $apidata);
                $apidata['token']=$token;
                $data= $this->_curl($apiurl, $apidata, false);
                $data=json_decode($data, true);

                $array = array_keys($data['result']);
                if ($array[0]>0) {
                    $result = $data['result'][$array[0]];
                } else {
                    $result = $data['result'][0];
                }

                if ($data['status'] == 1 && $result) {
                    $commission = round($result['coupon_price']*($result['commission_rate']/10000), 2);
                    $lijin = round($commission*(C('yh_taolijin')/100), 2);

                    $lim = (float)C('yh_jingpintie');
                    if ($lim>1 && $lijin>$lim) {
                        $lijin = $lim;
                    }

                    $result['lijin'] = $lijin;
                    $result['status'] = 1;
                    $price = $result['coupon_price'] ? $result['coupon_price'] : $result['price'];
                    if ($lijin>1) {
                        $result['fuliprice'] = round(($price - $lijin), 2);
                    } elseif ($result['coupon_price']) {
                        $result['fuliprice'] = $result['coupon_price'];
                        $result['rebate'] = Rebate1($price*$result['commission_rate']/10000, $this->memberinfo['webmaster_rate']);
                    } else {
                        $result['rebate'] = Rebate1($price*$result['commission_rate']/10000, $this->memberinfo['webmaster_rate']);
                    }
                    $result['limit'] = abs(3 - S($this->ip));
                    S('chalijin_'.$result['num_iid'], $result);
                    exit(json_encode($result));
                }

                $this->ajaxReturn(0, '没有查询到此商品的优惠信息');
            }
            $this->ajaxReturn(0, '查询内容不符合要求');
        }
    }

    public function index()
    {
        $back = $_SERVER["HTTP_REFERER"];
        if ($back && stristr($back, trim(C('yh_headerm_html')))) {
            $this->assign('back', $back);
        }
        $this->_config_seo([
            'title' => '淘礼金红包免费查询工具-'.C('yh_site_name'),
        ]);
        $this->display();
    }
}
