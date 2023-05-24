<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/23
 * Time: 14:09
 */

namespace Admin\Model;

use Think\Model;

class StoreMemberModel extends Model{
    protected $_validate = array(
        array('score','/^\\d+$/','积分必须是正整数！'),
    );

    public function getInfoById($id){
        $store_id = session('store_id');
        $result = $this->where(array('id'=>$id,'store_id'=>$store_id))->select();
        return $result[0];
    }

    public function deleteMemberByUserId($user_id){
        $store_id = session('store_id');
        $result = $this->where(array('user_id'=>$user_id,'store_id'=>$store_id))->delete();
        return $result;
    }

    public function deleteMemberById($id){
        $store_id = session('store_id');
        $result = $this->where(array('store_id'=>$store_id,'id'=>$id))->delete();
        return $result;
    }

    public function getInfoByUserId($user_id){
        $store_id = session('store_id');
        $result = $this->where(array('store_id'=>$store_id,'user_id'=>$user_id))->select();
        return $result;
    }
}