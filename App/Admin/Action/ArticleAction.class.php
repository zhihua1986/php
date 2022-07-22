<?php
namespace Admin\Action;
use Common\Model;
class ArticleAction extends BaseAction{
	
    public function _initialize(){
        parent :: _initialize();
        $this -> _mod = D('article');
        $this -> _cate_mod = D('articlecate');
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
        ($time_start = I('time_start','', 'trim')) && $map['add_time'][] = array('egt', strtotime($time_start));
        ($time_end = I('time_end', '','trim')) && $map['add_time'][] = array('elt', strtotime($time_end) + (24 * 60 * 60-1));
        ($status = I('status','', 'trim')) && $map['status'] = $status;
        ($keyword = I('keyword','', 'trim')) && $map['title'] = array('like', '%' . $keyword . '%');
        $cate_id = I('cate_id','','intval');
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
		$articleId=$this->getArticleId(); 
		$this -> assign('urlid', $articleId);
    }
	
	
protected function artlink($id,$url){
	
if(C('APP_SUB_DOMAIN_DEPLOY')){
$url=trim(C('yh_headerm_html'))	.$url;
}else{
$domain=str_replace('/index.php/m','',trim(C('yh_headerm_html')));
$url=$domain.'/'.U('/m/article/read',array('id'=>$id));
}	

return $url;
}
	
public function xiongzhang(){
$id=I('id');
$act=I('act');
$suburl = I('suburl');
$apiurl=str_replace('&amp;','&',trim(C('yh_robots_key')));

if(!empty($apiurl)){

$urls=array($this->artlink($id,$suburl));

if($act && $act=='batch'){
$num=I('num');
$list=$this -> _mod ->field('id,url')->where(array('is_xz'=>0))->order('id desc')->limit($num)->select();
if($list){
$ids='';
foreach($list as $k=>$v){
$urlss[$k]=$this->artlink($v['id'],$v['url']);
$ids=$ids.$v['id'].',';
}
$urls=$urlss;
$id=substr($ids,0,-1);
}
}

$ch = curl_init();
$options =  array(
    CURLOPT_URL => $apiurl,
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => implode("\n", $urls),
    CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
);
curl_setopt_array($ch, $options);
$result = curl_exec($ch);
$result=json_decode($result,true);
if($result['success']>0){
$where['id']= array('in',$id);
$data=array(
'is_xz'=>1
);

$res=$this ->_mod->where($where)->save($data);

if($res){
$json=array(
'status'=>1,
'msg'=>'提交成功 还可以提交'.$result['remain_daily'].'条',
);	
}else{
$json=array(
'status'=>0,
'msg'=>'提交失败',
);	
}

}else{
$json=array(
'status'=>0,
'msg'=>$result['message'],
);
}

}else{
$json=array(
'status'=>0,
'msg'=>'站点设置中的百度快速收录推送接口没有填写',
);
	
	
}

exit(json_encode($json));

	
	
}
	
	
    public function _before_edit(){
        $id = I('id','','intval');
        $article = $this -> _mod -> field('id,cate_id') -> where(array('id' => $id)) -> find();
        $spid = $this -> _cate_mod -> where(array('id' => $article['cate_id'])) -> getField('spid');
        if($spid == 0){
            $spid = $article['cate_id'];
        }else{
            $spid .= $article['cate_id'];
        }
        $this -> assign('selected_ids', $spid);
    }
    public function ajax_upload_img(){
    	
	if($_FILES['img']){
	            $file = $this->_upload($_FILES['img'], 'article',$thumb = array('width'=>250,'height'=>250));
	            if($file['error']) {
	            	$this->ajaxReturn(0,$file['info']);
	            } else {
	             $data['img']=$file['mini_pic'];
				 $this->ajaxReturn(1, L('operation_success'), $data['img']);
	            }
	   		 } else {
            $this->ajaxReturn(0, L('illegal_parameters'));
        }	
				
    }
    public function ajax_gettags(){
        $title = I("title",'',"trim");
        $tag_list = d("items") -> get_tags_by_title($title);
        $tags = implode(" ", $tag_list);
        $this -> ajaxReturn(1, l("operation_success"), $tags);
    }
}
?>