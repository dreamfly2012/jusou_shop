<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/11/27
 * Time: 10:27
 */

namespace Admin\Controller;

use Think\Auth;

class GoodsController extends AuthController
{
    //商品批量处理
    // TODO:目前只是批量处理删除操作
    /**
     *
     */
    public function goodsBatch()
    {
        $goods_ids = I('post.goods_ids');
        $operation = I('post.operation');
        $goods_ids = trim($goods_ids, ':');
        $goods_ids_arr = explode(':', $goods_ids);
        foreach ($goods_ids_arr as $k => $v) {
            if ($operation == 'recycle') {
                $g = D('Goods');
                if(!$g->GoodsToRecycleBin($v)){
                    echo "false";
                }
            }
        }
        echo "true";
    }


    //批量删除回收站商品
    public function recycleBatch()
    {
        $goods_ids = I('post.goods_ids');
        $operation = I('post.operation');
        $goods_ids = trim($goods_ids, ':');
        $goods_ids_arr = explode(':', $goods_ids);
        $result = "true";
        foreach ($goods_ids_arr as $k => $v) {
            $g = D('Goods');
            if ($operation == 'delete') {
                $result = $g->DeleteRecycleBin($v);
                if(!$result){
                    echo "false";
                }
            } else if ($operation == "restore") {
                $result = $g->GoodsFromRecycleBin($v);
                if(!$result){
                    echo "false";
                }
            }
        }
        echo $result;
    }


    //商品添加
    public function goodsAdd()
    {
        $b = D('Brand');
        $store_id = session('store_id');
        $brands = $b->getBrandInfo($store_id);
        $this->assign('brands', $brands);
        $this->display('Goods/goods_add');
    }


    //商品排序
    public function goodsSort()
    {
        $sort_by = I('post.sort_by');
        $sort_order = I('post.sort_order');
        $sort[$sort_by] = $sort_order;

        if (is_array($sort)) {
            if ($sort_order == "DESC") {
                $this->assign('sort_order', 'ASC');
            } else {
                $this->assign('sort_order', 'DESC');
            }

            $this->goodsTable($sort);
            $this->display('Goods/goods-table');
        }
    }

    //商品编辑
    public function goodsEdit()
    {
        $store_id = session('store_id');

        $g = D('Goods');
        $b = D('Brand');
        $gg = D('GoodsGallery');
        $ga = D('GoodsAttr');

        $goods_id = I('request.goods_id');

        $goods_gallery = $gg->getGalleryInfo($goods_id);
        $goods_attr = $ga->getGoodsAttrInfo($goods_id);


        $goods_info = $g->getGoodsInfo($goods_id);
        $brands = $b->getBrandInfo();
        $this->assign('skus', $goods_attr);
        $this->assign('galleries', $goods_gallery);
        $this->assign('goods', $goods_info);
        $this->assign('brands', $brands);
        $this->display('Goods/goods_edit');
    }

    //商品进入回收站
    public function goodsRecycleBin()
    {
        $goods_id = I('request.goods_id');
        $goods = D('Goods');
        $result = $goods->GoodsToRecycleBin($goods_id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    //商品上架下架处理
    public function goodsOnSaleOrNot()
    {
        $goods_id = I('request.goods_id', null);
        $is_on_sale = I('request.is_on_sale', null);
        if (!is_null($goods_id)) {
            $goods = D('Goods');
            $result = $goods->UpdateGoodsOnsale($goods_id, $is_on_sale);
            if($result){
                echo "true";
            }else{
                echo "false";
            }
        }
    }

    //检查商品编号是否存在
    public function checkGoodsSn()
    {
        $goods_sn = I('request.goods_sn');
        $goods = D('Goods');

        $result = $goods->checkExistGoodsSn($goods_sn);
        if ($result) {
            echo json_encode(array('error' => 1, 'message' => "商品编号已经存在"));
        } else {
            echo json_encode(array('error' => 0, 'message' => ""));
        }
    }


    //加载商品的品牌
    public function loadGoodsBrand()
    {
        $parent = 0;
        $childbrand = $this->getChildBrand($parent);
        echo json_encode($$childbrand);
    }


    //获得商品品牌的子类别
    public function getChildBrand($parent)
    {
        $brand = D('Brand');
        $children = $brand->getChildBrand($parent);
        if ($children) {
            foreach ($children as $k => $v) {
                $children[$k]["children"] = $this->getChildCategory($v["cat_id"]);
                $children[$k]["label"] = $v["cat_name"];
            }
        }
        return $children;
    }


    //上传图片
    public function uploadGoodsImg($store_id)
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

        $upload->savePath = 'images/' . $store_id . '/goods_img/'; // 设置附件上传目录

        $info = $upload->upload(array('goods_img' => $_FILES['goods_img']));// 上传文件

        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            return $info['goods_img']['savepath'] . $info['goods_img']['savename']; //$this->success('上传成功！');
        }
    }


    //上传相册
    public function uploadGallery($store_id)
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

        $upload->savePath = 'images/' . $store_id . '/goods_gallery/'; // 设置附件上传目录

        $info = $upload->upload(array('img_url' => $_FILES['img_url']));// 上传文件

        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            return $info; //$this->success('上传成功！');
        }
    }

    //裁剪图片
    public function cropGoodsImg($path)
    {
        $image = new \Think\Image();
        $image->open('./Uploads/' . $path);
        $pos = strrpos($path, '/');
        $folder = substr($path, 0, $pos + 1);
        $goods_img = substr($path, $pos + 1);
        $goods_thumb = $folder . "thumb_" . $goods_img;
        $goods_thumb_save = './Uploads/' . $goods_thumb;
        //TODO :裁切失败的处理没有加入
        $info = $image->thumb(100, 100, \Think\Image::IMAGE_THUMB_FIXED)->save($goods_thumb_save, 'jpg', 100);
        return $goods_thumb;
    }

    public function deleteUploadFile($path)
    {
        return unlink('./Uploads/' . $path);
    }

    public function getGoodsImg($goods_id)
    {
        $goods = D('Goods');
        $img = $goods->getGoodsImgById($goods_id);
        return $img;
    }

    public function getGoodsThumb($goods_id)
    {
        $goods = D('Goods');
        $thumb = $goods->getGoodsThumbById($goods_id);
        return $thumb;
    }

    //ajax 删除商品相册 删除数据库，删除文件
    public function delGoodsGallery()
    {
        $goods_gallery = D('GoodsGallery');
        $img_id = I('post.img_id');
        $img_url = $goods_gallery->getImgUrlById($img_id);
        $thumb_url = $goods_gallery->getThumbUrlById($img_id);
        $this->deleteUploadFile($img_url);
        $this->deleteUploadFile($thumb_url);
        $result = $goods_gallery->deleteGalleryByImgId($img_id);
        echo $result;
    }

    //ajax 删除商品属性
    public function delGoodsAttr()
    {
        $goods_attr = D('GoodsAttr');
        $goods_attr_id = I('post.goods_attr_id');
        $result = $goods_attr->deleteAttrByGoodsAttrId($goods_attr_id);
        echo $result;
    }


    //添加商品处理
    public function addGoods()
    {
        $goods = D('Goods');
        $data["goods_sn"] = I('post.goods_sn');
        $data["store_id"] = session('store_id');
        if (!$goods->create()) {
            $this->error($goods->getError());
        }

        if ($goods->where(array('goods_sn' => $data["goods_sn"],'store_id'=>$data["store_id"]))->find()) {
            $this->error('商品编号已经存在');
        }
        $data["goods_id"] = I('post.goods_id');
        $data["goods_name"] = I('post.goods_name');
        $data['cat_id'] = I('post.cat_id');
        $data["brand_id"] = I('post.brand_id');
        $data["market_price"] = I('post.market_price');
        $data["shop_price"] = I('post.shop_price');
        $data["promote_price"] = I('post.promote_price');
        $data["is_promote"] = I('post.is_promote');
        $data["promote_start_date"] = I('post.promote_start_date', null, 'strtotime');
        $data["promote_end_date"] = I('post.promote_end_date', null, 'strtotime');
        $store_id = session('store_id');

        //图片被上传才进行相应的处理
        if ($_FILES["goods_img"]["error"] == 0) {
            $data["goods_img"] = $this->uploadGoodsImg($store_id);
            $data["goods_thumb"] = $this->cropGoodsImg($data["goods_img"]);
            $oldimg = $this->getGoodsImg($data["goods_id"]);
            $oldthumb = $this->getGoodsThumb($data['goods_id']);
            $this->deleteUploadFile($oldimg);
            $this->deleteUploadFile($oldthumb);
        }

        //TODO 商品相册处理 暂时未发现bug
        if (!is_null($_FILES['img_url'])) {
            $goodsgallery = D('GoodsGallery');
            $info = $this->uploadGallery($store_id);
            foreach ($info as $k => $v) {
                $gallery['img_url'] = $v["savepath"] . $v["savename"];
                $gallery['thumb_url'] = $this->cropGoodsImg($gallery['img_url']);
                $gallery['goods_id'] = I('post.goods_id');
                $goodsgallery->add($gallery);
            }
        }


        //TODO 商品sku 处理
        $goodsattr = D('GoodsAttr');
        if (!$goodsattr->create()) {
            $this->error($goodsattr->getError());
        }

        if (!is_null(I('post.attr_name', null))) {

            $sku['goods_attr_id'] = I('post.goods_attr_id');
            $sku['attr_name'] = I('post.attr_name', null);
            $sku['attr_value'] = I('post.attr_value', null);
            $sku['attr_price'] = I('post.attr_price', null);

            //有goods_attr_id 的 进行更新操作
            if (is_array($sku['goods_attr_id'])) {
                foreach ($sku['goods_attr_id'] as $k => $v) {
                    $ssku = array();
                    $ssku["goods_attr_id"] = $v;
                    $ssku["attr_name"] = $sku['attr_name'][$k];
                    $ssku["attr_value"] = $sku['attr_value'][$k];
                    $ssku["attr_price"] = $sku['attr_price'][$k];
                    $ssku["goods_id"] = $data["goods_id"];
                    $goodsattr->save($ssku);
                }
            }

            //没有goods_attr_id的进行添加操作
            $sku_length = 0;

            if ($sku['goods_attr_id']) {
                $sku_length = count($sku['goods_attr_id']);
            }
            foreach ($sku['attr_name'] as $k => $v) {
                if ($k >= $sku_length) {
                    $ssku = array();
                    $ssku["attr_name"] = $sku['attr_name'][$k];
                    $ssku["attr_value"] = $sku['attr_value'][$k];
                    $ssku["attr_price"] = $sku['attr_price'][$k];
                    $ssku["goods_id"] = $data["goods_id"];
                    $goodsattr->add($ssku);
                }
            }

        }

        //TODO :显示的时候需不需要过滤php标签
        $data["goods_desc"] = I('post.goods_desc');
        $data["goods_weight"] = I('post.goods_weight');
        $data["goods_number"] = I('post.goods_number');
        $data["warn_number"] = I('post.warn_number');

        //checkbox未选中传递值修改为0
        $data["is_best"] = I('post.is_best', 0);
        $data["is_new"] = I('post.is_new', 0);
        $data["is_hot"] = I('post.is_hot', 0);
        $data["is_shipping"] = I('post.is_shipping', 0);

        $data["keywords"] = I('post.keywords');

        $result = $goods->add($data);
        if ($result) {
            $this->success('成功添加商品');
        } else {
            $this->error('添加商品失败');
        }
    }


    //修改商品
    public function updateGoods()
    {
        $goods = D('Goods');

        if (!$goods->create()) {
            $this->error($goods->getError());
        }

        $map["goods_id"] = array('neq', I('post.goods_id'));
        
        $map["goods_sn"] = I('post.goods_sn');

        //排除原来的编号进行判断，如果存在，就抛出异常
        
        if ($goods->field('goods_sn')->where($map)->select()) {
           $this->error('商品编号已经存在，请修改');
        }
        $data["goods_id"] = I('post.goods_id');
        $data["goods_name"] = I('post.goods_name');
        $data["goods_sn"] = I('post.goods_sn');
        $data['cat_id'] = I('post.cat_id');
        $data["brand_id"] = I('post.brand_id');
        $data["market_price"] = I('post.market_price');
        $data["shop_price"] = I('post.shop_price');
        $data["promote_price"] = I('post.promote_price');
        $data["is_promote"] = I('post.is_promote');
        $data["promote_start_date"] = I('post.promote_start_date', null, 'strtotime');
        $data["promote_end_date"] = I('post.promote_end_date', null, 'strtotime');
        $store_id = session('store_id');

        //图片被上传才进行相应的处理
        if ($_FILES["goods_img"]["error"] == 0) {
            $data["goods_img"] = $this->uploadGoodsImg($store_id);
            $data["goods_thumb"] = $this->cropGoodsImg($data["goods_img"]);
            $oldimg = $this->getGoodsImg($data["goods_id"]);
            $oldthumb = $this->getGoodsThumb($data['goods_id']);
            $this->deleteUploadFile($oldimg);
            $this->deleteUploadFile($oldthumb);
        }

        //TODO 商品相册处理 暂时未发现bug
        if (!is_null($_FILES['img_url'])) {
            $goodsgallery = D('GoodsGallery');
            $info = $this->uploadGallery($store_id);
            foreach ($info as $k => $v) {
                $gallery['img_url'] = $v["savepath"] . $v["savename"];
                $gallery['thumb_url'] = $this->cropGoodsImg($gallery['img_url']);
                $gallery['goods_id'] = I('post.goods_id');
                $goodsgallery->add($gallery);
            }
        }


        //TODO 商品sku 处理
        $goodsattr = D('GoodsAttr');
        if (!$goodsattr->create()) {
            $this->error($goodsattr->getError());
        }

        if (!is_null(I('post.attr_name', null))) {

            $sku['goods_attr_id'] = I('post.goods_attr_id');
            $sku['attr_name'] = I('post.attr_name', null);
            $sku['attr_value'] = I('post.attr_value', null);
            $sku['attr_price'] = I('post.attr_price', null);

            //有goods_attr_id 的 进行更新操作
            if (is_array($sku['goods_attr_id'])) {
                foreach ($sku['goods_attr_id'] as $k => $v) {
                    $ssku = array();
                    $ssku["goods_attr_id"] = $v;
                    $ssku["attr_name"] = $sku['attr_name'][$k];
                    $ssku["attr_value"] = $sku['attr_value'][$k];
                    $ssku["attr_price"] = $sku['attr_price'][$k];
                    $ssku["goods_id"] = $data["goods_id"];
                    $goodsattr->save($ssku);
                }
            }

            //没有goods_attr_id的进行添加操作
            $sku_length = 0;

            if ($sku['goods_attr_id']) {
                $sku_length = count($sku['goods_attr_id']);
            }
            foreach ($sku['attr_name'] as $k => $v) {
                if ($k >= $sku_length) {
                    $ssku = array();
                    $ssku["attr_name"] = $sku['attr_name'][$k];
                    $ssku["attr_value"] = $sku['attr_value'][$k];
                    $ssku["attr_price"] = $sku['attr_price'][$k];
                    $ssku["goods_id"] = $data["goods_id"];
                    $goodsattr->add($ssku);
                }
            }

        }

        //TODO :显示的时候需不需要过滤php标签
        $data["goods_desc"] = I('post.goods_desc',null,'');
        $data["goods_weight"] = I('post.goods_weight');
        $data["goods_number"] = I('post.goods_number');
        $data["warn_number"] = I('post.warn_number');

        //checkbox未选中传递值修改为0
        $data["is_best"] = I('post.is_best', 0);
        $data["is_new"] = I('post.is_new', 0);
        $data["is_hot"] = I('post.is_hot', 0);
        $data["is_shipping"] = I('post.is_shipping', 0);

        $data["keywords"] = I('post.keywords');

        $result = $goods->save($data);

        if ($result !== false) {
            $this->success('保存商品成功！');
        } else {
            $this->error('保存商品失败！');
        }

    }

    //从回收站恢复商品(ajax)
    public function goodsRevert()
    {
        $goods_id = I('request.goods_id');
        $goods = D('Goods');
        $result = $goods->GoodsFromRecycleBin($goods_id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    //删除回收站的商品(ajax)
    public function goodsDelRecycleBin()
    {
        $goods_id = I('request.goods_id');
        $goods = D('Goods');
        $result = $goods->DeleteRecycleBin($goods_id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    public function goodsRecycleBinList()
    {
        $store_id = session("store_id");
        $goods = D('Goods');
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

        $count = $goods->where(array('store_id' => $store_id, 'is_delete' => 1))->count();
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
            $list = $goods->where(array('store_id' => $store_id, 'is_delete' => 1))->order('goods_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $goods->where(array('store_id' => $store_id, 'is_delete' => 1))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Goods/goods_recyclebin_list');
    }

    //商品显示
    public function goodsList()
    {
        $store_id = session("store_id");
        $goods = D('Goods');
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

        $count = $goods->where(array('store_id' => $store_id, 'is_delete' => 0))->count();
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
            $list = $goods->where(array('store_id' => $store_id, 'is_delete' => 0))->order('goods_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $goods->where(array('store_id' => $store_id, 'is_delete' => 0))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Goods/goods_list');
    }
}