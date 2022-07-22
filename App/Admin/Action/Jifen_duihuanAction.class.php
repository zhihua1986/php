<?php
namespace Admin\Action;
use Common\Model;
class Jifen_duihuanAction extends BackendAction {
    public function _initialize() {

        parent::_initialize();
        $this->_name = 'jifen_duihuan';
        $this->_mod = D('jifen_duihuan');
        $this->_user_mod = D('user');
        $this->_goods_mod = D('goodsjifen');

    }

    public function _before_index() {
        $user = $this->_user_mod->field('id,nickname')->select();
        $user_list = array();
        foreach ($user as $val) {
            $user_list[$val['id']] = $val['nickname'];
        }
        $this->assign('user_list', $user_list);

        $goods = $this->_goods_mod->field('id,title')->select();
        $goods_list = array();
        foreach ($goods as $val) {
            $goods_list[$val['id']] = $val['title'];
        }
        $this->assign('goods_list', $goods_list);

    }

    protected function _search() {
        $map = array();
        ($time_start = $this->_request('time_start', 'trim')) && $map['add_time'][] = array('egt', strtotime($time_start));
        ($time_end = $this->_request('time_end', 'trim')) && $map['add_time'][] = array('elt', strtotime($time_end)+(24*60*60-1));
       
        $this->assign('search', array(
            'time_start' => $time_start,
            'time_end' => $time_end,
            ));
        return $map;
    }

    public function add() {
        if (IS_POST) {
            if (false === $data = $this->_mod->create()) {
                $this->error($this->_mod->getError());
            }
            if( !$data['cate_id']||!trim($data['cate_id']) ){
                $this->error('请选择商品分类');
            }

            $item_id = $this->_mod->add($data);

            !$item_id && $this->error(L('operation_failure'));
            $this->success(L('operation_success'));
        } else {
            $orig_list = M('items_orig')->where(array('pass'=>1))->select();
            $this->assign('orig_list',$orig_list);
            $this->display();
        }
    }

    public function ajax_upload( ){
        if(!empty( $_FILES['img']['name'])){
            $result = $this->_upload( $_FILES['img'], "item/" );
            if ( $result['error']){
                $this->error( $result['info'] );
            }else{
                $data['img'] = $result['info'][0]['savename'];
                $this->ajaxReturn( 1, L( "operation_success" ), C( "yh_attach_path" )."item/".$data['img'] );
            }
        }else{
            $this->ajaxReturn( 0, L( "illegal_parameters" ) );
        }
    }
}