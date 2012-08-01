<?php
/**
 * Get a list of Items
 *
 * @package paymentvar
 * @subpackage processors
 */
$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start',$_REQUEST,0);
$limit = $modx->getOption('limit',$_REQUEST,20);
$sort = $modx->getOption('sort',$_REQUEST,'name');
$dir = $modx->getOption('dir',$_REQUEST,'ASC');


$c = $modx->newQuery('paymentvarItem');


$count = $modx->getCount('paymentvarItem',$c);



$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$items = $modx->getCollection('paymentvarItem',$c);

$list = array();
foreach ($items as $item) {
    $itemArray = $item->toArray();
    $list[]= $itemArray;
}
return $this->outputArray($list,$count);