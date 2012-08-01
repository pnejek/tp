<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('banners.core_path',null,$modx->getOption('core_path').'components/banners/');
require_once $corePath.'model/banners/banners.class.php';
$modx->banners = new banners($modx);

$modx->lexicon->load('banners:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->banners->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));