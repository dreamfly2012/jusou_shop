<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/4/16
 * Time: 11:47
 */
namespace Home\Model;
use Think\Model;

class ArticleModel extends Model
{
    public function getArticleById($id)
    {
        $result = $this->where(array('article_id'=>$id))->select();
        return $result[0];
    }
}