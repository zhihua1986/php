<?php
namespace Admin\Action;
use Common\Model;
class SpreadAction extends BaseAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('items');
        $this->_cate_mod = D('itemscate'); 
    }
    public function _empty(){
    	$this->index();
    }

    public function _before_index() {
		$search[cate_id]=$this->_get("cate_id", 'trim');
		$search[price_min]=$this->_get("price_min", 'trim');
		$search[price_max]=$this->_get("price_max", 'trim');
		$search[keyword]=$this->_get("keyword");
		if ($search[cate_id]) {
			$map['cate_id'] = $search[cate_id];
		}
		if ($search[price_min] and $search[price_max]) {
			$map['coupon_price'] = array(array('gt',$search[price_min]),array('lt',$search[price_max]));
		}
		if ($search[keyword]) {
			$map['title|tags|num_iid|id'] = array( 'like', '%' . $search[keyword] . '%' );
		}
		$map['pass'] = 1 ;
		$map['coupon_end_time'] = array('egt', time());
		$p = $this->_get('p', 'trim');
		if ($p<=1) {$p=0;}else{$p=($p-1)*100;}
        $items = $this->_mod->where($map)->order('id DESC')->limit("$p,100")->select();
		if ($this->_get("sort", 'trim')) {
			$sort = $this->_get("sort", 'trim');
		} else {
			$sort = 'id';
		}
		if ($this->_get("order", 'trim')) {
			$order = $this->_get("order", 'trim');
		} else {
			$order = 'DESC';
		}
		$count = $this->_mod->where($map)->count('id');
		$pager = new Page($count, 100);
		$select = $this->_mod->where($map)->order('id DESC');
		$select->order($sort . ' ' . $order);
		$select->limit($pager->firstRow.','.$pager->listRows);
		$page = $pager->show();//echo $page;
		$this->assign('page11', $page);
        $this->assign('items_list', $items);
        $this->assign('search', $search);
    }
	
	    public function img() {
		$img = $this->_get('img');
		$id = $this->_get('id');
		file_put_contents("./static/item/".($id*5-2).".jpg",file_get_contents($img));
		echo json_encode(array("img"=>"/static/item/".($id*5-2).".jpg"));
    }
}