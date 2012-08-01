<?php
/**
 * Create an Item
 * 
 * @package paymentvar
 * @subpackage processors
 */
/*$alreadyExists = $modx->getObject('paymentvarItem',array(
    'name' => $_POST['name'],
));
if ($alreadyExists) {
    $modx->error->addField('name',$modx->lexicon('paymentvar.item_err_ae'));
}*/

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$item = $modx->newObject('paymentvarItem');
$item->fromArray($_POST);

if ($item->save() == false) {
    return $modx->error->failure($modx->lexicon('paymentvar.item_err_save'));
}

return $modx->error->success('',$item);