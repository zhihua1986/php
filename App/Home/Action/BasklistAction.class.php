<?php
namespace Home\Action;
use Think\Page;
class BasklistAction extends BaseAction
{

    public function _initialize()
    {
        parent::_initialize();
        $this->mo = M('basklist');
        $this->assign('user', $this->visitor->get());
    }

    /**
     * * 首页（全部）
     */
    public function index()
    {
        $page   = I('p',1 ,'intval');
        $size   = 6;
        $start = $size * ($page - 1);
        $where = array(
            'status' => 1
            );
		$prefix = C('DB_PREFIX');
        $field = '*,
        (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as nickname,
        (select avatar from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as avatar,
        (select goods_title from '.$prefix.'order where '.$prefix.'order.id = '.$prefix.'basklist.orderid) as title';
        //$field = table_auto_format($field);
        $list = $this->mo->where($where)->field($field)->order('id desc')->limit($start . ',' . $size)->select();
        $count =$this->mo->where($where)->count();
        $this->assign('total_item',$count);
        $this->assign('size',$size);
        $pager = new Page($count, $size);
        $this->assign('p', $page);
        $this->assign('page', $pager->show());
        $this->assign('list', $list);

        $where = array(
            'status' => 1,
            );
        $field = 'sum(integray) as integray,
        (select avatar from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as avatar,
        (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as nickname';
        //$field = table_auto_format($field);
        $talent = $this->mo->where($where)->field($field)->group('uid')->order('integray desc')->limit(5)->select();
        $this->assign('talent',$talent);
        $this->_config_seo(array(
            'title' => '晒单列表-'.C('yh_site_name'),
            ));
			
			 $orlike = D('items')->cache(true, 10 * 60)
            ->field('id,pic_url,title,coupon_price,price,quan,shop_type,volume,add_time')
            ->limit('0,14')
            ->order('is_commend desc,id desc')
            ->select();
        
 $this->assign('sellers', $orlike);

        $this->display();
    }

    public function read()
    {
        $id = I('id', '1', 'intval');
        ! $id && $this->_404();
        $where = array(
            'id' => $id,
            'status'=>1
            );
		$prefix = C('DB_PREFIX');
        $field = '*,
        (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as nickname,
        (select avatar from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as avatar,
        (select goods_title from '.$prefix.'order where '.$prefix.'order.id = '.$prefix.'basklist.orderid) as title';
       // $field = table_auto_format($field);
        $info = $this->mo->where($where)->field($field)->find();
        if(!$info){
            $this->_404();
        }
        $this->assign('info',$info);
        $field = '*,
        (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as nickname,
        (select avatar from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as avatar,
        (select goods_title from '.$prefix.'order where '.$prefix.'order.id = '.$prefix.'basklist.orderid) as title';
       // $field = table_auto_format($field);
        $where2['id']     = array('lt',$id);
        $where2['status']     = 1;
        $max              = $this->mo->field("max(id)")->where($where2)->find();
        $where3['id']     = $max['max(id)'];
        $where3['status']     = 1;
        $previous_article = $this->mo->where($where3)->find();
        $row1 = M('order')->where("id='".$previous_article['orderid']."'")->find(); 
        if($row1){
            $previous_article['title'] = $row1['goods_title'];
        }

        $where4['id']     = array('gt',$id);
        $where4['status']     = 1;
        $min              = $this->mo->field("min(id)")->where($where4)->find();
        $where5['id']     = $min['min(id)'];
        $where5['status']     = 1;
        $next_article     = $this->mo->where($where5)->find();
        $row2 = M('order')->where("id='".$next_article['orderid']."'")->find(); 
        if($row2){
            $next_article['title'] = $row2['goods_title'];
        }   

        $where = array(
            'status' => 1,
            );
        $field = 'sum(integray) as integray,
        (select avatar from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as avatar,
        (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid) as nickname';
        //$field = table_auto_format($field);
        $talent = $this->mo->where($where)->field($field)->group('uid')->order('integray desc')->limit(5)->select();
        $this->assign('talent',$talent);
        
        $this -> assign('previous_article',$previous_article);
        $this -> assign('next_article',$next_article);
        $count = $this->mo->where("status=1")->count();
        $this->assign('count',$count);
		 $orlike = D('items')->cache(true, 10 * 60)
            ->field('id,pic_url,title,coupon_price,price,quan,shop_type,volume,add_time')
            ->limit('0,14')
            ->order('is_commend desc,id desc')
            ->select();
        
 $this->assign('sellers', $orlike);

        $this->_config_seo(array(
            'title' => '晒单详情-'.C('yh_site_name'),
            ));
        $this->display('read');
    }

    public function detail() {
        if($this->visitor->is_login == false){
            $url=U('login/index');
            redirect($url);
        }
        if(IS_POST){
            $orderid = I('orderid','trim');
            $content = I('content','trim');
            $images = I('img');
            $map['order_sn'] = $orderid; 
            $is = M('basklist')->where($map)->find();
           if($is){
               $this->ajaxReturn(0,'该订单已晒过单');
           }
           if(!$images){
            $this->ajaxReturn(0,'至少上传一张图片');
        }
        $imgArr = implode(',',$images);
        $img = $images[0];
        $where['orderid'] = $orderid; 
        $info = M('order')->where($where)->field('id')->find();
        $row = $this->mo->add(array(
            'uid'=>$this->visitor->get('id'),
            'order_sn'=>$orderid,
            'orderid'=>$info['id'],
            'content'=>$content,
            'img'=>$img,
            'images'=>$imgArr,
            'create_time'=>NOW_TIME
            ));
        if($row){
        	    M('order')->where(array('id'=>$info['id']))->save(array('bask'=>1));
            $this->ajaxReturn(1,'晒单成功，等待管理员审核...',U('basklist/index'));
        }
        $this->ajaxReturn(0,'操作失败');
    }
    $id = I('id','trim');
    $info = M('order')->where('id='.$id.'')->field('goods_title,orderid,price')->find();
    $this->assign('info',$info);
    $this->_config_seo(array(
        'title' => '我要晒单-'.C('yh_site_name'),
        ));
    $this->display();
}

public function getOrderInfo() {
    $mo = M('order');
    if(IS_POST){
        $orderid = I('orderid','trim');
        $where['orderid'] = $orderid; 
        $info = $mo->where($where)->field('goods_title')->find();
        if($info){
            $this->ajaxReturn(0,'success',$info['goods_title']);
        } else {
            $this->ajaxReturn(1,'订单不存在');
        }
    }
}

public function uploadPics(){
    if(IS_POST){
        if($_FILES['file']){
            $file = $this->_upload($_FILES['file'],'site',$thumb = array('width'=>350,'height'=>350));
            
			if($file['error']) {
	            	$this->ajaxReturn(1,$file['info']);
	         } else {
	             $data['img']=$file['mini_pic'];
				 $this->ajaxReturn(array('error'=>0,'url'=>$data['img']));
	            }
        }
    }
}

public function mylist(){
    if($this->visitor->is_login == false){
        $url=U('login/index');
        redirect($url);
    }
    $id = $this->visitor->get('id');
    $page   = I('p',1 ,'intval');
    $size   = 6;
    $start = $size * ($page - 1);
    $where = array(
        'uid' => $id
        );
	$prefix = C('DB_PREFIX');
    $field = '*,
    (select nickname from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid limit 1) as nickname,
    (select avatar from '.$prefix.'user where '.$prefix.'user.id = '.$prefix.'basklist.uid limit 1) as avatar,
    (select goods_title from '.$prefix.'order where '.$prefix.'order.id = '.$prefix.'basklist.orderid) as title,
    (select price from '.$prefix.'order where '.$prefix.'order.id = '.$prefix.'basklist.orderid) as price';
    //$field = table_auto_format($field);

    $list = $this->mo->where($where)->field($field)->order('id desc')->limit($start . ',' . $size)->select();

    $count =$this->mo->where($where)->count();
    $this->assign('total_item',$count);
    $this->assign('size',$size);
    $pager = new Page($count, $size);
     $this->assign('page', $pager->show());
    $this->assign('p', $page);

    $count1 = $this->mo->where("status=0 AND uid=".$id."")->count();
    $count2 = $this->mo->where("status=1 AND uid=".$id."")->count();
    $count3 = $this->mo->where("status=2 AND uid=".$id."")->count();
    $total_score = $this->mo->field('sum(integray) as integrays')->where('uid='.$id.' AND status=1')->select();

    $this->assign('list',$list);
    $this->assign('total_score',$total_score);
    $this->assign('count1',$count1);
    $this->assign('count2',$count2);
    $this->assign('count3',$count3);
    $this->_config_seo(array(
        'title' => '我的晒单-'.C('yh_site_name'),
        ));
    $this->display();
}

}