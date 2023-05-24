<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date:  
 * Time: 
 */

namespace Admin\Controller;


class AuthController extends CommonController{
    public function authManage(){
        $ag = D('AuthGroup');

        $info = $ag->getAuthGroupInfo();

        $this->assign('auth_group',$info);

        $this->display('auth_list');
    }


    public function authAddGroup(){
        $ar = D('AuthRule');
        $auth_rule = $ar->getAllRules();

        $this->assign('auth_rule',$auth_rule);

        $this->display('auth_add_group');
    }

    public function authAddRule(){
        $this->display('auth_add_rule');
    }

    public function authAddRuleHandle(){
        $ar = D('AuthRule');
        $data["name"] = I('request.name');
        $data['title'] = I('request.title');

        if(!$ar->create($data)){
            $this->error($ar->getError());
        }

        if($ar->add($data)){
            $this->success("成功添加规则");
        }else{
            $this->error("添加权限规则失败");
        }


    }

    public function authAddGroupHandle(){
        $ag = D('AuthGroup');
        $auth_rule_id = I('auth_rule_id');
        $rules = "";
        foreach($auth_rule_id as $v){
            $rules .= $v . ',';
        }

        $rules = trim($rules,',');

        $title = I('title');
        $data["title"] = $title;
        $data["rules"] = $rules;


        if(!$ag->create()){
            $this->error($ag->getError());
        }

        if($ag->add($data)){
            $this->success("添加权限组成功！");
        }else{
            $this->error("添加权限组失败！");
        }


    }


    public function authRules(){
        $ag = D('AuthGroup');
        $ar = D('AuthRule');

        $id = I('request.id');

        $ownrules = $ag->getRulesById($id);

        $ownrules_arr = explode(',',$ownrules);

        $allrules = $ar->getAllRules();

        foreach($allrules as $k=>$v){
            if(in_array($v['id'],$ownrules_arr)){
                $allrules[$k]['checked'] = true;
            }else{
                $allrules[$k]['checked'] = false;
            }

        }

        $this->assign('list',$allrules);

        $this->assign('auth_group_id',$id);

        $this->display('auth_rule_list');

    }

    public function authGroupRuleEdit(){
        $ag = D('AuthGroup');

        $auth_group_id = I('request.auth_group_id');

        $auth_group_rules = I('request.auth_rule_id');

        $rules = "";

        foreach($auth_group_rules as $v){
            $rules .= $v . ",";
        }

        $rules = trim($rules,',');

        $data['rules'] = $rules;
        $data['id'] = $auth_group_id;

        if($ag->save($data)){
            $this->success('成功修改权限！');
        }else{
            $this->error('修改权限失败！');
        }


    }

    public function authRole(){

        $aga = D('AuthGroupAccess');
        $au = D('AdminUser');

        $count = $aga->count();
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

        $list = $aga->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach($list as $k=>$v){
            $list[$k]['name'] = $au->getNameById($v['uid']);
        }


        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Auth/auth_role_list');
    }


    public function authRoleDelete(){
        $aga = D('AuthGroupAccess');

        $uid = I('request.uid');

        if($uid==1){
            $this->error("管理员不允许被删除！");
        }
        $group_id = I('request.group_id');
        $result = $aga->deleteRoleFromGroup($uid,$group_id);

        if($result){
            $this->success('成功删除角色权限');
        }else{
            $this->error('角色删除失败');
        }

    }
}