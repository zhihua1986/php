<?php
namespace M\Action;

class ActivityAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
		 $this->_mod = D('items')->cache(true, 5 * 60);
    }
	
	
	public function create(){
		$id = I('id', 0, 'number_int');
		$item = $this->_mod->field('ordid,ali_id,zc_id,orig_id,tag,qq', true)->where([
		    'num_iid' => $id
		])->find();
		
		if($this->memberinfo && $item['ems']==1 && $this->getRobot()==false){
		$R = A("Records");
		$Arr = explode('-',$item['num_iid']);
		$id = $Arr[1]?$Arr[1]:$item['num_iid'];
		$res= $R ->content($id,$this->memberinfo['id']); 
		$Repid = $res['pid'];
		$item['quanurl'] = $this->Tbconvert($item['num_iid'],$Repid,$item['Quan_id']);	
		$item['kouling']=kouling($item['pic_url'], $item['title'], $item['quanurl']);
		}elseif ($this->getRobot()==false){
		    $apiurl=$this->tqkapi.'/gconvert';
		    $apidata=[
		        'tqk_uid'=>$this->tqkuid,
		        'time'=>time(),
		        'good_id'=>''.$id.''
		    ];
		    $token=$this->create_token(trim(C('yh_gongju')), $apidata);
		    $apidata['token']=$token;
		    $res= $this->_curl($apiurl, $apidata, false);
		    $res = json_decode($res, true);
		    $me=$res['me'];
		    if (\strlen($me)>5){
		        $activityId =$item['Quan_id'] ? '&activityId='.$item['Quan_id'] : '';
		        $quanurl='https://uland.taobao.com/coupon/edetail?e='.$me.$activityId.'&itemId='.$id.'&pid='.trim(C('yh_taobao_pid')).'&af=1';
		        $item['kouling']=kouling($item['pic_url'], $item['title'], $quanurl);
		    }else{
				
				$item['kouling']=kouling($item['pic_url'], $item['title'], $item['quanurl']);
				
			}
		}
		
		$this->assign('info',$item);
		$this->_config_seo([
		    'title' => $item['title']. C('yh_site_name'),
		]);
		$this->display();
		
	}
	

    public function index()
    {
	
	$useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
	        if ((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1) {
	            $this->assign('isweixin', true);
	        }
	
	        $page	= I('p', 0, 'number_int');
	        $size	= 20;
	        $sort	= I('sort', 'new', 'trim');
	        $start = $size * $page;
	        $this->assign('txt_sort', $sort);
	        $key    = I("k");
	        $where['ems'] = 1;
	        $where['status'] = 'underway';
	        $where['cate_id'] = 11111;
			$where['shop_type'] = 'B';
			$where['change_price'] = array('gt',0) ;
			if($key){
			 $where['title'] = array( 'like', '%' . $key . '%' );
			 $this->assign('sokey', $key);
			}
	        $count =$this->_mod->where($where)->count();
	        $pagesize=ceil($count/$size);
	        $pagesize==0 ? $pagesize=1 : $pagesize;
	        $this -> assign('page_size', $pagesize);
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
	        $items_list = $this->_mod->where($where)->field('id,pic_url,num_iid,change_price,mobilezk,title,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit(0, 20)->select();
	        if ($items_list) {
	            $today=date('Ymd');
	            $goodslist=[];
	            foreach ($items_list as $k=>$v) {
	                $goodslist[$k]['id']=$v['id'];
	                $goodslist[$k]['num_iid']=$v['num_iid'];
	                $goodslist[$k]['pic_url']=$v['pic_url'];
	                $goodslist[$k]['title']=$v['title'];
	                $goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
	                $goodslist[$k]['coupon_price']=$v['coupon_price'];
	                $goodslist[$k]['price']=$v['price'];
	                $goodslist[$k]['quan']=(int) ($v['quan']);
	                $goodslist[$k]['shop_type']=$v['shop_type'];
	                $goodslist[$k]['volume']=$v['volume'];
	                if ($today==date('Ymd', $v['add_time'])) {
	                    $goodslist[$k]['is_new']=1;
	                } else {
	                    $goodslist[$k]['is_new']=0;
	                }
					if ($v['change_price']>0) {
					   $yushou = '定金'.intval($v['change_price']).'元';
					} 
					if ($v['mobilezk']>0) {
					   $yushou .= ' 付定金立减'.intval($v['mobilezk']).'元';
					} 
					$goodslist[$k]['yushou']=$yushou?:'';
	                if (C('APP_SUB_DOMAIN_DEPLOY')) {
	                    $goodslist[$k]['linkurl']=U('/item/', ['id'=>$v['num_iid']]);
	                } else {
	                    $goodslist[$k]['linkurl']=U('item/index', ['id'=>$v['num_iid']]);
	                }
	            }
	        }
	        $this->assign('list', $goodslist);	
		

        $this->_config_seo([
            'title' => '2022天猫双618预售实时热销爆款榜 - '. C('yh_site_name'),
            'keywords' => '2022天猫双618',
            'description' => '2022天猫双618预售实时热销爆款榜'
        ]);

        $this->display();
    }


  public function pagelist()
    {
        $page	= I('page', 0, 'number_int');
        $size	= 10;
        $sort	= I('sort', 'new', 'trim');
        $start = abs($size * $page);
        $this->assign('txt_sort', $sort);
        $where['ems'] = 1;
        $where['status'] = 'underway';
        $where['cate_id'] = 11111;
		$where['shop_type'] = 'B';
		$where['change_price'] = array('gt',0) ;
		$key    = I("k");
		if($key){
		 $where['title'] = array( 'like', '%' . $key . '%' );
		 $this->assign('sokey', $key);
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

        $items_list = $this->_mod->where($where)->field('id,pic_url,num_iid,change_price,mobilezk,title,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit($start . ',' . $size)->select();
        $this -> assign('page_size', $size);
        if ($items_list) {
            $today=date('Ymd');
            $goodslist=[];
            foreach ($items_list as $k=>$v) {
                $goodslist[$k]['id']=$v['id'];
                $goodslist[$k]['num_iid']=$v['num_iid'];
                $goodslist[$k]['pic_url']=$v['pic_url'];
                $goodslist[$k]['title']=$v['title'];
                $goodslist[$k]['coupon_price']=$v['coupon_price'];
                $goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
                $goodslist[$k]['price']=$v['price'];
                $goodslist[$k]['quan']=$v['quan'];
                $goodslist[$k]['shop_type']=$v['shop_type'];
                $goodslist[$k]['volume']=$v['volume'];
                if ($today==date('Ymd', $v['add_time'])) {
                    $goodslist[$k]['is_new']=1;
                } else {
                    $goodslist[$k]['is_new']=0;
                }
				
				if ($v['change_price']>0) {
				   $yushou = '定金'.intval($v['change_price']).'元';
				} 
				if ($v['mobilezk']>0) {
				   $yushou .= ' 付定金立减'.intval($v['mobilezk']).'元';
				} 
				$goodslist[$k]['yushou']=$yushou?:'';
                if (C('APP_SUB_DOMAIN_DEPLOY')) {
                    $goodslist[$k]['linkurl']=U('/item/', ['id'=>$v['num_iid']]);
                } else {
                    $goodslist[$k]['linkurl']=U('item/index', ['id'=>$v['num_iid']]);
                }
            }
        }

        $this->assign('topone', $goodslist);

        $this->display();
    }

   
}
