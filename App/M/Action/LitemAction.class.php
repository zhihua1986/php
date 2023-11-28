<?php
namespace M\Action;

class LitemAction extends BaseAction
{
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

    public function gconvert()
    {
        $id = I('num_iid');
        $item = $this->GetTbDetail($id);
        $quan = $item['quan'];
        $coupon_price = $item['coupon_price'];
        $commission = round($coupon_price*($item['commission_rate']/10000), 2);
        $linjin = round($commission*(C('yh_taolijin')/100), 2);
		
		$lim = (float)C('yh_jingpintie');
		if($lim>1 && $linjin>$lim){
			$linjin = $lim;
		}
		
        if ($linjin>1) {
            $apiurl=$this->tqkapi.'/chalijin';
            $TljPid = trim(C('yh_taolijin_pid'));
            $TbPid = trim(C('yh_taobao_pid'));
            $apidata=[
                'tqk_uid'=>$this->tqkuid,
                'time'=>time(),
                'numiid'=>$id,
                'lijin'=>$linjin,
                'pid'=>$TljPid ? $TljPid : $TbPid,
                'commission_rate'=>$item['commission_rate']
            ];

            $token=$this->create_token(trim(C('yh_gongju')), $apidata);
            $apidata['token']=$token;
            $data= $this->_curl($apiurl, $apidata, true);
            $result = json_decode($data, true);
            if ($result['code']==200 && $result['content']) {
                $json=[
                    'status'=>200,
                    'result'=>'8'.$result['content'].'://'
                ];
                exit(json_encode($json));
            }

            $json=[
                'status'=>400,
                'result'=>$result['msg']
            ];
            exit(json_encode($json));
        }
    }

    public function index()
    {
        $id = I('id');
        $item = $this->GetTbDetail($id);
        $quan = $item['quan'];
        $coupon_price = $item['coupon_price'];
        $commission = round($coupon_price*($item['commission_rate']/10000), 2);
        $linjin = round($commission*(C('yh_taolijin')/100), 2);
		
		$lim = (float)C('yh_jingpintie');
		if($lim>1 && $linjin>$lim){
			$linjin = $lim;
		}
		
        $item['taolijin']=$linjin;
        $item['sellerId']=$item['sellerId'];
        $item['pic_url']=$item['pic_url'];
        $item['price']=$item['price'];
        $item['link']=$item['link'];
        $item['quan']=$item['quan'];
        $item['commission_rate']=$item['commission_rate'];
        $item['tk_commission_rate']=$item['commission_rate'];
        $item['click_url']='https:'.$item['click_url'];
        $item['volume']=$item['volume'];
        $item['coupon_price']=$item['coupon_price'];
        $item['coupon_end_time']=$item['coupon_end_time'];
        $item['ems']=2;
        $quanurl = $item['quanurl'];
        $item['quanurl']=$quanurl ? 'https:'.$quanurl : 'https:'.$item['url'];
        $item['Quan_id']=$item['Quan_id'];
        $item['zhuanxiang']=round($item['coupon_price']-$item['taolijin'], 2);
		$this->assign('mdomain', str_replace('/index.php/m', '', C('yh_headerm_html')));
        $file = 'orlike_m' .  $item['id'];
        if (false === $orlike = S($file)) {
            $where=[
                'id'=>['neq', $id]
            ];
            $orlike = $this->_mod->where($where)->field('id,volume,num_iid,quan,commission_rate,title,pic_url,coupon_price,price,shop_type')->limit('0,6')->order('id desc')->select();
            S($file, $orlike);
        } else {
            $orlike = S($file);
        }

        $this->assign('orlike', $orlike);

        $this->_config_seo(C('yh_seo_config.item'), [
            'title' => $item['title'],
            'intro' => $item['intro'],
            'price' => $item['price'],
            'quan' => floattostr($item['quan']),
            'coupon_price' => $item['coupon_price'],
            'tags' => implode(',', $this->GetTags($item['title'], 4)),
        ]);

        $this->assign('item', $item);

        $this->display('Item/litem');
    }
}

    function floattostr($val)
    {
        preg_match("#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o);
        return $o[1].sprintf('%d', $o[2]).($o[3]!='.' ? $o[3] : '');
    }
