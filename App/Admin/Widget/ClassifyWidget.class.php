<?php
namespace Admin\Widget;

use Think\Controller;

class ClassifyWidget extends Controller
{
    public function tree($tree,$level = 1, $deep = 4)
    {
        if(!C('CLASSIFY_MAX_DEEP')){
            C('CLASSIFY_MAX_DEEP',$deep);
        }
        $this->assign('tree', $tree);
        $this->assign('level',$level);

        $this->display('Classify/tree');
    }

    public function option($tree,$select,$self,$level = 1, $deep = 4)
    {
        if(!C('CLASSIFY_MAX_DEEP')){
            C('CLASSIFY_MAX_DEEP',$deep);
        }
        $this->assign('tree', $tree);
        $this->assign('select', $select);
        $this->assign('self', $self);
        $this->assign('level', $level);

        $this->display('Classify/option');
    }
}
