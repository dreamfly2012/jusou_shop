<?php

namespace Home\Controller;

class CartController extends CommonController{
	public function showCart(){
		$this->display('Cart/shopping_cart');
	}

	//更新购物车 rec_id TODO:库存考虑
	public function updateCart(){
		if(!$this->checkLogin())
		{
			$this->error('ERROR_NO_LOGIN');
		}
		else
		{
			$rec_id = I('request.rec_id',null,'intval');
			$goods_id = I('request.goods_id',null,'intval');
			$goods_number = I('request.goods_number',null,'intval');
			$user_id = session('user_id');
			$c = D('Cart');
			$c->where(array('rec_id'=>$rec_id,'user_id'=>$user_id))->setField('goods_number',$goods_number);
		}
	}

	//ajax删除购物车商品
	public function deleteGoods()
    {
		$c = D('Cart');
		$condition['goods_id'] = I('request.goods_id',null,'intval');
        $condition['user_id'] = session('user_id');
        $condition['_logic'] = 'and';
        $status = $c->where(array($condition))->delete();
        if($status)
        {
			$data['info'] = 1;
			$data['message'] = 'success';
		}
        else
        {
			$data['info'] = 0;
			$data['message'] = 'error';
		}
		$this->ajaxReturn($data);
	}

	//ajax清空购物车
	function emptyCart()
    {
		$c = D('Cart');
		$condition['user_id'] = session('user_id');

        $status = $this->where($condition)->delete();
        if($status)
        {
			$data['info'] = 1;
			$data['message'] = 'success';
		}
        else
        {
			$data['info'] = 0;
			$data['message'] = 'error';
		}
		$this->ajaxReturn($data);
	}

	//购物券兑换积分
	function couponChange()
    {
        if(!$this->checkLogin())
        {
        	$this->error(C('ERROR_NO_LOGIN'));
        }

        $coupon_code = I('post.coupon_code',null);

        $user_id = session('user_id');

        if(empty($coupon_code))
        {
			$this->error(C('EMPTY_COUPON'));
		}

		$c = D('Coupon');
		$u = D('User');

		$coupon_info = $c->where($coupon_code)->find();

		if(empty($coupon_info))
		{
			$this->error(C('ERROR_COUPON_CODE'));
		}
		else
		{
			$pay_points = $coupon_info['pay_points'];
			$u->where(array('user_id'=>$user_id))->setInc('pay_points',$pay_points);
			$this->success(C('SUCCESS_COUPON_EXCHANGE'));
		}

	}

	//添加到购物车
	public function addToCart(){

	}

	//添加到购物车
	public function addToAjaxCart(){
		$data['info'] = '0';
		$data['message'] = 'unlogin';
		if(!$this->checkLogin()){
			$this->ajaxReturn($data);
		}else{
			$goods_id = I('request.goods_id',null,'intval');
			$user_id = session('user_id');
			$goods_num = I('request.goods_num',1,'intval');
			$condition['goods_id'] = $goods_id;
			$condition['user_id'] = $user_id;
			$c = M('Cart');
			$count = $c->where($condition)->count();
			if($count){
				$c->where($condition)->setInc('goods_number',$goods_num);
				//已经存在,进行添加更新操作
				$data['info'] = '1';
				$data['message'] = 'success';
			}else{
				$condition['goods_sn'] = $info['goods_sn'];
				$condition['product_id'] = $info['product_id'];
				$condition['goods_name'] = $info['goods_name'];
				$condition['market_price'] = $info['market_price'];
				$condition['goods_price'] = $info['goods_price'];
				$condition['goods_number'] = $goods_num;
				if($c->add($condition)!==false){
					$data['info'] = '2';//添加购物车成功
					$data['message'] = 'success';
				}else{
					$data['info'] = '3';
					$data['message'] = 'error';
				}
			}
			$this->ajaxReturn($data);
		}
	}

	
}
