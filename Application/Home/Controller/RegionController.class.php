<?php

namespace Home\Controller;

class RegionController extends CommonController
{
	public function getInfoByTypeAndParent($region_type,$parent_id)
	{
		$r = D('Region');
		$info = $r->getInfoByTypeAndParent($region_type,$parent_id);
		$this->ajaxReturn($info);
	}

	public function getAjaxData()
	{
		$region_type = I('request.region_type',null,'intval');
		$parent_id = I('request.region_id',null,'intval');
		$this->getInfoByTypeAndParent($region_type,$parent_id);
	}
}