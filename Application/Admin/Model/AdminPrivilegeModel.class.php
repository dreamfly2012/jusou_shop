<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/10
 * Time: 11:18
 */

namespace Admin\Model;

use Think\Model;

class AdminPrivilegeModel extends Model{
    public function getPrivilegeByRoleId($role_id){
        $result = $this->field('privilege')->where(array('role'=>$role_id))->select();
        return $result[0]['privilege'];
    }
}