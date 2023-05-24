<?php
/**
 * @author dreamfly	
 * @version 0.2
 */
namespace Home\Controller;

class IndexController extends CommonController
{
    public function index() {
        //调用Brand控制器的赋值品牌方法.
        R('Brand/assignBrand', array($this->store_id));
        
        //调用Goods控制器的赋值商品方法
        R('Goods/assignGoods', array($this->store_id));
        
        $this->display('Index/index');
    }
}
