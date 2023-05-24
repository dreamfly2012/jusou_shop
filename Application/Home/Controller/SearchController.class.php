<?php
namespace Home\Controller;
use Think\Controller;

class SearchController extends CommonController{
	public function searchGoods(){
		$goods_name = I('request.goods_name',null);
		$g = D('Goods');
		$condition['goods_name'] = array('like','%'.$goods_name.'%');
		$goods_list = $g->where($condition)->select();
		$this->assign('search_keywords',$goods_name);
		$this->assign('goods_list',$goods_list);

		//调用Nav控制器的赋值导航方法
		R('Nav/assignNav',array($this->store_id));
		$this->display('Goods/goods_list');
	}
}