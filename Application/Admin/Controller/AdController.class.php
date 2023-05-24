<?php

namespace Admin\Controller;

class AdController extends CommonController{
	public function adAdd(){
		$ap = D('AdPosition');
		$position = $ap->getAllPosition();
		$this->assign('position',$position);
		$this->display('Ad/ad_add'); 
	}

	public function addAd(){
		$a = D('Ad');
		$store_id = session('store_id');
		$data["ad_name"] = I('post.ad_name');
		$data["ad_link"] = I('post.ad_link');
		$data["ad_code"] = I('post.ad_code');
		$data["position_id"] = I('post.position_id');
		$data["start_time"] = I('post.start_time',null,'strtotime');
		$data["end_time"] = I('post.end_time',null,'strtotime');

        //图片处理 ad_link
		$data["ad_img"] = $this->uploadAdImg($store_id);

		if(!$a->create($data)){
			$this->error($a->getError());
		}

		if($a->add($data)){
			$this->success("成功添加广告");
		}else{
			$this->error("添加广告失败");
		}
	}

	//上传图片
    public function uploadAdImg($store_id)
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

        $upload->savePath = 'Ad/' . $store_id . '/ad_img/'; // 设置附件上传目录

        $info = $upload->upload(array('ad_img' => $_FILES['ad_img']));// 上传文件

        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            return $info['ad_img']['savepath'] . $info['ad_img']['savename']; //$this->success('上传成功！');
        }
    }

	public function adList(){
		$store_id = session("store_id");
        $goods = D('Ad');
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
            $list = $goods->where(array('store_id' => $store_id, 'is_delete' => 0))->order('ad_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else {
            $list = $goods->where(array('store_id' => $store_id, 'is_delete' => 0))->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $this->assign('list', $list);// 赋值数据集
        $this->assign('page', $show);// 赋值分页输出
        
		$this->display('Ad/ad_list');
	}

    public function adEdit(){
        $a = D('Ad');
        $ap = D('AdPosition');
        $position = $ap->getAllPosition();

        $ad_id = I('request.ad_id');
        $ad_info = $a->getAdById($ad_id);

        $this->assign('position',$position);
        $this->assign('ad',$ad_info[0]);
        $this->display('Ad/ad_edit');
    }

    public function updateAd(){
        $a = D('Ad');
        $store_id = session('store_id');
        $data['ad_id'] = I('post.ad_id');
        $data["ad_name"] = I('post.ad_name');
        $data["ad_link"] = I('post.ad_link');
        $data["ad_code"] = I('post.ad_code');
        $data["position_id"] = I('post.position_id');
        $data["start_time"] = I('post.start_time',null,'strtotime');
        $data["end_time"] = I('post.end_time',null,'strtotime');

        //图片处理 ad_link
        if ($_FILES["ad_img"]["error"] == 0) {
            $ad = $a->getAdById($data["ad_id"]);

            $ad_img = $ad[0]['ad_img'];

            $this->deleteUploadAd($ad_img);

            $data["ad_img"] = $this->uploadAdImg($store_id);

        }

        if(!$a->create($data)){
            $this->error($a->getError());
        }

        if($a->save($data)){
            $this->success("成功修改广告");
        }else{
            $this->error("修改广告失败");
        }

    }

    public function deleteUploadAd($path){
        return unlink('./Uploads/' . $path);
    }


}