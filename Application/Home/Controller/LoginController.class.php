<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/4/16
 * Time: 10:07
 */
namespace Home\Controller;

class LoginController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    //登录首页
    public function index()
    {
        $this->display('index');
    }

    //登录处理
    public function loginHandle()
    {
        $data["user_name"]  = I('post.user_name',null);
        $data["password"] = I('post.password',null,'md5');
        $u = D('User');

        if(!$u->create($data))
        {
            $this->error($u->getError());
        }

        $count = $u->where($data)->count();

        if($count)
        {
            $info = $u->where($data)->select();
            $user_id = $info[0]['user_id'];
            session('user_id',$user_id);
            $this->success('登陆成功',U('User/index'));
        }
        else
        {
            $this->error('用户名密码错误！');
        }

    }


    //忘记密码页面
    function forgetPassword()
    {
        $this->display('forget_password');
    }

    //忘记密码处理
    public function forgetpasswordHandle()
    {
        Vendor('phpMailer.class#phpmailer');
        Vendor('phpMailer.class#smtp');
        $email = I('post.email',null);
        if(!preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email))
        {
            $this->error('邮箱非法');
        }

        $u = D('User');

        $result = $u->getInfoByEmail($email);

        if(!empty($result))
        {
            $user_id = $result['user_id'];
            $rand_string = $this->getRandChar(8);
            $u->where(array('user_id'=>$user_id))->setField('password',md5($rand_string));
            $title = '密码更改邮件 ！';
            $content = '<div>';
            $content .= sprintf('Dear %s，您忘记密码了吗？<br>', $result['user_name']);
            $content .= '为了保险起见，我们将您的密码更新成新密码，看到新密码后，您可以立即登陆会员中心修改密码。<br>';
            $content .= sprintf('您的个人信息请妥善保管个人注册信息<br>用户名：%s<br>新密码：%s<br>', $result['user_name'], $rand_string);
            $content .= '■重要信息：由于此邮件包含个人注册资料，请妥善保存!</div>';

            $mail = new \PHPMailer(); //new一个PHPMailer对象出来
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
            $mail->MsgHTML($content);
            $mail->AddAddress($email);
            if($mail->Send())
            {
                $this->success('已经发送邮件！');
            }
            else
            {
                $this->error('发送邮件失败！');
            }
        }
        else
        {
            $this->error(C('ERROR_EMAIL'));
        }

    }

    //退出
    public function loginout()
    {
        //清除session,cookie
        session('user_id',null);
        cookie('user_id',null);
        $this->redirect('Index');
    }

}