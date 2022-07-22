<?php
namespace Home\Action;

use Think\Page;
use Common\Model\jditemsModel;

class JdAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $mod = new jditemsModel();
        $this->_mod = $mod ->cache(true, 5 * 60);
    }

    public function index()
    {
        $this->assign('ItemCate', $this->_mod->Jdcate());
        $cid		= I('cid', 0, 'intval');
        $sort	= I('sort', 'new', 'trim');
        $page	= I('p', 1, 'intval');
        $this->assign('txt_sort', $sort);
        $this->assign('cid', $cid);
        $start = $size * ($page - 1);
        $key    = I("k");
        $key    = urldecode($key);
        if ($key) {
            if ($this->FilterWords($key)) {
                $this->_404();
            }
            $where['title'] = ['like', '%' . $key . '%'];
            $this->assign('k', $key);
            $url='https://suggest.taobao.com/sug?code=utf-8&q='.$key;
            $rest=$this->_curl($url);
            if ($rest) {
                $likekey=json_decode($rest, true);
                $this->assign('likekey', $likekey['result']);
            }
        }
        $stype    = I("stype");
        if ($stype) {
            $where['item_type'] = $stype;
            $this->assign('stype', $stype);
        }

        $pop   = I("pop");
        if ($pop == 1) {
            $where['owner'] = 'g';
            $this->assign('pop', $pop);
        }

        if ($cid) {
            $where['cate_id'] = $cid;
        }
        switch ($sort) {
            case 'new':
                $order = 'id DESC';
                break;
            case 'price':
                $order = 'coupon_price asc';
                break;
            case 'rate':
                $order = 'quan desc';
           $where['quan'] = ['gt', 0];
                break;
            case 'hot':
                $order = 'comments DESC';
                break;
            default:
                $order = 'id desc';
}
        $size = 40;
        $categoryid = I('gid');
        if (is_numeric($categoryid)) {
            $categoryid	= $categoryid;
            $this->assign('gid', $categoryid);
        }

        $data = $this->JdGoodsList($size, $where, $order, $page, true, $key, $categoryid);
        $count =$data['total'];
        $pager = new Page($count, $size);
        $this->assign('p', $page);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this -> assign('page_size', $size);
        $this->assign('list', $data['goodslist']);

        if ($cid) {
            $cateinfo=$this->_mod->Jdcate($cid);
        }
        $seo = C('yh_seo_config.searchjd');
        if ($key && $seo['title']) {
            $this->_config_seo($seo, [
                'key' => $key,
            ]);
        } elseif ($cateinfo) {
            $this->_config_seo([
                'cate_name' => $cateinfo,
                'title' => '京东'.$cateinfo.'优惠券 - '. C('yh_site_name'),
                'keywords' => $cateinfo,
                'description' => $cateinfo
            ]);
        } else {
            $this->_config_seo(C('yh_seo_config.jindong'));
        }

        $this->display();
    }
}
