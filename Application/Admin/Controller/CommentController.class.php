<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/23
 * Time: 16:44
 */

namespace Admin\Controller;

class CommentController extends CommonController{
    //增删改查
  

    public function index(){
        $this->commentList();
    }

    //添加匿名评论，user_id = 0;
    public function commentAdd(){
    	$comment_ranks = array();
    	for($i=1;$i<=5;$i++){
    		$comment_ranks[$i]['value'] = $i;
    	}
    	$g = D('Goods');
    	$goods_list = $g->getAllGoods();
    	$this->assign('goods_list',$goods_list);
    	$this->assign('comment_ranks',$comment_ranks);
    	$this->display('comment_add');
    }

    public function addComment(){
    	$c = D('Comment');
    	$data['content'] = I('request.content',null);
    	$data['comment_rank'] = I('request.comment_rank',null);
    	$data['user_id'] = 0;
    	$data['add_time'] = time();
    	$data['user_name'] = '匿名用户';
    	$data['store_id'] = session('store_id');
    	$data['goods_id'] = I('request.goods_id');
    	if($c->add($data)!=false){
    		$this->success('添加匿名评论成功');
    	}else{
    		$this->error('添加匿名评论失败');
    	}
    }

    public function commentEdit()
    {
        $comment_ranks = array();
        for($i=1;$i<=5;$i++){
            $comment_ranks[$i]['value'] = $i;
        }
        $g = D('Goods');
        $c = D('Comment');
        $store_id = session('store_id');
        $goods_list = $g->getAllGoods();
        $comment = $c->getInfoByCommentId($store_id);
        $this->assign('comment',$comment);
        $this->assign('goods_list',$goods_list);
        $this->assign('comment_ranks',$comment_ranks);
        $this->display('comment_edit');
    }

    public function updateComment()
    {
        $c = D('Comment');
        $data['content'] = I('request.content',null);
        $data['comment_rank'] = I('request.comment_rank',null);
        $data['user_id'] = I('request.user_id',null,'intval');
        $data['user_name'] = I('request.user_name',null);
        $data['store_id'] = session('store_id');
        $data['goods_id'] = I('request.goods_id');
        $data['comment_id'] = I('request.comment_id',null,'intval');

        if($c->save($data)!==false){
            $this->success('修改评论成功');
        }else{
            $this->error('修改评论失败');
        }
    }

    public function commentRecycleBin()
    {
        $comment_id = I('request.comment_id');
        $c = D('Comment');
        $result = $c->CommentToRecycleBin($comment_id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    public function commentRecycleBinList()
    {
        $store_id = session("store_id");

        $c = D('Comment');
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

        $count = $c->where(array('store_id' => $store_id, 'is_show' => 0))->count();
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
            $list = $c->where(array('store_id' => $store_id, 'is_show' => 0))->order('comment_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $c->where(array('store_id' => $store_id, 'is_show' => 0))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        $this->display('Comment/comment_recyclebin_list');
    }

    public function recycleBatch()
    {
        $comment_ids = I('post.comment_ids');
        $operation = I('post.operation');
        $comment_ids = trim($comment_ids, ':');
        $comment_ids_arr = explode(':', $comment_ids);
        $c = D('Comment');
        $result = "true";

        foreach ($comment_ids_arr as $k => $v) {
            if ($operation == 'restore') {
                if(!$c->CommentFromRecycleBin($v)){
                    echo "false";
                }
            } else if ($operation == 'delete') {
                if(!$c->DeleteRecycleBin($v)){
                    echo "false";
                }
            }
        }
        echo $result;
    }

    public function commentRevert()
    {
        $comment_id = I('request.comment_id');
        $c = D('Comment');
        $result = $c->CommentFromRecycleBin($comment_id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    public function commentDelRecycleBin()
    {
        $comment_id = I('request.comment_id');
        $c = D('Comment');
        $result = $c->DeleteRecycleBin($comment_id);
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    public function commentBatch()
    {
        $comment_ids = I('post.comment_ids');
        $operation = I('post.operation');
        $comment_ids = trim($comment_ids, ':');
        $comment_ids_arr = explode(':', $comment_ids);
        $c = D('Comment');
        $result = "true";

        foreach ($comment_ids_arr as $k => $v) {
            if ($operation == 'recycle') {
                if(!$c->CommentToRecycleBin($v)){
                    echo "false";
                }
            }
        }
        echo $result;
    }



    public function commentList(){
        $c = D('Comment');
        $store_id = session('store_id');

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

        $count = $c->where(array('store_id' => $store_id, 'is_show' => 1))->count();

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
            $list = $c->where(array('store_id' => $store_id, 'is_show' => 1))->order('comment_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $c->where(array('store_id' => $store_id, 'is_show' => 1))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list',$list);

        $this->display('Comment/comment_list');
    }
}