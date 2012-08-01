<?php
/**
 * Loads the home page.
 *
 * @package paymentvar
 * @subpackage controllers
 */
$modx->regClientStartupScript($paymentvar->config['jsUrl'].'mgr/widgets/items.grid.js');
$modx->regClientStartupScript($paymentvar->config['jsUrl'].'mgr/widgets/home.panel.js');
$modx->regClientStartupScript($paymentvar->config['jsUrl'].'mgr/sections/home.js');
$output = '<div id="paymentvar-panel-home-div"></div>';

return $output;
