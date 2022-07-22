<?php
namespace Admin\Widget;

use Think\Controller;

class AuthWidget extends Controller
{
    protected function _initialize(){
        C('CLASSIFY_MAX_DEEP',3);
    }

    public function tree($tree,$level = 1)
    {
        $this->assign('tree', $tree);
        $this->assign('level',$level);

        $this->display('Auth/tree');
    }

    public function option($tree,$select,$self,$level = 1)
    {
        $this->assign('tree', $tree);
        $this->assign('select', $select);
        $this->assign('self', $self);
        $this->assign('level', $level);

        $this->display('Auth/option');
    }
}
