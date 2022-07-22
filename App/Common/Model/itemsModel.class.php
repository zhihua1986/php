<?php
namespace Common\Model;
use Think\Model;
class ItemsModel extends Model
{
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
		array('pass',1),
    );
    
protected $fields = array('id', 'ordid', 'cate_id', 'ali_id', 'zc_id', 'orig_id', 'num_iid', 'title', 'intro', 'tags', 'nick', 'change_price', 'mobilezk', 'area', 'sellerId', 'uid'
, 'uname', 'pic_url', 'pic_urls', 'price', 'link', 'click_url', 'ding', 'volume', 'commission', 'commission_rate', 'tk_commission_rate', 'coupon_type', 'coupon_price', 'coupon_rate'
, 'coupon_start_time', 'coupon_end_time', 'pass', 'status', 'fail_reason', 'shop_name', 'shop_type', 'item_url', 'ems', 'qq'
, 'mobile', 'realname', 'hits', 'isshow', 'likes', 'inventory', 'seo_title', 'seo_keys', 'seo_desc', 'add_time', 'last_rate_time', 'is_collect_comments', 'isq'
, 'quan', 'quanurl', 'quankouling', 'quanshorturl', 'Quan_condition', 'Quan_surplus', 'Quan_receive', 'tk', 'que'
, 'quan_pl', 'quan_rq', 'last_time', 'Quan_id', 'desc', 'tuisong', 'is_commend', 'up_time','item_id');
protected $pk     = 'id';

	public function GoodsList($size,$where,$order){
	$where['status'] = 'underway';
	$list=$this->cache(true, 5 * 60)->where($where)->field('id,pic_url,title,num_iid,ali_id,coupon_price,price,quan,shop_type,volume,add_time,commission_rate,cate_id')->order($order)->limit($size)->select();
	return $list;
	}

    /**
     * 发布一个商品
     * $item 商品信息
     */
    public function publish($item) {
		$result['title']			= $item['title'];
		$result['pic_url']			= $item['pic_url'];
		$result['nick']				= $item['nick'];
		$result['coupon_price']		= $item['coupon_price'];
		$result['commission_rate']	= $item['commission_rate'];
		$result['price']			= $item['price'];
		$result['volume']			= $item['volume'];
		$result['commission']		= $item['commission'];
		$result['commission_num']	= $item['commission_num'];
		$result['commission_volume']= $item['commission_volume'];
		$result['click_url']		= $item['click_url'];
		$result['shop_click_url']	= $item['shop_click_url'];
        //已经存在？
		if(!$item['coupon_rate']){
			$item['coupon_rate']	= round(($item['coupon_price']/$item['price'])*10000, 0);
		}
        if ($this->where(array('num_iid'=>$item['num_iid']))->count()) {
		   $result['msg']	= '已经添加!';
           return $result;
        }
   
		$item['pass'] = 1;
		$item['coupon_start_time']	= strtotime($item['coupon_start_time']);
		$item['coupon_end_time']	= strtotime($item['coupon_end_time']);
        $this->create($item);
        $item_id = $this->add();
        if ($item_id) {
            $result['msg'] = '采集成功!';
            return $result;
        } else {
            $result['msg'] = '添加失败!';
            return $result;
        }
    }



	/**
     * ajax发布商品
     */
    public function ajax_yh_publish($item) {
        //是否存在
        if ($this->where(array(
            'num_iid'=>$item['num_iid']
        ))->count()) {
			if($item['recid']==1){
				unset($item['recid']);
			}else{
				unset($item['recid']);
				unset($item['cate_id']);
			}
			$item['status'] ='underway';
			$item_id = $this->where(array(
			    'num_iid'=>$item['num_iid'],
			    'tuisong'=>array('neq', '1')
			))->save($item);
			if ($item_id) {
				return 0;
			} else {
				return 0;
			}
        }
		unset($item['recid']);
		$item['pass'] = 1;
		$item['status'] ='underway';
        $this->create($item);
        $item_id = $this->add();
        if ($item_id) {
            return 1;
        } else {
            return 0;
        }
    }


	/**
     * ajax发布商品
     */
    public function ajax_publish($item) {
		$result['title']			= $item['title'];
		$result['pic_url']			= $item['pic_url'];
		$result['nick']				= $item['nick'];
		$result['coupon_price']		= $item['coupon_price'];
		$result['commission_rate']	= $item['commission_rate'];
		$result['price']			= $item['price'];
		$result['volume']			= $item['volume'];
		$result['commission']		= $item['commission'];
		$result['commission_num']	= $item['commission_num'];
		$result['commission_volume']= $item['commission_volume'];
		$result['click_url']		= $item['click_url'];
		$result['shop_click_url']	= $item['shop_click_url'];
        //已经存在？
        if ($this->where(array('num_iid'=>$item['num_iid']))->count()) {
           return 0;
        }
		if(!$item['coupon_rate']){
			$item['coupon_rate']	= round(($item['coupon_price']/$item['price'])*10000, 0);
		}
       
		$item['pass'] = 1;
		$item['coupon_start_time']	= strtotime($item['coupon_start_time']);
		$item['coupon_end_time']	= strtotime($item['coupon_end_time']);
        $this->create($item);
        $item_id = $this->add();
        if ($item_id) {
            return 1;
        } else {
            return 0;
        }
    }

	/**
     * ajax发布商品
     */
    public function ajax_tb_publish($item) {
		//echo(print_r($item));
		$result['title']			= $item['title'];
		$result['pic_url']			= $item['pic_url'];
		$result['nick']				= $item['nick'];
		$result['coupon_price']		= $item['coupon_price'];
		$result['price']			= $item['price'];
		$result['ems']				= $item['ems'];
		$result['volume']			= $item['volume'];
		$result['coupon_rate']		= $item['coupon_rate'];
		$result['coupon_end_time']	= $item['coupon_end_time'];
		$result['coupon_start_time']= $item['coupon_start_time'];
		$robot_setting = F('robot_setting');
		if($robot_setting['ems'] >$item['ems']){
			return 0;
		}
        //已经存在？
        if ($this->where(array('num_iid'=>$item['num_iid']))->count()) {
           return 0;
        }
		if(!$item['coupon_rate']){
			$item['coupon_rate']	= round(($item['coupon_price']/$item['price'])*10000, 0);
		}
		$item['pass'] = 1;
        $this->create($item);
        $item_id = $this->add();
        if ($item_id) {
            return 1;
        } else {
            return 0;
        }
    }

	/**
     * ajax发布商品
     */
    public function ajax_yg_publish($item) {
		$result['title']			= $item['title'];
		$result['pic_url']			= $item['pic_url'];
		$result['nick']				= $item['nick'];
		$result['coupon_price']		= $item['coupon_price'];
		$result['price']			= $item['price'];
		$result['ems']				= $item['ems'];
		$result['volume']			= $item['volume'];
		$result['coupon_rate']		= $item['coupon_rate'];
		$result['coupon_end_time']	= $item['coupon_end_time'];
		$result['coupon_start_time']= $item['coupon_start_time'];
        //已经存在？
        if ($this->where(array('num_iid'=>$item['num_iid']))->count()) {
           return 0;
        }
		if(!$item['coupon_rate']){
			$item['coupon_rate']	= round(($item['coupon_price']/$item['price'])*10000, 0);
		}
       
		$item['pass'] = 1;
        $this->create($item);
        $item_id = $this->add();
        if ($item_id) {
            return 1;
        } else {
            return 0;
        }
    }


public function get_tags_by_title($title, $num=5){
        vendor('pscws4.pscws4', '', '.class.php');
        $pscws = new \PSCWS4();
        $pscws->set_dict(TQK_DATA_PATH . 'scws/dict.utf8.xdb');
        $pscws->set_rule(TQK_DATA_PATH . 'scws/rules.utf8.ini');
        $pscws->set_ignore(true);
        $pscws->send_text($title);
        $words = $pscws->get_tops($num);
        $pscws->close();
        $tags = array();
        foreach ($words as $val) {
            $tags[] = $val['word'];
        }
        return $tags;
    }

    /**
     *返回出售状态
     */
    public function status($status, $stime ,$etime) {
		if(!$stime || !$etime){
			return 'buy';
		}
		if('underway'!=$status){
			return 'sellout';
		}elseif($stime>time()){
			return 'wait';
		}elseif($etime<time()){
			return 'end';
		}elseif($stime<time()){
			return 'buy';
		}else{
			return 'buy';
		}
	}

	public function click_url($url,$num_iid) {
        if ($url && $num_iid) {
			 $this->where(array('num_iid'=>$num_iid))->save(array('click_url'=>$url));
            return true;
        }
    }

}