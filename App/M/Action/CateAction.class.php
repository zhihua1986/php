<?php
namespace M\Action;
use Common\Model;
use Common\Model\itemscateModel;
use Think\Page;
class CateAction extends BaseAction{
	public function _initialize() {
        parent::_initialize();
        $this->_mod = D('items')->cache(true, 5 * 60);
    }

    /**
     * @return void
     */
    public function  index(){
        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
        if((strpos($useragent, 'micromessenger') > 1 && strpos($useragent, 'android')>1) || strpos($useragent, 'android')>1){
            $this->assign('isweixin',true);
        }
        $ItemCate = new itemscateModel();
        $this->assign('ItemCate', $ItemCate->cate_cache());
        $cid = I('cid', 0, 'intval');
        $this->assign('cid', $cid);
        $sid = I('sid', 0, 'intval');
        $this->assign('sid', $sid);
        $sort	= I('sort');
        $this->assign('txt_sort', $sort);
        $size	= 20;
        $key    = I("k");
        $key    = urldecode($key);
        $where['ems'] = 1;
        $where['status'] = 'underway';
        if($key){
            if($this->FilterWords($key)){
                $this->_404();
            }
        }
        if($key){
            $where['title'] = array( 'like', '%' . $key . '%' );
            $this->assign('sokey', $key);
        }
        if($cid){
            $where['cate_id'] = $cid;
        }
        if ($sid) {
            $where['ali_id'] = $sid;
        }
        switch ($sort){
            case 'new':
                $order = 'id DESC';
                break;
            case 'price':
                $order = 'coupon_price ASC';
                break;
            case 'rate':
                $order = 'quan DESC';
                break;
            case 'hot':
                $order = 'volume DESC';
                break;
            default:
                $order = 'ordid desc';
        }
        $items_list = $this->_mod->where($where)->field('id,pic_url,title,num_iid,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit(0,$size)->select();
        $goodslist=[];
        if($items_list){
            $goodslist=array();
            foreach($items_list as $k=>$v){
                if($this->FilterWords($v['title'])){
                    continue;
                }
                $goodslist[$k]['id']=$v['id'];
                $goodslist[$k]['num_iid']=$v['num_iid'];
                $goodslist[$k]['pic_url']=$v['pic_url'];
                $goodslist[$k]['title']=$v['title'];
                $goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
                $goodslist[$k]['coupon_price']=$v['coupon_price'];
                $goodslist[$k]['price']=$v['price'];
                $goodslist[$k]['quan']=intval($v['quan']);
                $goodslist[$k]['shop_type']=$v['shop_type'];
                $goodslist[$k]['volume']=$v['volume'];
                $goodslist[$k]['category_id']=$v['category_id'];
                $goodslist[$k]['is_new']=0;
                if(C('APP_SUB_DOMAIN_DEPLOY')){
                    $goodslist[$k]['linkurl']=U('/item/',array('id'=>$v['num_iid']));
                }else{
                    $goodslist[$k]['linkurl']=U('item/index',array('id'=>$v['num_iid']));
                }

            }
        }

            $Datalist = $this->GetApiList($sid,$key,1,$sort,$items_list,$goodslist);

        $this->assign('list',$Datalist);
        if ($cid) {
            $cid = $sid ? $sid : $cid;
            $cateinfo=$this->_cate_mod->where('ali_id='.$cid)->field('id,name,seo_title,seo_keys,seo_desc')->find();
        }

        $seo = C('yh_seo_config.search');
        if ($key && $seo['title']) {
            $this->_config_seo($seo, [
                'key' => $key,
            ]);
        } elseif ($cateinfo) {
            $this->_config_seo([
                'cate_name' => $cateinfo['name'],
                'title' => $cateinfo['seo_title'] ? $cateinfo['seo_title'] : '' . $cateinfo['name'] . '淘宝优惠券 - '. C('yh_site_name'),
                'keywords' => $cateinfo['seo_keys'],
                'description' => $cateinfo['seo_desc']
            ]);
        } else {
            $this->_config_seo(C('yh_seo_config.cate'));
        }
        $this->display();
        if($goodslist && 12 > strlen($key) && strlen($key)>3){
            if(function_exists('opcache_invalidate')){
                $basedir = $_SERVER['DOCUMENT_ROOT'];
                $dir=$basedir.'/data/Runtime/Data/data/hotkey.php';
                $ret=opcache_invalidate($dir,TRUE);
            }
            $disable_num_iids = F('data/hotkey');
            if(!$disable_num_iids){
                $disable_num_iids = array();
            }
            if(count($disable_num_iids)>5){
                $disable_num_iids=array_slice($disable_num_iids,1,5);
            }
            if(!in_array($key, $disable_num_iids)){
                $disable_num_iids[] = $key;
            }
            F('data/hotkey',$disable_num_iids);
        }

    }

    /**
     * @return void
     */

public function catelist(){
$page	= I('page',0,'number_int');
$size	= 20;
$sid = I('sid', 0, 'intval');
$cid	= I('cid',0,'number_int');
$sort	= I('sort', 'new', 'trim');
$start = $size * $page;
$this->assign('txt_sort', $sort);
$this->assign('cid', $cid);
$key    = trimall(I("k",'','htmlspecialchars'));
$key    = urldecode($key);
$where['ems'] = 1;
$where['status'] = 'underway';
    if($key){
     $where['title'] = array( 'like', '%' . $key . '%' );
    }
    if($cid){
     $where['cate_id'] = $cid;
    }
    if ($sid) {
        $where['ali_id'] = $sid;
    }

switch ($sort){
    		case 'new':
				$order = 'id DESC';
				break;
			case 'price':
				$order = 'coupon_price ASC';
				break;
			case 'rate':
				$order = 'quan DESC';
				break;
			case 'hot':
				$order = 'volume DESC';
				break;
			default:
				$order = 'ordid desc';
}

$items_list = $this->_mod->where($where)->field('id,pic_url,title,num_iid,commission_rate,coupon_price,price,quan,shop_type,volume,add_time')->order($order)->limit($start . ',' . $size)->select();
if($items_list){
$goodslist=array();
foreach($items_list as $k=>$v){
if($this->FilterWords($v['title'])){
continue;
}
$goodslist[$k]['id']=$v['id'];
$goodslist[$k]['num_iid']=$v['num_iid'];
$goodslist[$k]['pic_url']=$v['pic_url'];
$goodslist[$k]['title']=$v['title'];
$goodslist[$k]['commission_rate']=$v['commission_rate']; //比例
$goodslist[$k]['coupon_price']=$v['coupon_price'];
$goodslist[$k]['price']=$v['price'];
$goodslist[$k]['quan']=intval($v['quan']);
$goodslist[$k]['shop_type']=$v['shop_type'];
$goodslist[$k]['volume']=$v['volume'];	
$goodslist[$k]['category_id']=$v['category_id'];
$goodslist[$k]['is_new']=0;
if(C('APP_SUB_DOMAIN_DEPLOY')){
$goodslist[$k]['linkurl']=U('/item/',array('id'=>$v['num_iid']));
}else{
$goodslist[$k]['linkurl']=U('item/index',array('id'=>$v['num_iid']));
}
	
}
}

$Datalist = $this->GetApiList($sid,$key,$page,$sort,$items_list,$goodslist);



$this->assign('list',$Datalist);
$this->display('catelist2');
}




}