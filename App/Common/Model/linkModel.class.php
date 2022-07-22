<?php
namespace Common\Model;
use Think\Model;
class linkModel extends RelationModel
{
 protected $fields = array('id','name','img','url','ordid','status');
protected $pk     = 'id';

    /**
     * 检测链接名称是否存在
     *
     * @param string $name
     * @param int $pid
     * @return bool
     */
    public function name_exists($name, $id=0)
    {
        $pk = $this->getPk();
        $where = "name='" . $name . "'  AND ". $pk ."<>'" . $id . "'";
        $result = $this->where($where)->count($pk);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}