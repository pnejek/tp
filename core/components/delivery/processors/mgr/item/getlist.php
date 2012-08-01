<?php
/**
 * Get a list of Items
 *
 * @package delivery
 * @subpackage processors
 */
$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start',$_REQUEST,0);
$limit = $modx->getOption('limit',$_REQUEST,20);
$sort = $modx->getOption('sort',$_REQUEST,'name');
$dir = $modx->getOption('dir',$_REQUEST,'ASC');


$c = $modx->newQuery('deliveryItem');


$count = $modx->getCount('deliveryItem',$c);



$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$items = $modx->getCollection('deliveryItem',$c);

$list = array();
foreach ($items as $item) {
    $itemArray = $item->toArray();
    $list[]= $itemArray;
}
return $this->outputArray($list,$count);