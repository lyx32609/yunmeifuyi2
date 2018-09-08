<?php
namespace official\api\staffList;

use app\foundation\Api;
use app\services\StaffListService;

/**
 * 人员查询
 * @return array result [{ groupid:122, groupName:”深林狼”， groupStaff:[{staffed:”1232”,staffName:”张师傅”},{第二个组员}]}，{第二个小组}}]
 * @author lzk
 */
class StaffListApi extends Api
{
    public function run()
    {
        $data = StaffListService::instance()->getStaffList();
        return ['result'=>$data];
    }
}