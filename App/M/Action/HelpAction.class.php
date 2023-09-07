<?php
namespace M\Action;
use Common\Model\helpModel;
class HelpAction extends BaseAction
{
	
	public function agent(){
		$this->_config_seo(array(
            'title' => '代理介绍'
        ));
		$this->display();
	}
	

    public function index()
    {
        $id = I('id');
        ! $id && $this->_404();
        $help_mod =new helpModel();
		$where = array(
		'url'=>$id
		);
        $help = $help_mod->field('id,title,info,seo_title,seo_keys,seo_desc')->where($where)->find();
        $this->_config_seo(array(
            'title' => $help['seo_title']?$help['seo_title']:$help['title'].'_'.C('yh_site_name'),
            'keywords'=>$help['seo_keys'],
            'description'=>$help['seo_desc']
        ));
        $this->assign('id', $id);
        $this->assign('help', $help); // 分类选中
        $this->display('read');
    }


}