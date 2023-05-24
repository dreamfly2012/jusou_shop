<?php

/**
 * time：2015年1月15日14:11:12
 * author : dreamfly
 * 	function: 导航控制器
 */

namespace Home\Controller;
use Think\Controller;

class NavController extends Controller
{
    public function getNavChildren($id) {
        $n = D('Nav');
        $children = $n->getNavByParentId($id);
        if (!is_null($children)) {
            foreach ($children as $k => $v) {
                $children[$k]['children'] = $this->getNavChildren($v['id']);
            }
        }
        return $children;
    }
    
    public function assignNav() {
        $n = D('Nav');
        $nav = $n->getNavByParentId(0);
        foreach ($nav as $k => $v) {
            $nav[$k]['children'] = $this->getNavChildren($v['id']);
        }
        $this->assign('nav', $nav);
    }
}
