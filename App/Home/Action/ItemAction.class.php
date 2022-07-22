<?php
namespace Home\Action;

class ItemAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $reurl=$_SERVER['REQUEST_URI'];
        $reurl=str_replace('index.php/', '', $reurl);
        if ($this->isMobile()) {
            echo('<script>window.location.href="'.C('yh_headerm_html').$reurl.'"</script>');
        }
        $this->_mod = D('items');
        $this->assign('nav_curr', 'index');
        if ($this->getRobot()==false) {
            $this->getrobot='no';
        }
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
        $data= I('dataurl', '', 'trim');
        $data=htmlspecialchars_decode(base64_decode($data));
        $level = 'L';
        $size = 4;
        vendor("phpqrcode.phpqrcode");
        $object = new \QRcode();
        ob_clean();
        $object->png($data, false, $level, $size, 0);
    }

    public function index()
    {
        if ($this->memberinfo && C('yh_bingtaobao') == 2 && !$this->visitor->get('webmaster_pid')) {
            $this->assign('Tbauth', true);
        }
		
        //$id = I('id', 0, 'number_int');
		$id = I('id');
        $item = $this->_mod->field('ordid,ali_id,zc_id,orig_id,tag,qq', true)->where([
            'num_iid' => $id
        ])->find();

        if (!$item) {
            $item = $this->GetTbDetail($id);
            !$item && $this->_404();
            $item['sellerId']=$item['seller_id'];
            $item['pic_url']=$item['pict_url'];
            $item['price']=$item['zk_final_price'];
            $item['quan']=$item['coupon_amount'];
            $item['link']=$item['item_url'];
            $item['commission_rate']=$item['commission_rate'];
            $item['tk_commission_rate']=$item['commission_rate'];
            $item['click_url']='https:'.$item['url'];
            $item['volume']=$item['volume'];
            $item['coupon_price']=$item['zk_final_price']-$item['coupon_amount'];
            $item['coupon_end_time']=$item['coupon_end_time'];
            $item['ems']=2;
            $quanurl = $item['coupon_share_url'];
            $item['quanurl']=$quanurl ? 'https:'.$quanurl : 'https:'.$item['url'];
            $item['Quan_id']=$item['coupon_id'];
            $this->assign('act', 'yes');
        } else {
            !$item && $this->_404();
        }

        if (!$this->memberinfo && $this->getrobot=='no' && $item['Quan_id'] && $item['ems']==1) {
            $last_time=date('Y-m-d', $item['last_time']);
            $today=date('Y-m-d', time());
            if ($last_time!=$today && $item['ems']==1) {
                $this->assign('gconvert', true);
            }
        }
		
		
		if($this->memberinfo && $item['ems']==1){
		$R = A("Records");
		$Arr = explode('-',$item['num_iid']);
		$itemId = $Arr[1]?$Arr[1]:$item['num_iid'];
		$res= $R ->content($itemId,$this->memberinfo['id']); 
		$Repid = $res['pid'];
		$item['quanurl'] = $this->Tbconvert($item['num_iid'],$Repid,$item['Quan_id']);	
		$item['quankouling']=kouling($item['pic_url'], $item['title'], $item['quanurl']);
		$this->assign('act', 'yes');
		}
		
        $file = 'orlike_' .  $item['id'];
        if (false === $orlike = S($file)) {
            if ($item["cate_id"]) {
                $orlike = $this->_mod->field('id,pic_url,num_iid,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->where([
                    'cate_id' => $item["cate_id"]
                ])
                        ->limit('0,12')
                        ->order('id desc')
                        ->select();
            } else {
                $orlike = $this->_mod->field('id,pic_url,num_iid,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->limit('0,12')
                        ->order('id desc')
                        ->select();
            }

            S($file, $orlike);
        } else {
            $orlike = S($file);
        }
        $items = [];
        $pagecount = 0;
        foreach ($orlike as $key => $val) {
            $items[$key] = $val;
            $items[$key]['pics'] = $this->pic = $val['pic_url'];
            $items[$key]['titles'] = $this->title = $val['title'];
            $items[$key]['zk'] = round(($val['coupon_price'] / $val['price']) * 10, 1);
            $items[$key]['itemurl'] = C('yh_site_url') . '/item/' . $val['id'] . '.html';
            $items[$key]['jumpurl'] = C('yh_site_url') . '/jumpto/' . $val['id'] . '.html';
            if (! $val['click_url']) {
                $items[$key]['click_url'] = ""; // U('jump/index',array('id'=>$val['id']));
            }
            if ($val['coupon_start_time'] > time()) {
                $items[$key]['click_url'] = ""; // U('item/index',array('id'=>$val['id']));
                $items[$key]['timeleft'] = $val['coupon_start_time'] - time();
            } else {
                $items[$key]['timeleft'] = $val['coupon_end_time'] - time();
            }
            $url = C('yh_site_url') . U('item/index', [
                'id' => $val['id']
            ]);
            $items[$key]['id'] = $val['id'];
            $items[$key]['num_id'] = $val['num_id'];
            $items[$key]['url'] = urlencode($url);
            $items[$key]['urltitle'] = urlencode($val['title']);
            $items[$key]['price'] = $val['price'];
            $items[$key]['coupon_price'] = $val['coupon_price'];
            $pagecount ++;
        }
		
        $item['quan'] = floattostr($item['quan']);
        if (!$item['quankouling']) {
            $kouling=kouling($item['pic_url'], $item['title'], $item['quanurl']);
            $item['quankouling']=$kouling;
            $this->_mod->where([
                'num_iid' => ''.$item['num_iid'].''
            ])->save(['quankouling'=>$kouling, 'last_time'=>time()]);
        }

        $RelationId = $this->memberinfo['webmaster_pid'] ? $this->memberinfo['webmaster_pid'] : $this->GetTrackid('t_pid');
        if ($RelationId && $item['ems']==1) {
            $item['quanurl']=$item['quanurl'].'&relationId='.$RelationId;
            $item['quankouling']=kouling($item['pic_url'], $item['title'], $item['quanurl']);
        }
        $this->assign('item', $item);
        $this->assign('items_list', $items);
        $this->assign('cate_list', $cate_list); // 分类
        $Tag= $this->GetTags($item['title'], 4);
        $this->assign('tag', $Tag);

if(C('yh_dn_item_desc') == '1'){
        $map=[
            'title'=> ['like', '%' . $Tag[0] . '%']
        ];
        $Tagitem = $this->_mod->where($map)->field('id,pic_url,shop_type,num_iid,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,commission_rate')->limit('0,9')->order('id desc')->select();
        $this->assign('Tagitem', $Tagitem);
}		
		

        $this->_config_seo(C('yh_seo_config.item'), [
            'title' => $item['title'],
            'intro' => $item['intro'],
            'price' => $item['price'],
			'shop_type' => $item['shop_type']=='B'?'天猫':'淘宝',
            'quan' => floattostr($item['quan']),
            'coupon_price' => $item['coupon_price'],
            'tags' => implode(',', $Tag),
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
                    $quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.$activityId.'&itemId='.$num_iid.'&pid='.trim(C('yh_taobao_pid')).'&af=1';
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
    /**
     * 获取紧接着的下一级分类ID
     */
    public function ajax_getchilds()
    {
        $id = I('id', '', 'intval');
        $map = [
            'pid' => $id,
            'status' => '1'
        ];
        $return = M('itemscate')->field('id,name')
            ->where($map)
            ->select();
        if ($return) {
            $this->ajaxReturn(1, L('operation_success'), $return);
        } else {
            $this->ajaxReturn(0, L('operation_failure'));
        }
    }
}
function floattostr($val)
{
    preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
    return $o[1] . sprintf('%d', $o[2]) . ($o[3] != '.' ? $o[3] : '');
}
