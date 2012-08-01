<?php
/**
 * paymentvar Connector
 *
 * @package paymentvar
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('paymentvar.core_path',null,$modx->getOption('core_path').'components/paymentvar/');
require_once $corePath.'model/paymentvar/paymentvar.class.php';
$modx->paymentvar = new paymentvar($modx);

$modx->lexicon->load('paymentvar:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->paymentvar->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));