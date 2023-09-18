<?php
namespace M\Action;
use Common\Model;
class ItemAction extends BaseAction{

    public function _initialize()
    {
        parent::_initialize();
        $this->_mod = D('items');
        if ($this->getRobot()==false) {
            $this->getrobot='no';
        }

        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if (strpos($useragent, 'micromessenger')>0) {
            $this->assign('weixin', true);
        }
    }
	
	
	public function  index(){
		if ($this->memberinfo && C('yh_bingtaobao') == 2 && $this->visitor->get('webmaster')!=1){
		    $inviterCode = $this->RelationInviterCode($this->memberinfo);
		    $this->assign('inviterCode', $inviterCode);
			$this->assign('callback',$this->fullurl());
		    $this->assign('Tbauth', true);
		}
		
		$id = I('id');
		$item = $this->_mod->field('ordid,ali_id,zc_id,orig_id,tag', true)->where(['num_iid' => $id])->find();
		$item && $item['pic_urls']=unserialize($item['pic_urls']);
		if (!$item) {
		    $item = $this->GetTbDetail($id);
		    !$item && $this->_404();
		    $item['sellerId']=$item['seller_id'];
		    $item['pic_url']=$item['pict_url'];
		    $item['price']=$item['zk_final_price'];
		    $item['link']=$item['item_url'];
            $item['shop_type']=$item['user_type']==1?'B':'C';
		    $item['quan']=$item['coupon_amount'];
		    $item['commission_rate']=$item['commission_rate'];
		    $item['tk_commission_rate']=$item['commission_rate'];
		    $item['click_url']='https:'.$item['url'];
		    $item['volume']=$item['volume'];
		    $item['coupon_price']=$item['zk_final_price']-$item['coupon_amount'];
		    $item['coupon_end_time']=strtotime($item['coupon_end_time']);
			$item['coupon_start_time']=strtotime($item['coupon_start_time']);
		    $item['ems']=2;
		    $quanurl = $item['coupon_share_url'];
		    $item['quanurl']=$quanurl ? 'https:'.$quanurl : 'https:'.$item['url'];
		    $item['Quan_id']=$item['coupon_id'];
			$item['pic_urls']=$item['small_images']['string'];
		    $this->assign('act', 'yes');
		} else {
		    !$item && $this->_404();
		}

		$this->assign('mdomain', str_replace('/index.php/m', '', C('yh_headerm_html')));
		if (!$this->memberinfo && $this->getrobot=='no' && $item['Quan_id'] && $item['ems']==1) {
		    $last_time=date('Y-m-d', $item['last_time']);
		    $today=date('Y-m-d', time());
		
		    if ($last_time!=$today && $item['ems']==1) {
			$item['quanurl'] = $this->Tbconvert($item['num_iid'],$this->memberinfo,$item['Quan_id']);
			$item['quankouling']=kouling($item['pic_url'], $item['title'], $item['quanurl']);
		        // $this->assign('gconvert', true);
		    }
		}
		
		
		if($this->memberinfo && $item['ems']==1){
		$item['quanurl'] = $this->Tbconvert($item['num_iid'],$this->memberinfo,$item['Quan_id']);
		$item['quankouling']=kouling($item['pic_url'], $item['title'], $item['quanurl']);
		$this->assign('act', 'yes');
		
		}
		
		$file = 'orlike_m' .  md5($item['id']);
		if (false === $orlike = S($file)) {
		    $cid = $item["cate_id"];
		    $where=[
		        'cate_id'=>$cid,
		        'id'=>['neq', $id]
		    ];
		    $orlike = $this->_mod->where($where)->field('id,volume,num_iid,quan,commission_rate,title,pic_url,coupon_price,price,shop_type')->limit('0,6')->order('id desc')->select();
		    S($file, $orlike);
		} else {
		    $orlike = S($file);
		}
		
		$this->assign('orlike', $orlike);
		if (!$item['quankouling']) {
		    $kouling=kouling($item['pic_url'].'_200x200.jpg', $item['title'], $item['quanurl']);
		    $item['quankouling']=$kouling;
		    $this->_mod->where([
		        'num_iid' => ''.$item['num_iid'].''
		    ])->save(['quankouling'=>$kouling, 'last_time'=>time()]);
		}
		
//		$RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
//		if ($RelationId && $item['ems']==1) {
//		    $item['quanurl']=$item['quanurl'].'&relationId='.$RelationId;
//		    $item['quankouling']=kouling($item['pic_url'], $item['title'], $item['quanurl']);
//		}
		
		$this->assign('item', $item);
		
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
		
		$regex = '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#';
		preg_match_all($regex, $item['quankouling'], $matches);
		if($matches[0]){
			 $this->assign('iosLink', str_replace('https://m.tb.cn','https://s.tb.cn',$matches[0][0]));
		}
		
		}
		
		$this->_config_seo(C('yh_seo_config.item'), [
		    'title' => $item['title'],
		    'intro' => $item['intro'],
		    'price' => $item['price'],
		    'shop_type' => $item['shop_type']=='B' ? '天猫' : '淘宝',
		    'quan' => floattostr($item['quan']),
		    'coupon_price' => $item['coupon_price'],
		    'tags' => implode(',', $this->GetTags($item['title'], 4)),
		]);
		
		$this->display();
	}
	

    public function gconvert()
    {
        if (IS_POST) {
            $last_time = $this->params['last_time'];
            $num_iid = $this->params['num_iid'];
            $Quan_id = $this->params['Quan_id'];
            $pic_url = $this->params['pic_url'];
            $title = $this->params['title'];
            $pic_urls = $this->params['pic_urls'];
            $last_time=date('Y-m-d', $last_time);
            $today=date('Y-m-d', time());
            if ($last_time!=$today) {
                $apiurl=$this->tqkapi.'/gconvert';
                $apidata=[
                    'tqk_uid'=>$this->tqkuid,
                    'time'=>time(),
                    'good_id'=>''.$num_iid.''
                ];
                $token=$this->create_token(trim(C('yh_gongju')), $apidata);
                $apidata['token']=$token;
                $res= $this->_curl($apiurl, $apidata, false);
                $res = json_decode($res, true);
                $me=$res['me'];
                if (\strlen($me)>5) {
                    $activityId =$Quan_id ? '&activityId='.$Quan_id : '';
                    $quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.$activityId.'&pid='.trim(C('yh_taobao_pid')).'&af=1';
					$kouling=kouling($pic_url, $title, $quanurl);
                    if (!$pic_urls) {
                        $pic_urls=$this->taobaodetail($num_iid);
                        if ($pic_urls) {
                            $tbpic_urls = serialize($pic_urls['small_images']['string']);
                        }
                    }
                    $data=[
                        'last_time'=>time(),
                        'quankouling'=>$kouling,
                        'quanurl'=>$quanurl,
                        'pic_urls'=>$tbpic_urls ? $tbpic_urls : $pic_urls,
                        'ding'=>0,
                        'que'=>1
                    ];
                    $re=$this->_mod->where([
                        'num_iid' => $num_iid
                    ])->save($data);
                    if ($re) {
                        $item['quankouling']=$kouling;
                        $item['quanurl']=$quanurl;
                        $item['src']=U('item/qrcode').'?dataurl='.base64_encode($quanurl);

                        $RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
                        if ($RelationId) {
                            $item['quanurl']=$item['quanurl'].'&relationId='.$RelationId;
                            $item['quankouling']=kouling($pic_url, $title, $item['quanurl']);
                        }
                    }

                    $json=[
                        'status'=>200,
                        'result'=>$item
                    ];
                    exit(json_encode($json));
                }
            }
        }

        $json=[
            'status'=>400,
            'result'=>'error'
        ];
        exit(json_encode($json));
    }

    public function productinfo()
    {
        $num_iid=I('numiid');
        if ($num_iid) {
            $json = $this->taobaodetail($num_iid);
            $imglist = $json['small_images']['string'];
            $desc = '';
            foreach ($imglist as $k=>$v) {
                $desc = $desc.'<img class="lazy" src=' . $v . '>';
            }
            $json=[
                'status'=>'ok',
                'content'=>$desc
            ];
            exit(json_encode($json));
        }
    }

    public function qrcode()
    {
        vendor("phpqrcode.phpqrcode");
        $data= I('dataurl', '', 'trim');
        $data=urldecode($data);
        $level = 'H';
        $size = 4;
        $object = new \QRcode();
        ob_clean();
        $object->png($data, false, $level, $size, 0);
    }
}

function floattostr($val)
{
    preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
    return $o[1].sprintf('%d', $o[2]).($o[3]!='.' ? $o[3] : '');
}
