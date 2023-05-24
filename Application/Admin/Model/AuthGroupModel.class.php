<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/18
 * Time: 10:11
 */

namespace Admin\Model;

use Think\Model;

class AuthGroupModel extends Model{
    protected $_validate = array(
        array('title','require','权限组名称不能为空'),
        array('title','','权限组名称重复',0,'unique',1),
    );

    public function getAuthGroupInfo(){
        $result = $this->select();
        return $result;
    }

    public function getRulesById($id){
        $result = $this->field('rules')->where(array('id'=>$id))->select();
        return $result[0]['rules'];
    }
}