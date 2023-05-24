<?php

namespace Home\Model;

class RegionModel extends CommonModel
{
	
	public function getInfoByTypeAndParent($region_type = 0, $parent_id = 0)
	{
	    $result = $this->where(array('region_type'=>$region_type,'parent_id'=>$parent_id))->select();
		return $result;
	}


}