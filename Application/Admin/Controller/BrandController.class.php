<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/5
 * Time: 15:13
 */
namespace Admin\Controller;

class BrandController extends CommonController
{
    public function brandAdd()
    {
       $this->display('Brand/brand_add');
    }

    //品牌批量编辑
    public function brandBatch()
    {
        $brand_ids = I('post.brand_ids');
        $operation = I('post.operation');
        $brand_ids = trim($brand_ids, ':');
        $brand_ids_arr = explode(':', $brand_ids);
        $b = D('Brand');
        $result = "true";

        foreach ($brand_ids_arr as $k => $v) {
            if ($operation == 'recycle') {
                if(!$b->BrandToRecycleBin($v)){
                    echo "false";
                }
            }
        }
        echo $result;
    }

    //品牌回收站批量编辑
    public function recycleBatch()
    {
        $brand_ids = I('post.brand_ids');
        $operation = I('post.operation');
        $brand_ids = trim($brand_ids, ':');
        $brand_ids_arr = explode(':', $brand_ids);
        $b = D('Brand');
        $result = "true";

        foreach ($brand_ids_arr as $k => $v) {
            if ($operation == 'restore') {
                if(!$b->BrandFromRecycleBin($v)){
                    echo "false";
                }
            } else if ($operation == 'delete') {
                if(!$b->DeleteRecycleBin($v)){
                    echo "false";
                }
            }
        }
        echo $result;
    }

    //删除回收站的品牌
    public function brandDelRecycleBin()
    {
        $brand_id = I('request.brand_id');
        $b = D('Brand');
        $result = $b->DeleteRecycleBin($brand_id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    //从回收站恢复品牌
    public function brandRevert()
    {
        $brand_id = I('request.brand_id');
        $b = D('Brand');
        $result = $b->BrandFromRecycleBin($brand_id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    //品牌编辑
    public function brandEdit()
    {
        $b = D('Brand');
        $brand_id = I('request.brand_id');
        $brand = $b->getBrandInfoById($brand_id);
        $this->assign('brand', $brand);
        $this->display('Brand/brand_edit');
    }

    public function addBrand()
    {
        $b = D('Brand');
        $store_id = session('store_id');
        $data["store_id"] = $store_id;
        $data["brand_name"] = I('post.brand_name');
        $data["brand_desc"] = I('post.brand_desc');
        $data["site_url"] = I('post.site_url');
        $data["sort_order"] = I('post.sort_order');

        if ($_FILES["brand_logo"]["error"] == 0) {
            $data["brand_logo"] = $this->uploadBrandLogo($store_id);
        }

        if (!$b->create($data)) {
            $this->error($b->getError());
        }

        if($b->add($data)){
            $this->success("成功添加品牌");
        }else{
            $this->error("品牌添加失败");
        }

    }

    public function brandList()
    {
        $store_id = session("store_id");
        $brand = D('Brand');
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

        $count = $brand->where(array('store_id' => $store_id, 'is_delete' => 0))->count();
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
            $list = $brand->where(array('store_id' => $store_id, 'is_delete' => 0))->order('brand_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $brand->where(array('store_id' => $store_id, 'is_delete' => 0))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Brand/brand_list');
    }

    public function uploadBrandLogo($store_id)
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = C('UPLOAD_MAX_SIZE');// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

        $upload->savePath = 'Brand/' . $store_id . '/brand_logo/'; // 设置附件上传目录

        $info = $upload->upload(array('brand_logo' => $_FILES['brand_logo']));// 上传文件

        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            return $info['brand_logo']['savepath'] . $info['brand_logo']['savename']; //$this->success('上传成功！');
        }
    }

    public function getBrandLogo($brand_id)
    {
        $b = D('Brand');
        $logo = $b->getBrandLogo($brand_id);
        return $logo;
    }

    public function deleteUploadFile($path)
    {
        return unlink('./Uploads/' . $path);
    }


    public function updateBrand()
    {
        $b = D('Brand');
        $store_id = session('store_id');

        $data['brand_name'] = I('post.brand_name');
        $data['brand_desc'] = I('post.brand_desc');
        $data['site_url'] = I('post.site_url');
        $data['sort_order'] = I('post.sort_order');
        $data['brand_id'] = I('post.brand_id');

        if (!$b->create($data)) {
            $this->error($b->getError());
        }

        //图片被上传才进行相应的处理
        if ($_FILES["brand_logo"]["error"] == 0) {
            $old_logo = $this->getBrandLogo($data['brand_id']);
            $this->deleteUploadFile($old_logo);
            $data["brand_logo"] = $this->uploadBrandLogo($store_id);
        }

        if ($b->save($data)) {
            $this->success('成功修改品牌');
        } else {
            $this->error('修改品牌失败');
        }
    }


    public function brandRecycleBin()
    {
        $brand_id = I('request.brand_id');
        $b = D('Brand');
        $result = $b->BrandToRecycleBin($brand_id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    public function brandRecycleBinList()
    {
        $store_id = session("store_id");

        $brand = D('Brand');
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

        $count = $brand->where(array('store_id' => $store_id, 'is_delete' => 1))->count();
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
            $list = $brand->where(array('store_id' => $store_id, 'is_delete' => 1))->order('brand_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $brand->where(array('store_id' => $store_id, 'is_delete' => 1))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Brand/brand_recyclebin_list');
    }
}