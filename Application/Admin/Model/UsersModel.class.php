<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/8
 * Time: 14:19
 */

namespace Admin\Model;

use Think\Model;

class UsersModel extends Model{
    public function checkEmailExist($email){
        $info = $this->field('email')->where(array('email'=>$email))->select();
        if($info){
            return true;
        }else{
            return false;
        }
    }


    public function getUserInfoByCode($code){
        $info = $this->where(array('code'=>$code))->select();
        if($info){
            return $info;
        }else{
            return false;
        }
    }

    public function getSalt($user_name){
        $salt = $this->field('salt')->where(array('user_name'=>$user_name))->select();
        return $salt[0]['salt'];
    }


    public function getUserInfoByUserNameContains($user_name,$order_user_ids){
        $map['user_name'] = array('like',"%".$user_name."%");
        $map['user_id']  = array('in',$order_user_ids);
        $result = $this->field('user_id')->where($map)->select();
        return $result[0]['user_id'];
    }

    public function getInfoById($user_id){
        $result = $this->where(array('user_id'=>$user_id))->select();
        return $result[0];
    }

    public function getUserNameById($user_id){
        $result = $this->field('user_name')->where(array('user_id'=>$user_id))->select();
        return $result[0]['user_name'];
    }

    public function getUserIdByEmailOrName($email,$user_name){
        $condition['email'] = $email;
        $condition['user_name'] = $user_name;
        $condition["_logic"] = 'OR';
        $result = $this->field('user_id')->where($condition)->select();
        return $result[0]['user_id'];
    }
}