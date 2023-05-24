<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/1/13
 * Time: 15:44
 */
namespace Home\Controller;

class GoodsController extends CommonController{
	public function assignGoods($store_id){
		$g = D('Goods');
		$c = D('Category');
        $goods = $g->getAllGoodsByStoreId($store_id);
        foreach($goods as $k=>$v){
            $goods[$k]['goods_category'] = $c->getCatNameById();
            $goods_price_info = $g->getPriceInfo($v['goods_id']);
            $goods[$k]['goods_price'] = $goods_price_info['is_promote'] > 0 ? $goods_price_info['promote_price'] : ($goods_price_info['shop_price']!=0 ? $goods_price_info['shop_price'] : $goods_price_info['market_price']);
        }
        $this->assign('goods_list',$goods);
	}

	public function exchangeGoods(){
		//TODO：hot_goods
		$g = D('Goods');
		$hot_goods_list = $g->getAllGoodsByStoreId($this->store_id);
		$this->assign('hot_goods_list',$hot_goods_list);
		$this->display('exchange');
	}

	public function getGoodsPrice($goods_id){
		$g = D('Goods');

		$goods_price_info = $g->getPriceInfo($goods_id);

		//TODO:注册hook,将来编写插件
		$price = hook_goods_price($goods_price_info);
		return $price;
	}


	public function goodsDetail(){
		$goods_id = I('request.goods_id',null);
		$g = D('Goods');
		$c = D('Category');
		$gg = D('GoodsGallery');
		$store_id = $g->getStoreIdByGoodsId($goods_id);
		$goods = $g->getGoodsById($goods_id);
		$goods_gallery = $gg->getGoodsGalleryById($goods_id);
        $goods['goods_category'] = $c->getCatNameById($goods['cat_id']);
        $goods_price_info = $g->getPriceInfo($goods_id);
        $goods['goods_price'] = $goods_price_info['is_promote'] > 0 ? $goods_price_info['promote_price'] : ($goods_price_info['shop_price']!=0 ? $goods_price_info['shop_price'] : $goods_price_info['market_price']);
        $goods['saverate'] = round(($goods['market_price'] - $goods['goods_price'])*100/$goods['market_price']).'%';
        $this->assign('goods_gallery',$goods_gallery);
        $this->assign('goods',$goods);

        R('Brand/assignBrand',array($store_id));
		
		$this->display('Goods/goods_detail');
	}

}