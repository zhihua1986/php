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

        if($key && !$this->memberinfo['id']){
            echo('<script> alert("请登录后再搜索！");window.history.back()</script>');
            exit;
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

        $file = 'pdd_list'.md5($key.$cid.$page.$sort.$stype);
        if (false === $data = S($file)) {

            $data = $this->PddGoodsSearch($cid, $page, $key, $sort, '', $size = 40, $hash);
            if($data['res']){
                $back = $_SERVER["HTTP_REFERER"];
                if ($back) {
                    $url = U('auth/pdd',array('back'=>urlencode(urlencode($back)),'ac'=>urlencode(urlencode($data['res']))));
                    redirect($url);
                }
            }

            S($file,$data);
        }else{
            $data = S($file);
        }
        $this->assign('search_id', $data['search_id']);
        $this->assign('list_id', $data['list_id']);
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


    public function catelist(){

        $page = I('p', 1, 'intval');
        if($key && !$this->memberinfo['id']){
            echo 1;
            exit;
        }
        if($page>1 && !$this->memberinfo['id']){
            echo 1;
            exit;
        }
        $cid = I('cid', 0, 'intval');
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
        $lid = I('lid');

        $file = 'pdd_list'.md5($key.$lid.$cid.$page.$sort.$stype);
        if (false === $data = S($file)) {
            $data = $this->PddGoodsSearch($cid, $page, $key, $sort, '', $size = 40, $hash);
            S($file,$data);
        }else{
            $data = S($file);
        }

        $this->assign('list',$data['goodslist']);

        $this->display('catelist');


    }




}