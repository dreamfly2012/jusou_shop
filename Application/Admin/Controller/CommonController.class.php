<?php

namespace Admin\Controller;

use Think\Controller;

class CommonController extends Controller
{
    protected $store_id;
    //TODO:权限验证列表需要完善，暂时按照控制器进行权限控制，同一类操作放到一个控制器中，比如，商品添加，修改，删除等。
    //但是需要控制每个商店的权限，只能对自己的商店进行修改等。
    //这里需要一个超级管理员，可以对所有商品进行管理。
    public function _initialize () {
        header("Content-type: text/html; charset=utf-8");
        $AUTH = new \Think\Auth();

        //$this->store_id = I('request.store_id',1,'intval');
        session('store_id',1);

        //类库位置应该位于ThinkPHP\Library\Think\
        if(in_array(session('uid'),C('AUTH_CONFIG.ADMINISTRATOR'))){
            return true;
        }else if(MODULE_NAME.'/'.CONTROLLER_NAME == 'Admin/Login'){
            return true;
        }else if(!$AUTH->check(MODULE_NAME.'/'.CONTROLLER_NAME, session('uid'))){
            $this->redirect('Login/login',array(),3,'没有权限操作！');
        }
    }


   /**
     * 获得当前格林威治时间的时间戳
     *
     * @return  integer
     */
    protected function gmtime()
    {
        return (time() - date('Z'));
    }

    protected function checkLogin()
    {
        if (is_null(session('store_id'))) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $password
     * @param $salt
     * @return string
     * 对密码进行加盐加密处理
     * 返回加盐值
     */
    protected function encryptionPassword($password,$salt="")
    {
        if(C('PASSWORD_IS_SALT')){
            $salt = rand(11111, 99999);
            return array('password'=>md5($password . $salt),'salt'=>$salt);
        }else{
            return array('password'=>md5($password),'salt'=>"");
        }
    }

    /**
     * @param $password
     * @param $user_id
     * 对密码进行重新加密加盐
     */
    protected function compilePassword($password, $user_id)
    {
        $au = D('AdminUser');
        $rand = rand(11111, 99999);
        $data["salt"] = $rand;
        $data['password'] = $this->encryptionPassword($password, $data["salt"]);
        $au->where(array('user_id' => $user_id))->save($data);
    }

    /**
     * @param $email
     * @return string
     *  对邮件进行加密
     *  返回邮件激活码
     */
    public function encryptionEmail($email){
        $rand = rand(1111,9999);
        return array('email'=>md5($email.$rand),'rand'=>$rand);
    }

}