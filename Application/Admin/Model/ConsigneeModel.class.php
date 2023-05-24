<?php

    namespace Admin\Model;

    use Think\Model;

    //模型层操作数据库，提供增删改查
    class ConsigneeModel extends Model{

        /**
         * @param $user_id 用户id
         * @return mixed 返回收货人信息
         */
        public function getConsigneeInfoByUserId($user_id){
            $result =  $this->where(array('user_id'=>$user_id))->select();
            return $result[0];
        }

        //设置收货人信息
        /**
         * @param $user_id 用户id
         * @return mixed 返回收货人信息
         */
        public function setConsigneeInfo(){
            $data = session('consigneeInfo');
            $user_id = session('user_id');
            $this->save($data);
        }

        public function getDefaultConsigneeInfoByUserId($user_id){
            $result =  $this->where(array('user_id'=>$user_id,'is_default'=>1))->select();
            return $result[0];
        }

        public function getProvinceIdById($consignee_id){
            $result = $this->field('province')->where(array('consignee_id'=>$consignee_id))->select();
            return $result[0]['province'];
        }
        public function getCityIdById($consignee_id){
            $result = $this->field('city')->where(array('consignee_id'=>$consignee_id))->select();
            return $result[0]['city'];
        }
        public function getDistrictIdById($consignee_id){
            $result = $this->field('district')->where(array('consignee_id'=>$consignee_id))->select();
            return $result[0]['district'];
        }

        public function getAddressNameById($consignee_id){
            $result = $this->field('address')->where(array('consignee_id'=>$consignee_id))->select();
            return $result[0]['address'];
        }


    }