<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/20
 * Time: 14:42
 */

namespace Admin\Controller;

class MemberController extends CommonController
{
    public function index()
    {
        $this->memberList();
    }

    public function memberAdd()
    {
        $this->assign('is_search', true);
        $this->display('Member/member_add');
    }

    /**
     * @param $rank
     * @param $score
     * @return bool
     * 检查会员积分和等级是否对应，不对应返回false
     */
    public function checkRankWithScore($rank, $score)
    {
        $smr = D('StoreMemberRank');
        $rank_id = $smr->getRankByScore($score);
        if ($rank != $rank_id) {
            return false;
        } else {
            return true;
        }
    }

    public function addMember()
    {
        $sm = D('StoreMember');

        $user_id = I('post.user_id');
        $data['user_id'] = $user_id;
        $data["store_id"] = session('store_id');
        $data["rank"] = I('post.rank');
        $data["score"] = I('post.score');
        $data["join_time"] = time();

        if (!$this->checkRankWithScore($data['rank'], $data['score'])) {
            $this->error('会员积分和等级不符合！');
        }

        if($this->checkUserIsExist($user_id)){
            $this->error('此会员已经存在，不需要重复添加！');
        }

        if ($sm->add($data)) {
            $this->redirect('Member/memberList', array(), 3, '添加会员成功！');
        } else {
            $this->error('添加会员失败！');
        }
    }

    public function searchUser()
    {
        $u = D('User');
        $user_name = I('post.user_name');
        $email = I('post.email');
        $id = $u->getUserIdByEmailOrName($email, $user_name);
        if ($id > 0) {
            $this->showAddMember($id);
        } else {
            $this->error("用户不存在");
        }
    }

    public function showAddMember($id)
    {
        $u = D('User');
        $smr = D('StoreMemberRank');
        $rank_info = $smr->getAllInfo();
        $user = $u->getInfoById($id);
        $this->assign('rank', $rank_info);
        $this->assign('user', $user);
        $this->display('Member/member_add');
    }


    public function memberList()
    {
        $store_id = session("store_id");
        $sm = D('StoreMember');
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

        $count = $sm->where(array('store_id' => $store_id, 'is_active' => 0))->count();

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
            $list = $sm->where(array('store_id' => $store_id, 'is_active' => 0))->order('user_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $smr = D('StoreMemberRank');
            $u = D('User');
            foreach ($list as $k => $v) {
                $list[$k]['user_info'] = $u->getInfoById($v['user_id']);
                $list[$k]['rank_info'] = $smr->getInfoById($v['rank']);
            }
        } else {
            $list = $sm->where(array('store_id' => $store_id, 'is_active' => 0))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $smr = D('StoreMemberRank');
            $u = D('User');
            foreach ($list as $k => $v) {
                $list[$k]['user_info'] = $u->getInfoById($v['user_id']);
                $list[$k]['rank_info'] = $smr->getInfoById($v['rank']);
            }
        }

        dump($list);

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出

        $this->display('Member/member_list');

    }

    //TODO: 会员积分和会员级别需要关联，如果修改会员级别，相应的会员积分也需要变化
    public function memberEdit()
    {
        $sm = D('StoreMember');
        $smr = D('StoreMemberRank');
        $u = D('User');
        $store_id = session('store_id');
        $id = I('request.id');
        $member_info = $sm->getInfoById($id);
        $user_info = $u->getInfoById($member_info['user_id']);
        $rank_info = $smr->getAllInfo();
        $this->assign('member', $member_info);
        $this->assign('rank', $rank_info);
        $this->assign('user', $user_info);
        $this->display('Member/member_edit');
    }

    public function updateMember()
    {
        $sm = D('StoreMember');
        $data['id'] = I('post.id');
        $data['rank'] = I('post.rank');
        $data['score'] = I('post.score');

        if (!$sm->create()) {
            $this->error($sm->getError());
        }

        if (!$this->checkRankWithScore($data['rank'], $data['score'])) {
            $this->error('会员积分和等级不符合！');
        }

        if ($sm->save($data) === false) {
            $this->error('修改会员信息失败！');
        } else {
            $this->success('修改会员成功！');
        }
    }


    public function memberDelete()
    {
        $sm = D('StoreMember');
        $user_id = I('post.user_id');
        $result = $sm->deleteMemberByUserId($user_id);
        if (!$result) {
            echo "false";
        } else {
            echo "true";
        }
    }


    public function memberRank()
    {
        $smr = D('StoreMemberRank');
        $store_id = session('store_id');
        $rank_info = $smr->getAllInfo();
        $this->assign('list', $rank_info);
        $this->display('Member/member_rank');
    }

    public function deleteMemberRank()
    {
        $smr = D('StoreMemberRank');
        $rank_id = I('post.rank_id', null);
        if (!is_null($rank_id)) {
            $result = $smr->deleteRank($rank_id);
            if (!$result) {
                echo "false";
            } else {
                echo "true";
            }
        }
    }

    public function rankEdit()
    {
        $rank_id = I('rank_id', null);
        if (is_null($rank_id)) {
            $this->error("非法错误的参数！");
        } else {
            $smr = D('StoreMemberRank');
            $rank_info = $smr->getInfoById($rank_id);
            $this->assign('rank', $rank_info);
            $this->display('Member/rank_edit');
        }
    }

    public function updateRank()
    {
        $smr = D('StoreMemberRank');
        $data["rank_id"] = I('post.rank_id');
        $data["rank_name"] = I('post.rank_name');
        $data["min_points"] = I('post.min_points');
        $data["max_points"] = I('post.max_points');
        $data["discount"] = I('post.discount');
        if (!$smr->create($data)) {
            $this->error($smr->getError());
        }

        if ($smr->save($data) === false) {
            $this->error('更新会员等级失败！');
        } else {
            $this->success('更新会员等级成功！');
        }
    }

    public function rankAdd()
    {
        $this->display('Member/rank_add');
    }

    public function addRank()
    {
        $smr = D('StoreMemberRank');
        $data['store_id'] = session('store_id');
        $data["rank_name"] = I('post.rank_name');
        $data["level_points"] = I('post.level_points');
        $data["discount"] = I('post.discount');
        if (!$smr->create($data)) {
            $this->error($smr->getError());
        }

        if ($smr->add($data) === false) {
            $this->error('添加会员等级失败！');
        } else {
            $this->success('添加会员等级成功！');
        }
    }


    public function memberBatch()
    {
        $sm = D('StoreMember');
        $user_ids = I('post.user_ids');
        $user_ids = trim($user_ids, ':');
        $user_ids_arr = explode(':', $user_ids);
        $operation = I('operation');
        if($operation=='delete'){
            foreach($user_ids_arr as $v){
                if(!$sm->deleteMemberById($v)){
                    echo "false";
                }
            }
        }
        echo "true";
    }

    public function checkUserIsExist($user_id){
        $sm = D('StoreMember');
        $id = $sm->getInfoByUserId($user_id);
        if(is_null($id)){
            return false;
        }else{
            return true;
        }
    }


}