<?php
namespace Admin\Action;

use Common\Action\BackendAction;

class BaseAction extends BackendAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->tqkuid=C('yh_app_kehuduan');
        if ($this->getRobot()!==false) {
            header('HTTP/1.1 404 Not Found');
            exit;
        }
    }

    protected function getopenid($appkey)
    {
        $this->tqkuid=C('yh_app_kehuduan');
        if (!$this->tqkuid && $appkey) {
            $map=[
                'appkey'=>$appkey
            ];
            $apiurl='http://api.tuiquanke.cn/getappkey';
            $result=$this->_curl($apiurl, $map);
            $result=json_decode($result, true);
            if ($result && $result['status']==1) {
                $mod=M('setting');
                $where=[
                    'name'=>'app_kehuduan'
                ];
                $datat=[
                    'data'=>$result['tqkuid']
                ];
                $mod->where($where)->save($datat);
                $this->tqkuid=$result['tqkuid'];
            } else {
                $this->error('无法获取数据，请联系推券客客服。');
            }
        }
    }
	
	

	
	
}
