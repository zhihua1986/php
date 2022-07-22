<?php
namespace M\Action;
use Common\Model;
class BaomingAction extends BaseAction{
    public function _initialize() {
        parent::_initialize();
        $this->_cate_mod = D('itemscate');
        C('DATA_CACHE_TIME',C('yh_site_cachetime'));
    }

    public function index()
    {
        $cate_list = D('itemscate')->cate_cache();
        $this->assign('cate_list', $cate_list['p']); 
        $this->_config_seo(array(
            'title'=>'卖家报名'
            )); 
        $this->display();
    }

    public function enroll()
    {
        $content = I('post.');
        $content['num_iid'] = $this->getId($content['goods_url']);
        if(!$content['num_iid']){
            $this->ajaxReturn(0, '商品链接格式不正确');
        }
		$fromurl=$content['from'];
        unset($content['from']);
		$data=array(
		'fromurl'=>$fromurl,
		'content'=>json_encode($content),
		'tqk_uid'=>$this->tqkuid,
		'time'=>time()
		);
        $url = $this->tqkapi.'/saveenroll';
        $result = $this->_curl($url,$data);
        $this->ajaxReturn(1, '报名成功，我们将尽快和您取得联系！');
    }

    private function getId($strurl)
    {
        $preg1 = '/id=(\d+)/is';
        preg_match($preg1, $strurl, $allhtml1);
        if (! empty($allhtml1)) {
            return $allhtml1[1];
        }

        $preg2 = '/taobao.com\/i(\d+).htm/is';
        preg_match($preg2, $strurl, $allhtml2);

        if (! empty($allhtml2)) {
            return $allhtml2[1];
        }

        $preg3 = '/itemId=(\d+)/is';
        preg_match($preg3, $strurl, $allhtml3);

        if (! empty($allhtml3)) {
            return $allhtml3[1];
        }
        return false;
    }

    public function jubao()
    {
        $cate_list = D('itemscate')->cate_cache();
        $this->assign('cate_list', $cate_list['p']); 

        $this->_config_seo(array(
            'title'=>'举报商家'
            )); 
        $this->display();
    }

    public function report()
    {
        $content = I('post.');
        if($content['num_iid'] == ''){
            $this->ajaxReturn(0, '举报商品不存在');
        }

        if($content['reason'] == ''){
            $this->ajaxReturn(0, '举报内容不能为空');
        }

        $fromurl = $content['from'];
        unset($content['from']);
		$data=array(
		'from'=>$fromurl,
		'content'=>json_encode($content),
		'tqk_uid'=>$this->tqkuid,
		'time'=>time()
		);
        $url = $this->tqkapi.'/savereport';
        $result = $this->_curl($url,$data);

        $this->ajaxReturn(1, '举报成功，我们会第一时间进行处理！');
    }


}