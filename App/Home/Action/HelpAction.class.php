<?php
namespace Home\Action;
class HelpAction extends BaseAction
{

    public function index()
    {
        $id = I('id');
        ! $id && $this->_404();
        $help_mod = M('help');
		$where = array(
		'url'=>$id
		);
        $help = $help_mod->field('id,title,info')->where($where)->find();
        // $helps = $help_mod->field('id,title,url')->select();
        $this->_config_seo(array(
            'title' => $help['title']
        ));
        // $this->assign('helps', $helps);
        $this->assign('id', $id);
        $this->assign('help', $help); // 分类选中
        $this->display('read');
    }


}