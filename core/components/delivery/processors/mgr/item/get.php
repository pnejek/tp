<?php
/**
 * Get an Item
 * 
 * @package delivery
 * @subpackage processors
 */
/* get board */
if (empty($scriptProperties['ID'])) return $modx->error->failure($modx->lexicon('delivery.item_err_ns'));
$item = $modx->getObject('deliveryItem', array('ID'=>$scriptProperties['id']));
if (!$item) return $modx->error->failure($modx->lexicon('delivery.item_err_nf'));

/* output */
$itemArray = $item->toArray('',true);
return $modx->error->success('',$itemArray);