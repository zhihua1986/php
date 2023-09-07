<?php
namespace M\Action;
use Common\Model;
use Think\Page;
class ArticleAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->_mod = D('article')->cache(true, 10 * 60);
        $this->_cate_mod = D('articlecate')->cache(true, 10 * 60);
    }

    /**
     * @return void
     */
    public function guide(){

        $this->_config_seo(array(
            'title' => '新手必读 - '. C('yh_site_name'),
        ));
        $ac = I('ac');
        switch ($ac){

            case 'tb':
                $this->display('tb');
                break;
            case 'jd':
                $this->display('jd');
                break;
            case 'dy':
                $this->display('dy');
                break;
            case 'pdd':
                $this->display('pdd');
                break;
            case 'vph':
                $this->display('vph');
                break;
            default:
                $this->display();
        }



    }


    /**
     * * 首页（全部）
     */
    public function index()
    {
        $articlecate= $this->_cate_mod->where('status=1')->field('id,name')->select();
        $this->assign('articlecate', $articlecate);
        $cateid = I('cateid');
        $this->assign('cateid', $cateid);

        $page	= I('p', 1, 'intval');
        $size	= 16;
        $start = $size * ($page - 1);
        $cid		= I('cateid');
        $order = 'ordid asc,id desc';
        $where['ordid'] = ['gt', 0];
        $where['status'] = '1';
        if ($cid) {
            $where['cate_id'] = $cid;
        }

        $items_list = $this->_mod->where($where)->field('title,cate_id,add_time,id,urlid,pic,info,url')->order($order)->limit($start . ',' . $size)->select();
        $count =$this->_mod->where($where)->count();
        $this->assign('total_item', $count);
        $this->assign('size', $size);
        $pager = new Page($count, $size);
        $this->assign('p', $page);
        $this->assign('page', $pager->show());
        if ($items_list) {
            $goodslist=[];
            foreach ($items_list as $k=>$v) {
                $goodslist[$k]['id']=$v['id'];
				 $goodslist[$k]['ccid']='cc_'.rand(1,99999);
                $goodslist[$k]['pic']=$v['pic'];
                $goodslist[$k]['cateid']=$v['cate_id'];
                $goodslist[$k]['catename']= $this->_cate_mod->where('id='.$v['cate_id'])->getField('name');
                $goodslist[$k]['title']=$v['title'];
                $goodslist[$k]['add_time']=date('Y-m-d', $v['add_time']);
                $goodslist[$k]['infocontent']=cut_html_str($v['info'], 80);
                if (C('APP_SUB_DOMAIN_DEPLOY') && C('URL_MODEL')==2) {
                    $goodslist[$k]['linkurl']=$v['url'];
                } else {
                    $goodslist[$k]['linkurl']=U('/m/article/read', ['id'=>$v['urlid']]);
                }
            }
            $this->assign('list', $goodslist);
        }

        if ($cateid) {
            $cateinfo=$this->_cate_mod->where('id='.$cateid)->field('id,name,seo_title,seo_keys,seo_desc')->find();
            $this->_config_seo([
                'cate_name' => $cateinfo['name'] ? $cateinfo['name'] : '优惠券头条',
                'cate_id' => $cateinfo['id'],
                'keywords' => $cateinfo['seo_keys'],
                'description' => $cateinfo['seo_desc'],
                'title' => $cateinfo['seo_title'] ? $cateinfo['seo_title'] : $cateinfo['name'].'优惠券头条- '. C('yh_site_name')
            ]);
        } else {
            $this->_config_seo(C('yh_seo_config.toutiao'));
        }

        $this->display();
    }

    public function read()
    {
        $help_mod = M('article');
        $id = I('id', '1', 'intval');
		$help = $help_mod->where(array('urlid'=>$id))->field('id,title,info,author,seo_title,url,urlid,seo_keys,seo_desc,add_time,cate_id')->find();
        !$help && $this->_404();
        $articlecate= $this->_cate_mod->where('status=1')->field('id,name')->select();
        $this->assign('articlecate', $articlecate);
        $hits_data = ['hits'=>['exp', 'hits+1']];
        $help_mod->where(['urlid'=>$id])->setField($hits_data);
        $this->_config_seo([
            'title' => $help['seo_title'] ? $help['seo_title'] : $help['title'].'_'.C('yh_site_name'),
            'keywords'=>$help['seo_keys'],
            'description'=>$help['seo_desc']
        ]);
        $help['catename']=$this->_cate_mod->where('id='.$help['cate_id'])->getField('name');
        $this->assign('info', $help);
        $orlike = D('items')->field('id,pic_url,num_iid,volume,title,coupon_price,price,quan,click_url,coupon_start_time,coupon_end_time,shop_type')->cache(true, 10 * 60)->where("title like '%" . $help['author'] . "%' ")
             ->field('id,pic_url,num_iid,title,coupon_price,price,quan,shop_type,volume,add_time')
            ->limit('0,4')
            ->order('is_commend desc,id desc')
            ->select();
        $where=[
            'cate_id'=>$help['cate_id'],
            'urlid'=>['neq', $id]
        ];
        $articlelike = $this->_mod->where($where)->field('id,title,pic,add_time,cate_id,url,urlid')->limit('0,5')->order('id desc')->select();
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
                $article[$k]['linkurl']=U('/m/article/read', ['id'=>$v['urlid']]);
            }
        }
        $this->assign('articlelike', $article);
        $this->assign('sellers', $orlike);
        $this->display('read');
    }
}
