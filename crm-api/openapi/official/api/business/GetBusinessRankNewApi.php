<?php
namespace official\api\business;
use app\foundation\Api;
use app\services\GetBusinessRankNewService;

class GetBusinessRankNewApi extends Api
{
    public function run()
    {
        
        $service = GetBusinessRankNewService::instance();
        $result = $service->getBusinessRank($area,$city,$department,$stime,$etime,$type,$page,$pageSize,$group);
        if($result === false)
        {
            return $this->logicError($service->error);
        }
        return ['msg' => $result];
    }
}