<?php
namespace Home\Action;
use Think\Page;
use Common\Model\articleModel;
class ArticleAction extends BaseAction
{

    public function _initialize()
    {
        parent::_initialize();
        $this->_mod = D('article')->cache(true, 10 * 60);
        $this->_cate_mod = D('articlecate')->cache(true, 10 * 60);
        $articlecate= $this->_cate_mod->where('status=1')->field('id,name')->select();
        $this->assign('articlecate',$articlecate);
    }

    public function index()
    {
       $cateid = I('cateid'); 
       $this->assign('cateid',$cateid);
       
       $page	= I('p',1 ,'intval');
       $size	= 10;
       $start = $size * ($page - 1);
       $cid		= I('cateid','','trim');
       $order = 'ordid asc,id desc';
       $where['ordid'] = array('gt',0);
       $where['status'] = '1';
       if($cid){
           $where['cate_id'] = $cid;
       }
       $prefix = C(DB_PREFIX);
       $items_list = $this->_mod->where($where)->field('title,cate_id,add_time,id,pic,url,urlid,info,(select name from '.$prefix.'articlecate where '.$prefix.'articlecate.id = '.$prefix.'article.cate_id) as catename')->order($order)->limit($start . ',' . $size)->select();	
       $count =$this->_mod->where($where)->count('1');
       $this->assign('total_item',$count);
       $this->assign('size',$size);
       $pager = new Page($count, $size);
       $this->assign('p', $page);
       $this->assign('page', $pager->show());
       if($items_list){
        $goodslist=array();
        foreach($items_list as $k=>$v){
            $goodslist[$k]['id']=$v['id'];
			$goodslist[$k]['ccid']='cc_'.rand(1,888888);
            $goodslist[$k]['pic']=$v['pic'];
            $goodslist[$k]['cateid']=$v['cate_id'];
            $goodslist[$k]['catename']= $v['catename'];
            $goodslist[$k]['title']=$v['title'];
            $goodslist[$k]['add_time']=date('Y-m-d',$v['add_time']);
            $goodslist[$k]['infocontent']=cut_html_str($v['info'],80);	
            if(C('APP_SUB_DOMAIN_DEPLOY') && C('URL_MODEL')==2){
                $goodslist[$k]['linkurl']=$v['url'];
            }else{
                $goodslist[$k]['linkurl']=U('/article/read',array('id'=>$v['urlid']));
            }
            
        }

        $orlike = D('items')->cache(true, 10 * 60)
        ->field('id,pic_url,num_iid,title,coupon_price,price,quan,shop_type,volume,add_time')
        ->limit('0,14')
        ->order('id desc')
        ->select();
        
        $this->assign('sellers', $orlike);


        $this->assign('list',$goodslist);

    }
    
if($cateid){
 $cateinfo=$this->_cate_mod->where('id='.$cateid)->field('id,name,seo_title,seo_keys,seo_desc')->find();
 $this->assign('catename',$cateinfo['name']);
 $this->_config_seo(array(
		    'cate_name' => $cateinfo['name']?$cateinfo['name']:'优惠券头条',
		    'cate_id' => $cateinfo['id'],
            'keywords' => $cateinfo['seo_keys'],
            'description' => $cateinfo['seo_desc'],
            'title' => $cateinfo['seo_title']?$cateinfo['seo_title']:$cateinfo['name'].'优惠券头条- '. C('yh_site_name')
        ));
}else{
	$this->_config_seo(C('yh_seo_config.toutiao'));
}
        
    $this->display();
}

public function read()
{
    $id = I('id', '1', 'intval');
    $help_mod = $this->_mod;
    $hits_data = array('hits'=>array('exp','hits+1'));
    $help_mod->where(array('urlid'=>$id))->setField($hits_data);
	$help = $help_mod->where(array('urlid'=>$id))->field('id,title,info,author,seo_title,url,seo_keys,seo_desc,add_time,cate_id')->find();
	!$help && $this->_404();
	$Replace = A("Replace");
	$info= $Replace ->content($help['info']); 
	$help['info'] =$info;
    $this->_config_seo(array(
        'title' => $help['seo_title']?$help['seo_title']:$help['title'].'_'.C('yh_site_name'),
        'keywords'=>$help['seo_keys'],
        'description'=>$help['seo_desc']
        ));
    $help['catename']=$this->_cate_mod->where('id='.$help['cate_id'])->getField('name');   
    $this->assign('info', $help); 
    $orlike = D('items')->field('id,pic_url,num_iid,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->cache(true, 10 * 60)->where("title like '%" . $help['author'] . "%' ")
    ->limit('0,8')
    ->order('id desc')
    ->select();
	
	$sql = "(SELECT id,title,url,urlid FROM tqk_article WHERE id = (SELECT max(id) FROM tqk_article WHERE id < ".$help['id'].")) union all (SELECT id,title,url,urlid FROM tqk_article WHERE id = (SELECT min(id) FROM tqk_article WHERE id > ".$help['id']."))";
	$article = M()->query($sql);
	$array['previous_article'] = $article[0];
	$array['next_article'] = $article[1];
	$this->assign($array);
	$ArticleMod = new articleModel();
	$toutiao = $ArticleMod->articleList(10);
	$this->assign('toutiao', $toutiao);	
	
	$topic=M('topic')->cache(true, 10 * 60)->where('status = 1')->field('name,url')->order('id desc')->limit(20)->select();
	$this->assign('topic',$topic); 
	
    $this->assign('sellers', $orlike);
    $this->display('read');
}


}