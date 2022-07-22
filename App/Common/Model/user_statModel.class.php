<?php
namespace Common\Model;
use Think\Model;

class user_statModel extends Model
{
    protected $_auto = array (array('last_time','time',3,'function'));
}