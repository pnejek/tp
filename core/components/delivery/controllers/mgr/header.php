<?php

/**
 * Loads the header for mgr pages.
 *
 * @package delivery
 * @subpackage controllers
 */
$modx->regClientCSS($delivery->config['cssUrl'].'mgr.css');
$modx->regClientStartupScript($delivery->config['jsUrl'].'mgr/delivery.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    delivery.config = '.$modx->toJSON($delivery->config).';
    delivery.config.connector_url = "'.$delivery->config['connectorUrl'].'";
    delivery.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
});
</script>');

return '';