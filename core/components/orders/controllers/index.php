<?php
/**
 * @package orders
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)).'/model/orders/orders.class.php';
$orders = new orders($modx);
return $orders->initialize('mgr');