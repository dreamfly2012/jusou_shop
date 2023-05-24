<?php
namespace Home\Controller;
use Think\Controller;

class BrandController extends CommonController{
	public function assignBrand($store_id){
		$b = D('Brand');
		$brand_ids = $b->getIdsByStoreId($store_id);
        $brand_ids_str = "";
        foreach ($brand_ids as $k => $v) {
            $brand_ids_str  .= $v['brand_id'] .',';
        }
        $brand_ids_str = trim($brand_ids_str,',');
        $brand_info = $b->getBrandInfoByIds($brand_ids_str);
        $this->assign('brand',$brand_info);
	}
}