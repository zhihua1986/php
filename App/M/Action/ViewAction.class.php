<?php
namespace M\Action;
class ViewAction extends BaseAction
{

    public function _initialize()
    {
      parent::_initialize();
      $this->_mod = D('article')->cache(true, 10 * 60);
      $this->_cate_mod = D('articlecate')->cache(true, 10 * 60);
    }
	
	public function index(){
		
		$id = I('id', '1', 'intval');
		$help_mod = $this->_mod;
		$hits_data = array('hits'=>array('exp','hits+1'));
		$help_mod->where(array('urlid'=>$id))->setField($hits_data);
		$help = $help_mod->where(array('urlid'=>$id))->field('id,title,info,author,seo_title,seo_keys,seo_desc,add_time,cate_id')->find();
		!$help && $this->_404();
		$articlecate= $this->_cate_mod->where('status=1')->field('id,name')->select();
		$this->assign('articlecate',$articlecate);
		$this->_config_seo(array(
		    'title' => $help['seo_title']?$help['seo_title']:$help['title'],
		    'keywords'=>$help['seo_keys'],
		    'description'=>$help['seo_desc']
		    ));
		$help['catename']=$this->_cate_mod->where('id='.$help['cate_id'])->getField('name');   
		$this->assign('info', $help); 
		$orlike = D('items')->field('id,pic_url,num_iid,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->cache(true, 10 * 60)->where("title like '%" . $help['author'] . "%' ")
		->limit('0,8')
		->order('id desc')
		->select();
		$where=[
		    'cate_id'=>$help['cate_id'],
		    'id'=>['neq', $id]
		];
		$articlelike = $this->_mod->where($where)->field('id,title,pic,add_time,cate_id,url')->limit('0,5')->order('id desc')->select();
		$article=[];
		foreach ($articlelike as $k=>$v) {
		    $article[$k]['id']=$v['id'];
		    $article[$k]['title']=$v['title'];
		    $article[$k]['pic']=$v['pic'];
		    $article[$k]['cate_id']=$v['cate_id'];
		    $article[$k]['add_time']=$v['add_time'];
		    if (C('APP_SUB_DOMAIN_DEPLOY') && C('URL_MODEL')==2) {
		        $article[$k]['linkurl']=$v['url'];
		    } else {
		        $article[$k]['linkurl']=U('/m/article/read', ['id'=>$v['id']]);
		    }
		}
		$this->assign('articlelike', $article);
		$this->assign('sellers', $orlike);
		$this->display('Article/read');
		
		
	}
	
	
	}