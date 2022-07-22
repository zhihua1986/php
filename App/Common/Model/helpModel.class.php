<?php
namespace Common\Model;
use Think\Model;
class helpModel extends RelationModel{
	
protected $fields = array('id','title','info','seo_title','seo_keys','seo_desc','last_time','url');
protected $pk     = 'id';
	
    //自动完成
    protected $_auto = array(
        array('last_time', 'time', 1, 'function'),
    );
    //自动验证
    protected $_validate = array(
        array('title', 'require', '{%article_title_empty}'),
    );
}