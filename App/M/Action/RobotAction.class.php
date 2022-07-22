<?php
namespace M\Action;
class RobotAction extends BaseAction
{
	
public function _initialize()
    {
        parent::_initialize();

    }
	
	
	public function index(){
			
	$sysntime=F('data/sysntime');
	if((NOW_TIME-$sysntime)>300){
	if(function_exists('opcache_invalidate')){
	$basedir = $_SERVER['DOCUMENT_ROOT']; 
	$dir=$basedir.'/data/runtime/Data/data/sysntime.php';
	$ret=opcache_invalidate($dir,TRUE);	
	}
	F('data/sysntime',NOW_TIME);
	R('Home/api/tool_caiji',array(trim(C('yh_gongju'))));
	
	}
		
		
		
	}
	
	
}