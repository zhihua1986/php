<?php
namespace M\Action;

use Common\Model\jditemsModel;

class JditemAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->_mod = new jditemsModel();
        if ($this->getRobot()==false) {
            $this->getrobot=true;
        }
    }

    public function jump()
    {
	$jd_pid = $this->memberinfo['jd_pid'] ? $this->memberinfo['jd_pid'] : $this->GetTrackid('jd_pid');
	if(I('skuid')){
	$cach_name='jump_jd_'.I('skuid').$jd_pid;
	$sinfo = S($cach_name);
	$result = $this->jddetail(I('skuid'));
	$result['coupon_price'] = $sinfo['coupon_price'];
	$result['link'] = $sinfo['link'];
	$result['quan'] = $sinfo['quan'];
	$result['comments'] = $sinfo['comments'];
	$result['commission_rate'] = $sinfo['commission_rate'];
	
	if(!$result['detailImages']){
		$info = $this->JdGoodsQuery(I('skuid'));
		$imglist = explode(",", $info['data'][0]['detailImages']);
		$result['detailImages'] = $imglist;
	}
	
	if (!$result['click_url']) {
	    $click = $this->jdpromotion($result['materialUrl'], $result['link']);
	    if ($click && $click!=1) {
	        $result['click_url'] = $click;
	    } else {
	        echo('<script>alert("此商品优惠券失效！");</script>');
	    }
	}	
	
	  $this->assign('item', $result);
	
		}else{
		
        $result = $this->jddetail(I('i'));

        if ($result) {
            $result['quan']= I('q');
            $couponlink = base64_decode(I('l'));
            $cach_name='jump_jd_'.I('i').$jd_pid;
            $result['coupon_price']= I('p');
            $result['commission_rate']= I('r');
            $sinfo = S($cach_name);
            if ($sinfo) {
                $result['coupon_price'] = $sinfo['coupon_price'];
                $result['link'] = $sinfo['link'];
                $result['quan'] = $sinfo['quan'];
                $result['comments'] = $sinfo['comments'];
                $result['commission_rate'] = $sinfo['commission_rate'];
                $couponlink = $sinfo['link'];
            }
            $click = $this->jdpromotion($result['materialUrl'], $couponlink);
            if ($click && $click!=1) {
                $result['click_url'] = $click;
            } else {
                echo('<script>alert("此商品优惠券失效！");</script>');
            }
            $this->assign('item', $result);
        } else {
            echo('<script>alert("商品解析失败，请返回重试！");</script>');
        }

}

        $this->assign('mdomain', str_replace('/index.php/m', '', C('yh_headerm_html')));
        $orlike = $this->_mod->where($where)->field('id,pict,title,itemid,coupon_price,price,item_type,quan,item_type,comments,commission_rate')->limit('0,6')->order('id desc')->select();
        $this->assign('orlike', $orlike);
        $this->_config_seo(C('yh_seo_config.jditem'), [
            'title' => $result['goodsName'],
            'price' => $result['unitPrice'],
            'quan' => floattostr($result['quan']),
            'coupon_price' => $result['coupon_price'],
            'title' => $result['goodsName'],
            'tags' => implode(',', $this->GetTags($result['goodsName'], 4)),
            // 'keywords' => '',
            // 'description' => '',
        ]);
        $this->display();
    }

    public function jumpclick()
    {
        $params=I('param.');
        $params['link'] = htmlspecialchars_decode($params['link']);
        $skuId=$params['skuid'];
        if (is_numeric($skuId) && $skuId>0) {
			$jd_pid = $this->memberinfo['jd_pid'] ? $this->memberinfo['jd_pid'] : $this->GetTrackid('jd_pid');
            $cach_name='jump_jd_'.$params['skuid'].$jd_pid;
            $value = S($cach_name);
            if (false === $value) {
                S($cach_name, $params, 86400);
            }
            $json=[
                'status'=>1,
                'urls'=>U('jditem/jump', ['i'=>$skuId])
            ];

            exit(json_encode($json));
        }
    }

    public function index()
    {
        $id = I('id', 0, 'number_int');
        $result=$this->_mod->where(['itemid'=>$id])->find();
        if (!$result && $this->getrobot) {
            $detail = $this->JdGoodsDetail($id);
            !$detail && $this->_404();
            $result['commission_rate']=$detail['commissionShare'];
            $result['quan']=$detail['couponAmount']>0?$detail['couponAmount']:0;
            $result['couponlink']=$detail['couponLink'];
            $result['pict']=$detail['picMain'];
            $result['itemurl']=$detail['materialUrl'];
            $result['coupon_price']=$detail['actualPrice'];
            $result['price']=$detail['originPrice'];
            $result['owner']=$detail['isOwner'];
            $result['comments']=$detail['comments'];
            $result['cate_id']=$detail['cid1'];
            $result['itemid']=$detail['skuId'];
            $result['title']=$detail['skuName'];
            $result['detailImages']=$detail['detailImages'];
        } else {
            !$result && $this->_404();
        }
		
		if(!$result['detailImages']){
			$info = $this->JdGoodsQuery($id);
			$imglist = explode(",", $info['data'][0]['detailImages']);
			$result['detailImages'] = $imglist;
		}

        if ($result['isSeckill'] == 1) {
            $btn = '立即秒杀';
        } elseif ($result['item_type'] == 2) {
            $btn = '立即拼购';
        } elseif ($result['quan'] > 0) {
            $btn = '立即领券';
        } else {
            $btn = '立即查看';
        }
        $this->assign('btn', $btn);
        if (!$result['click_url']) {
            $click = $this->jdpromotion($result['itemurl'], $result['couponlink']);
            if ($click && $click!=1) {
                $this->_mod->where([
                    'id' => $result['id']
                ])->save(['click_url'=>$click]);
                $result['click_url'] = $click;
            } else {
                $this->_mod->where([
                    'id' => $result['id']
                ])->delete();
                echo('<script>alert("此商品优惠券失效！");history.go(-1)</script>');
            }
        }
        $this->assign('item', $result);

        $this->assign('mdomain', str_replace('/index.php/m', '', C('yh_headerm_html')));
        $cid = $result["cate_id"];
        $where=[
            'cate_id'=>$cid,
            'itemid'=>['neq', $id]
        ];

        $orlike = $this->_mod->where($where)->field('id,pict,itemid,title,coupon_price,price,item_type,quan,item_type,comments,commission_rate')->limit('0,6')->order('id desc')->select();
        $this->assign('orlike', $orlike);
        //if(!file_exists('data/waphtml/jd/'.$id.'.html') && C('CREATE_HTML_ON')){
        // $this->buildHtml($id.'.html', 'data/waphtml/jd/', 'Jditem/index');
        //}

        $this->_config_seo(C('yh_seo_config.jditem'), [
            'title' => $result['title'],
            'price' => $result['price'],
            'quan' => floattostr($result['quan']),
            'coupon_price' => $result['coupon_price'],
            'title' => $result['title'],
            'tags' => implode(',', $this->GetTags($result['title'], 4)),
            // 'keywords' => '',
            // 'description' => '',
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
}

function floattostr($val)
{
    preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
    return $o[1].sprintf('%d', $o[2]).($o[3]!='.' ? $o[3] : '');
}
