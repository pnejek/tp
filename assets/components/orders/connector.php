<?php
/**
 * orders Connector
 *
 * @package orders
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('orders.core_path',null,$modx->getOption('core_path').'components/orders/');
require_once $corePath.'model/orders/orders.class.php';
$modx->orders = new orders($modx);

$modx->lexicon->load('orders:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->orders->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));