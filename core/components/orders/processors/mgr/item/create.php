<?php
/**
 * Create an Item
 * 
 * @package orders
 * @subpackage processors
 */
/*$alreadyExists = $modx->getObject('ordersItem',array(
    'name' => $_POST['name'],
));
if ($alreadyExists) {
    $modx->error->addField('name',$modx->lexicon('orders.item_err_ae'));
}*/

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$item = $modx->newObject('ordersItem');
$item->fromArray($_POST);

if ($item->save() == false) {
    return $modx->error->failure($modx->lexicon('orders.item_err_save'));
}

return $modx->error->success('',$item);