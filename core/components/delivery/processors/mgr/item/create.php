<?php
/**
 * Create an Item
 * 
 * @package delivery
 * @subpackage processors
 */
/*$alreadyExists = $modx->getObject('deliveryItem',array(
    'name' => $_POST['name'],
));
if ($alreadyExists) {
    $modx->error->addField('name',$modx->lexicon('delivery.item_err_ae'));
}*/

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$item = $modx->newObject('deliveryItem');
$item->fromArray($_POST);

if ($item->save() == false) {
    return $modx->error->failure($modx->lexicon('delivery.item_err_save'));
}

return $modx->error->success('',$item);