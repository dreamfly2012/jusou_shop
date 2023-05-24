<?php

namespace Home\Controller;

use Think\Controller;

class CategoryController extends Controller{
	public function categoryList(){
		$cat_id = I('request.cat_id',null,'intval');
		$g = D('Goods');
		$goods_list = $g->getGoodsByCatId($cat_id);
		$this->assign('goods_list',$goods_list);
		$this->display('category_list');
	}

	public function assignNavCategory($store_id){
		$c = D('Category');
		$g = D('Goods');
		$category_info = $c->getCategoryByStoreId($store_id);
		
		foreach($category_info as $k=>$v){
			$cat_id = $v['cat_id'];
			$category_info[$k]['goods_list'] = $g->getGoodsByCatId($cat_id);
		}

		$this->assign('nav_categories',$category_info);
	}
}

