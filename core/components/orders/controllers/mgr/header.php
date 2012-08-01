<?php

/**
 * Loads the header for mgr pages.
 *
 * @package orders
 * @subpackage controllers
 */
$modx->regClientCSS($orders->config['cssUrl'].'mgr.css');
$modx->regClientStartupScript($orders->config['jsUrl'].'mgr/orders.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    orders.config = '.$modx->toJSON($orders->config).';
    orders.config.connector_url = "'.$orders->config['connectorUrl'].'";
    orders.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
});
</script>');

return '';