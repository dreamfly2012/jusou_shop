<?php

    namespace Admin\Model;

    use Think\Model;

    class AdminUserModel extends Model{
        protected $_validate = array(
            array('user_name','require','请输入用户名',1),//必须验证name
            array('password','require','请输入密码',1),//必须验证name
        );

        public function getSalt($user_name){
            $salt = $this->field('salt')->where(array('user_name'=>$user_name))->select();
            return $salt[0]['salt'];
        }

        /**
         * @param $code
         * @return bool
         * 验证激活码是否正确
         */
        public function checkActivationCode($code){
            $result = $this->field('code')->where(array('code'=>$code))->select();
            if(is_null($result)){
                return false;
            }else{
                return true;
            }
        }

        public function checkEmailExist($email){
            $info = $this->field('email')->where(array('email'=>$email))->select();
            if($info){
                return true;
            }else{
                return false;
            }
        }

        public function getRoleIdByUserId($user_id){
            $result = $this->where(array('user_id'=>$user_id))->select();
            return $result[0]['role_id'];
        }

        public function getNameById($user_id){
            $result = $this->where(array('user_id'=>$user_id))->select();
            return $result[0]['user_name'];
        }

        public function getUserIdByEmail($email){
            $result = $this->where(array('email'=>$email))->select();
            return $result[0]["user_id"];
        }

        public function getUserIdByCode($code){
            $result = $this->where(array('code'=>$code))->select();
            return $result[0]['user_id'];
        }
    }