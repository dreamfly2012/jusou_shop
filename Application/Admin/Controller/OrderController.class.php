<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/10
 * Time: 10:13
 */


namespace Admin\Controller;

class OrderController extends CommonController{
    public function index(){

    }

    //根据pay_id 返回支付的相关新消息
    public function getPaymentInfoById($pay_id){
        $d = D('Payment');
        $info = $d->getPaymentInfoByid($pay_id);
        return $info;
    }

    /**
     * 记录订单操作记录
     *
     * @access  public
     * @param   string  $order_sn           订单编号
     * @param   integer $order_status       订单状态
     * @param   integer $shipping_status    配送状态
     * @param   integer $pay_status         付款状态
     * @param   string  $note               备注
     * @param   string  $username           用户名，用户自己的操作则为 buyer
     * @return  void
     */
    public function order_action($order_sn, $order_status, $shipping_status, $pay_status, $note = '', $username = null, $place = 0)
    {
        if (is_null($username))
        {
            $username = 'admin';
        }

        $store_id = session('store_id');

        $oa = D('OrderAction');

        $oi = D('OrderInfo');

        $order_id = $oi->getOrderIdBySn($order_sn);

        $data['order_id'] = $order_id;
        $data["store_id"] = $store_id;
        $data["action_user"] = $username;

        $data["order_status"] = $order_status;
        $data["shipping_status"] = $shipping_status;
        $data["pay_status"] = $pay_status;
        $data["action_place"] = $place;

        $data["action_note"] = $note;
        $data['log_time'] = $this->gmtime();

        if($oa->add($data)){
            return true;
        }else{
            return false;
        }
    }

    //取得订单可操作列表
    public function operable_list($order)
    {
        /* 取得订单状态、发货状态、付款状态 */
        $os = $order['order_status'];
        $ss = $order['shipping_status'];
        $ps = $order['pay_status'];

        /* 取得订单支付方式是否货到付款 */
        $payment = $this->getPaymentInfoById($order['pay_id']);
        $is_cod  = $payment['is_cod'] == 1;//是否支持货到付款

        /* 根据状态返回可执行操作 */
        $list = array();
        if (OS_UNCONFIRMED == $os)
        {
            /* 状态：未确认 => 未付款、未发货 */
            $data['confirm']    = "确认";
            $data['invalid']    = "无效";
            $data['cancel']     = "取消";
            if ($is_cod)
            {
                /* 货到付款 */
                $list['prepare'] = "配货";
            }
            else
            {
                /* 不是货到付款 */
                $list['pay'] = "付款";
            }

        }
        elseif (OS_CONFIRMED == $os )
        {
            /* 状态：已确认 */
            if (PS_UNPAYED == $ps)
            {
                /* 状态：已确认、未付款 */
                if (SS_UNSHIPPED == $ss || SS_PREPARING == $ss)
                {
                    /* 状态：已确认、未付款、未发货（或配货中） */
                    $list['cancel'] = "取消"; // 取消
                    $list['invalid'] = "无效"; // 无效

                    if ($is_cod)
                    {
                        /* 货到付款 */
                        if (SS_UNSHIPPED == $ss)
                        {
                            $list['prepare'] = "配货"; // 配货
                        }
                    }
                    else
                    {
                        /* 不是货到付款 */
                        $list['pay'] = "付款"; // 付款
                    }
                }
                /* 状态：已确认、未付款、发货中 */
                elseif (SS_SHIPPED_ING == $ss || SS_SHIPPED_PART == $ss)
                {
                    $list['to_delivery'] = "去发货"; // 去发货
                }
                else
                {
                    /* 状态：已确认、未付款、已发货或已收货 => 货到付款 */
                    $list['pay'] = "付款"; // 付款

                    if (SS_SHIPPED == $ss)
                    {
                        $list['receive'] = "收货确认"; // 收货确认
                    }
                    $list['unship'] = "设为未发货"; // 设为未发货
                    $list['return'] = "退货"; // 退货
                }
            }
            else
            {
                /* 状态：已确认、已付款和付款中 */
                if (SS_UNSHIPPED == $ss || SS_PREPARING == $ss)
                {
                    /* 状态：已确认、已付款和付款中、未发货（配货中） => 不是货到付款 */
                    if (SS_UNSHIPPED == $ss)
                    {
                        $list['prepare'] = "配货"; // 配货
                    }
                    $list['unpay'] = "设为未付款"; // 设为未付款
                    $list['cancel'] = "取消"; // 取消
                }
                /* 状态：已确认、未付款、发货中 */
                elseif (SS_SHIPPED_ING == $ss || SS_SHIPPED_PART == $ss)
                {
                    $list['to_delivery'] = "去发货"; // 去发货
                }
                else
                {
                    /* 状态：已确认、已付款和付款中、已发货或已收货 */
                    if (SS_SHIPPED == $ss)
                    {
                        $list['receive'] = "收货确认"; // 收货确认
                    }
                    if (!$is_cod)
                    {
                        $list['unship'] = "设为未发货"; // 设为未发货
                    }

                    if ($is_cod)
                    {
                        $list['unpay']  = "设为未付款"; // 设为未付款
                    }
                    $list['return'] = "退货"; // 退货（包括退款）
                }
            }
        }
        elseif (OS_CANCELED == $os)
        {
            /* 状态：取消 */
            $list['confirm'] = "确认";

            $list['remove'] = "移除";

        }
        elseif (OS_INVALID == $os)
        {
            /* 状态：无效 */
            $list['confirm'] = "确认";

            $list['remove'] = "移除";

        }
        elseif (OS_RETURNED == $os)
        {
            /* 状态：退货 */
            $list['confirm'] = "确认";
        }

        /* 修正发货操作 */
        if (!empty($list['split']))
        {
            /* 如果是团购活动且未处理成功，不能发货 */
            if ($order['extension_code'] == 'group_buy')
            {
               //TODO：　团购处理没有完成
                $this->orderGroupBy();

                /*$group_buy = group_buy_info(intval($order['extension_id']));
                if ($group_buy['status'] != GBS_SUCCEED)
                {
                    unset($list['split']);
                    unset($list['to_delivery']);
                }*/
            }

            /* 如果部分发货 不允许 取消 订单 */
           // $list['return'] = true; // 退货（包括退款）
            //unset($list['cancel']); // 取消

        }

        /* 售后 */
        $list['after_service'] = "售后";

        return $list;
    }

    /**
     * 订单详情页
     */
    public function orderShow(){
        //$languages_array = array('confirm'=>'确认', 'pay'=>'付款', 'unpay'=>'取消付款', 'prepare'=>'配货', 'ship'=>'配货', 'unship'=>'设为未发货', 'receive'=>'', 'cancel'=>'取消', 'invalid'=>'无效', 'return'=>'退货', 'drop'=>'取消');

        $order_id = I("request.order_id");

        $oi = D('OrderInfo');

        $c = D('Consignee');

        $og = D('OrderGoods');

        $orderinfo = $oi->getOrderInfoById($order_id);

        $consignee = $c->getConsigneeInfoByUserId($orderinfo['user_id']);

        $goods_info = $og->getGoodsInfoById($order_id);

        $operable_list = $this->operable_list($orderinfo);

        $this->assign('operations',$operable_list);

        $this->assign('goods_info',$goods_info);

        $this->assign('consignee',$consignee);

        $this->assign('order',$orderinfo);

        $this->display('Order/order_show');
    }


    //TODO：退换用户积分，比如这里，只是定义了函数，具体的操作并没有去完成，就标注一下todo.
    public function orderInteralReturn($order_id){

    }

    //TODO: 处理退款操作,需要考虑客户是否进行了汇款
    public function orderReturnMoney($order_id){

    }

    //TODO: 删除发货单
    public function orderDeliveryDelete($order_id){

    }

    //TODO: 处理退换积分，余额，红包等，需要考虑
    public function orderSurplusIntegralBonusReturn($order_id){

    }

    //TODO: 处理商品库存
    public function orderChangeStorage($order_id){

    }

    //TODO: 订单备份，记录退货的订单，产品
    public function orderDeliveryBackup($delivery){

    }

    //TODO:团购处理order

    public function orderGroupBy(){

    }

    public function getDetailConsigneeAddressInfoById($consignee_id){
        $c = D('Consignee');
        $r = D('Region');
        $province_id = $c->getProvinceIdById($consignee_id);
        $province = $r->getProvinceNameById($province_id);
        $city_id = $c->getCityIdById($consignee_id);
        $city = $r->getCityNameById($city_id);
        $district_id = $c->getDistrictIdById($consignee_id);
        $district = $r->getDistrictNameById($district_id);
        $address = $c->getAddressNameById($consignee_id);

        return $province . $city . $district . $address;
    }

    public function getDetailAddressInfoById($address_id){
        $aa = D('AdminAddress');
        $r  = D('Region');
        $province_id = $aa->getProvinceIdById($address_id);
        $province = $r->getProvinceNameById($province_id);
        $city_id = $aa->getCityIdById($address_id);
        $city = $r->getCityNameById($city_id);
        $district_id = $aa->getDistrictIdById($address_id);
        $district = $r->getDistrictNameById($district_id);
        $address = $aa->getAddressNameById($address_id);

        return $province . $city . $district . $address;
    }

    public function getDefaultDeliverAddress($address_id){
        $aa = D('AdminAddress');
        $r  = D('Region');
        $province_id = $aa->getProvinceIdById($address_id);
        $province = $r->getProvinceNameById($province_id);
        $city_id = $aa->getCityIdById($address_id);
        $city = $r->getCityNameById($city_id);
        $district_id = $aa->getDistrictIdById($address_id);
        $district = $r->getDistrictNameById($district_id);
        $address = $aa->getAddressNameById($address_id);

        return $province . $city . $district . $address;
    }

    public function getDefaultRefundAddress($address_id){
        $aa = D('AdminAddress');
        $r  = D('Region');
        $province_id = $aa->getProvinceIdById($address_id);
        $province = $r->getProvinceNameById($province_id);
        $city_id = $aa->getCityIdById($address_id);
        $city = $r->getCityNameById($city_id);
        $district_id = $aa->getDistrictIdById($address_id);
        $district = $r->getDistrictNameById($district_id);
        $address = $aa->getAddressNameById($address_id);

        return $province . $city . $district . $address;
    }

    //TODO: 思考库存减少的时机，暂时固定为下订单时，减少库存。
    public function orderOperate(){
        $order_id = I('post.order_id');
        $action_note = I('post.action_note');
        $operation = I('post.method');
        $oi = D('OrderInfo');

        /* TODO: 检查权限 */
        /* 查询订单信息 */
        $order = $oi->getOrderInfoById($order_id);

        /* 确认 */
        if ('confirm' == $operation)
        {
            /* 标记订单为已确认 */
            $order_amount =  $oi->getOrderAmountById($order_id);

            $oi->where(array('order_id'=>$order_id))->setField(array('order_status' => OS_CONFIRMED, 'confirm_time' => $this->gmtime(), 'order_amount'=>$order_amount));

            /* 记录log */
            $this->order_action($order['order_sn'], OS_CONFIRMED, SS_UNSHIPPED, PS_UNPAYED, $action_note);

            /* TODO: 如果原来状态不是“未确认”，且使用库存，且下订单时减库存，则减少库存 */

            /* TODO：是否发送邮件 附加功能，需要开通 */
        }
        /* 付款 */
        elseif ('pay' == $operation)
        {
            /* 标记订单为已确认、已付款，更新付款时间和已支付金额，如果是货到付款，同时修改订单为“收货确认” */
            if ($order['order_status'] != OS_CONFIRMED)
            {
                $data['order_status']    = OS_CONFIRMED;
                $data['confirm_time']    = $this->gmtime();
            }
            $data['pay_status']  = PS_PAYED;
            $data['pay_time']    = $this->gmtime();
            $data['money_paid']  = $order['money_paid'] + $order['order_amount'];
            $data['order_amount']= 0;
            $payment = $this->getPaymentInfoById($order['pay_id']);
            if ($payment['is_cod'])
            {
                $data['shipping_status'] = SS_RECEIVED;
                $order['shipping_status'] = SS_RECEIVED;
            }
            $oi->where(array('order_id'=>$order_id))->save($data);

            /* 记录log */
            $this->order_action($order['order_sn'], OS_CONFIRMED, $order['shipping_status'], PS_PAYED, $action_note);
        }
        /* 设为未付款 */
        elseif ('unpay' == $operation)
        {
            /* 标记订单为未付款，更新付款时间和已付款金额 */
            $data = array(
                'pay_status' => PS_UNPAYED,
                'pay_time' => 0,
                'money_paid' => 0,
                'order_amount' => $order['money_paid']
            );
            $oi->where(array('order_id'=>$order_id))->save($data);

            /*  处理退款 */
            $this->orderReturnMoney($order_id);

            /* 记录log **/
            $this->order_action($order['order_sn'], OS_CONFIRMED, $order['shipping_status'], PS_PAYED, $action_note);
        }
        /* 配货 */
        elseif ('prepare' == $operation)
        {
            /* 标记订单为已确认，配货中 */
            if ($order['order_status'] != OS_CONFIRMED)
            {
                $data['order_status']    = OS_CONFIRMED;
                $data['confirm_time']    = $this->gmtime();
            }
            $data['shipping_status']     = SS_PREPARING;//配货中

            $oi->where(array('order_id'=>$order_id))->save($data);

             /* 记录log */
            $this->order_action($order['order_sn'], OS_CONFIRMED, SS_PREPARING, $order['pay_status'], $action_note);

            $c = D('Consignee');
            $r = D('Region');
            $aa = D('AdminAddress');
            $user_id = session('user_id');

            $consignee = $c->getDefaultConsigneeInfoByUserId($order["user_id"]);
            $detail_address = $this->getDetailConsigneeAddressInfoById($consignee['consignee_id']);

            $province_list = $r->getProvinceListByCountry($consignee['country']);
            $city_list = $r->getCityListByProvince($consignee['province']);
            $district_list = $r->getDistrictListByCity($consignee['city']);

            $address_deliver_list = $aa->getDeliverAddressByUserId($user_id);
            $address_refund_list = $aa->getRefundAddressByUserId($user_id);

            $default_deliver_id = $aa->getDefaultAddressIdByUserId($user_id,'deliver');
            $default_refund_id = $aa->getDefaultAddressIdByUserId($user_id,'refund');

            $deliver_default_address = $this->getDefaultDeliverAddress($default_deliver_id);
            $refund_default_address = $this->getDefaultRefundAddress($default_refund_id);

            $this->assign('address_deliver_list',$address_deliver_list);
            $this->assign('address_refund_list',$address_refund_list);

            $this->assign('deliver_default_address',$deliver_default_address);
            $this->assign('refund_default_address',$refund_default_address);

            $this->assign('province_list',$province_list);
            $this->assign('city_list',$city_list);
            $this->assign('district_list',$district_list);
            $this->assign('consignee',$consignee);
            $this->assign('detail_address',$detail_address);
            $this->display('Order/order_deliver');
        }
        elseif('deliver' == $operation)
        {
            if ($order['order_status'] != OS_CONFIRMED)
            {
                $data['order_status']    = OS_CONFIRMED;
                $data['confirm_time']    = $this->gmtime();
            }
            $data['shipping_status']     = SS_SHIPPED;//发货送货中

            $oi->where(array('order_id'=>$order_id))->save($data);

            /* 记录log */
            $this->order_action($order['order_sn'], OS_CONFIRMED, SS_SHIPPED, $order['pay_status'], $action_note);
        }
        /* 取消发货 */
        elseif ('unship' == $operation)
        {
            /* 标记订单为“未发货”，更新发货时间, 订单状态为“确认” */

            $oi->where(array('order_id'=>$order_id))->setField(array('shipping_status' => SS_UNSHIPPED, 'shipping_time' => 0, 'invoice_no' => '', 'order_status' => OS_CONFIRMED));

            /* 记录log */
            $this->order_action($order['order_sn'], $order['order_status'], SS_UNSHIPPED, $order['pay_status'], $action_note);

            /* 计算积分，并退回 */
            $this->orderInteralReturn($order_id);

            /* 删除发货单 */
            $this->orderDeliveryDelete($order_id);

        }
        /* 收货确认 */
        elseif ('receive' == $operation)
        {
            /* 标记订单为“收货确认”，如果是货到付款，同时修改订单为已付款 */
            $data = array('shipping_status' => SS_RECEIVED);
            $payment = $this->getPaymentInfoById($order_id);
            if ($payment['is_cod'])
            {
                $data['pay_status'] = PS_PAYED;
                $order['pay_status'] = PS_PAYED;
            }
            $this->where(array('order_id'=>$order_id))->save($data);

            /* 记录log */
            $this->order_action($order['order_sn'], $order['order_status'], SS_RECEIVED, $order['pay_status'], $action_note);
        }
        /* 取消 */
        elseif ('cancel' == $operation)
        {
            /* 标记订单为“取消”，记录取消原因 */
            $data = array(
                'order_status'  => OS_CANCELED,
                'to_buyer'      => $action_note,
                'pay_status'    => PS_UNPAYED,
                'pay_time'      => 0,
                'money_paid'    => 0,
                'order_amount'  => $order['money_paid']
            );
            $oi->where(array('order_id'=>$order_id))->save($data);

            /*处理退款 退款*/
            $this->orderReturnMoney($order_id);

             /* 记录log */
            $this->order_action($order['order_sn'], OS_CANCELED, $order['shipping_status'], PS_UNPAYED, $action_note);

            /**退还用户余额、积分、红包 */
            $this->orderSurplusIntegralBonusReturn($order_id);
        }
        /* 设为无效 */
        elseif ('invalid' == $operation)
        {
            /* 标记订单为“无效”、“未付款” */
            $oi->where(array('order_id'=>$order_id))->setField(array('order_status' => OS_INVALID));

            /* 记录log */
            $this->order_action($order['order_sn'], OS_INVALID, $order['shipping_status'], PS_UNPAYED, $action_note);

            /*TODO: 占时不处理 退货用户余额、积分、红包 */
            $this->orderSurplusIntegralBonusReturn($order_id);
        }
        /* 退货 */
        elseif ('return' == $operation)
        {
            /* 定义当前时间 */
            define('GMTIME_UTC', $this->gmtime()); // 获取 UTC 时间戳

            /* 标记订单为“退货”、“未付款”、“未发货” */
            $data = array('order_status'     => OS_RETURNED,
                'pay_status'       => PS_UNPAYED,
                'shipping_status'  => SS_UNSHIPPED,
                'money_paid'       => 0,
                'invoice_no'       => '',
                'order_amount'     => $order['money_paid']);

            $oi->where(array('order_id'=>$order_id))->save($data);

            /*处理退款 */
            $this->orderReturnMoney($order_id);

            /* 记录log */
            $this->order_action($order['order_sn'], OS_RETURNED, SS_UNSHIPPED, PS_UNPAYED, $action_note);

            //积分处理
            $this->orderInteralReturn($order_id);

            //红包，余额等处理
            $this->orderSurplusIntegralBonusReturn($order_id);

            /*TODO库存处理 如果使用库存，则增加库存（不论何时减库存都需要） */

            $this->orderChangeStorage($order_id);

            /* 获取当前操作员 */
            $delivery['action_user'] = $_SESSION['admin_name'];
            /*添加退货记录  操作back_order 表*/

            $orderDelivery = $this->getOrderDeliveryInfo($order_id);

            if ($orderDelivery)
            {
                foreach ($orderDelivery as $delivery)
                {
                    //TODO: 遍历发货单，将发货单信息插入到备份订单中，备份商品中
                    $this->orderDeliverBackup($delivery);
                }
            }

            /*修改订单的发货单状态为退货 */
            $do = D('DeliveryOrder');

            $do->where(array('order_id'=>$order_id))->setField(array('status'=>1));
        }
        /* TODO: 售后信息*/
        elseif ('after_service' == $operation)
        {
            /* 记录log */
            $this->order_action($order['order_sn'], $order['order_status'], $order['shipping_status'], $order['pay_status'], '[售后]'.$action_note);
        }
        /**非法信息**/
        else
        {
            die('非法参数');
        }

        $this->success('修改成功！');
    }

    public function orderSearch(){
        $this->display('order_search');
    }

    public function orderList(){
        $this->checkPrivilege();

        $store_id = session("store_id");
        $o = D('OrderInfo');

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

        if($store_id=='admin'){
            $count = $o->count();
        }else{
            $count = $o->where(array('store_id' => $store_id))->count();
        }

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
            if($store_id=="admin"){
                $list = $o->order('goods_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            }else{
                $list = $o->where(array('store_id' => $store_id))->order('order_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            }

        } else {
            if($store_id=="admin"){
                $list = $o->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            }else{
                $list = $o->where(array('store_id' => $store_id))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            }
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Order/order_list');
    }


    public function showDeliver($order_id){

    }

    public function orderDeliverList(){
        $this->display('Order/order_deliver_list');
    }

    public function orderReturnList(){
        $this->display('Order/order_return_list');
    }


    public function orderSearchHandle(){

        $add_time_start = I('request.add_time_start',null,'strtotime');
        $add_time_end = I('request.add_time_end',null,'strtotime');
        $goods_name = I('request.goods_name');

        $user_name = I('request.user_name');

        $order_sn = I('request.order_sn');

        $oi = D('OrderInfo');

        $order_info = $oi->getOrderInfo();

        $order_ids = "";
        foreach ($order_info as $key => $val) {
            $order_ids .= $val['order_id'] . ",";
        }

        $order_ids = trim($order_ids, ',');


        if(($add_time_end>$add_time_start) && $add_time_start!=false && $add_time_end!=false){
            $this->error("成交开始时间不能大于");
        }

        if(!empty($goods_name)) {

            $og = D('OrderGoods');

            $order_goods_info = $og->getOrderIdByGoodsCotains($goods_name, $order_ids);

            $order_ids = "";
            foreach ($order_goods_info as $key => $val) {
                $order_ids .= $val['order_id'] . ",";
            }

            $order_ids = trim($order_ids, ',');
        }

        if(!empty($user_name)){
            $u = D('Users');

            $order_info = $oi->getOrderInfo();

            $user_ids = "";
            $temp = array();

            foreach($order_info as $key=>$val){
                if(!in_array($val['user_id'],$temp)){
                    $temp[$key] = $val['user_id'];
                    $user_ids .= $val['user_id'] . ',';
                }
            }

            $user_ids = trim($user_ids,',');

            $user_ids = $u->getUserInfoByUserNameContains($user_name,$user_ids);

            $order_info = $oi->getOrderInfoByUserIdContains($user_ids,$order_ids);

            $order_ids = "";
            foreach ($order_info as $key => $val) {
                $order_ids .= $val['order_id'] . ",";
            }

            $order_ids = trim($order_ids, ',');

        }

        if(!empty($order_sn)){
            $oi = D('OrderInfo');

            $order_info = $oi->getOrderInfoByOrderSnContains($order_sn,$order_ids);

            $order_ids = "";
            foreach ($order_info as $key => $val) {
                $order_ids .= $val['order_id'] . ",";
            }

            $order_ids = trim($order_ids, ',');

        }

        $order_info = $oi->getOrderInfoByOrderIds($order_ids);

        $this->assign('list',$order_info);

        $content = $this->fetch('order_list');//需要先赋值，再去

        $this->assign('list',$order_info);
        $this->show($content);



    }

}