<?php
namespace M\Action;

class JumptoAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->assign('nav_curr', 'index');
        $this->_mod = D('items');
        $this->_cate_mod = D('itemscate')->cache(true, 10 * 60);

        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if (strpos($useragent, 'micromessenger')>0) {
            $this->assign('weixin', true);
        }
    }

    public function index()
    {
        if ($this->memberinfo && C('yh_bingtaobao') == 2 && !$this->visitor->get('webmaster_pid')) {
            $inviterCode = $this->RelationInviterCode($this->memberinfo);
            $this->assign('inviterCode', $inviterCode);
            $this->assign('Tbauth', true);
        }
        $this->assign('mdomain', str_replace('/index.php/m', '', C('yh_headerm_html')));

        $id=I('item');
        $this->assign('itemid', $id);

        $cach_name='jump_'.$id;
        $sinfo = S($cach_name);
        $wx_quan=I('quan');
        $wx_quanid=I('quanid');
        $commission_rate=I('commission_rate');
        $data=[
            'quan'=>$wx_quan ? $wx_quan : $sinfo['quan'],
            'quanid'=>$wx_quanid ? $wx_quanid : $sinfo['quanid'],
            'commission_rate'=>$commission_rate,
            'id'=>I('item'),
            'pid'=>I('pid')
        ];
        if (!empty($id)) {
            $newitem=$this->taobaodetail($id);
            $newitem['coupon_price']=$newitem['zk_final_price']-$data['quan'];
            $newitem['quan']=$data['quan'];
            $newitem['quanid']=$data['quanid'];
            $newitem['pic_urls']=$newitem['small_images']['string'];
            $newitem['pid']=$data['pid'];
            $newitem['commission_rate']=$data['commission_rate'];
        }

        $agent_pid=trim(C('yh_taobao_pid'));
        if (!$sinfo && !$wx_quan && !$wx_quanid) {
            $result = $this->GetTbDetail($id);

            if ($result) {
                $sinfo=	$result;
                $newitem['coupon_price']=$sinfo['zk_final_price']-$sinfo['coupon_amount'];
                $newitem['quan']=$sinfo['coupon_amount'];
                $newitem['rate']=$sinfo['commission_rate'];
                $newitem['pict_url']=$sinfo['pict_url'];
                $newitem['quanid']=$sinfo['coupon_id'];
                $newitem['quanurl']=$sinfo['coupon_share_url'] ?: $sinfo['url'];
                $newitem['commission_rate']=$sinfo['commission_rate'];
                if (empty($agent_pid)) {
                    S($cach_name, $newitem, 86400);
                }
            }
        } elseif (!$wx_quan && !$wx_quanid) {
            $newitem['coupon_price']=$newitem['zk_final_price']-$sinfo['quan'];
            $newitem['quan']=$sinfo['quan'];
            $newitem['quanid']=$sinfo['quanid'];
            $newitem['shop_type']=$sinfo['shop_type'];
            $newitem['commission_rate']=$sinfo['commission_rate'];
            $newitem['quanurl']=$sinfo['coupon_click_url'];
            //$newitem['pid']=$data['pid'];
        }

        $orlike = $this->_mod->field('id,volume,quan,title,pic_url,coupon_price,price,shop_type,commission_rate,num_iid')->limit('0,6')->order('id desc')->select();
        $this->assign('orlike', $orlike);

        if (!isset($sinfo['kouling']) || empty($sinfo['kouling']) || !empty($agent_pid)) {
            $apiurl=$this->tqkapi.'/gconvert';
            //$agentpid=$this->agent_pid();
            $this->assign('act', 'yes');
			
			if($this->memberinfo){
				$R = A("Records");
				$Arr = explode('-',$id);
				$id = $Arr[1]?$Arr[1]:$id;
				$data= $R ->content($id,$this->memberinfo['id']); 
				$agent_pid = $data['pid'];
			}
            $apidata=[
                'tqk_uid'=>$this->tqkuid,
                'good_id'=>''.$id.'',
				'pid'=>$agent_pid,
                'time'=>time()
            ];
            $token=$this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token']=$token;
            $pid=trim(C('yh_taobao_pid'));
            $res= $this->_curl($apiurl, $apidata, false);
            $ret = json_decode($res, true);
            if ($res && \strlen($ret['me'])>5) {
                if (!empty($sinfo['quanid'])) {
                    $quanurl='https://uland.taobao.com/coupon/edetail?e='.$ret['me'].'&activityId='.$sinfo['quanid'].'&itemId='.$id.'&pid='. $pid .'&af=1';
                } else {
                    $quanurl	=$ret['quanurl'];
                }
            } else {
                 if ($ret['item_url']) {
                   $quanurl=$ret['item_url'];
                } else {
                    $quanurl='https://uland.taobao.com/coupon/edetail?e=&activityId='.$newitem['quanid'].'&itemId='.$id.'&pid='. $pid .'';
                }
            }

            $newitem['quanurl']=$quanurl;
            if (substr($quanurl, 0, 2)=='//') {
                $quanurl='https:'.$quanurl;
            }
            $kouling=kouling($newitem['pict_url'].'_200x200.jpg', $newitem['title'], $quanurl);
            $newitem['kouling']=$kouling;

            if (empty($agent_pid)) {
                S($cach_name, $newitem, 86400);
            }
        } else {
            $sinfo = S($cach_name);
            $newitem['quanurl']=$sinfo['quanurl'];
            $newitem['kouling']=$sinfo['kouling'];
        }

        $RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
        if ($RelationId) {
            $newitem['quanurl']=$newitem['quanurl'].'&relationId='.$RelationId;
            $newitem['kouling']=kouling($newitem['pict_url'], $newitem['title'], $newitem['quanurl']);
        }

        $this->assign('item', $newitem);
		
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
		
		$regex = '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#';
		preg_match_all($regex, $newitem['kouling'], $matches);
		if($matches[0]){
			 $this->assign('iosLink', str_replace('https://m.tb.cn','https://s.tb.cn',$matches[0][0]));
		}
		
		}

        $this->_config_seo(C('yh_seo_config.item'), [
            'title' => $newitem['title'],
            'price' =>  $newitem['reserve_price'],
            'quan' =>  $this->floattostr($newitem['quan']),
            'shop_type' => $newitem['user_type']=='1' ? '天猫' : '淘宝',
            'coupon_price' => $newitem['zk_final_price']-$sinfo['quan'],
            'seo_title' => $newitem['title'],
            'tags' => implode(',', $this->GetTags($newitem['title'], 4)),
        ]);
        $buyer=$this->buyer(5);
        $this->assign('buyer', $buyer);
        $this->display();
    }

    public function jhs()
    {
        $this->assign('mdomain', str_replace('/index.php/m', '', C('yh_headerm_html')));
        $id=I('item');
        $this->assign('itemid', $id);
        $cach_name='jhs_'.$id;
        $sinfo = S($cach_name);
        $wx_quan=I('quan', '', 'trim');
        $wx_quanid=I('quanid', '', 'trim');
        $commission_rate=I('commission_rate', '', 'trim');
        $data=[
            'quan'=>$wx_quan,
            'quanid'=>$wx_quanid,
            'commission_rate'=>$sinfo['commission_rate'],
            'id'=>I('item', '', 'trim'),
            'pid'=>I('pid', '', 'trim')
        ];
        if (!empty($id)) {
            $newitem=$this->taobaodetail($id);
            $newitem['coupon_price']=$newitem['zk_final_price'];
            $newitem['quan']=$sinfo['quan'];
            $newitem['quanid']=$data['quanid'];
            $newitem['pic_urls']=$newitem['small_images']['string'];
            $newitem['pid']=$data['pid'];
            $newitem['jdd_num']=$sinfo['jdd_num'];
            $newitem['jdd_price']=$sinfo['jdd_price'];
            $newitem['commission_rate']=$sinfo['commission_rate'];
        }

        $orlike = $this->_mod->field('id,num_iid,volume,quan,title,pic_url,coupon_price,price,shop_type,commission_rate')->limit('0,6')->order('id desc')->select();
        $this->assign('orlike', $orlike);
        if (!isset($sinfo['kouling']) || empty($sinfo['kouling'])) {
            $newitem['quanurl']=$sinfo['quanurl'];
            if (substr($newitem['quanurl'], 0, 2)=='//') {
                $newitem['quanurl']='https:'.$newitem['quanurl'];
            }
            $kouling=kouling($newitem['pict_url'].'_200x200.jpg', $newitem['title'], $newitem['quanurl']);
            $newitem['kouling']=$kouling;
            if (empty($agent_pid)) {
                S($cach_name, $newitem, 86400);
            }
        } else {
            $sinfo = S($cach_name);
            $newitem['quanurl']=$sinfo['quanurl'];
            $newitem['kouling']=$sinfo['kouling'];
        }

        //$agent=strtolower($_SERVER['HTTP_USER_AGENT']);
        //if(strpos($agent,'ucbrowser')>10 || strpos($agent,'mqqbrowser')>10){
        //$newitem['kouling']=str_replace("￥","《",$newitem['kouling']);
        //}

        $this->assign('item', $newitem);

        $this->_config_seo(C('yh_seo_config.item'), [
            'title' => $newitem['title'],
            'seo_title' => $newitem['title']
        ]);
        $this->display();
    }

    public function jumpclick()
    {
        $params=I('param.');
        $params['quanurl'] = htmlspecialchars_decode($params['quanurl']);
        $numid=$params['numid'];
        if (is_numeric($numid) && $numid>0) {
            $cach_name='jump_'.$numid;
            $value = S($cach_name);
            if (false === $value) {
                S($cach_name, $params, 86400);
            }

            $json=[
                'status'=>1,
                'urls'=>U('jumpto/index', ['item'=>$numid])
            ];

            exit(json_encode($json));
        }
    }

    public function jhsclick()
    {
        $params=I('param.');
        $params['quanurl'] = htmlspecialchars_decode($params['quanurl']);
        $numid=$params['numid'];
        if (is_numeric($numid) && $numid>0) {
            $cach_name='jhs_'.$numid;
            $value = S($cach_name);
            if (false === $value) {
                S($cach_name, $params, 86400);
            }
            $json=[
                'status'=>1,
                'urls'=>U('jumpto/jhs', ['item'=>$numid])
            ];

            exit(json_encode($json));
        }
    }

    public function out()
    {
        $couponurl=I('quanurl', '', 'trim');
        if (!empty($couponurl)) {
            $quanurl=base64_decode($couponurl);
        } else {
            $id=I('item');
            $cach_name='jump_'.$id;
            $sinfo = S($cach_name);
            if (false === $sinfo) {
                echo('<script>alert("此商品优惠券失效！");history.go(-1)</script>');
            } else {
                $quanurl=$sinfo['quanurl'];
            }
        }

        $this->assign('quanurl', $quanurl);
        $this->display();
    }

    protected function floattostr($val)
    {
        preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
        return $o[1] . sprintf('%d', $o[2]) . ($o[3] != '.' ? $o[3] : '');
    }
}
