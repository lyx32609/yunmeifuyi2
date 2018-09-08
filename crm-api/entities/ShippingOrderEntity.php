<?php
namespace app\entities;

class ShippingOrderEntity
{
    /**
     * @var int 订单编号
     */
    public $order_id;
    /**
     * @var int 发货单来源, 可用的取值为\app\models\ShippingOrder::SOURCE_*
     */
    public $source;
    
    /**
     * @var int 物流运输单号
     */
    public $logistics_no;
    
    /**
     * @var string 发货单备注
     */
    public $memo;
    
}