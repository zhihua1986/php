<?php
namespace Admin\Action;
use Common\Model;
class UeuploadAction extends BaseAction {
	
    protected $list_relation = true;
    public function _initialize()
    {
        parent::_initialize();
        
    }
    

public function upload(){

 $ueditor_config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./Public/static/ueditor/php/config.json")), true);
        $action = $_GET['action'];
        switch ($action) {
            case 'config':
                $result = json_encode($ueditor_config);
                break;
            /* 上传图片 */
            case 'uploadimage':
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $upload = new \Think\Upload();
                $upload->maxSize = C('yh_attr_allow_size')*1024*1024;
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
                $upload->savePath =C('yh_attach_path').'article/';
                $upload->rootPath =ROOT_PATH.'/';
              //  $upload->rootPath ='./';
                $info = $upload->upload();
                if (!$info) {
                    $result = json_encode(array(
                        'state' => $upload->getError(),
                    ));
                } else {
               $url = trim(C('yh_site_url')).'/'.$info["upfile"]["savepath"] . $info["upfile"]['savename'];
                    $result = json_encode(array(
                        'url' => $url,
                        'title' => htmlspecialchars($_POST['pictitle'], ENT_QUOTES),
                        'original' => $info["upfile"]['name'],
                        'state' => 'SUCCESS'
                    ));
                }
                break;
            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }	
	
	
}

}