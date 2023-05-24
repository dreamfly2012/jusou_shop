<?php

namespace Home\Controller;


use Think\Controller;

/**
 * Class CommonController
 * @package Home\Controller
 */
class CommonController extends Controller{
    private $role;
    private $privilege;
    protected $store_id;

    public function __construct(){
        parent::__construct();
        header("Content-type: text/html; charset=utf-8");
        
        $this->store_id = I('request.store_id','1','intval');
        $storeConfigM = D('StoreConfig');
        $storeConfig = $storeConfigM->getAllInfoByStoreId($this->store_id);

        $store_config = array();
        foreach($storeConfig as $key=>$val)
        {
            $store_config[$val['key']]  = $val['value'];
        }

        $this->assign('store_config',$store_config);

        //导航赋值
        R('Nav/assignNav');

        //产品分类赋值
        R('Category/assignNavCategory',array('store_id'=>$this->store_id));
    }

    //检查是否登录
    public function checkLogin(){
        if(is_null(session('user_id'))){
            return false;
        }else{
            return true;
        }
    }

    //生成随机字符串
    public function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }


    
    /**
     * @return mixed
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @param mixed $privilege
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }


    protected function hasPrivilege(){
        //$this->display("");
    }


    //根据百度api获取详细地址
    //http://developer.baidu.com/map/index.php?title=webapi/guide/webservice-geocoding
    public function get_detail_address()
    {
        $key = 'sWkz5OPjBb9AaSQ7RNGKieTX';
        $callback = 'renderReverse';

        //$location = '43.89833761,125.31364243';

        $ip = $this->get_real_ip();

        $ip_url = 'http://api.map.baidu.com/location/ip?ak=' . $key . '&ip=' . $ip . '&coor=bd09ll';

        $ip_content = file_get_contents($ip_url);

        $ip_info = json_decode($ip_content);

        $location_x = $ip_info->content->point->x;

        $location_y = $ip_info->content->point->y;

        $location = $location_y . ',' . $location_x;

        $location_url = 'http://api.map.baidu.com/geocoder/v2/?ak=' . $key . '&callback=' . $callback . '&location=' . $location . '=&output=json&pois=0';

        $location_content = file_get_contents($location_url);

        $location_content = trim($location_content,'renderReverse&&renderReverse');
        $location_content = trim($location_content,'(');
        $location_content = trim($location_content,')');

        $location_info = json_decode($location_content);

        if($location_info->status==0)
        {
            return $location_info;
        }
        else
        {
            return -1;
        }
    }



    protected function get_real_ip() {
        $ip = false;
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = FALSE;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi("^(10|172.16|192.168).", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
}