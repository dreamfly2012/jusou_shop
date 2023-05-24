<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/4/16
 * Time: 15:27
 */
namespace Home\Model;
use Think\Model;

class UserModel extends CommonModel
{
    protected $_validate = array(
        array('user_name','require','用户名不能为空'),
        array('user_name', '/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/' , '用户名格式错误' , 1 , 'regex' ,1),
        array('password','require','密码不能为空'),
    );

    public function  __construct()
    {
        parent::__construct();
    }

    public function getInfoByUserId($user_id)
    {
        $result = $this->where(array('user_id'=>$user_id))->select();
        return $result[0];
    }

    public function checkLogin($condition)
    {
        $result = $this->where($condition)->select();
        return $result[0];
    }

    public function getInfoByEmail($email)
    {
        $result = $this->where(array('email'=>$email))->select();
        return $result[0];
    }

}