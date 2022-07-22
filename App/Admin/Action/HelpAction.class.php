<?php
namespace Admin\Action;
use Common\Model;
class HelpAction extends BaseAction
{
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('help');
        $this->_cate_mod = D('articlecate');
    }

    public function _before_index() {
        $p = I('p','1','intval');
        $this->assign('p',$p);
		
		$cate = array(
		'aboutus'=>'关于我们',
		'contactus'=>'联系我们',
		'disclaimer'=>'免责声明',
		'pc'=>'新手帮助(电脑站)',
		'wap'=>'新手帮助(手机站)',
		'wx'=>'新手帮助(微信小程序)',
		'bd'=>'新手帮助(百度小程序)'
		);
		
		$this->assign('cate',$cate);

        //默认排序
        $this->sort = 'id';
        $this->order = 'ASC';
    }


	/**
     * 编辑
     */
    public function edit() {
        $help_mod = D('help');
        if (IS_POST) {
            if (false === $data = $help_mod->create()) {
                $this->error($help_mod->getError());
            }
            
          if(!empty($data['info'])){
            	$data['info']= stripslashes(htmlspecialchars_decode($data['info']));
			}
            
            if (!$help_mod->where(array('id'=>$data['id']))->count()) {
                $help_mod->add($data);
            } else {
				$data['last_time'] = time();
                $help_mod->save($data);
            }
            $this->success(L('operation_success'), U('help/index'));
        } else {
            $id = I('id','','intval');
            $info = $help_mod->where(array('id'=>$id))->find();
            $this->assign('info', $info);
            $this->display();
        }
    }


}