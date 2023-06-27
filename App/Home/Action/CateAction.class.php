<?php
namespace Home\Action;

use Think\Page;
use Common\Model\itemsModel;
use Common\Model\itemscateModel;

class CateAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
    }
	

    public function index()
    {
        $ItemCate = new itemscateModel();
        $this->assign('ItemCate', $ItemCate->cate_cache());
        $cid = I('cid', 0, 'intval');
        $this->assign('cid', $cid);
        $sid = I('sid', 0, 'intval');
        $this->assign('sid', $sid);
		$coupon = I('coupon', '', 'intval');
		$this->assign('cou', $coupon);
        $mod = new itemsModel();
        $this->_mod = $mod ->cache(true, 5 * 60);
        $page	= I('p', 1, 'intval');
        $size	= 40;
        $sort	= I('sort', 'new');
        $stype    = I("stype");
        $start = $size * ($page - 1);
        $this->assign('txt_sort', $sort);
        $key    = I("k");
        $key    = urldecode($key);
        $where['ems'] = 1;
        $where['status'] = 'underway';
		
		
        if ($key && filter_var($key, FILTER_VALIDATE_URL) === false && !$this->hasEmoji($key)) {
            if ($this->FilterWords($key)) {
                $this->_404();
            }
            $where['title'] = ['like', '%' . $key . '%'];
            $this->assign('k', $key);
			$url='https://suggest.taobao.com/sug?code=utf-8&q='.$key;
			$rest=$this->_curl($url);
			if($rest){
				$likekey=json_decode($rest,true);
				$this->assign('likekey',$likekey['result']);
			}
			
        }
        if ($cid) {
            $where['cate_id'] = $cid;
        }
        if ($stype == 1) {
            $where['shop_type'] = 'B';
            $this->assign('stype', $stype);
        }
        if ($stype == 2) {
            $where['cate_id'] = 27160;
            $this->assign('stype', $stype);
        }

        if ($sid) {
            $where['ali_id'] = $sid;
        }

        switch ($sort) {
            case 'new':
                $order = 'id DESC';
                break;
            case 'price':
                $order = 'coupon_price ASC';
                break;
            case 'rate':
                $order = 'quan DESC';
                break;
            case 'hot':
                $order = 'volume DESC';
                break;
            default:
                $order = 'ordid desc';
}
        $items_list = $this->_mod->where($where)->field('id,pic_url,title,num_iid,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit($start . ',' . $size)->select();
        $count =$this->_mod->where($where)->count();
        $pager = new Page($count, $size);
        $this->assign('p', $page);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $size);
        if ($items_list) {
            $goodslist=[];
            foreach ($items_list as $k=>$v) {
                if ($this->FilterWords($v['title'])) {
                    continue;
                }
                $goodslist[$k]['id']=$v['id'];
                $goodslist[$k]['num_iid']=$v['num_iid'];
                $goodslist[$k]['pic_url']=$v['pic_url'];
                $goodslist[$k]['title']=$v['title'];
                $goodslist[$k]['coupon_price']=$v['coupon_price'];
                $goodslist[$k]['commission_rate']=$v['commission_rate'];//比例
                $goodslist[$k]['price']=$v['price'];
                $goodslist[$k]['quan']=$v['quan'];
                $goodslist[$k]['shop_type']=$v['shop_type'];
                $goodslist[$k]['volume']=$v['volume'];
                $goodslist[$k]['category_id']=$v['category_id'];
                $goodslist[$k]['is_new']=1;
                if (C('APP_SUB_DOMAIN_DEPLOY')) {
                    $goodslist[$k]['linkurl']=U('/item/', ['id'=>$v['id']]);
                } else {
                    $goodslist[$k]['linkurl']=U('item/index', ['id'=>$v['id']]);
                }
            }
        }
        $appkey=trim(C('yh_taobao_appkey'));
        $appsecret=trim(C('yh_taobao_appsecret'));
        $apppid=trim(C('yh_taobao_pid'));
        $apppid=explode('_', $apppid);
        $AdzoneId=$apppid[3];
        $count=\count($items_list);
        if (!empty($appkey) && !empty($appsecret) && !$this->hasEmoji($key) && ($key || $sid) && $count<40 && !empty($AdzoneId)) {
            vendor("taobao.taobao");
            $c = new \TopClient();
            $c->appkey = $appkey;
            $c->secretKey = $appsecret;
            $c->format = 'json';
            $req = new \TbkDgMaterialOptionalRequest();
            $req->setAdzoneId($AdzoneId);
            $req->setPlatform("1");
            if ($stype) {
                $req->setIsTmall("true");
            }
		  if($coupon){
           $req->setHasCoupon("true");
		   }
            $req->setPageSize("100");
            if ($sid) {
                $req->setCat("".$sid."");
            }
            if ($key) {
                $req->setQ((string)$key);
            }
            if ($page>0) {
                $req->setPageNo("".$page."");
            } else {
                $req->setPageNo(1);
            }

            //$req->setIncludePayRate30("true");
            if ($sort=='hot') {
                $req->setSort("total_sales_des");
            } elseif ($sort=='price') {
                $req->setSort("price_asc");
            } elseif ($sort=='rate') {
                $req->setSort("tk_rate_des");
            } else {
                $req->setSort("tk_des");
            }
            $resp = $c->execute($req);
            $resp = json_decode(json_encode($resp), true);
            $resp=$resp['result_list']['map_data'];
            $patterns = "/\d+/";
            foreach ($resp as $k=>$v) {

                if ($this->FilterWords($v['title']) || !$v['item_id']) {
                    continue;
                }

                $goodslist[$k+$count]['quan']=$v['coupon_amount'];
                $goodslist[$k+$count]['coupon_click_url']=$v['coupon_share_url'] ? $v['coupon_share_url'] : $v['url'];
                $goodslist[$k+$count]['num_iid']=$v['item_id'];
                $goodslist[$k+$count]['title']=$v['title'];
                $goodslist[$k+$count]['coupon_id']=$v['coupon_id'];
                $goodslist[$k+$count]['coupon_price']=$v['zk_final_price']-$goodslist[$k+$count]['quan'];
                if ($v['user_type']=="1") {
                    $goodslist[$k+$count]['shop_type']='B';
                } else {
                    $goodslist[$k+$count]['shop_type']='C';
                }
                $goodslist[$k+$count]['commission_rate']=$v['commission_rate']; //比例
                $goodslist[$k+$count]['price']=$v['zk_final_price'];
                $goodslist[$k+$count]['volume']=$v['volume'];
                $goodslist[$k+$count]['pic_url']=$v['pict_url'];
                $goodslist[$k+$count]['category_id']=$v['category_id'];
            }
        }

        $this->assign('list', $goodslist);
        if ($cid) {
            $cid = $sid ? $sid : $cid;
            $cateinfo=$this->_cate_mod->where('ali_id='.$cid)->field('id,name,seo_title,seo_keys,seo_desc')->find();
        }

        $seo = C('yh_seo_config.search');
        if ($key && $seo['title']) {
            $this->_config_seo($seo, [
                'key' => $key,
            ]);
        } elseif ($cateinfo) {
            $this->_config_seo([
                'cate_name' => $cateinfo['name'],
                'title' => $cateinfo['seo_title'] ? $cateinfo['seo_title'] : '' . $cateinfo['name'] . '淘宝优惠券 - '. C('yh_site_name'),
                'keywords' => $cateinfo['seo_keys'],
                'description' => $cateinfo['seo_desc']
            ]);
        } else {
            $this->_config_seo(C('yh_seo_config.cate'));
        }

        $this->display();

        if (preg_match('/[a-zA-Z]/', $key)) {
            return false;
        }
        if ($goodslist && 12 > \strlen($key) && \strlen($key)>3) {
            if (\function_exists('opcache_invalidate')) {
                $basedir = $_SERVER['DOCUMENT_ROOT'];
                $dir=$basedir.'/data/Runtime/Data/data/hotkey.php';
                $ret=opcache_invalidate($dir, true);
            }
            $disable_num_iids = F('data/hotkey');
            if (!$disable_num_iids) {
                $disable_num_iids = [];
            }
            if (\count($disable_num_iids)>5) {
                $disable_num_iids=\array_slice($disable_num_iids, 1, 5);
            }
            if (!\in_array($key, $disable_num_iids)) {
                $disable_num_iids[] = $key;
            }
            F('data/hotkey', $disable_num_iids);
        }
    }

    public function so()
    {
        if (IS_POST) {
            $tb = I('tb');
            $jd = I('jd');
            $pdd = I('pdd');
            if (!$tb && !$jd && !$pdd) {
                $this->ajaxReturn(0, '搜索内容不能为空');
            }

            if ($jd) {
                $skuid = $this->jditemid($jd);
                if ($skuid) {
                    $apiurl=$this->tqkapi.'/sojingdong';
                    $data=[
                        'key'=>$this->_userappkey,
                        'time'=>time(),
                        'tqk_uid'=>	$this->tqkuid,
                        'skuid'=>$skuid,
                    ];
                    $token=$this->create_token(trim(C('yh_gongju')), $data);
                    $data['token']=$token;
                    $result=$this->_curl($apiurl, $data, true);
                    $result=json_decode($result, true);
                    if ($result['status'] == 1) {
						$jd_pid = $this->memberinfo['jd_pid'] ? $this->memberinfo['jd_pid'] : $this->GetTrackid('jd_pid');
                        $cach_name='jump_jd_'.$skuid.$jd_pid;
                        S($cach_name, $result['result'], 86400);
                        $url = U('jditem/jump', ['skuid'=>$skuid]);
                        $json=[
                            'code'=>200,
                            'result'=>$url,
                            'msg'=>'jump'
                        ];
                        exit(json_encode($json));
                    }
                }
                $url = U('jd/index', ['k'=>$jd]);
                $json=[
                    'code'=>200,
                    'result'=>$url,
                    'msg'=>'jump'
                ];
                exit(json_encode($json));
            }

            if ($pdd) {
               $skuid = $this->pdditemid($pdd);
               if ($skuid) {
					$info = $this->PddGoodsSearch('','',$skuid);
					if($info['goodslist'][0]['goods_sign']){
						$url = U('pdditem/jump/'.$info['goodslist'][0]['goods_sign'].'');
						$json=[
						    'code'=>200,
						    'result'=>$url,
						    'msg'=>'jump'
						];
						exit(json_encode($json));
						
					}
					
                
                }

                $url = U('pdd/index', ['k'=>$pdd]);
                $json=[
                    'code'=>200,
                    'result'=>$url,
                    'msg'=>'jump'
                ];
                exit(json_encode($json));
            }

            if ($tb) {
				
				
				if(false !== strpos($tb,'https://')) {
					$linkid =$this->_itemid($tb);	
				}
				
				if(!$linkid){
					preg_match('/([a-zA-Z0-9]{11})/',$tb,$allhtml1);
					if($allhtml1[1] && !is_numeric($allhtml1[1])){
					$kouling = $allhtml1[1];
					}
				}
				if(!$linkid && !$kouling){
					if(false !== strpos($tb,'https://m.tb.cn') && preg_match('/[\x{4e00}-\x{9fa5}]/u', $tb)>0){
						$kouling = true;
					}	
				}
                if (($kouling || $linkid) && strpos($tb, 'http')!== false) {
                    $apiurl=$this->tqkapi.'/so';
                    $data=[
                        'key'=>$this->_userappkey,
                        'time'=>time(),
                        'tqk_uid'=>	$this->tqkuid,
                        'hash'=>true,
                        'k'=>urlencode($tb),
                    ];
                    $token=$this->create_token(trim(C('yh_gongju')), $data);
                    $data['token']=$token;
                    $result=$this->_curl($apiurl, $data, true);
                    $result=json_decode($result, true);
                    $newitem = [];
                    $newitem=	$result['result'][0];
                    if ($result['status'] == 1 && !$newitem['tongkuan']) {
                        $cach_name='jump_'.$newitem['num_iid'];
                        $newitem['coupon_price']=$newitem['price']-$newitem['quan'];
                        $newitem['quan']=$newitem['quan'];
                        $newitem['rate']=$newitem['commission_rate'];
                        $newitem['pict_url']=$newitem['pic_url'];
                        $newitem['quanid']=$newitem['Quan_id'];
                        $newitem['quanurl']=$newitem['url'];
                        S($cach_name, $newitem, 86400);
                        $url = U('jumpto/index', ['item'=>$newitem['num_iid']]);
                        $json=[
                            'code'=>200,
                            'result'=>$url,
                            'msg'=>'jump'
                        ];
                        exit(json_encode($json));
                    }
                }

                $url = U('/cate/', ['k'=>$tb]);
                $json=[
                    'code'=>200,
                    'result'=>$url,
                    'msg'=>'jump'
                ];
                exit(json_encode($json));
            }
        } else {
            $json=[
                'status'=>0,
                'msg'=>'异常操作！'
            ];
        }
        exit(json_encode($json));
    }
}
