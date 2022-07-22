<?php
namespace Admin\Action;
use Common\Model;
class BrandAction extends BaseAction{
    public function _initialize(){
        parent :: _initialize();
        $this -> _mod = D('brand');
        $this -> _cate_mod = D('brandcate');
    }
    public function _before_index(){
        $sort = I("sort", 'ordid', 'trim');
        $order = I("order", 'DESC', 'trim');
        $res = $this -> _cate_mod -> order($sort . ' ' . $order) -> field('id,name') -> select();
        $cate_list = array();
        foreach ($res as $val){
            $cate_list[$val['id']] = $val['name'];
        }
        $this -> assign('cate_list', $cate_list);
        $p = I('p', 1, 'intval');
        $this -> assign('p', $p);

    }
    protected function _search(){
        $map = array();
        ($time_start = I('time_start', '','trim')) && $map['add_time'][] = array('egt', strtotime($time_start));
        ($time_end = I('time_end','', 'trim')) && $map['add_time'][] = array('elt', strtotime($time_end) + (24 * 60 * 60-1));
        ($status = I('status','', 'trim')) && $map['status'] = $status;
        ($keyword = I('keyword','', 'trim')) && $map['brand'] = array('like', '%' . $keyword . '%');
        $cate_id = I('cate_id','', 'intval');
        $selected_ids = '';
        if ($cate_id){
            $id_arr = $this -> _cate_mod -> get_child_ids($cate_id, true);
            $map['cate_id'] = array('IN', $id_arr);
            $spid = $this -> _cate_mod -> where(array('id' => $cate_id)) -> getField('spid');
            $selected_ids = $spid ? $spid . $cate_id : $cate_id;
        }
        $this -> assign('search', array('time_start' => $time_start, 'time_end' => $time_end, 'cate_id' => $cate_id, 'selected_ids' => $selected_ids, 'status' => $status, 'keyword' => $keyword,));
        return $map;
    }
    public function _before_add(){
        $author = $_SESSION['pp_admin']['username'];
        $this -> assign('author', $author);
        $site_name = D('setting') -> where(array('name' => 'site_name')) -> getField('data');
        $this -> assign('site_name', $site_name);
        $first_cate = $this -> _cate_mod -> field('id,name') -> where(array('pid' => 0)) -> order('ordid DESC') -> select();
        $this -> assign('first_cate', $first_cate);
    }
    public function _before_edit(){
        $id = I('id','', 'intval');
        $brand = $this -> _mod -> field('id,cate_id') -> where(array('id' => $id)) -> find();
        $spid = $this -> _cate_mod -> where(array('id' => $brand['cate_id'])) -> getField('spid');
        if($spid == 0){
            $spid = $brand['cate_id'];
        }else{
            $spid .= $brand['cate_id'];
        }
        $this -> assign('selected_ids', $spid);
    }
    public function ajax_upload_img(){
        $type = I('type', 'img', 'trim');
        if (!empty($_FILES[$type]['name'])){
            $dir = date('ym/d/');
            $result = $this -> _upload($_FILES['img'], 'brand');
            if ($result['error']){
                $this -> ajaxReturn(0, $result['info']);
            }else{
               
                $data['img'] = $result['pic_path'];
                $this -> ajaxReturn(1, L('operation_success'), C('yh_site_url').$data['img']);
            }
        }else{
            $this -> ajaxReturn(0, L('illegal_parameters'));
        }
    }
    public function ajax_gettags(){
        $title = I("title",'', "trim");
        $tag_list = d("items") -> get_tags_by_title($title);
        $tags = implode(" ", $tag_list);
        $this -> ajaxReturn(1, l("operation_success"), $tags);
    }
}
?>