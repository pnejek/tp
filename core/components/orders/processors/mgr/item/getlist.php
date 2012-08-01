<?php
/**
 * Get a list of Items
 *
 * @package orders
 * @subpackage processors
 */
$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start',$_REQUEST,0);
$limit = $modx->getOption('limit',$_REQUEST,20);
$sort = $modx->getOption('sort',$_REQUEST,'ID');
$dir = $modx->getOption('dir',$_REQUEST,'ASC');
$status = $modx->getOption('STATUS',$_REQUEST,-1);

$c = $modx->newQuery('ordersItem');
if ($status>=0) {
$c->where(array('STATUS' => $status));
$c->sortby('DATE','DESC');
}
else {
$c->where(array('STATUS:>=' => $status));
$c->sortby('DATE','DESC');
}

$count = $modx->getCount('ordersItem',$c);



$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$items = $modx->getCollection('ordersItem',$c);

$list = array();
foreach ($items as $item) {
    $itemArray = $item->toArray();
    $list[]= $itemArray;
}
return $this->outputArray($list,$count);