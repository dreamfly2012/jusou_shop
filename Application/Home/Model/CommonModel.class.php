<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/4/16
 * Time: 14:15
 */

namespace Home\Model;
use Think\Model;

class CommonModel extends Model
{
    protected $store_id;

    public function __construct()
    {
        parent::__construct();
        $this->store_id = I('request.store_id','1','intval');
    }
}