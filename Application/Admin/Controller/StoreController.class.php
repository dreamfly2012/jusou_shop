<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/17
 * Time: 10:29
 */


namespace Admin\Controller;

class StoreController extends CommonController
{
    /**
     *
     */
    public function storeBasicSetting()
    {
        $s = D('Store');
        $store_id = session('store_id');
        $store_info = $s->getStoreInfo($store_id);
        $this->assign('store', $store_info);
        $this->display('Store/store_basic_setting');
    }

    public function storeDisplaySetting()
    {
        $s = D('Store');
        $store_id = session('store_id');
        $store_info = $s->getStoreInfo($store_id);
        $this->assign('store', $store_info);
        $this->display('Store/store_display_setting');
    }

    public function storeBasicSettingHandle()
    {
        $s = D('Store');
        if (!$s->create()) {
            $this->error($s->getError());
        } else {
            $data["store_id"] = session('store_id');

            if (is_null($data["store_id"])) {
                $this->error('非法的用户');
            }
            $data["display_name"] = I('display_name');
            $data["title"] = I('title');
            $data["keywords"] = I('keywords');
            $data["address"] = I('address');
            $data["service_phone"] = I('service_phone');
            $data["service_qq"] = I('service_qq');
            $data["service_email"] = I('service_email');
            $data["notice_user"] = I('notice_user');
            $data["notice_shop"] = I('notice_shop');
            if ($s->save($data)) {
                $this->success("修改商店基本信息成功！");
            } else {
                $this->error("修改商店基本信息失败！");
            }
        }
    }

    public function storeDisplaySettingHandle()
    {
        $s = D('Store');
        if (!$s->create()) {
            $this->error($s->getError());
        } else {
            $data["store_id"] = session('store_id', null);
            if (is_null($data["store_id"])) {
                $this->error('非法的用户');
            }
            $data["display_name"] = I('display_name');
            $data["title"] = I('title');
            $data["keywords"] = I('keywords');
            $data["address"] = I('address');
            $data["service_phone"] = I('service_phone');
            $data["service_qq"] = I('service_qq');
            $data["service_email"] = I('service_email');
            $data["notice_user"] = I('notice_user');
            $data["notice_shop"] = I('notice_shop');
            $s->save($data);
        }
    }
}
