<?php

/* get board */
if (empty($scriptProperties['ID'])) return $modx->error->failure($modx->lexicon('banners.item_err_ns'));
$item = $modx->getObject('bannersItem', array('ID'=>$scriptProperties['id']));
if (!$item) return $modx->error->failure($modx->lexicon('banners.item_err_nf'));

/* output */
$itemArray = $item->toArray('',true);
return $modx->error->success('',$itemArray);