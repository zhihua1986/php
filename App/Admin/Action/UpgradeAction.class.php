<?php
namespace Admin\Action;
use Common\Model;
use Common\TagLib\Dir;
class UpgradeAction extends BaseAction{

	private $_tbconfig = null;
    public function _initialize( ){
        parent::_initialize( );
    }

    public function index(){
        exit();
    }

    public function version(){
        vendor("PclZip.PclZip");
       $result = $this->http("http://api.tuiquanke.cn/version/upgrade?tqk_uid=".C('yh_app_kehuduan')."&v=".TQK_RELEASE."&time=".time(),true);
       $result = json_decode($result,true);
        if($result && $result['status'] == 1){
//			if(class_exists("ZipArchive")){
				$durl = $result['url'];
				$newfname = date('YmdHis',time()).".zip";
				$width = 500;
		     $ret = $this->getFile($durl,'.',$newfname,1);	
			if(!$ret){
				$this->ajaxReturn( 0, "下载升级包失败！" );
			}		
			$archive = new \PclZip($newfname);
				if (($list = $archive->listContent()) == 0) {
			 $this->ajaxReturn( 0, "Error : ".$archive->errorInfo(true) );
           }
           foreach ($list as $k=>$v){
           	if($v['folder']===false && file_exists($v['filename'])){
           			@unlink($v['filename']);
           	}
           }
			$res =	$archive->extract(PCLZIP_OPT_REPLACE_NEWER);
			if ($res == 0) {  
			 $this->ajaxReturn( 0, "Error : ".$archive->errorInfo(true) );
			} 
			if(file_exists($newfname)){
					@unlink( $newfname );
			}
			if($res){
				if(file_exists('update.sql')){
					$this->_database_mod = M();
					$sql_str = file('update.sql');
					$sql_str = str_replace("\r", '', implode('', $sql_str));
					$ret = explode(";\n", $sql_str);
					$ret_count = count($ret);
					for ($i = 0; $i < $ret_count; $i++)
					{
						$ret[$i] = trim($ret[$i], " \r\n;"); 
						if (!empty($ret[$i]))
						{
							$this->_database_mod->execute($ret[$i]);
						}
					}
					@unlink('update.sql');
				}
				$this->clearall();
				$this->ajaxReturn( 1 ,"更新成功！");
				
				}else{
					
				$this->ajaxReturn( 0 ,"站点目录没有权限，升级包解压失败！");
					
				}
				
//				
//			}else{
//				$this->ajaxReturn( 0, "请开启支持在线更新相关类：php.ini中 php_zip.dll扩展");
//			}
		}else{
            $this->ajaxReturn( 0, $result['msg'] );
        }
    }
    
    protected function clearall(){
    	    $obj_dir = new Dir();
        is_dir(DATA_PATH . '_fields/') && $obj_dir->del(DATA_PATH . '_fields/');
        is_dir(TEMP_PATH) && $obj_dir->delDir(TEMP_PATH);
		is_dir(CACHE_PATH) && $obj_dir->delDir(CACHE_PATH);
		is_dir(DATA_PATH) && $obj_dir->del(DATA_PATH);
		is_dir(LOG_PATH) && $obj_dir->delDir(LOG_PATH);
		is_dir(RUNTIME_PATH) && $obj_dir->del(RUNTIME_PATH);
		if(C('DATA_CACHE_TYPE') == 'Memcached'){
		$handler = new \Memcached();
		$handler->addServer(C('MEMCACHED_HOST'), C('MEMCACHED_PORT'));
		$handler->flush(2);
		}
    }
    

    public function http($url,$c = false){
        set_time_limit(0);
        if($c == false){
	        $result = file_get_contents($url);
        }else{
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
        }
        if(empty($result)){
            $result = false;
        }
        return $result;
    }
    

protected function getFile($url, $save_dir = '', $filename = '', $type = 0) {  
    if (trim($url) == '') {  
        return false;  
    }  
    if (trim($save_dir) == ''){
        $save_dir = './';  
    }  
    if (0 !== strrpos($save_dir, '/')) {  
        $save_dir.= '/';  
    }  
    if($type){
        $ch = curl_init();  
        $timeout = 5;  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);  
        $content = curl_exec($ch);  
        curl_close($ch);  
    }else{ 
        ob_start();  
        readfile($url);  
        $content = ob_get_contents();  
        ob_end_clean();  
    }  
    $size = strlen($content);  
    $fp2 = @fopen($save_dir . $filename, 'a');  
    fwrite($fp2, $content);  
    fclose($fp2);  
    unset($content, $url);  
    return array(  
        'file_name' => $filename,  
        'save_path' => $save_dir . $filename,  
        'file_size' => $size  
    );  
} 

    
}
?>
