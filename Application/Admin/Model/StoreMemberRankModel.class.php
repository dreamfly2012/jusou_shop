<?php
/**
 * Created by PhpStorm.
 * User: dreamfly
 * Date: 2014/12/20
 * Time: 15:42
 */

namespace Admin\Model;

use Think\Model;

class StoreMemberRankModel extends Model
{
    protected $_validate = array(
        array('rank_name', 'require', '会员等级不能为空！'),
        array('level_points', 'require', '所需积分不能为空！'),
        array('discount', 'require', '折扣不能为空！'),
        array('level_points', '/^\\d+$/', '所需积分必须是正整数!'),
        array('discount', '/^\\d+$/', '折扣必须是正整数!'),
    );

    public function getInfoById($rank_id)
    {
        $store_id = session('store_id');
        $result = $this->where(array('rank_id' => $rank_id,'store_id'=>$store_id))->select();
        return $result[0];
    }

    public function getAllInfo()
    {
        $store_id = session('store_id');
        $result = $this->where(array('store_id' => $store_id))->select();
        return $result;
    }

    public function getRankByScore($score)
    {
        $store_id = session('store_id');
        $result = $this->where(array('store_id' => $store_id))->order('level_points')->select();
        $length = count($result);

        if ($result) {
            for ($i = 0; $i < $length; $i++) {
                if ($score >= $result[$i]["level_points"] && $score < $result[$i+1]["level_points"]) {
                    return $result[$i]["rank_id"];
                }
            }
        }
    }

    public function deleteRank($rank_id){
        $store_id = session('store_id');
        $result = $this->where(array('store_id'=>$store_id,'rank_id'=>$rank_id))->delete();
        return $result;
    }


}
