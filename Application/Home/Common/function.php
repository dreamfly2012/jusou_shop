<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2015/4/16
 * Time: 13:31
 */

function CategoryNameById($id)
{
    $gc = D('GoodsCat');
    $categoryName = $gc->getCatNameById($id);
    return $categoryName;
}