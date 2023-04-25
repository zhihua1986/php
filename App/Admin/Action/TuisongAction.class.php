<?php
namespace Admin\Action;
use Common\Model;
class TuisongAction extends BaseAction
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 数据库备份
     */
    public function index()
    {
        $this->display();
    }  

    public function tuisong()
    {
        $items_mo = D('items');
        $article_mo = D('article');
        $url_type = I('url_type','','trim');
        $url_num = I('url_num','','trim');
		$url_cli = I('url_cli');
        $urls = array();
        $now=time();
        if($url_type == 1){
            if(C('url_model') == 1){
                $item_url = C('yh_site_url')."/index.php/item/id/";
            } else {
                $item_url = C('yh_site_url')."/item/id/";
            }
            $items_list = $items_mo->where('tuisong=0')->order('id desc')->field("num_iid,id")->limit($url_num)->select();
            $i = 0;
            if(count($items_list)>0){
                foreach ($items_list as $key => $val) {
                    $urls[$i] = $item_url. $val['num_iid'].".html,";
                    $i ++;
                }
                
                $is_ssl=stristr(C('yh_site_url'), 'http://');
                $api=trim(C('yh_zhunru'));
                $ch = curl_init();
                $options = array(
                    CURLOPT_URL => $api,
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => implode("\n", $urls),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: text/plain'
                        )
                    );

                curl_setopt_array($ch, $options);
                $result = json_decode(curl_exec($ch),true);
                if($result['success'] && $result['success'] > 0){
                    foreach ($items_list as $key => $val) {
                        $res = $items_mo->where(array('id' => $val['id']))->data(array('tuisong' => 1))->save();
                    }
                    $this->success('您已成功推送'.$result['success'].'个商品');
                } else {
                    $this->error('错误:检查站点设置电脑域名是否填写正确'.$result['error'] . $result['message']);
                }

            }else{
                $this->error('暂时没有可以推送的商品');
            }
        } else {
//            if(C('url_model') == 1){
//                $item_url = C('yh_site_url')."/index.php/article/read/id/";
//            } else {
//                $item_url = C('yh_site_url');
//            }
            $article_list = $article_mo->where('tuisong=0')->order('id desc')->field("id,urlid,url")->limit($url_num)->select();
            $i = 0;
            if(count($article_list)>0){
                foreach ($article_list as $key => $val) {

                    if (C('APP_SUB_DOMAIN_DEPLOY') && C('URL_MODEL') == 2) {
                        $urls[$i] =trim(C('yh_site_url')) . $val['url'];
                    } else {
                        $urls[$i] = trim(C('yh_site_url')) . U('/article/read', array('id' => $val['urlid']));
                    }
                    $i ++;
                }
                
                $api=trim(C('yh_zhunru'));
                $ch = curl_init();
                $options = array(
                    CURLOPT_URL => $api,
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => implode("\n", $urls),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: text/plain'
                        )
                    );

                curl_setopt_array($ch, $options);
                $result = json_decode(curl_exec($ch),true);
                if($result['success'] && $result['success'] > 0){
                    foreach ($article_list as $key => $val) {
                        $res = $article_mo->where(array('id' => $val['id']))->data(array('tuisong' => 1))->save();
                    }
                    $this->success('您已成功推送'.$result['success'].'篇文章');
                } else {
                    $this->error('错误'.$result['error'] . $result['message']);
                }

            }else{
                $this->error('暂时没有可以推送的文章');
            }
        }
    }  
}
