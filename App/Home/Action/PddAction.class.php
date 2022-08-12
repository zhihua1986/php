<?php
namespace Home\Action;

use Common\Model;
use Think\Page;
use Common\Model\itemsModel;
class PddAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        /*
        当前duoId被禁止调用商品详情。封禁原因：系统检测该duoId商品详情请求查询非多多进宝商品占比过高，涉嫌爬虫或本地缓存商品信息。
        拼多多商品禁止搜索引擎访问，防止出现接口被封
        */
        if ($this->getRobot() !== false) {
            exit;
        }
    }
    public function index()
    {
        $cats = $this->PddGoodsCats();
        $this->assign('ItemCate', $cats);
        $cid = I('cid', 0, 'intval');
        $page = I('p', 1, 'intval');
        $key = I("k");
        $key = urldecode($key);
        if ($key) {
            if ($this->FilterWords($key)) {
                $this->_404();
            }
            $this->assign('k', $key);
        }
        $sort = I('sort', '12', 'intval');
        $this->assign('txt_sort', $sort);
        $this->assign('cid', $cid);
        $stype = I("stype");
        $hash = 'false';
        if ($stype == 1) {
            $hash = 'true';
            $this->assign('stype', $stype);
        }
        $data = $this->PddGoodsSearch($cid, $page, $key, $sort, '', $size = 40, $hash);
        $count = $data['count'];
        if (!$count) {
            $count = 2000;
        }
        $pager = new Page($count, 40);
        $this->assign('p', $page);
        $this->assign('page', $pager->show());
        $this->assign('total_item', $count);
        $this->assign('page_size', $size);
        $this->assign('list', $data['goodslist']);
        if ($cid) {
            $cateinfo['name'] = $cats[$cid];
        }
        $seo = C('yh_seo_config.searchduoduo');
        if ($key && $seo['title']) {
            $this->_config_seo($seo, array('key' => $key));
        } else {
            if ($cateinfo) {
                $this->_config_seo(array('cate_name' => $cateinfo['name'], 'title' => '拼多多' . $cateinfo['name'] . '优惠券 - ' . C('yh_site_name'), 'keywords' => $cateinfo['name'], 'description' => $cateinfo['name']));
            } else {
                $this->_config_seo(C('yh_seo_config.pinduoduo'));
            }
        }
        $this->display();
    }
}