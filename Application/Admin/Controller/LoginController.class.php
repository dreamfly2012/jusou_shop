<?php

namespace Admin\Controller;

use Think\Controller;

class LoginController extends CommonController
{
    public function loginHandle()
    {
        $au = D('AdminUser');

        $data['user_name'] = I('post.user_name', null);

        $salt = $au->getSalt($data['user_name']);

        $password = I('post.password', null);

        $data['password'] = md5($password, $salt);

        $res = $au->where($data)->field('user_id,store_id')->select();

        if (is_null($res)) {
            $this->error('用户名密码错误');
        } else {
            $user_id = $res[0]['user_id'];
            $store_id = $res[0]['store_id'];

            session('uid', $user_id);
            session('store_id', $store_id);

            $this->redirect('Goods/goodsList');
        }
    }

    public function logout()
    {
        session('uid', null);
        session('store_id');
        $this->redirect('Login/login');
    }


    public function findPassword()
    {
        $this->display('Login/findpassword');
    }

    public function findPasswordHandle()
    {
        //TODO: 邮件目前统一由管理员来发送
        Vendor('phpMailer.class#phpmailer');
        Vendor('phpMailer.class#smtp');
        $au = D('AdminUser');
        $email = I('request.email');

        if (!$au->checkEmailExist($email)) {
            $this->error('邮箱错误！');
        }

        $data["user_id"] = $au->getUserIdByEmail($email);
        $encryption = $this->encryptionEmail($email);
        $data["code"] = $encryption["email"];
        $data["code_time"] = time();
        $data["email"] = $email;

        if (!$au->save($data)) {
            $this->error($au->getError());//保存验证code信息错误
        }

        $mail = new \PHPMailer(); //new一个PHPMailer对象出来

        $body = $this->fetch('Mail/findpassword'); //对邮件内容进行必要的过滤

        $body = str_replace('#code#', $data["code"], $body);
        $body = str_replace('#server#', 'http://' . $_SERVER['HTTP_HOST'], $body);
        $body = str_replace('#time#', $data["code_time"], $body);

        $mail->CharSet = "UTF8";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP(); // 设定使用SMTP服务
        //$mail->SMTPDebug  = 1;                     // 启用SMTP调试功能
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true;                  // 启用 SMTP 验证功能
        $mail->SMTPSecure = "ssl";                 // 安全协议，可以注释掉
        $mail->Host = C('MAIL_SMTP');      // SMTP 服务器
        $mail->Port = C('MAIL_PORT');                    // SMTP服务器的端口号
        $mail->Username = C('MAIL_LOGINNAME');   // SMTP服务器用户名
        $mail->Password = C('MAIL_PASSWORD');             // SMTP服务器密码
        $mail->SetFrom(C('MAIL_ADDRESS'), C('MAIL_NICKNAME'));
        $mail->AddReplyTo(C('MAIL_ADDRESS'), C('MAIL_NICKNAME'));
        $mail->Subject = '忘记密码';
        $mail->AltBody = '请换用兼容的邮件查看器阅读'; // optional, comment out and test
        $mail->MsgHTML($body);
        $mail->AddAddress($email);
        //$mail->AddAttachment("images/phpmailer.gif");      // attachment
        //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
        if (!$mail->Send()) {
            $this->error($mail->ErrorInfo);
        } else {
            $this->success("修改密码邮件发送成功，请注意查收！");
        }
    }

    public function forgetPassword($code)
    {
        $this->assign('code', $code);
        $this->display('Login/forgetpassword');
    }

    public function forgetPasswordHandle()
    {
        $rules = array(
            array('password', 'require', '新密码不能为空'),
            array('rpassword', 'require', '确认密码不能为空'),
            array('password', 'rpassword', '两次密码不一致', 0, 'confirm'),
        );

        $au = D('AdminUser');

        if (!$au->field('password,rpassword')->validate($rules)->create())
            $this->error($au->getError());

        $key = I('post.code');

        if ($key) {
            $bool = $au->checkActivationCode($key);
            if ($bool) {
                $encryption = $this->encryptionPassword(I('post.password'));

                $map["password"] = $encryption['password'];
                $map["salt"] = $encryption['salt'];

                $au->where(array('code' => $key))->save($map);
                //显示找回密码后，跳转到登录页面
                $this->redirect('Login/login', array(), 3, '成功找回密码！');
            } else {
                $this->error('非法的验证链接');
            }
        } else {
            $this->error('非法的验证链接');
        }
    }

    public function findPasswordEmail()
    {
        $code = I('request.code', null);
        $time = I('request.time', null);
        if (is_null($code) || is_null($time)) {
            $this->show('非法的参数链接!');
        } else {
            $end_time = intval($time + 60 * 60 * 24);
            $now_time = intval(time());
            if ($now_time > $end_time) {
                $this->show('此链接已经过期！');
            } else {
                $au = D('AdminUser');
                $user_id = $au->getUserIdByCode($code);
                if ($user_id > 0) {
                    $this->forgetPassword($code);
                } else {
                    $this->show("非法的参数参数链接！");
                }
            }
        }

    }

    public function index()
    {
        if (is_null(session('uid'))) {
            $this->redirect('Login/login');
        } else {
            $this->display('Goods/goodsList');
        }

    }
}