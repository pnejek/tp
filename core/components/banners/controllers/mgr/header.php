<?php

$modx->regClientCSS($banners->config['cssUrl'].'mgr.css');
$modx->regClientStartupScript($banners->config['jsUrl'].'mgr/banners.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    banners.config = '.$modx->toJSON($banners->config).';
    banners.config.connector_url = "'.$banners->config['connectorUrl'].'";
    banners.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
});
</script>');

return '';