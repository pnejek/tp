<?php
/**
 * Loads the home page.
 *
 * @package delivery
 * @subpackage controllers
 */
$modx->regClientStartupScript($delivery->config['jsUrl'].'mgr/widgets/items.grid.js');
$modx->regClientStartupScript($delivery->config['jsUrl'].'mgr/widgets/home.panel.js');
$modx->regClientStartupScript($delivery->config['jsUrl'].'mgr/sections/home.js');
$output = '<div id="delivery-panel-home-div"></div>';

return $output;
