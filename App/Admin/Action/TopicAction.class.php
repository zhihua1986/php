<?php
namespace Admin\Action;
use Common\Model;
class TopicAction extends BaseAction{
	
    public function _initialize(){
        parent :: _initialize();
        $this -> _mod = D('topic');
    }

	
	protected function _search(){
        $map = array();
        ($keyword = I('keyword','', 'trim')) && $map['name'] = array('like', '%' . $keyword . '%');
        return $map;
    }
	
	    public function _before_add(){
		$author=$this->getArticleId(); //生成唯一的urlid
        $this -> assign('urlid', $author);
    }

	
    public function ajax_upload_img(){
    	
	if($_FILES['img']){
	            $file = $this->_upload($_FILES['img'], 'article',$thumb = array('width'=>200,'height'=>200));
	            if($file['error']) {
	            	$this->ajaxReturn(0,$file['info']);
	            } else {
	             $data['img']=$file['mini_pic'];
				 $this->ajaxReturn(1, L('operation_success'),  C('yh_site_url').$data['img']);
	            }
	   		 } else {
            $this->ajaxReturn(0, L('illegal_parameters'));
        }	
				
    }
	
	
public function xiongzhang(){
$id=I('id');
$apiurl=str_replace('&amp;','&',trim(C('yh_robots_key')));
$url=trim(C('yh_headerm_html'))	.'/topics/'.$id;
$urls=array($url);
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
if($result['success_realtime']==1){
$where=array(
'urlid'=>''.$id.''
);
$data=array(
'is_xz'=>1
);
$res=M('topic')->where($where)->save($data);
if($res){
$json=array(
'status'=>1,
'msg'=>'提交成功 还可以提交'.$result['remain_realtime'].'条',
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
'msg'=>$result,
);
}


exit(json_encode($json));

	
	
}
	
    public function ajax_gettags(){
        $title = I("title",'',"trim");
        $tag_list = d("items") -> get_tags_by_title($title);
        $tags = implode(" ", $tag_list);
        $this -> ajaxReturn(1, l("operation_success"), $tags);
    }
}
?>