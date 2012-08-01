<?php

$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start',$_REQUEST,0);
$limit = $modx->getOption('limit',$_REQUEST,20);
$sort = $modx->getOption('sort',$_REQUEST,'name');
$dir = $modx->getOption('dir',$_REQUEST,'ASC');


$c = $modx->newQuery('bannersItem');


$count = $modx->getCount('bannersItem',$c);



$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$items = $modx->getCollection('bannersItem',$c);

$list = array();
foreach ($items as $item) {
    $itemArray = $item->toArray();
    $list[]= $itemArray;
}
return $this->outputArray($list,$count);