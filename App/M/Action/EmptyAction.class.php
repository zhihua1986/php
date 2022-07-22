<?php
namespace M\Action;
use Think\Controller;
class EmptyAction extends BaseAction{

    public function index()
    {
		header('HTTP/1.1 404 Moved Permanently');
      $this->display('Index/404');
    }
	
}

