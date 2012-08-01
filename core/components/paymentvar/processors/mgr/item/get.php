<?php
/**
 * Get an Item
 * 
 * @package paymentvar
 * @subpackage processors
 */
/* get board */
if (empty($scriptProperties['ID'])) return $modx->error->failure($modx->lexicon('paymentvar.item_err_ns'));
$item = $modx->getObject('paymentvarItem', array('ID'=>$scriptProperties['id']));
if (!$item) return $modx->error->failure($modx->lexicon('paymentvar.item_err_nf'));

/* output */
$itemArray = $item->toArray('',true);
return $modx->error->success('',$itemArray);