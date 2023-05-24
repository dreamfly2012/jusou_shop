<?php
namespace Admin\Controller;

class ConsigneeController extends CommonController
{
    private $status;

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function checkConsigneeInfo()
    {

        $consignee = D('Consignee');


        $consigneeInfo = $consignee->getConsigneeInfo(0);

        dump($consigneeInfo);


        die();

        ($consignee->validate()->create($data));

        session('consignee', array('consignee' => 'jfu', 'email' => 'jia.fu@avepoint.com', 'country' => '0', 'province' => '2', 'city' => '52', 'district' => '502', 'address' => '湖西路市民大厦', 'tel' => '15044025583', 'mobile' => '15044025583'));

        dump(session('consignee'));

        die();

        return $this->status;
    }

    public function getConsigneeInfo2()
    {

    }
}