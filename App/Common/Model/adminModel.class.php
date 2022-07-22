<?php
namespace Common\Model;
use Think\Model;
class AdminModel extends RelationModel
{
	
protected $fields = array('id','username','password','role_id','last_ip','last_time','email','status');
protected $pk     = 'id';
	
	
    protected $_validate = array(
        array('username', 'require', '{%admin_username_empty}'), //不能为空
        array('username', '', '{%admin_name_exists}', 0, 'unique', 1), //新增的时候检测重复
    );

    protected $_link = array(
        //关联角色
        'role' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'adminrole',
            'foreign_key' => 'role_id',
        )
    );

    /*
     * 检测名称是否存在
     *
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function name_exists($name, $id=0) {
        $pk = $this->getPk();
        $where = "username='" . $name . "'  AND ". $pk ."<>'" . $id . "'";
        $result = $this->where($where)->count($pk);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}