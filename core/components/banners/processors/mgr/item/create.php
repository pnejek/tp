<?php


if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$item = $modx->newObject('bannersItem');
$item->fromArray($_POST);

if ($item->save() == false) {
    return $modx->error->failure($modx->lexicon('banners.item_err_save'));
}

return $modx->error->success('',$item);