<?php

namespace Home\Model;

class UserAddressModel extends CommonModel
{
	public function getDefaultAddress($user_id)
	{
		$result = $this->where(array('user_id'=>$user_id))->count();

		if($result==0)
		{
			return $result;
		}
		else if($result==1)
		{
			$this->where(array('user_id'=>$user_id))->setField(array('isdefault'=>1));
			return $result[0];
		}
		else
		{
			$result2 = $this->where(array('user_id'=>$user_id,'isdefault'=>1))->select();
			if(empty($result2))
			{
				return $result;
			}
			else
			{
				return $result2;
			}
		}
	}
}