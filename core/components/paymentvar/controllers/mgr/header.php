<?php

/**
 * Loads the header for mgr pages.
 *
 * @package paymentvar
 * @subpackage controllers
 */
$modx->regClientCSS($paymentvar->config['cssUrl'].'mgr.css');
$modx->regClientStartupScript($paymentvar->config['jsUrl'].'mgr/paymentvar.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    paymentvar.config = '.$modx->toJSON($paymentvar->config).';
    paymentvar.config.connector_url = "'.$paymentvar->config['connectorUrl'].'";
    paymentvar.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
});
</script>');

return '';