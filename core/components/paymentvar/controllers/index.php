<?php
/**
 * @package paymentvar
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)).'/model/paymentvar/paymentvar.class.php';
$paymentvar = new paymentvar($modx);
return $paymentvar->initialize('mgr');