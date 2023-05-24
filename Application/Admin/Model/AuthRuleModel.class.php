<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/18
 * Time: 11:09
 */

namespace Admin\Model;

use Think\Model;

class AuthRuleModel extends Model{
    protected $_validate = array(
        array('name','require','权限规则不能为空'),
        array('name','','权限规则已经存在',0,'unique',1),
        array('title','require','权限名称不能为空'),
    );

    public function getAllRules(){
        $result = $this->order('title')->select();
        return $result;
    }
}