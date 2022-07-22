<?php
namespace Common\Model;
class ArticleModel extends RelationModel
{
	
protected $fields = array('id','cate_id','title','author','info','hits','ordid','add_time','last_time'
,'status','seo_title','seo_keys','seo_desc','tuisong','pic','is_xz','url','urlid');
protected $pk     = 'id';
	
	
    //自动完成
    protected $_auto = array(
        array('add_time', 'time', 1, 'function'),
    );
    //自动验证
    protected $_validate = array(
        array('title', 'require', '{%article_title_empty}'),
    );

    public function addtime()
    {
        return date("Y-m-d H:i:s",time());
    }
	public function hits($id){
		$this->where(array('id'=>$id))->setInc('hits',1);
	}
	
	public function articleList($size,$cid=''){
	$where = array();
	$where['ordid'] = array('gt',0);
	$where['status'] = 1;
	if($cid){
	 $where['cate_id'] = $cid;
	}
	$list = $this->cache(true, 5 * 60)->field('title,cate_id,add_time,id,pic,info,url')->where($where)->order('ordid asc,id desc')->limit($size)->select();
	if($list){
	$goodslist = array();
	 foreach($list as $k=>$v){
      $goodslist[$k]['id']=$v['id'];
      $goodslist[$k]['cateid']=$v['cate_id'];
       $goodslist[$k]['pic']=$v['pic'];
      $goodslist[$k]['title']=$v['title'];
      $goodslist[$k]['add_time']=date('Y-m-d',$v['add_time']);
      $goodslist[$k]['infocontent']=cut_html_str($v['info'],80);	
      if(C('APP_SUB_DOMAIN_DEPLOY') && C('URL_MODEL') == 2){
        $goodslist[$k]['linkurl']=$v['url'];
      }else{
        $goodslist[$k]['linkurl']=U('/article/read',array('id'=>$v['id']));
      }
}
    }
    
    
	return $goodslist;
		
	}
	
}