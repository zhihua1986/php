<?php
namespace Common\Model;
use Think\Model;
class settingModel extends Model
{
protected $fields = array('data', 'name', 'remark');
protected $pk     = 'name';
    /**
     * 获取配置信息写入缓存
     */
    public function setting_cache() {
        $setting = array();
        $res = $this->getField('name,data');
        foreach ($res as $key=>$val) {
           // $str = preg_replace_callback('#s:(\d+):"(.*?)";#s',function($match){return 's:'.strlen($match[2]).':"'.$match[2].'";';},$val);
            $setting['yh_'.$key] = unserialize($val) ? unserialize($val) : $val;
        }
        S('setting', $setting);
		//F('setting', $setting);
        return $setting;
    }

    /**
     * 后台有更新则删除缓存
     */
    protected function _before_write($data='',$option='') {
        S('setting', NULL);
		//F('setting', NULL);
    }
}