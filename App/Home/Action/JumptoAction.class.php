<?php
namespace Home\Action;

class JumptoAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->assign('nav_curr', 'index');
        $this->_mod = D('items');
        $this->_cate_mod = D('itemscate')->cache(true, 10 * 60);
    }

    public function index()
    {
        if ($this->memberinfo && C('yh_bingtaobao') == 2 && !$this->visitor->get('webmaster_pid')) {
            $this->assign('Tbauth', true);
        }
        $id=I('item');
        $this->assign('itemid', $id);
        $cach_name='jump_'.$id;
        $sinfo = S($cach_name);
        $newitem = $this->taobaodetail($id);
        $agent_pid=trim(C('yh_taobao_pid'));
        if (false === $sinfo) {
			 $result = $this->GetTbDetail($id);
            if ($result) {
                $sinfo=	$result;
                $newitem['coupon_price']=$sinfo['zk_final_price']-$sinfo['coupon_amount'];
                $newitem['quan']=$sinfo['coupon_amount'];
                $newitem['rate']=$sinfo['commission_rate'];
                $newitem['pict_url']=$sinfo['pict_url'];
                $newitem['quanid']=$sinfo['coupon_id'];
                $newitem['quanurl']=$sinfo['coupon_share_url']?:$sinfo['url'];
                 S($cach_name, $newitem, 86400);
            }
        } else {
            $newitem['coupon_price']=$newitem['zk_final_price']-$sinfo['quan'];
            $newitem['quan']=$sinfo['quan'];
            $newitem['rate']=$sinfo['rate'];
            $newitem['quanid']=$sinfo['quanid'];
            $newitem['quanurl']=$sinfo['quanurl'];
            //$newitem['pid']=$data['pid'];
        }
        $orlike = $this->_mod->field('id,title,pic_url,coupon_price,price,shop_type,quan,volume,num_iid')->limit('0,8')->order('id desc')->select();
        $this->assign('items_list', $orlike);
		
        if (!isset($newitem['kouling']) || empty($newitem['kouling']) || !empty($agent_pid)){
            $apiurl=$this->tqkapi.'/gconvert';
			
			if($this->memberinfo){
				$R = A("Records");
				$Arr = explode('-',$id);
				$id = $Arr[1]?$Arr[1]:$id;
				$Res= $R ->content($id,$this->memberinfo['id']); 
				$agent_pid = $Res['pid'];
			}
                $apidata=[
                    'tqk_uid'=>$this->tqkuid,
                    'good_id'=>''.$id.'',
                    'pid'=>$agent_pid,
                    'time'=>time()
                ];
                $token=$this->create_token(trim(C('yh_gongju')), $apidata);
                $apidata['token']=$token;
                $pid=$agent_pid;
				
            $res= $this->_curl($apiurl, $apidata, false);
            $ret = json_decode($res, true);
            if ($res && \strlen($ret['me'])>5) {
                if (!empty($newitem['quanid'])) {
                    $quanurl='https://uland.taobao.com/coupon/edetail?e='.$ret['me'].'&activityId='.$newitem['quanid'].'&itemId='.$id.'&pid='. $pid .'&af=1';
                } else {
                    $quanurl	=$ret['quanurl'];
                }
            } else {
                if ($ret['item_url']) {
                    $quanurl=$ret['item_url'];
                } elseif (!empty($newitem['quanid'])) {
                    $quanurl='https://uland.taobao.com/coupon/edetail?e=&activityId='.$newitem['quanid'].'&itemId='.$id.'&pid='. $pid .'';
                } else {
                    echo('<script>alert("没有查到此商品的优惠信息！");history.go(-1)</script>');
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

        if (substr($newitem['quanurl'], 0, 2)=='//') {
            $newitem['quanurl']='https:'.$newitem['quanurl'];
        }
        if (!$this->getRobot() && !C('yh_dn_item_desc')) {
            $this->assign('desc', true);
        }
        $Tag= $this->GetTags($newitem['title'], 4);
        $this->assign('tag', $Tag);

      //if(C('yh_dn_item_desc') == '1'){
              $map=[
                  'title'=> ['like', '%' . $Tag[0] . '%']
              ];
              $Tagitem = $this->_mod->where($map)->field('id,pic_url,shop_type,num_iid,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,commission_rate')->limit('0,9')->order('id desc')->select();
              $this->assign('Tagitem', $Tagitem);
      //}
	  

        $this->assign('item', $newitem);

        $this->_config_seo(C('yh_seo_config.item'), [
            'title' => $newitem['title'],
            'price' =>  $newitem['reserve_price'],
            'quan' =>  $this->floattostr($newitem['quan']),
			'shop_type' => $newitem['user_type']=='1'?'天猫':'淘宝',
            'coupon_price' => $newitem['zk_final_price']-$sinfo['quan'],
            'tags' => implode(',', $Tag),
            'seo_title' => $newitem['title'],
            'intro' => $newitem['title'],
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

    public function out()
    {
        $id=I('item');
        $cach_name='jump_'.$id;
        $sinfo = S($cach_name);
        if (false === $sinfo) {
            echo('<script>alert("此商品优惠券失效！");history.go(-1)</script>');
        } else {
            $quanurl=$sinfo['quanurl'];
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
