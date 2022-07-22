<?php
namespace Home\Action;

class PdditemAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
       /*
       当前duoId被禁止调用商品详情。封禁原因：系统检测该duoId商品详情请求查询非多多进宝商品占比过高，涉嫌爬虫或本地缓存商品信息。
       拼多多商品禁止搜索引擎访问，防止出现接口被封
       */
      if ($this->getRobot()!==false) {
       exit;
       }
    }
	
	
    public function index()
    {
        $id = I('s');
        $result = $this->PddGoodsDetail($id);
        !$result && $this->_404();
        $Tag= $this->GetTags($result['goods_name'], 4);
        $this->assign('tag', $Tag);
        $this->assign('item', $result);
        $this->assign('mdomain', str_replace('/index.php/m', '', C('yh_headerm_html')));
        $orlike = M('items')->field('id,pic_url,num_iid,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->limit('0,12')
              ->order('id desc')
              ->select();
        $this->assign('orlike', $orlike);

        $this->_config_seo(C('yh_seo_config.pdditem'), [
            'title' => $result['goods_name'],
            'intro' => $result['goods_desc'],
            'price' => $result['min_group_price'],
            'quan' => floattostr($result['coupon_discount']),
            'coupon_price' => $result['min_group_price']-$result['coupon_discount'],
            'tags' => implode(',', $Tag),
            'title' => $result['goods_name'],
            // 'keywords' => '',
            'description' => $result['goods_desc'],
        ]);

        $this->display();
    }
    protected function delitem($id)
    {
        $where=[
            'goods_id'=>$id,
        ];
        $this->_mod->where($where)->delete();
    }

    public function shortlink($id, $group=0, $search_id='')
    {
		
	$t = $this->memberinfo['id']?$this->memberinfo['id']:$this->GetTrackid('t');
	$data = $this->pddPromotionUrl($id,$search_id,$t);
	
	if(!$data['url']){
	$data = $this->pddPromotionUrl($id,$search_id,$t,'true');	
	}
	
	if($data['url']){
		// if($t && $t!==null){
		// 	//$data['url'] = urldecode($data['url']);
		// 	//$data['url'] = str_replace('&goods_sign','&customParameters='.$t.'&goods_sign',$data['url']);
		// }
		$json= [
		    'code'=>200,
		    'result'=>base64_encode($data['url'])
		];
		exit(json_encode($json));
	}	
	
	
	

    }

    public function jump()
    {
        $result = $this->PddGoodsDetail(I('id'));
        !$result && $this->_404();
        $Tag= $this->GetTags($result['goods_name'], 4);
        $this->assign('tag', $Tag);

        $this->assign('item', $result);
        $this->assign('mdomain', str_replace('/index.php/m', '', C('yh_headerm_html')));

        $orlike = M('items')->field('id,pic_url,num_iid,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->limit('0,12')
                ->order('id desc')
                ->select();
        $this->assign('orlike', $orlike);
        $this->_config_seo(C('yh_seo_config.pdditem'), [
            'title' => $result['goods_name'],
            'intro' => $result['goods_desc'],
            'price' => $result['min_group_price']/100,
            'quan' => floattostr($result['coupon_discount']/100),
            'coupon_price' => $result['min_group_price']/100-$result['coupon_discount']/100,
            'tags' => implode(',', $Tag),
            'title' => $result['goods_name'],
            'keywords' => '',
            'description' => $result['goods_desc'],
        ]);

        $this->display();
    }

    public function jumpclick()
    {
        $params=I('param.');
        $skuId=$params['goods_id'];
        if (is_numeric($skuId) && $skuId>0) {
            $cach_name='jump_pdd_'.$params['goods_id'];
            $value = S($cach_name);
            if (false === $value) {
                S($cach_name, $params, 86400);
            }
            $json=[
                'status'=>1,
                'urls'=>U('pdditem/'.$skuId.'')
            ];

            exit(json_encode($json));
        }
    }

    public function jumpout()
    {
        $id = I('dataurl');
        $this->assign('id', $id);
        $this->display();
    }
}

function floattostr($val)
{
    preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
    return $o[1].sprintf('%d', $o[2]).($o[3]!='.' ? $o[3] : '');
}
