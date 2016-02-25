<?php
/**
 * 订单工具类
 * @author		zhaozhonglin@shinc.net
 * @version		v1.0
 * @copyright	shinc
 */
namespace App\Libraries;
class OrderUtil{
    
    /**
     * 生成订单号
     */
    public function createOrdersn() {
        date_default_timezone_set('PRC');
        $yCode = array('A','B','C','D','E','F','G','H','I','J');
        $orderSn = '';
        $orderSn .= $yCode[(intval(date('Y')) - 1970) % 10];
        $orderSn .= strtoupper(dechex(date('m')));
        $orderSn .= date('d').substr(time(), -5);
        $orderSn .= substr(microtime(), 2, 6);
        return $orderSn;
    }
}
?>