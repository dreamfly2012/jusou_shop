<?php
/**
 * @author dreamfly	
 * @version 0.2
 *
 */

namespace Home\Controller;

class CommentController extends CommentController{

	/**
	 * 添加评论处理
	 */
	function addCommentHandle()
    {
        $_POST = $this->zaddslashes($_POST);
		$_POST['add_date'] = time();
		$_POST['show'] = 0;

        if(md5($_POST['code']) != $_SESSION['verify'])
        {
			$this->error(C('ERROR_VERIFY_ERROR'));
		}
        else
        {
            $addStatus = $this->goodsModel->getAddGoodsCommentDataStatus($_POST);
        	if($addStatus)
            {
        		$this->success(C('SUCCESS_COMMENT_SUCCESS'));
        	}
            else
            {
        		$this->error(C('ERROR_COMMENT_FAILURE'));
        	}
		}
	}
}