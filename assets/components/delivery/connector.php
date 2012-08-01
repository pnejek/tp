<?php
/**
 * delivery Connector
 *
 * @package delivery
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('delivery.core_path',null,$modx->getOption('core_path').'components/delivery/');
require_once $corePath.'model/delivery/delivery.class.php';
$modx->delivery = new delivery($modx);

$modx->lexicon->load('delivery:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->delivery->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));