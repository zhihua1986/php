<?php
namespace Home\Action;
use Common\Model\articleModel;
class ViewAction extends BaseAction
{

    public function _initialize()
    {
        parent::_initialize();
        $this->_mod = D('article')->cache(true, 10 * 60);
        $this->_cate_mod = D('articlecate')->cache(true, 10 * 60);
        $articlecate= $this->_cate_mod->where('status=1')->field('id,name')->select();
        $this->assign('articlecate',$articlecate);
    }
	
	public function index(){
		
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
		
		$sql = "(SELECT id,title,url,urlid FROM tqk_article WHERE id = (SELECT max(id) FROM tqk_article WHERE id < ".$help['id'].")) union all (SELECT id,title,url,urlid FROM tqk_article WHERE id = (SELECT min(id) FROM tqk_article WHERE id > ".$help['id']."))";
		$article = M()->query($sql);
		$array['previous_article'] = $article[0];
		$array['next_article'] = $article[1];
		$this->assign($array);
		   
		$this->assign('sellers', $orlike);
		$ArticleMod = new articleModel();
		$toutiao = $ArticleMod->articleList(10);
		$this->assign('toutiao', $toutiao);	
		$topic=M('topic')->cache(true, 10 * 60)->where('status = 1')->field('name,url')->order('id desc')->limit(20)->select();
		$this->assign('topic',$topic); 
		$this->display('Article/read');
		
		
	}
	
	
	}