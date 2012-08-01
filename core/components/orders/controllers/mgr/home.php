<?php
/**
 * Loads the home page.
 *
 * @package orders
 * @subpackage controllers
 */
$modx->regClientStartupScript($orders->config['jsUrl'].'mgr/widgets/items.grid.js');
$modx->regClientStartupScript($orders->config['jsUrl'].'mgr/widgets/home.panel.js');
$modx->regClientStartupScript($orders->config['jsUrl'].'mgr/sections/home.js');
$output = '<div id="orders-panel-home-div"></div>';

return $output;
