<?php
/**
 * Update an Item
 * 
 * @package paymentvar
 * @subpackage processors
 */
/* get board */
if (empty($scriptProperties['ID'])) return $modx->error->failure($modx->lexicon('paymentvar.item_err_ns'));
$item = $modx->getObject('paymentvarItem', array('ID'=>$scriptProperties['ID']));
if (!$item) return $modx->error->failure($modx->lexicon('paymentvar.item_err_nf'));

$item->fromArray($scriptProperties);

if ($item->save() == false) {
    return $modx->error->failure($modx->lexicon('paymentvar.item_err_save'));
}

/* output */
$itemArray = $item->toArray('',true);
return $modx->error->success('',$itemArray);