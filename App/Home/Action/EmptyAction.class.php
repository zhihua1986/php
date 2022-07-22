<?php
namespace Home\Action;
use Think\Controller;

class EmptyAction extends BaseAction{
    public function index(){
       //跳转或加载404页
    header('HTTP/1.1 404 Moved Permanently');
   $this->display('Index/404');
    exit;
    }
}

