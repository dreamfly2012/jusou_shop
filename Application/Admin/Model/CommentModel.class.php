<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/4/21
 * Time: 9:36
 */
namespace Admin\Model;
use Think\Model;

class CommentModel extends Model
{
    public function getAllInfoByStoreId($store_id)
    {
        $result = $this->where(array('store_id'=>$store_id))->select();
        return $result;
    }

    public function getInfoByCommentId($comment_id)
    {
        $result = $this->where(array('comment_id'=>$comment_id))->select();
        return $result[0];
    }


    public function CommentToRecycleBin($comment_id){
        $store_id = session('store_id');
        $result = $this->where(array('comment_id'=>$comment_id,'store_id'=>$store_id))->save(array('is_show'=>0));
        return $result;
    }

    public function CommentFromRecycleBin($comment_id){
        $store_id = session('store_id');
        $result = $this->where(array('comment_id'=>$comment_id,'store_id'=>$store_id))->save(array('is_show'=>1));
        return $result;
    }

    public function DeleteRecycleBin($comment_id){
        $store_id = session('store_id');
        $result = $this->where(array('comment_id'=>$comment_id,'store_id'=>$store_id,'is_show'=>0))->delete();
        return $result;
    }
}