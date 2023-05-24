<?php

namespace Admin\Model;

use Think\Model;


class AdPositionModel extends Model
{
	public function getAllPosition(){
		$result = $this->select();
		return $result;
	}
}