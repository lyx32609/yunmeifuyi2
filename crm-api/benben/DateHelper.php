<?php
namespace  app\benben;
class DateHelper
{
    /* 
     * 获取今天是几号
     *  
     *  */
    public static function getDateIsToday()
    {
        return date('d');
    }
    /**
     * 获取今天的开始时间
     */
    public static function getTodayStartTime()
    {
        return strtotime(date('Y-m-d', time()));
    }
    /**
     * 获取今天的结束时间
     */
    public static function getTodayEndTime()
    {
        return strtotime(date('Y-m-d', time()))+86400-1;
    }
    /**
     * 获取昨天的开始时间
     */
    public static function getYesterdayStartTime()
    {
        return strtotime(date('Y-m-d', strtotime("-1 day")));
    }
    /**
     * 获取昨天的结束时间
     */
    public static function getYesterdayEndTime()
    {
        return strtotime(date('Y-m-d', strtotime("-1 day")))+86400-1;
    }
    
    /* *************************************************** */
    /* 周  */
    /* *************************************************** */
    
    
    /**
     * 获取今天是本周周几 
     * 周日是 0 周一到周六是 1 - 6 
     */
    public static function getDayOfTheWeek()
    {
        return $w=date('w');
    }
    /**
     * 获取本周的开始时间
     */
    public static function getThisWeekStartTime()
    {
        return $thisWeekStartTime=self::getTodayStartTime()-self::getDayOfTheWeek()*60*60*24;
    }
    /**
     * 获取本周的结束时间
     */
    public static function getThisWeekEndTime()
    {
        return $thisWeekEndTime=self::getTodayStartTime()+(7-self::getDayOfTheWeek())*60*60*24-1;
    }
    /**
     * 获取前几天的开始时间
     */
    public static function getDayStartTime($days)
    {
        $days = $days-1;
        return strtotime(date('Y-m-d', strtotime("-$days day")));
    }
    /**
     * 获取最近4周的开始时间
     */
    public static function getWeekStartTime($weeks)
    {
        if ($weeks==0){
            return  strtotime(date('Y-m-d',(time()-((date('w')==0?7:date('w'))-1)*24*3600)));
        }else {
            return  strtotime(date('Y-m-d',strtotime('-'.$weeks.' week last monday', time())));
        }
         
    }
    /* 获取 */
    
    
    /* *************************************************** */
    /* 月份 */
    /* *************************************************** */
    
    
    /* 
     * 获取当月月份
     *  
     *  */
    public static function getMonth()
    {
        return $month=date('m');
    }
    /* 
     * 获取月的总计天数
     *  */
    public static function getMonthTotalDay($status=1)
    {
        if($status==1)
        {
            //获取本月时间
            $month=date('m');
            $year=date('Y');
        }
        if($status==2)
        {
            //获取上月信息
            $data=self::getPreMonthMessage();
            $month=$data['lastmonth'];
            $year=$data['lastyear'];
        }
     //   return $monthTotalDay=cal_days_in_month(CAL_GREGORIAN,$month,$year);
        return $monthTotalDay=date('t',strtotime($year.'-'.$month));
    }
    
    /**
     * 获取本月的开始时间
     */
    public static function getMonthStartTime()
    {
        return $monthStart = strtotime(date("Y-m-01",strtotime(date('Y-m',time()))));
    }
    /**
     * 获取本月的结束时间
     */
    public static function getMonthEndTime()
    {
        return mktime(23,59,59,date('m'),date('t'),date('Y'));
    }
    /**
     * 获取上个月的开始时间
     */
    public static function getPreMonthStartTime()
    {
        return strtotime(date('Y-m-01', strtotime("-1 month")));
    }
    /**
     * 获取上个月的结束时间
     */
    public static function getPreMonthEndTime()
    {
       $lastMonthLastDay=self::getPreMonth();
       return strtotime($lastMonthLastDay.' 23:59:59');
    }
    
    /* 
     * 获取上月时间
     *  1 结束时间  2  开始时间
     *  */
    private  static function getPreMonth($status=1)
    {
        
        $data=self::getPreMonthMessage();
        $lastStartDay = $data['lastyear'] . '-' . $data['lastmonth'] . '-1';
        $lastEndDay = $data['lastyear'] . '-' . $data['lastmonth'] . '-' . date('t', strtotime($lastStartDay));

        switch ($status){
            case 1:
                return $lastEndDay;
                break;
            case 2:
                return $lastStartDay;
                break;
        }
    }
    /*
     * 获取上月的年和月信息
     *  */
    public  static function getPreMonthMessage()
    {
        $thismonth = date('m');
        $thisyear = date('Y');
        if ($thismonth == 1) {
            $lastmonth = 12;
            $lastyear = $thisyear - 1;
        } else {
            $lastmonth = $thismonth - 1;
            $lastyear = $thisyear;
        }
        return [
            'lastmonth'=>$lastmonth,
            'lastyear'=>$lastyear
            
        ];
    }
    
}