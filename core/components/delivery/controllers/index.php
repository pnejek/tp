<?php
/**
 * @package delivery
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)).'/model/delivery/delivery.class.php';
$delivery = new delivery($modx);
return $delivery->initialize('mgr');