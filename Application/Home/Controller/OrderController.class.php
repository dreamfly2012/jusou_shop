<?php

namespace Home\Controller;

class OrderController extends CommonController{
	public function payOrder(){
		$this->display('Order/pay_order');
	}

	
	//checkout
	public function checkOut()
    {
		//TODO:测试数据，需要删除
		session('user_id',4);
		if(!$this->checkLogin())
		{
			$this->error('非法操作');
		}
		else
		{
			$c = D('Cart');
			$user_id= session('user_id');
			$cart_info = $c->getAllInfoByUserId($user_id);

	        if(empty($cart_info))
	        {
				$this->error(C('ERROR_CART_EMPTY'));
			}
	        else
	        {
	            //TODO:赋值 购物车信息,地址信息,付款方式.
	            $ua = D('UserAddress');
	            $p = D('Payment');
	            $r = D('Region');//地址信息从数据库选择
	            $address_info = $ua->getDefaultAddress($user_id);
	            if(empty($address_info))
	            {
	            	$provinces = $r->getInfoByTypeAndParent('1',1);
	            	$this->assign('provinces',$provinces);
	            	$this->display('consignee_address');
	            }
	            else
	            {
	            	$provinces = $r->getInfoByTypeAndParent('1',1);
		            $payment_info = $p->getAllInfo();
		            $this->assign('provinces',$provinces);
					$this->assign('cart_info',$cart_info);
		            $this->assign('address_info',$address_info);
		            $this->assign('payment_info',$payment_info);
		            $this->display('checkout');
	            }
			}
		}
    }

	public function delivery(){
		$this->display('Order/delivery');
	}
}