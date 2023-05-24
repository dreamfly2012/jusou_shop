<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/12
 * Time: 13:51
 */

namespace Admin\Model;

use Think\Model;

class RegionModel extends Model{
    public function getProvinceListByCountry($country_id){
        $result = $this->field(array('region_id','region_name'))->where(array('parent_id'=>$country_id,'region_type'=>PROVINCE_TYPE))->select();
        return $result;
    }

    public function getCityListByProvince($province_id){
        $result = $this->field(array('region_id','region_name'))->where(array('parent_id'=>$province_id,'region_type'=>CITY_TYPE))->select();
        return $result;
    }

    public function getDistrictListByCity($city_id){
        $result = $this->field(array('region_id','region_name'))->where(array('parent_id'=>$city_id,'region_type'=>DISTRICT_TYPE))->select();
        return $result;
    }

    public function getProvinceNameById($region_id){
        $result = $this->field(array('region_name'))->where(array('region_id'=>$region_id))->select();
        return $result[0]['region_name'];
    }

    public function getCityNameById($region_id){
        $result = $this->field(array('region_name'))->where(array('region_id'=>$region_id))->select();
        return $result[0]['region_name'];
    }

    public function getDistrictNameById($region_id){
        $result = $this->field(array('region_name'))->where(array('region_id'=>$region_id))->select();
        return $result[0]['region_name'];
    }
}