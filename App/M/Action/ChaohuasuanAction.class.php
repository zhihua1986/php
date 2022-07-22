<?php
namespace M\Action;

class ChaohuasuanAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->_mod = D('items')->cache(true, 5 * 60);
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
        $key    = trimall(I("k", '', 'htmlspecialchars'));
        $key    = urldecode($key);
        $where['ems'] = 1;
        $where['status'] = 'underway';
        $where['cate_id'] = 28026;
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
        $items_list = $this->_mod->where($where)->field('id,pic_url,num_iid,title,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit(0, 20)->select();
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
                if (C('APP_SUB_DOMAIN_DEPLOY')) {
                    $goodslist[$k]['linkurl']=U('/item/', ['id'=>$v['num_iid']]);
                } else {
                    $goodslist[$k]['linkurl']=U('item/index', ['id'=>$v['num_iid']]);
                }
            }
        }
        $this->assign('list', $goodslist);

        if ($cid) {
            $cateinfo=$this->_cate_mod->where('id='.$cid)->field('id,name,seo_title,seo_keys,seo_desc')->find();
        }
        $this->_config_seo(C('yh_seo_config.chaohuasuan'), [
            'cate_name' => $cateinfo['name'],
            'title' => $cateinfo['seo_title'] ? $cateinfo['seo_title'] : $cateinfo['name'],
            'keywords' => $cateinfo['seo_keys'],
            'description' => $cateinfo['seo_desc'],
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
        $where['cate_id'] = 28026;
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

        $items_list = $this->_mod->where($where)->field('id,pic_url,num_iid,title,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit($start . ',' . $size)->select();
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
