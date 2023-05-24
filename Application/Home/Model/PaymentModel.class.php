<?php

namespace Home\Model;

class PaymentModel extends CommonModel
{
	public function getAllInfo()
	{
		$result = $this->where(array('store_id'=>$this->store_id))->select();
		return $result;
	}
}
