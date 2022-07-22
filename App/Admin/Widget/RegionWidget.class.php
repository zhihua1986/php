<?php
namespace Admin\Widget;

use Think\Controller;

class RegionWidget extends Controller
{
    public function option($tree, $select, $self, $level= 1)
    {
        $this->assign('tree', $tree);
        $this->assign('select', $select);
        $this->assign('self', $self);
        $this->assign('level', $level);
        
        $this->display('Region/option');
    }
}
