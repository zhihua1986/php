<?php
namespace Common\Model;
use Think\Model;
class balanceModel extends RelationModel {
    //关联关系
    protected $_link = array(
        'user' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'user',
            'foreign_key' => 'uid',
            'mapping_fields'=>'username,money,nickname,phone,email,avatar'
        ),
    );
}