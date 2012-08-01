<?php
/**
 * Remove an Item.
 * 
 * @package paymentvar
 * @subpackage processors
 */
/* get board */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('paymentvar.item_err_ns'));
$item = $modx->getObject('paymentvarItem', array ('ID'=>$scriptProperties['id']));
if (!$item) return $modx->error->failure($modx->lexicon('paymentvar.item_err_nf'));

if ($item->remove() == false) {
    return $modx->error->failure($modx->lexicon('paymentvar.item_err_remove'));
}

/* output */
return $modx->error->success('',$item);