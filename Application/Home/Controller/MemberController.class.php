<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/4/15
 * Time: 23:15
 */

namespace Home\Controller;
use Think\Controller;

class MemberController extends CommonController{
    public function index()
    {
        //个人中心首页
        $this->display('index');
    }

    public function trade()
    {
        $user_id = session('user_id');
        //获得订单信息


        $this->display('trade');
    }

    public function coupon()
    {
        //显示优惠券页面
        $this->display('coupon');
    }

    public function comment()
    {
        //显示评论
        $this->display('comment');
    }

    public function point()
    {
        //显示积分
        $this->display('point');
    }

    public function baseInfoSet()
    {
        //个人信息
        $this->display('baseInfoSet');
    }

    public function deliver_address()
    {
        //收货地址
        $this->display('deliver_address');
    }
    
}