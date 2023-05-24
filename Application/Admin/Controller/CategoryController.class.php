<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/1
 * Time: 13:16
 */


namespace Admin\Controller;

class CategoryController extends CommonController
{

    public function index()
    {
        
    }

    public function categoryList()
    {
        $this->display('category_list');
    }

    //加载类别
    public function loadCategory()
    {
        $store_id = session('store_id');
        $childcategory = $this->getChildCategory(0, $store_id);
        echo json_encode($childcategory);
    }

    //获得所有子类别
    public function getChildCategory($parent, $store_id)
    {
        $c = D('Category');
        $children = $c->getChildCategory($parent, $store_id);
        if ($children) {
            foreach ($children as $k => $v) {
                $children[$k]["children"] = $this->getChildCategory($v["cat_id"],$store_id);
                $children[$k]["label"] = $v["cat_name"];
                $children[$k]["id"] = $v["cat_id"];
            }
        }
        return $children;
    }

    public function addCategory($info, $parent_id)
    {
        $c = D('Category');
        $result = true;
        foreach ($info as $k => $v) {
            $data = array();
            $data['cat_id'] = $v['cat_id'];
            $data['store_id'] = session('store_id');
            $data['cat_name'] = $v['cat_name'];
            $data['parent_id'] = $parent_id;
            if(!$c->add($data)){
                $result = false;
            }
            if (isset($v['children'])) {
                $this->addCategory($v['children'], $v['cat_id']);
            }
        }
        return $result;
    }

    //ajax save category
    //TODO: 商品分类树的处理还需要考虑，对于树结构的修改是否会影响其他商铺.

    public function updateCategory()
    {
        $category = I('post.category');

        $c = D('Category');

        //删除原树，添加新的数据
        $c->deleteByStoreId(session('store_id'));

        if($this->addCategory($category, 0)){
            echo "true";
        }else{
            echo "false";
        }

    }
}