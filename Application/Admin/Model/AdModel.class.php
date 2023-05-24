<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/19
 * Time: 11:43
 */

namespace Admin\Model;

use Think\Model;

class AdModel extends Model{
    protected $_validate = array(
        array('ad_name','require','广告名称必须填写'),
        array('start_time','compareDate','结束时间必须大于开始时间',0,'callback',3),
        array('ad_img','require','广告图片必须上传'),

    );

    public function compareDate(){
        $start_time = I('post.start_time',null);

        $end_time = I('post.end_time',null);

        $str_start_time = strtotime($start_time);

        $str_end_time = strtotime($end_time);

        if(!is_null($start_time) && !is_null($end_time)){
            return ($str_end_time-$str_start_time)>0 ? true : false;
        }else{
            return true;
        }
    }

    public function getAdById($ad_id){
        $store_id = session('store_id');
        $result = "";
        if($store_id=='admin'){
            $result = $this->where(array('store_id'=>$store_id,'ad_id'=>$ad_id))->select();
        }else{
            $result = $this->where(array('store_id'=>$store_id,'ad_id'=>$ad_id))->select();
        }
        return $result;
    }


}