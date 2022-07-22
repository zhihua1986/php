<?php
namespace Common\Model;
class adModel extends RelationModel {
	
	protected $fields = array('id','name','url','img','add_time','ordid','status','type','beginTime','endTime');
protected $pk     = 'id';
	
	
	
    //关联关系
    protected $_link = array(
        'adbord' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'adboard',
            'foreign_key' => 'board_id',
        ),
    );
}