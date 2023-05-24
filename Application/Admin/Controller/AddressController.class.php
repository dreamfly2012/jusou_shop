<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/12
 * Time: 10:43
 */


namespace Admin\Controller;

class AddressController extends CommonController{
    public function getDefaultDeliverAddress($address_id){
        $aa = D('AdminAddress');
        $r  = D('Region');
        $province_id = $aa->getProvinceIdById($address_id);
        $province = $r->getProvinceNameById($province_id);
        $city_id = $aa->getCityIdById($address_id);
        $city = $r->getCityNameById($city_id);
        $district_id = $aa->getDistrictIdById($address_id);
        $district = $r->getDistrictNameById($district_id);
        $address = $aa->getAddressNameById($address_id);

        return $province . $city . $district . $address;
    }

    public function getDefaultRefundAddress($address_id){
        $aa = D('AdminAddress');
        $r  = D('Region');
        $province_id = $aa->getProvinceIdById($address_id);
        $province = $r->getProvinceNameById($province_id);
        $city_id = $aa->getCityIdById($address_id);
        $city = $r->getCityNameById($city_id);
        $district_id = $aa->getDistrictIdById($address_id);
        $district = $r->getDistrictNameById($district_id);
        $address = $aa->getAddressNameById($address_id);

        return $province . $city . $district . $address;
    }

    public function addressList(){
        $type = I('request.type');

        $user_id = session('user_id');

        $aa = D('AdminAddress');
        $order = array();

        $order_by = I('request.order_by', null);
        $order_sort = I('request.order_sort', null);

        if (!is_null($order_by)) {
            $order[$order_by] = $order_sort;
        }
        if ($order_sort == "DESC") {
            $this->assign('order_sort', 'ASC');
        } else {
            $this->assign('order_sort', 'DESC');
        }

        $count = $aa->where(array('user_id' => $user_id,'type'=>$type))->count();
        // 查询满足要求的总记录数
        $Page = new \Think\Page($count, 10);
        $config = array(
            'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
            'prev' => '上一页',
            'next' => '下一页',
            'first' => '第一页',
            'last' => '最后一页',
            'theme' => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
        );
        foreach ($config as $k => $v) {
            $Page->setConfig($k, $v);
        }

        // 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();
        // 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        if (empty($order)) {
            $list = $aa->where(array('user_id' => $user_id,'type'=> $type))->order('address_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $aa->where(array('user_id' => $user_id,'type'=> $type))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('type',$type);

        if($type=='deliver'){
            $this->assign('title','发货地址管理');
            $this->display('Address/address_list');
        }else{
            $this->assign('title','退货地址管理');
            $this->display('Address/address_list');
        }
    }

    public function addressEdit(){
        $address_id = I('request.address_id');
        $aa = D('AdminAddress');
        $r = D('Region');
        $address = $aa->getAddressInfoById($address_id);


        //TODO: 分配省份，城市，地区列表

        $province_list = $r->getProvinceListByCountry($address['country']);
        $city_list = $r->getCityListByProvince($address['province']);
        $district_list = $r->getDistrictListByCity($address['city']);

        $this->assign('province_list',$province_list);
        $this->assign('city_list',$city_list);
        $this->assign('district_list',$district_list);

        $this->assign('address',$address);

        $this->display('Address/address_edit');

    }

    public function addressUpdate(){
        $data['province'] = I('post.province');
        $data['city'] = I('post.city');
        $data['district'] = I('post.district');
        $data['address_name'] = I('post.address');
    }


    public function addressChangeProvince(){
        $r = D('Region');
        $province_id  = I('request.province');
        $city_list = $r->getCityListByProvince($province_id);
        $result = "";
        foreach ($city_list as $k=>$v) {
            $result .= "<option value='".$v['region_id']."'>".$v["region_name"]."</option>";
        }
        echo $result;
    }

    public function addressDefaultSet($address_id,$type){
        $aa = D('AdminAddress');
        $aa->addressSetDefault($address_id,$type);

    }


    public function addressChangeCity(){
        $r = D('Region');
        $city_id = I('request.city');
        $district_list = $r->getDistrictListByCity($city_id);
        $result = "";
        foreach($district_list as $k=>$v){
            $result .= $result .= "<option value='".$v['region_id']."'>".$v["region_name"]."</option>";
        }
        echo $result;
    }

    public function addressDeliverHandle(){
        $address_id = I('request.address_id');
        $this->addressDefaultSet($address_id,'deliver');
        echo $this->getDefaultDeliverAddress($address_id);
    }

    public function addressRefundHandle(){
        $address_id = I('request.address_id');
        $this->addressDefaultSet($address_id,'refund');
        echo $this->getDefaultRefundAddress($address_id);
    }
}