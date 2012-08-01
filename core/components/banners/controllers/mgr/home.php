<?php

$modx->regClientStartupScript($banners->config['jsUrl'].'mgr/widgets/items.grid.js');
$modx->regClientStartupScript($banners->config['jsUrl'].'mgr/widgets/home.panel.js');
$modx->regClientStartupScript($banners->config['jsUrl'].'mgr/sections/home.js');
$output = '<div id="banners-panel-home-div"></div>';

return $output;
