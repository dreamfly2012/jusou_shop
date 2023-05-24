<?php
namespace Home\Controller;

class LotteryController extends CommonController
{
    
    private $begin_time;
    
    //开始时间  0-不限制
    
    private $stop_time;
    
    //结束时间  0-不限制
    
    private $cost_points;
    
    //消耗积分
    
    //本次抽奖的奖项信息，必须按照从大到小的顺序进行填写，id为奖次，prize为中奖信息,v为中奖概率,num为奖品数量
    //需要注意的是，该处也必须包含不中奖的信息，概率从小到大进行排序
    private $prize_arr;
    
    // array(
    //     '0' => array('id' => 1, 'prize' => '44元购买1G/年空间', 'v' => 1,'num'=>1),
    //     '1' => array('id' => 2, 'prize' => '55元购买1G/年空间', 'v' => 2,'num'=>2),
    //     '2' => array('id' => 3, 'prize' => '66元购买1G/年空间', 'v' => 5,'num'=>2),
    //     '3' => array('id' => 4, 'prize' => '77元购买1G/年空间', 'v' => 10,'num'=>3),
    //     '4' => array('id' => 5, 'prize' => '88元购买1G/年空间', 'v' => 15,'num'=>4),
    //     '5' => array('id' => 6, 'prize' => '99元购买1G/年空间', 'v' => 67,'num'=>10),
    // );
    
    public function index() {
        $this->display('index');
    }
    
    public function randomInit() {
        
        $this->ajaxReturn($data);
    }
    
    /**
     * 生成中奖信息，ajax进行请求该方法，需要客户填写QQ号码
     */
    public function generateAward() {
        if (empty($user_id)) {
            $data['info'] = 0;
            $data['message'] = C('ERROR_NO_LOGIN');
            $this->ajaxReturn($data);
            exit;
        }
        
        if (!empty($this->begin_time) && time() < strtotime($this->begin_time)) {
            $data['info'] = 1;
            $data['message'] = '抽奖还没有开始，开始时间为：' . $this->begin_time;
            $this->ajaxReturn($data);
            exit;
        }
        
        if (!empty($this->stop_time) && time() > strtotime($this->stop_time)) {
            $data['info'] = 1;
            $data['info'] = '本次抽奖已经结束，结束时间为：' . $this->stop_time;
            $this->ajaxReturn($data);
            exit;
        }
        
        //获取奖项信息数组，来源于私有成员
        $prize_arr = $this->prize_arr;
        
        foreach ($prize_arr as $key => $val) {
            $arr[$val['id']] = $val['v'];
        }
        
        //$rid中奖的序列号码
        $rid = $this->get_rand($arr);
        
        //根据概率获取奖项id
        
        $str = $prize_arr[$rid - 1]['prize'];
        
        //中奖项
        
        $cost_points = $this->cost_points;


        
        //从数据库中获取特定QQ号已经参加抽奖的次数，如果大于等于3则提示次数用完
        if ($Choujiang->where("qq_no='{$qq_no}'")->count() >= 3) {
            $str = '您3次抽奖机会已经用完！';
            $rid = 0;
            
            //从数据库中获取特定奖项序号的次数，大于等于设置的最大次数则提示奖品被抽完，如果需要一直中最后一个纪念奖，则修改该处即可
        } 
        elseif ($Choujiang->where("rid={$rid}")->count() >= $prize_arr[$rid - 1]['num']) {
            $str = '很抱歉，您所抽中的奖项已经中完！';
            $rid = 0;
        }
        
    }


    private function get_rand($proArr) {
        $result = '';
        
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } 
            else {
                $proSum-= $proCur;
            }
        }
        unset($proArr);
        return $result;
    }
}
    