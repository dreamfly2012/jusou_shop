<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/4/16
 * Time: 10:44
 */

namespace Home\Controller;

class RegisterController extends CommonController {

    //登陆页面
    public function index()
    {
        $this->display('index');
    }

    //注册处理
    public function registerHandle()
    {
        $data['user_name'] = I('post.user_name',null);
        $data['email'] = I('post.email',null);
        $data['password'] = I('post.password',null,'md5');
       

        $rules = array(
             array('user_name','require','用户名必须填写'), //默认情况下用正则进行验证
             array('password','require','密码必须填写'), 
             array('rpassword','require','确认密码必须填写'), 
             array('user_name','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
             array('email','','邮箱已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
             array('rpassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
        );

        $u = D('User');

        if(!$u->validate($rules)->create())
        {
            $this->error($u->getError());
        }

        if($user_id = $u->add($data))
        {
            session('user_id',$user_id);
            $this->success('注册成功！',U('User/index'));
        }

    }
}
?>