<?php

    function now_date($str){
        if(empty($str)){
            return date('Y-m-d');
        }else{
            return date('Y-m-d H:i:s',$str);
        }
    }

    function get_shipping_name_by_id($id){
        $s = D('Shipping');
        $shipping_name = $s->getShippingNameById($id);
        return $shipping_name;
    }

    function get_pay_method_by_id($id){
        $p = D('Payment');
        $payment_info = $p->getPaymentInfoById($id);
        return $payment_info['pay_name'];
    }

    function get_shipping_status_name($n){
        switch($n)
        {
            case 0:
                return "未发货";
                break;
            case 1:
                return "已发货";
                break;
            case 2:
                return "已收货";
                break;
            case 3:
                return "备货中";
                break;
            case 4:
                return "已发货(部分商品)";
                break;
            case 5:
                return "发货中(处理分单)";
                break;
            default:
                return "";
        }
    }

    function get_order_status_name($n){
        switch($n)
        {
            case 0:
                return "未确认";
                break;
            case 1:
                return "已确认";
                break;
            case 2:
                return "已取消";
                break;
            case 3:
                return "无效";
                break;
            case 4:
                return "退货";
                break;
            case 5:
                return "已分单";
                break;
            case 6:
                return "部分分单";
                break;
            default:
                return "";
        }
    }

    function get_pay_status_name($n){
        switch($n)
        {
            case 0:
                return "未付款";
            case 1:
                return "付款中";
            case 2:
                return "已付款";
            default:
                return "";
        }
    }

    function mydatetime($str){
        if($str==0){
            return '';
        }else{
            return date('Y-m-d H:i:s',$str);
        }

    }


    function checkPassword($password){

    }

    //密码进行加密




