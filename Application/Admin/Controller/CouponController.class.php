<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/4/21
 * Time: 11:18
 */

namespace Admin\Controller;
use Think\Controller;

class CouponController extends Controller
{
    public function index()
    {
        $this->coupon_list();
    }

    public function coupon_list()
    {
        $this->display('coupon_list');
    }

    public function couponAdd()
    {
        $choice_statuses[0]['name'] = '可用';
        $choice_statuses[0]['value'] = '1';
        $choice_statuses[1]['name'] = '不可用';
        $choice_statuses[1]['value'] = '0';
        $this->assign('choice_statuses',$choice_statuses);
        $this->display('Coupon/coupon_add');
    }

    public function generateCode()
    {
        $number = date('mds') . str_pad(mt_rand(1,999999), 7, '0', STR_PAD_LEFT);
        $data['info'] = 'JSShop'.mt_rand(0, 999).$number.mt_rand(0, 999);
        $data['message'] = "success";
        $this->ajaxReturn($data);
    }

    //批量编辑
    public function couponBatch()
    {
        $ids = I('post.ids');
        $operation = I('post.operation');
        $ids = trim($ids, ':');
        $ids_arr = explode(':', $ids);
        $c = D('Coupon');
        $result = "true";

        foreach ($ids_arr as $k => $v) {
            if ($operation == 'recycle') {
                if(!$c->CouponToRecycleBin($v)){
                    echo "false";
                }
            }
        }
        echo $result;
    }

    //回收站批量编辑
    public function recycleBatch()
    {
        $ids = I('post.ids');
        $operation = I('post.operation');
        $ids = trim($ids, ':');
        $ids_arr = explode(':', $ids);
        $c = D('Coupon');
        $result = "true";

        foreach ($ids_arr as $k => $v) {
            if ($operation == 'restore') {
                if(!$c->CouponFromRecycleBin($v)){
                    echo "false";
                }
            } else if ($operation == 'delete') {
                if(!$c->DeleteRecycleBin($v)){
                    echo "false";
                }
            }
        }
        echo $result;
    }

    //删除回收站
    public function couponDelRecycleBin()
    {
        $id = I('request.id');
        $c = D('Coupon');
        $result = $c->DeleteRecycleBin($id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    //从回收站恢复
    public function couponRevert()
    {
        $id = I('request.id');
        $c = D('Coupon');
        $result = $c->CouponFromRecycleBin($id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    //编辑兑换券
    public function couponEdit()
    {
        $c = D('Coupon');
        $id = I('request.id');
        $choice_statuses[0]['name'] = '可用';
        $choice_statuses[0]['value'] = '1';
        $choice_statuses[1]['name'] = '不可用';
        $choice_statuses[1]['value'] = '0';
        $this->assign('choice_statuses',$choice_statuses);
        $coupon = $c->getCouponInfoById($id);
        $this->assign('coupon', $coupon);
        $this->display('Coupon/coupon_edit');
    }

    public function addCoupon()
    {
        $c = D('Coupon');
        $store_id = session('store_id');
        $data["store_id"] = $store_id;
        $data["name"] = I('post.name');
        $data["coupon_code"] = I('post.coupon_code');
        $data["pay_points"] = I('post.pay_points');
        $data["validate_date"] = I('post.validate_date',null,'strtotime');
        $data['status'] = I('post.status',null,'intval');

        if (!$c->create($data)) {
            $this->error($c->getError());
        }

        if($c->add($data)!==false){
            $this->success("成功添加兑换券");
        }else{
            $this->error("添加兑换券失败");
        }

    }

    public function couponList()
    {
        $store_id = session("store_id");
        $coupon = D('Coupon');
        $order = array();

        $order_by = I('request.order_by', null);
        $order_sort = I('request.order_sort', null);

        if (!is_null($order_by)) {
            $order[$order_by] = $order_sort;
        }
        if ($order_sort == "DESC") {
            $this->assign('order_sort', 'ASC');
        } else {
            $this->assign('order_sort', 'DESC');
        }

        $count = $coupon->where(array('store_id' => $store_id, 'is_delete' => 0))->count();
        // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);
        $config = array(
            'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
            'prev' => '上一页',
            'next' => '下一页',
            'first' => '第一页',
            'last' => '最后一页',
            'theme' => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
        );
        foreach ($config as $k => $v) {
            $Page->setConfig($k, $v);
        }

        // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        if (empty($order)) {
            $list = $coupon->where(array('store_id' => $store_id, 'is_delete' => 0))->order('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $coupon->where(array('store_id' => $store_id, 'is_delete' => 0))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Coupon/coupon_list');
    }

    public function updateCoupon()
    {
        $c = D('Coupon');
        $store_id = session('store_id');

        $data['name'] = I('post.name');
        $data['coupon_code'] = I('post.coupon_code');
        $data['pay_points'] = I('post.pay_points');
        $data['validate_date'] = I('post.validate_date',null,'strtotime');
        $data['store_id'] = $store_id;
        $data['id'] = I('post.id',null,'intval');

        if (!$c->create($data)) {
            $this->error($c->getError());
        }

        if ($c->save($data)!==false) {
            $this->success('成功修改购物券');
        } else {
            $this->error('修改购物券失败');
        }
    }


    public function couponRecycleBin()
    {
        $id = I('request.id');
        $c = D('Coupon');
        $result = $c->CouponToRecycleBin($id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    public function couponRecycleBinList()
    {
        $store_id = session("store_id");

        $coupon = D('Coupon');
        $order = array();

        $order_by = I('request.order_by', null);
        $order_sort = I('request.order_sort', null);

        if (!is_null($order_by)) {
            $order[$order_by] = $order_sort;
        }
        if ($order_sort == "DESC") {
            $this->assign('order_sort', 'ASC');
        } else {
            $this->assign('order_sort', 'DESC');
        }

        $count = $coupon->where(array('store_id' => $store_id, 'is_delete' => 1))->count();
        // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);
        $config = array(
            'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
            'prev' => '上一页',
            'next' => '下一页',
            'first' => '第一页',
            'last' => '最后一页',
            'theme' => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
        );
        foreach ($config as $k => $v) {
            $Page->setConfig($k, $v);
        }

        // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        if (empty($order)) {
            $list = $coupon->where(array('store_id' => $store_id, 'is_delete' => 1))->order('id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $coupon->where(array('store_id' => $store_id, 'is_delete' => 1))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Coupon/coupon_recyclebin_list');
    }
}